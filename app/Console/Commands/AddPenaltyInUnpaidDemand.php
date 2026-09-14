<?php

namespace App\Console\Commands;

use App\Models\Demand;
use App\Models\DemandDetail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AddPenaltyInUnpaidDemand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-penalty-in-unpaid-demand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'When demand is not fully paid then add penalty on demand';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $penaltySubheadId = getServiceType('PNL_CHG');
        $penalties = config('constants.DEMAND_PENALTIES');
        $minPenaltyDays = $penalties[0]['days'];

        $unpaidDemands = Demand::whereIn('status', [
            getServiceType('DEM_PENDING'),
            getServiceType('DEM_PART_PAID')
        ])
            ->whereNotNull('approved_at')
            ->whereDate('approved_at', '<=', now()->subDays($minPenaltyDays))
            ->get();

        foreach ($unpaidDemands as $demand) {
            $existingPenalties = DemandDetail::where('demand_id', $demand->id)
                ->where('subhead_id', $penaltySubheadId)
                ->count();

            $daysSinceApproval = Carbon::parse($demand->approved_at)->diffInDays(now());

            foreach ($penalties as $index => $penalty) {
                if ($daysSinceApproval >= $penalty['days'] && $existingPenalties <= $index) {
                    try {
                        $this->addPenaltyHead($demand, $penalty['factor'], $penalty['days'], $penaltySubheadId);
                    } catch (\Exception $e) {
                        Log::error("Penalty insertion failed for Demand ID {$demand->id}: " . $e->getMessage());
                    }
                    break; // Apply only the next due penalty
                }
            }
        }
    }

    private function addPenaltyHead($demand, $multiplier, $penaltyDays, $penaltySubheadId)
    {
        $penaltyAmount = round($multiplier * $demand->balance_amount, 2);

        DB::transaction(function () use ($demand, $penaltyAmount, $penaltyDays, $penaltySubheadId) {
            DemandDetail::create([
                'demand_id' => $demand->id,
                'property_master_id' => $demand->property_master_id,
                'splited_property_detail_id' => $demand->splited_property_detail_id,
                'flat_id' => $demand->flat_id,
                'subhead_id' => $penaltySubheadId,
                'total' => $penaltyAmount,
                'net_total' => $penaltyAmount,
                'paid_amount' => 0,
                'balance_amount' => $penaltyAmount,
                'fy' => getFinancialYear(),
                'remarks' => "Penalty for non-payment of demand {$demand->unique_id} after {$penaltyDays} days"
            ]);

            $demand->increment('total', $penaltyAmount);
            $demand->increment('net_total', $penaltyAmount);
            $demand->increment('balance_amount', $penaltyAmount);
            Log::info("Penalty of ₹{$penaltyAmount} added to Demand ID {$demand->id} after {$penaltyDays} days.");
        });
    }
}
