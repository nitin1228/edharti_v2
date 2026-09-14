<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UpdateUserRegistrationActionTakenBy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-action-taken-by';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update action_taken_by after 5 and 10 days for RS_NEW status';

    /* public function handle()
    {
        // Step 1: Get IDs of RS_NEW and RS_UREW from items table
        $statusIds = DB::table('items')
            ->whereIn('item_code', ['RS_NEW', 'RS_UREW'])
            ->pluck('id'); // returns a collection of IDs

        if ($statusIds->isEmpty()) {
            $this->error('RS_NEW or RS_UREW status not found in items table.');
            return;
        }

        // Step 2: Calculate cut-off dates
        $fiveDaysAgo = Carbon::now()->subDays(5);

        // Step 3: Update to deputy-lndo after 5 days (if still section-officer)
        DB::table('user_registrations')
            ->whereIn('status', $statusIds) // ✅ handles both RS_NEW and RS_UREW
            ->where('action_taken_by', 'section-officer')
            ->whereDate('created_at', '<=', $fiveDaysAgo)
            ->update([
                'action_taken_by' => 'deputy-lndo',
                'forward_through' => 'cron'
            ]);

        $this->info('Records updated successfully for RS_NEW and RS_UREW.');
    } */

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Step 1: Get ID of RS_NEW from items table
        $rsNewStatusId = DB::table('items')->where('item_code', 'RS_NEW')->value('id');

        if (!$rsNewStatusId) {
            $this->error('RS_NEW status not found in items table.');
            Log::error('RS_NEW status not found in items table.');
            return;
        }

        // Step 2: Calculate cut-off date
        $fiveDaysAgo = Carbon::now()->subDays(5);

        // Step 3: Get records that will be updated
        $query = DB::table('user_registrations')
            ->where('status', $rsNewStatusId)
            ->where('action_taken_by', 'section-officer')
            ->whereDate('created_at', '<=', $fiveDaysAgo);

        $sql = Str::replaceArray('?', $query->getBindings(), $query->toSql());
        Log::info("Executing SQL: " . $sql);

        $records = $query->get(['id', 'applicant_number', 'created_at']);

        if ($records->isEmpty()) {
            $this->info('No records found to update.');
            Log::info('No user_registrations records found for update.');
            return;
        }

        // Log affected records before update
        foreach ($records as $record) {
            Log::info("Preparing update for ID: {$record->id}, Applicant: {$record->applicant_number}, Created At: {$record->created_at}");
        }

        // Step 4: Perform update
        $updated = DB::table('user_registrations')
            ->whereIn('id', $records->pluck('id'))
            ->update([
                'action_taken_by' => 'deputy-lndo',
                'forward_through' => 'cron',
                'updated_at' => now(),
            ]);

        // Step 5: Log summary
        $this->info("Updated {$updated} records from 'section-officer' to 'deputy-lndo'.");
        Log::info("Updated {$updated} records from 'section-officer' to 'deputy-lndo'.", [
            'record_ids' => $records->pluck('id')->toArray()
        ]);
    }
}
