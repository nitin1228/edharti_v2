<?php

namespace App\Services;

use App\Models\Demand;
use App\Models\CarriedDemandDetail;
use Illuminate\Support\Facades\Auth;

class DemandService
{
    /**
     * Withdraw a demand by ID
     */
    public function withdrawDemand($demandId, $userId = null, $isAutoCancel = null): array
    {
        $demand = Demand::find($demandId);
        if (empty($demand)) {
            return ['status' => false, 'message' => "No data found!!"];
        }

        // If carried forward
        if ($demand->carried_amount && $demand->carried_amount > 0) {
            $carriedDetails = CarriedDemandDetail::where('new_demand_id', $demandId)->first();

            if (!empty($carriedDetails)) {
                $oldDemand = Demand::find($carriedDetails->old_demand_id);

                if (!empty($oldDemand)) {
                    $statusToUpdate = getServiceType('DEM_PART_PAID');

                    if ($oldDemand->net_total == $oldDemand->balance_amount) {
                        $statusToUpdate = getServiceType('DEM_PENDING');
                    }

                    $oldDemand->update([
                        'status'     => $statusToUpdate,
                        'updated_by' => $userId ?? Auth::id()
                    ]);
                } else {
                    return ['status' => false, 'message' => "Old demand not found"];
                }
            } else {
                return ['status' => false, 'message' => "Carried demand details missing"];
            }
        }

        // Update demand itself
        $demand->update([
            'status'           => getServiceType('DEM_WD'),
            'updated_by'       => $userId ?? 1, // fallback to system user
            'is_auto_cancelled' => (bool) $isAutoCancel
        ]);

        return ['status' => true, 'message' => "Demand withdrawn successfully"];
    }
}
