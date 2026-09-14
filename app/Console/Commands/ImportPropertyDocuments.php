<?php

namespace App\Console\Commands;

use App\Models\Flat;
use App\Models\PropertyMaster;
use App\Models\PropertyScannedFile;
use App\Models\SplitedPropertyDetail;
use App\Models\TempScannedFilesPropertyId;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportPropertyDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-property-documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

 
    private function handlePropertyDocs($masterProperty, $propertyId, $splitedPropertyDetailId = null, $flatId = null)
    {
        // $colonyName = Str::slug($masterProperty->newColony->name);
        $colonyName = $masterProperty->newColony->name;
        $url = config('constants.propertyDocList') . $propertyId;
        Log::info($url);
        $docResponse = Http::timeout(20)->get($url);

        if ($docResponse->successful()) {
            $resp = $docResponse->json();
            if (count($resp) > 0) {
                $data = $resp[0];
                $fileUrlPath = $data['Path'] ?? '';
                $ListFileNames = $data['ListFileName'] ?? [];
                Log::info('ListFileNames', $ListFileNames);

                // ---- NEW: prepare naming pieces and find current max VOL for this property ----
                $cleanColony = Str::of($colonyName)->upper()->replace(' ', '_')->__toString();
                $existingDocs = PropertyScannedFile::where('old_property_id', $propertyId)->pluck('document_name')->all();
                $maxVol = 0;
                foreach ($existingDocs as $dn) {
                    if (preg_match('/_VOL_(\d+)/i', (string) $dn, $m)) {
                        $vol = (int) ($m[1] ?? 0);
                        if ($vol > $maxVol) $maxVol = $vol;
                    }
                }
                // -----------------------------------------------------------------------------

                foreach ($ListFileNames as $file) {
                    // $fileName = $file['PropertyFileName'] ?? null;
                    // if (!$fileName) {
                    //     Log::warning('filename not found');
                    //     continue;
                    // };

                     // NEW: old file name stored in DB should be decrypted/original name
                    $fileName = $file['PropertyFileNameDcrpt'] ?? null;

                    // NEW: token/hash used for download should be appended to Path
                    $fileToken = $file['PropertyFilepdf'] ?? null;

                    if (!$fileName) {
                        Log::warning('PropertyFileNameDcrpt not found');
                        continue;
                    }

                    if (!$fileToken) {
                        Log::warning('PropertyFilepdf not found for file: ' . $fileName);
                        continue;
                    }

                    if (PropertyScannedFile::where([
                        'property_master_id' => $masterProperty->id,
                        'splited_property_detail_id' => $splitedPropertyDetailId,
                        'flat_id' => $flatId,
                        'old_property_id' => $propertyId,
                        'colony_name' => $colonyName,
                        // use NEW document_name (no extension)
                        'old_property_file_name' => $fileName,
                    ])->whereNotNull('document_path')->exists()) {
                        $this->info('Document already exist new_document_name =>'. $fileName,
                           
                        );
                        continue;
                    }
                    


                    // ---- NEW: compute new document_name and file path (keep original extension) ----
                    $maxVol++; // next volume number
                    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) ?: 'pdf';
                    // controller/UI convention: document_name WITHOUT extension
                    $newDocName = "{$propertyId}_{$cleanColony}_VOL_{$maxVol}";
                    // stored file should include extension
                    $safeBaseName = $newDocName . '.' . $ext;

                    // log mapping old->new for debugging
                    Log::info('Renaming imported file', [
                        'old_name' => $fileName,
                        'new_document_name' => $newDocName,
                        'saved_filename' => $safeBaseName,
                    ]);
                    // ------------------------------------------------------------------------------

                    $matchArray = [
                        'property_master_id' => $masterProperty->id,
                        'splited_property_detail_id' => $splitedPropertyDetailId,
                        'flat_id' => $flatId,
                        'old_property_id' => $propertyId,
                        'colony_name' => $colonyName,
                        // use NEW document_name (no extension)
                        'document_name' => $newDocName,
                        'old_property_file_name' => $fileName,
                    ];
                    $updateArray = [
                        'document_path' => null,
                        'status' => 0,
                        
                    ];

                    // NEW: download using PropertyFilepdf appended to Path
                        $cleanToken = ltrim($fileToken, '/\\');
                        $encodedToken = rawurlencode($cleanToken);
                        $fileUrl = $fileUrlPath . $encodedToken; // DO NOT add extra slash
                        Log::warning('fileUrl' . $fileUrl);

                        $fileResponse = Http::get($fileUrl);

                        // fallback attempt (keep same behavior, but still use token)
                        if (!$fileResponse->successful()) {
                            $fileUrl = rtrim($fileUrlPath, '/') . '/' . $fileToken;
                            $fileResponse = Http::get($fileUrl);
                        }

                        if ($fileResponse->successful()) {
                            $fileContents = $fileResponse->body();
                            if (strlen($fileContents) < 5000) {
                                $this->warn('invalid file. skipping ' . $fileName);
                            } else {
                                $newFilePath = "documents/{$colonyName}/{$propertyId}/scannedFiles/{$safeBaseName}";
                                Storage::disk('public')->put($newFilePath, $fileResponse->body());
                                $updateArray = [
                                    'document_name' => $newDocName,
                                    'document_path' => $newFilePath,
                                ];
                                $this->info("property - $propertyId document {$safeBaseName} imported (from {$fileName}).");
                            }
                        } else {
                            $this->warn("Failed to download file: $fileUrl");
                        }
                    PropertyScannedFile::updateOrCreate($matchArray, $updateArray);
                }
                $downloadedCount = PropertyScannedFile::where([
                    'property_master_id' => $masterProperty->id,
                    'splited_property_detail_id' => $splitedPropertyDetailId,
                    'flat_id' => $flatId,
                    'old_property_id' => $propertyId,
                    'colony_name' => $colonyName
                ])->whereNotNull('document_path')->count();
                //$this->info($downloadedCount->toSql() . '------' . implode(', ', $downloadedCount->getBindings()));
                $this->info('property Id = ' . $propertyId . 'downloadedCount = ' . $downloadedCount . ", total-count = " . count($ListFileNames));

                if ($downloadedCount >= count($ListFileNames)) {
                    PropertyScannedFile::where([
                        'property_master_id' => $masterProperty->id,
                        'splited_property_detail_id' => $splitedPropertyDetailId,
                        'flat_id' => $flatId,
                        'old_property_id' => $propertyId,
                        'colony_name' => $colonyName,
                    ])->whereNotNull('document_path')->update(['status' => 1]);
                }
            } else {
                Log::warning('no data for this property');
            }
        } else {
            Log::warning("API returned error for property {$propertyId}: " . $docResponse->status());
        }
    }

    

    public function handle()
    {


        // NOTE: replace column name if your temp table column differs
        $tempIds = TempScannedFilesPropertyId::query()
            ->select('property_id')
            ->distinct()
            ->pluck('property_id');
        Log::info('tempProps = ' . $tempIds->count());

        if ($tempIds->isEmpty()) {
            $this->info('No property ids found in temp_scanned_files_property_id');
            return;
        }

        foreach ($tempIds as $propertyId) {
            try {
                // 1) do not re-hit if already completed
                $alreadyCompleted = PropertyScannedFile::where('old_property_id', $propertyId)
                    ->where('status', 1)
                    ->exists();

                if ($alreadyCompleted) {
                    $this->info("Skipping property {$propertyId} (already status=1).");
                    continue;
                }

                // 2) match in property_masters first
                $masterProperty = PropertyMaster::whereNull('is_joint_property')
                    ->where('old_propert_id', $propertyId)
                    ->first();

                if ($masterProperty) {
                    if (!$masterProperty->newColony) {
                        Log::warning("Skipping property ID {$masterProperty->id} due to missing colony.");
                        continue;
                    }
                    $this->handlePropertyDocs($masterProperty, $propertyId);
                    continue;
                }

                // 3) if not in masters, match in splited_property_details
                $sp = SplitedPropertyDetail::where('old_property_id', $propertyId)->first();
                if ($sp) {
                    // keep same behavior as your existing code
                    $this->handlePropertyDocs($sp->master, $propertyId, $sp->id);
                    continue;
                }

                // 4) if not found in either, do not hit api
                Log::warning("Skipping property {$propertyId} - not found in property_masters or splited_property_details.");
            } catch (\Exception $e) {
                Log::warning("Failed for property {$propertyId}: " . $e->getMessage());
            }
        }

        $this->info('All available documents inported');
    }
}
