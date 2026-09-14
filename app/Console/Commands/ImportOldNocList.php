<?php

namespace App\Console\Commands;

use App\Models\OldNocDetail;
use App\Models\PropertyMaster;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImportOldNocList extends Command
{
    protected $signature   = 'noc:import-old {colony=All} {--limit=0 : Only process first N API items (0 = all)}';
    protected $description = 'Import L&DO NOC list, store PDFs locally, dedupe via status, and backfill sections';

    public function handle(): int
    {
        $colony = (string) $this->argument('colony');
        $limit  = (int) $this->option('limit');

        $baseUrl = (string) config('constants.ldoOldNocListByColony'); // e.g. https://ldo.gov.in/edhartiapi/Api/NOCList/ListbyColony
        if (!$baseUrl) {
            $this->error('Missing config("constants.ldoOldNocListByColony").');
            return self::FAILURE;
        }

        $url = $baseUrl.'?ColonyCode='.$colony;
        $this->info("Fetching API: {$url}");

        $apiItems = [];
        try {
            $resp = Http::timeout(60)->retry(2, 600)->get($url);
            if ($resp->ok()) {
                $json = $resp->json();
                $apiItems = Arr::get($json, 'Data.NOCsList', []);
                if (!is_array($apiItems)) {
                    Log::error('Unexpected API payload structure', ['payload' => $json]);
                    $this->warn('Unexpected API payload; will only process backlog (status=0).');
                    $apiItems = [];
                }
            } else {
                Log::warning('API non-OK', ['status' => $resp->status(), 'url' => $url]);
                $this->warn("API HTTP {$resp->status()} — proceeding with backlog only.");
            }
        } catch (\Throwable $e) {
            Log::error('API exception', ['url' => $url, 'e' => $e]);
            $this->warn('API exception — proceeding with backlog only.');
        }

        if ($limit > 0 && !empty($apiItems)) {
            $apiItems = array_slice($apiItems, 0, $limit);
        }

        // Preload sections from property_masters
        $propertyIds = collect($apiItems)->pluck('PropertyID')->filter()->unique()->values()->all();
        $sectionMap  = $this->fetchSectionMap($propertyIds);

        $apiCount = 0;
        $skippedExisting = 0;
        $upserts = 0;
        $dlOk = 0;
        $dlFail = 0;

        // Process current API items
        foreach ($apiItems as $row) {
            $apiCount++;

            $applicationNumber = trim((string) Arr::get($row, 'applicationNumber', ''));
            if ($applicationNumber === '') {
                Log::warning('Skipped: empty applicationNumber', ['row' => $row]);
                continue;
            }

            // Dedupe guard: if status=1 exists, skip entirely
            $existsStored = OldNocDetail::where('application_number', $applicationNumber)
                ->where('status', 1)
                ->exists();
            if ($existsStored) {
                $skippedExisting++;
                $this->line("⏭  Exists (status=1), skip: {$applicationNumber}");
                continue;
            }

            $mapped = $this->mapRowToDb($row, $sectionMap);

            // Create/update with status=0 (pending) until file is saved
            $model = OldNocDetail::updateOrCreate(
                ['application_number' => $applicationNumber],
                array_merge(
                    Arr::except($mapped, ['file_path', 'status']),
                    ['status' => 0]
                )
            );
            $upserts++;

            // If file_path exists & file present, mark as stored (idempotency)
            if ($model->file_path && Storage::disk('local')->exists($model->file_path)) {
                $this->updateStatus($model, $model->file_path, 1);
                $this->line("✔  Already had PDF: {$applicationNumber} → {$model->file_path}");
                continue;
            }

            // Download strictly as PDF
            $ok = $this->downloadPdf(
                $model,
                (string) $mapped['file_link'],
                (string) $mapped['colony_name'],
                (string) $mapped['property_id'],
                $applicationNumber
            );
            $ok ? $dlOk++ : $dlFail++;
        }

        // Retry backlog: every status=0 item in DB
        $this->info('Re-trying backlog (status=0)…');
        $backlog = OldNocDetail::query()
            ->where('status', 0)
            ->whereNotNull('file_link')
            ->orderBy('id')
            ->cursor();

        foreach ($backlog as $model) {
            // If already present on disk, flip to 1
            if ($model->file_path && Storage::disk('local')->exists($model->file_path)) {
                $this->updateStatus($model, $model->file_path, 1);
                continue;
            }

            $ok = $this->downloadPdf(
                $model,
                (string) $model->file_link,
                (string) $model->colony_name,
                (string) $model->property_id,
                (string) $model->application_number
            );
            $ok ? $dlOk++ : $dlFail++;
        }

        $this->table(
            ['API items', 'Skipped existing (status=1)', 'DB upserts', 'PDFs saved', 'PDFs failed'],
            [[ $apiCount, $skippedExisting, $upserts, $dlOk, $dlFail ]]
        );

        return self::SUCCESS;
    }

    private function mapRowToDb(array $row, array $sectionMap): array
    {
        $nz = fn ($v) => ($v === null || trim((string)$v) === '' || (string)$v === '0') ? null : trim((string)$v);
        $toDate = function ($v) {
            if (!$v) return null;
            try { return CarbonImmutable::parse($v)->toDateString(); } catch (\Throwable) { return null; }
        };
        $urlOrNull = function ($v) {
            $v = trim((string)$v);
            return filter_var($v, FILTER_VALIDATE_URL) ? $v : null;
        };

        $propertyId = (string) Arr::get($row, 'PropertyID');

        return [
            'application_number' => (string) Arr::get($row, 'applicationNumber'),
            'property_id'        => $propertyId,
            'colony_code'        => (string) Arr::get($row, 'ColonyCode'),
            'colony_name'        => (string) Arr::get($row, 'ColonyName'),
            'block_number'       => $nz(Arr::get($row, 'BlockNumber')),
            'property_number'    => $nz(Arr::get($row, 'PropertyNumber')),
            'known_as'           => $nz(Arr::get($row, 'KnownAs')),
            'section'            => $sectionMap[$propertyId] ?? null, // from property_masters
            'file_num'           => $nz(Arr::get($row, 'fileNum')),
            'noc_issued_date'    => $toDate(Arr::get($row, 'NOCIssuedDate')),
            'dispatch_date'      => $toDate(Arr::get($row, 'dispatchDate')),
            'file_link'          => $urlOrNull(Arr::get($row, 'Filelink')) ?? (string) Arr::get($row, 'Filelink', ''),
            // file_path/status handled after download
        ];
    }

    /**
     * Download and save strictly as PDF:
     * storage/app/noc/{ColonyName}/{PropertyID}/NOC-{applicationNumber}.pdf
     * On success: set status=1 and file_path. On failure: keep status=0.
     */
    private function downloadPdf(OldNocDetail $model, string $fileLink, string $colonyName, string $propertyId, string $applicationNumber): bool
    {
        if (!$fileLink || !filter_var($fileLink, FILTER_VALIDATE_URL)) {
            Log::warning('Invalid/missing Filelink; cannot download', [
                'id' => $model->id, 'application' => $applicationNumber, 'url' => $fileLink
            ]);
            return false;
        }

        $dir = $this->buildDir($colonyName, $propertyId);
        $filename = "NOC-{$applicationNumber}.pdf";
        $path = "{$dir}/{$filename}";

        try {
            $resp = Http::timeout(60)->retry(2, 800)->get($fileLink);
            if (!$resp->ok()) {
                Log::error('PDF download HTTP error', [
                    'application' => $applicationNumber, 'status' => $resp->status(), 'url' => $fileLink
                ]);
                $this->warn("✖  HTTP {$resp->status()} for {$applicationNumber}");
                return false;
            }

            $bytes = $resp->body();

            // Optional guard: if server returns HTML by mistake, still save as .pdf but log
            $ct = strtolower((string) $resp->header('Content-Type', ''));
            if ($ct && str_contains($ct, 'text/html')) {
                Log::warning('Content-Type is text/html; expected PDF', [
                    'application' => $applicationNumber, 'url' => $fileLink, 'content_type' => $ct
                ]);
            }

            Storage::disk('local')->put($path, $bytes);
            $this->updateStatus($model, $path, 1);

            $this->line("✔  Saved PDF: {$applicationNumber} → {$path}");
            return true;

        } catch (\Throwable $e) {
            Log::error('PDF download exception', [
                'id' => $model->id, 'application' => $applicationNumber, 'url' => $fileLink, 'e' => $e
            ]);
            $this->warn("✖  Exception for {$applicationNumber}: ".$e->getMessage());
            return false;
        }
    }

    private function updateStatus(OldNocDetail $model, ?string $filePath, int $status): void
    {
        $model->file_path = $filePath;
        $model->status    = $status; // 1 = stored (dedupe), 0 = pending
        $model->save();
    }

    private function fetchSectionMap(array $propertyIds): array
    {
        if (empty($propertyIds)) return [];
        // Assumes property_masters has: old_property_id, section
        return PropertyMaster::query()
            ->whereIn('old_propert_id', $propertyIds)
            ->pluck('section_code', 'old_propert_id')
            ->all();
    }

    private function buildDir(string $colonyName, string $propertyId): string
    {
        $safeColony = $this->sanitizeDir($colonyName) ?: 'UnknownColony';
        $safeProp   = $this->sanitizeDir($propertyId) ?: 'UnknownProperty';
        return "noc/{$safeColony}/{$safeProp}";
    }

    private function sanitizeDir(string $name): string
    {
        // Remove slashes and control chars; keep letters, digits, space, dash, underscore, dot
        $name = str_replace(['/', '\\'], '-', $name);
        $name = preg_replace('/[^\pL\pN\-\._\s]/u', '', $name) ?? '';
        return trim($name);
    }
}
