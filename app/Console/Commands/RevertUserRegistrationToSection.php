<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RevertUserRegistrationToSection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:revert-user-registration-to-section';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revert reviwed user registrations back to section-officer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info("Revert User registration to section Running at: " . Carbon::now());

        // Step 1: Get ID of RS_UREW from items table
        $rsNewStatusId = DB::table('items')->where('item_code', 'RS_NEW')->value('id');
        $rsUnderReviewStatusId = DB::table('items')->where('item_code', 'RS_UREW')->value('id');

        if (!$rsUnderReviewStatusId) {
            $this->error('RS_UREW status not found in items table.');
            Log::error('RS_UREW status not found in items table.');
            return;
        }

        // Step 2: Calculate cut-off date
        $twoDaysAgo = Carbon::now()->subDays(2);

        // Step 3: Get records that will be updated
        $query = DB::table('user_registrations')
            ->where('status', $rsUnderReviewStatusId)
            ->where('action_taken_by', 'deputy-lndo')
            ->whereDate('updated_at', '<=', $twoDaysAgo);

        $sql = Str::replaceArray('?', $query->getBindings(), $query->toSql());
        Log::info("Executing SQL: " . $sql);

        $records = $query->get(['id', 'applicant_number', 'updated_at']);
        // dd($records);

        if ($records->isEmpty()) {
            $this->info('No records found to update.');
            Log::info('No user_registrations records found for update.');
            return;
        }

        // Log affected records before update
        foreach ($records as $record) {
            Log::info("Preparing update for ID: {$record->id}, Applicant: {$record->applicant_number}, Updated At: {$record->updated_at}");
        }

        // Step 4: Perform update
        $updated = DB::table('user_registrations')
            ->whereIn('id', $records->pluck('id'))
            ->update([
                'status' => $rsNewStatusId,
                'action_taken_by' => 'section-officer',
                'forward_through' => null,
                'updated_at' => now(),
            ]);

        // Step 5: Log summary
        $this->info("Updated {$updated} records from 'deputy-officer' to 'section-lndo'.");
        Log::info("Updated {$updated} records from 'deputy-officer' to 'section-lndo'.", [
            'record_ids' => $records->pluck('id')->toArray()
        ]);
    }
}
