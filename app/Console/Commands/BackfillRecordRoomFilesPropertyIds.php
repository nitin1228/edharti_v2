<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillRecordRoomFilesPropertyIds extends Command
{
    protected $signature = 'rrf:backfill-property-ids
                            {--batch=1000 : Batch size}
                            {--start=0 : Start after this record_room_files.id}
                            {--only-null : Only process rows where old_property_id/property_master_id/splited_property_detail_id are all NULL}';

    protected $description = 'Backfill old_property_id, property_master_id, splited_property_detail_id into record_room_files in batches';

    public function handle(): int
    {
        $batchSize = (int) $this->option('batch');
        $lastId = (int) $this->option('start');
        $onlyNull = (bool) $this->option('only-null');

        $this->info("Starting backfill. batch={$batchSize}, start_after_id={$lastId}, only_null=" . ($onlyNull ? 'yes' : 'no'));

        while (true) {
            // Get next batch ids (seek pagination)
            $idsQuery = DB::table('record_room_files')
                ->select('id')
                ->where('id', '>', $lastId)
                ->orderBy('id')
                ->limit($batchSize);

            if ($onlyNull) {
                $idsQuery->whereNull('old_property_id')
                        ->whereNull('property_master_id')
                        ->whereNull('splited_property_detail_id');
            }

            $ids = $idsQuery->pluck('id')->all();

            if (empty($ids)) {
                $this->info('Done. No more records.');
                break;
            }

            $minId = min($ids);
            $maxId = max($ids);

            // We update only those IDs in this batch, so no huge scans.
            // Using LEFT JOINs + COALESCE to prefer joint_split match over non_joint_lease.
            $sql = "
                UPDATE record_room_files rrf
                LEFT JOIN (
                    SELECT
                        rrf2.id AS rrf_id,
                        spd.old_property_id AS old_property_id,
                        spd.id AS splited_property_detail_id,
                        spd.property_master_id AS property_master_id
                    FROM record_room_files rrf2
                    JOIN property_masters pm
                      ON pm.new_colony_name = rrf2.colony_id
                     AND pm.block_no = rrf2.block
                     AND pm.is_joint_property = 1
                    JOIN splited_property_details spd
                      ON spd.property_master_id = pm.id
                     AND spd.plot_flat_no = rrf2.plot
                    WHERE rrf2.id BETWEEN ? AND ?
                ) jm ON jm.rrf_id = rrf.id
                LEFT JOIN (
                    SELECT
                        rrf3.id AS rrf_id,
                        pm.old_propert_id AS old_property_id,
                        NULL AS splited_property_detail_id,
                        pm.id AS property_master_id
                    FROM record_room_files rrf3
                    JOIN property_masters pm
                      ON pm.new_colony_name = rrf3.colony_id
                     AND pm.block_no = rrf3.block
                     AND pm.is_joint_property IS NULL
                    JOIN property_lease_details pld
                      ON pld.property_master_id = pm.id
                     AND pld.plot_or_property_number = rrf3.plot
                    WHERE rrf3.id BETWEEN ? AND ?
                ) njm ON njm.rrf_id = rrf.id
                SET
                    rrf.old_property_id = COALESCE(jm.old_property_id, njm.old_property_id),
                    rrf.splited_property_detail_id = COALESCE(jm.splited_property_detail_id, njm.splited_property_detail_id),
                    rrf.property_master_id = COALESCE(jm.property_master_id, njm.property_master_id)
                WHERE rrf.id BETWEEN ? AND ?
            ";

            // If you ONLY want to fill NULLs (and not overwrite existing), add:
            // AND rrf.old_property_id IS NULL AND rrf.property_master_id IS NULL AND rrf.splited_property_detail_id IS NULL
            if ($onlyNull) {
                $sql .= " AND rrf.old_property_id IS NULL AND rrf.property_master_id IS NULL AND rrf.splited_property_detail_id IS NULL";
            }

            DB::beginTransaction();
            try {
                $affected = DB::update($sql, [
                    $minId, $maxId,   // jm where range
                    $minId, $maxId,   // njm where range
                    $minId, $maxId,   // update where range
                ]);
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->error("Batch failed for IDs {$minId}..{$maxId}: " . $e->getMessage());
                return self::FAILURE;
            }

            $this->info("Updated batch IDs {$minId}..{$maxId} (rows affected: {$affected})");

            // Advance seek pointer
            $lastId = $maxId;
        }

        return self::SUCCESS;
    }
}
