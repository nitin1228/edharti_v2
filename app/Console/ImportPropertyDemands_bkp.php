<?php

namespace App\Console\Commands;

use App\Models\PropertyMaster;
use App\Models\SplitedPropertyDetail;
use App\Models\OldDemand;
use App\Models\OldDemandSubhead;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ImportPropertyDemands extends Command
{
    protected $signature = 'app:import-property-demands';
    protected $description = 'Import latest demands and subhead breakups for each property';

    /**
     * Fetch and store demand for a single property.
     */
    private function handleDemand($masterProperty, $propertyId, $splitedPropertyDetailId = null, $flatId = null)
    {
        $url = rtrim(config('constants.oldDemandByPropertyId'), '/') . '?PropertyID=' . urlencode($propertyId);

        Log::info("Processing demand for property_id={$propertyId} | URL={$url}");

        // Add retry to be more resilient
        $response = Http::timeout(20)
            ->retry(3, 500) // 3 attempts, 500 ms apart
            ->get($url);

        if (!$response->successful()) {
            Log::warning("Demand API returned error for property {$propertyId}: HTTP " . $response->status());
            return;
        }

        $json = $response->json();

        if (!is_array($json)) {
            Log::warning("Invalid JSON structure for property {$propertyId}", ['body' => $json]);
            return;
        }

        // API format:
        // { Status, Message, Data: { LatestDemanddetails: [...], SubHeadwiseBreakup: [...] } }
        $data           = $json['Data'] ?? [];
        $latestDemands  = $data['LatestDemanddetails'] ?? [];
        $subheads       = $data['SubHeadwiseBreakup'] ?? [];

        if (empty($latestDemands)) {
            Log::info("Property {$propertyId} has no LatestDemanddetails.");
            return;
        }

        // Usually one element, but loop to be safe
        foreach ($latestDemands as $demandRow) {
            $demandId      = $demandRow['DemandID'] ?? null;
            $propIdFromApi = $demandRow['PropertyID'] ?? null; // store exactly as API returns

            if (!$demandId) {
                Log::warning("Skipping demand with no DemandID for property {$propertyId}");
                continue;
            }

            // Transaction: demand row + all its subheads + status update
            DB::transaction(function () use ($demandRow, $subheads, $demandId, $propIdFromApi) {
                // Map property_status: Freehold => 951, else => 952
                $statusText            = $demandRow['PropertyStatus'] ?? '';
                $mappedPropertyStatus  = (trim($statusText) === 'Freehold') ? 952 : 951;
                $amount = $demandRow['Amount'] ?? 0;
                $outstanding = $demandRow['Outstanding'] ?? 0;
                $paid_amount = max (0, $amount-$outstanding);

                // Store in old_demands table
                $oldDemand = OldDemand::updateOrCreate(
                    [
                        'demand_id'   => $demandId,
                        'property_id' => $propIdFromApi,
                    ],
                    [
                        'new_demand_id'   => null,
                        'amount'          => $amount,
                        'paid_amount'     => $paid_amount,
                        'outstanding'     => $outstanding,
                        'demand_date'     => $demandRow['DemandDate'] ?? null,
                        'property_status' => $mappedPropertyStatus,
                        'status'          => 0, // will set to 1 after subheads are saved
                    ]
                );

                // Clear old subheads of this demand (keeps it idempotent)
                OldDemandSubhead::where('DemandID', $demandId)->delete();

                // Each subhead => 1 row in old_demand_subheads
                foreach ($subheads as $subheadRow) {
                    // Just in case API returns multiple DemandID values
                    if (($subheadRow['DemandID'] ?? null) != $demandId) {
                        continue;
                    }

                    OldDemandSubhead::create([
                        'DemandID'           => $subheadRow['DemandID'] ?? null,
                        'ComputerCode'       => $subheadRow['ComputerCode'] ?? null,
                        'Subhead'            => $subheadRow['Subhead'] ?? null,
                        'DateFrom'           => $subheadRow['DateFrom'] ?? null,
                        'DateTo'             => $subheadRow['DateTo'] ?? null,
                        'InterestSlab'       => $subheadRow['InterestSlab'] ?? null,
                        'InterestSlabAmount' => $subheadRow['InterestSlabAmount'] ?? null,
                        'Rate'               => $subheadRow['Rate'] ?? null,
                        'Amount'             => $subheadRow['Amount'] ?? null,
                        'PaymentStatus'      => $subheadRow['PaymentStatus'] ?? null,
                        'BreachType'         => $subheadRow['BreachType'] ?? null,
                        'Floor'              => $subheadRow['Floor'] ?? null,
                        'Area'               => $subheadRow['Area'] ?? null,
                        'AreaUnit'           => $subheadRow['AreaUnit'] ?? null,
                        'PaymentType'        => $subheadRow['PaymentType'] ?? null,
                    ]);
                }

                // If we reached here without exception, mark this demand as fully imported
                $oldDemand->update(['status' => 1]);
            });

            $this->info("Saved demand {$demandId} for property {$propIdFromApi} and its subheads.");
        }
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get property_ids whose demand has already been fully imported
        $importedPropertyIds = OldDemand::where('status', 1)
            ->pluck('property_id')
            ->filter()   // remove nulls if any
            ->unique()
            ->toArray();

        $this->info('Already imported property_ids (old_demands.status = 1): ' . count($importedPropertyIds));

        /**
         * MASTER PROPERTIES
         */
        PropertyMaster::whereNull('is_joint_property')
            ->whereNotIn('old_propert_id', $importedPropertyIds)
            ->orderBy('id')
            ->chunkById(500, function ($masterProperties) {
                foreach ($masterProperties as $masterProperty) {
                    if (!$masterProperty->newColony) {
                        Log::warning("Skipping master property ID {$masterProperty->id} due to missing colony.");
                        continue;
                    }

                    $propertyId = $masterProperty->old_propert_id;

                    try {
                        $this->handleDemand($masterProperty, $propertyId);
                    } catch (\Throwable $e) {
                        Log::error("Exception while importing demand for master property_id={$propertyId}: " . $e->getMessage(), [
                            'trace' => $e->getTraceAsString(),
                        ]);
                        // continue to next property
                    }
                }
            });

        /**
         * SPLITTED PROPERTIES
         */
        SplitedPropertyDetail::whereNotIn('old_property_id', $importedPropertyIds)
            ->orderBy('id')
            ->chunkById(500, function ($splitedProerties) {
                foreach ($splitedProerties as $sp) {
                    $propertyId = $sp->old_property_id;

                    try {
                        $this->handleDemand($sp->master, $propertyId, $sp->id);
                    } catch (\Throwable $e) {
                        Log::error("Exception while importing demand for splitted property_id={$propertyId}: " . $e->getMessage(), [
                            'trace' => $e->getTraceAsString(),
                        ]);
                        // continue to next split
                    }
                }
            });

        $this->info('All available demands processed (or skipped if completed earlier).');
    }
}
