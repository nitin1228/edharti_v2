<?php

namespace App\Services;

use App\Models\FloorAreaRatio;
use App\Models\Item;
use App\Models\PropertyMaster;
use App\Models\SplitedPropertyDetail;

class LandRateService
{
    public function getLandRates($from, $propertyType, $colony_id, $fromDate)
    {
        if (!in_array(strtolower($propertyType), ['residential', 'commercial', 'industrial', 'institutional'])) {
            return ['error' => 'data not available for ' . $propertyType . ' properites'];
        }
        $modelName = ucfirst($from) . ucfirst($propertyType) . 'LandRate';
        $model = '\\App\\Models\\' . $modelName;
        return $model::where('colony_id', $colony_id)->where(function ($query) use ($fromDate) {

            return $query->whereDate('date_from', '<=', $fromDate)->whereDate('date_to', '>=', $fromDate)->orWhere(
                function ($q1) use ($fromDate) {
                    return $q1->whereNull('date_from')->whereDate('date_to', '>=', $fromDate);
                }
            )->orWhere(function ($q2) use ($fromDate) {
                return $q2->whereNull('date_to')->whereDate('date_from', '<=', $fromDate);
            });
        })->first();
    }

    public function getApplicantTyps()
    {
        return Item::where('group_id', 4001)->where('is_active', 1)->orderBy('item_order')->get();
    }

    //function added by Nitin to get Land Rate for a property

    public function getLandRateForProperty($oldPropertyId, $date = null)
    {
        $date = $date ? $date : date('Y-m-d');
        $splittedProperty = SplitedPropertyDetail::where('old_property_id', $oldPropertyId)->first();
        if (!empty($splittedProperty)) {
            $propertyMaster = $splittedProperty->propertyMaster;
        } else {
            $propertyMaster = PropertyMaster::where('old_propert_id', $oldPropertyId)->first();
        }
        if (isset($propertyMaster) && !empty($propertyMaster)) {
            $colonyId = $propertyMaster->new_colony_name;
            $propertyType = $propertyMaster->propertyType_name;
            $from = 'circle';
            $landRateRow = $this->getLandRates($from, $propertyType, $colonyId, $date);
            $landRate = $landRateRow->land_rate;
            return ['status' => true, 'landRate' => $landRate];
        } else {
            return ['status' => false, 'message' => 'Property not found'];
        }
    }

    public function getFloorAreaRatioForProperty($oldPropertyId, $date = null)
    {
        $date = $date ? $date : date('Y-m-d');
        $splittedProperty = SplitedPropertyDetail::where('old_property_id', $oldPropertyId)->first();
        if (!empty($splittedProperty)) {
            $propertyMaster = $splittedProperty->propertyMaster;
            $propertyArea = $splittedProperty->area_in_sqm;
        } else {
            $propertyMaster = PropertyMaster::where('old_propert_id', $oldPropertyId)->first();
            $propertyArea = (float)$propertyMaster->propertyLeaseDetail?->plot_area_in_sqm;
        }
        if ($date <= date('1990-07-31')) {
            $propertyArea = (float)$propertyArea * 1.195991; //square meter to square yard
        }
        // dd($propertyArea);
        $floorAreaRecord = FloorAreaRatio::where(function ($query) use ($date) {
            return $query->whereDate('effective_from', '<=', $date)->whereDate('effective_to', '>=', $date)->orWhere(
                function ($q1) use ($date) {
                    return $q1->whereNull('effective_from')->whereDate('effective_to', '>=', $date);
                }
            )->orWhere(function ($q2) use ($date) {
                return $q2->whereNull('effective_to')->whereDate('effective_from', '<=', $date);
            });
        })->where('area_from', '<=', $propertyArea)->where(function ($q) use ($propertyArea) {
            return $q->whereNull('area_to')->orWhere('area_to', '>=', $propertyArea);
        })
            ->first();
        // dd($floorAreaRecord);
        if (empty($floorAreaRecord)) {
            return ['status' => false, 'message' => 'FAR record not found for this property and date'];
        }
        return ['status' => true, 'FAR' => $floorAreaRecord?->far];
    }
}
