<?php

namespace App\Services;

use App\Models\Item;
use App\Models\PropertyMaster;
use App\Models\SplitedPropertyDetail;
use App\Models\PropertySectionMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ConversionCharge;


class PropertyMasterService
{
    public function formatPropertyDetails($prop, $getSplited = false, $splitedPropId = null)
    {
        $leaseTenure = dateDiffInYears($prop->propertyLeaseDetail->date_of_expiration, $prop->propertyLeaseDetail->doe);
        $leaseTypeName = Item::itemNameById($prop->propertyLeaseDetail->type_of_lease);
        $leaseDate = $prop->propertyLeaseDetail->doe;
        $prop->proprtyTypeName = Item::itemNameById($prop->property_type);
        $prop->proprtySubtypeName = Item::itemNameById($prop->property_sub_type);
        $prop->landTypeName =  Item::itemNameById($prop->land_type);
        $prop->colony = $prop->newColony->name; //added by Nitin on 20-03-2025
        $prop->statusName = Item::itemNameById($prop->status);
        $prop->address = $prop->propertyLeaseDetail->presently_known_as;
        $prop->email = $prop->phone_no = null;
        if (isset($prop->propertyContactDetail) && !is_null($prop->propertyContactDetail)) {
            $prop->email = $prop->propertyContactDetail->email;
            $prop->phone_no = $prop->propertyContactDetail->phone_no;
        }
        $prop->leaseTenure = $leaseTenure;
        $prop->leaseTypeName = $leaseTypeName;
        $prop->leaseDate = $leaseDate;
        $prop->landSize = $prop->propertyLeaseDetail->plot_area_in_sqm;
        $prop->rgr = (!empty($prop->PropertyMiscDetail)) ? $prop->PropertyMiscDetail->is_gr_revised_ever : null;
        $prop->lesseName = (!is_null($prop->currentLesseeName)) ? $prop->currentLesseeName->lessees_name : null; //lesse name can be null id property splited
        if (!$getSplited) {
            return $prop;
        }
        $returnRows = [];
        $splitedProps = $prop->splitedPropertyDetail;
        foreach ($splitedProps as $sprop) {

            $sprop->old_propert_id = $sprop->old_property_id;
            $sprop->unique_propert_id = $sprop->child_prop_id;
            $sprop->colony = $prop->newColony->name; //added by Nitin on 20-03-2025
            $sprop->proprtyTypeName = Item::itemNameById($prop->property_type);
            $sprop->proprtySubtypeName = Item::itemNameById($prop->property_sub_type);
            $sprop->landTypeName =  Item::itemNameById($prop->land_type);
            $sprop->statusName = Item::itemNameById($sprop->property_status);
            $sprop->address = $sprop->presently_known_as;
            $sprop->leaseTenure = $leaseTenure;
            $sprop->leaseTypeName = $leaseTypeName;
            $sprop->leaseDate = $leaseDate;
            $sprop->landSize = $sprop->area_in_sqm;
            $sprop->rgr = (!empty($sprop->PropertyMiscDetail)) ? $sprop->PropertyMiscDetail->is_gr_revised_ever : null;
            $lesseNames = isset($sprop->currentLesseeName) ? $sprop->currentLesseeName->lessees_name : null;
            $sprop->lesseName = $lesseNames;
            if ($getSplited && $splitedPropId == $sprop->id) {
                return $sprop;
                break;
            }
            $returnRows[] = $sprop;
        }
        return $returnRows;
    }

    public function propertyFromSelected($propertyId)
    {
        if (strpos($propertyId, '_')) {
            $id_arr = explode('_', $propertyId);
            $masterPropertyId = $id_arr[0];
            $childPropertyId = $id_arr[1];
            if ($masterPropertyId != "" && $childPropertyId != "") {
                $masterProperty = PropertyMaster::find($masterPropertyId);
                $childProperty = SplitedPropertyDetail::find($childPropertyId);
                return ['status' => 'success', 'masterProperty' => $masterProperty, 'childProperty' => $childProperty];
            } else {
                return ['status' => 'error', 'details' => config('messages.property.error.invalidId')];
            }
        } else {
            $masterProperty = PropertyMaster::where('old_propert_id', $propertyId)->first();
            if (empty($masterProperty)) {
                $childProperty = SplitedPropertyDetail::where('old_property_id', $propertyId)->first();
                if (empty($childProperty)) {
                    return ['status' => 'error', 'details' => config('messages.property.error.notFound')];
                }
                $masterPropertyId = $childProperty->property_master_id;
                $masterProperty = PropertyMaster::find($masterPropertyId);
                return ['status' => 'success', 'masterProperty' => $masterProperty, 'childProperty' => $childProperty];
            } else {
                return ['status' => 'success', 'masterProperty' => $masterProperty];
            }
        }
    }

    public function getPreviousDemands($oldProeprtyId)
    {
        $url = config('constants.oldDemandByPropertyId');
        $data = array("PropertyID" => $oldProeprtyId);
        // Append query parameters to URL
        $url .= '?' . http_build_query($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); // Optional, explicitly setting GET method

        curl_close($ch);

        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        } else {
            $curl_output = trim(curl_exec($ch), '"');
            $response = json_decode($curl_output);
            if (isset($response->Status) && $response->Status == "True") {
                return $response->Data;
            } else {
                return false;
            }
        }
    }

    public function checkPropertyIsInUserSectoin($propertyId)
    {
        $hasAccess = false;
        $user = Auth::user();
        if (in_array($user->roles[0]->name, ['super-admin', 'admin', 'lndo'])) {
            $hasAccess = true;
        }
        if (in_array($user->roles[0]->name, ['section-officer', 'deputy-lndo'])) {
            $userSectionIds = $user->sections->pluck('id')->toArray();
            $property = PropertyMaster::find($propertyId);
            $hasAccess = PropertySectionMapping::whereIn('section_id', $userSectionIds)
                ->where('colony_id', $property->new_colony_name)
                ->where('property_type', $property->property_type)
                ->where('property_subtype', $property->property_sub_type)
                ->exists();
        }
        return $hasAccess;
    }
    
    public function userSectionProperties()
    {
        $userSectionIds = Auth::user()->sections->pluck('id')->toArray();
        return DB::table('property_section_mappings as psm')
            ->join('property_masters as pm', function ($join) {
                return $join->on('psm.colony_id', '=', 'pm.new_colony_name')
                    ->on('psm.property_type', '=', 'pm.property_type')
                    ->on('psm.property_subtype', '=', 'pm.property_sub_type');
            })
            ->leftJoin('splited_property_details as spd', 'pm.id', 'spd.property_master_id')
            ->whereIn('psm.section_id', $userSectionIds)
            ->select(DB::raw('coalesce(spd.old_property_id, pm.old_propert_id) as property_id'))
            ->pluck('property_id')
            ->toArray();
    }

    /* public function conversionCharges($propertyId, $remission = 0, $chargesOnly = true)
    {
        if ($propertyId != "") {
            $colonyId = $propertyTypeName = $propertyType = $area = "";
            if (strpos($propertyId, '_')) {
                $id_arr = explode('_', $propertyId);
                $masterPropertyId = $id_arr[0];
                $childPropertyId = $id_arr[1];
                if ($masterPropertyId != "" && $childPropertyId != "") {
                    $masterProperty = PropertyMaster::find($masterPropertyId);
                    $childProperty = SplitedPropertyDetail::find($childPropertyId);
                    if (!empty($masterProperty) && !empty($childProperty)) {
                        $old_property_id = $childProperty->old_property_id;
                        $allowAccess = userHasAccessToProperty($old_property_id);

                        if (!$allowAccess) {
                            return response()->json(['status' => 'error', 'details' => config('messages.property.error.accessDenied')]);
                        }
                        if ($childProperty->property_status != '951') {
                            return response()->json(['status' => 'error', 'details' => 'Please select lease hold property']);
                        }
                        $colonyId = $masterProperty->old_colony_name;
                        $propertyType = $masterProperty->property_type;
                        $propertyTypeName = $masterProperty->propertyTypeName;
                        $area = $childProperty->area_in_sqm;
                    } else {
                        return response()->json(['status' => 'error', 'details' => config('messages.property.error.invalidId')]);
                    }
                } else {
                    return response()->json(['status' => 'error', 'details' => config('messages.property.error.invalidId')]);
                }
            } else {
                $masterProperty = PropertyMaster::where('old_propert_id', $propertyId)->first();
                if (empty($masterProperty)) {
                    $childProperty = SplitedPropertyDetail::where('old_property_id', $propertyId)->first();
                    if (empty($childProperty)) {
                        return response()->json(['status' => 'error', 'details' => config('messages.property.error.invalidId')]);
                    }
                    $allowAccess = userHasAccessToProperty($childProperty->old_property_id);

                    if (!$allowAccess) {
                        return response()->json(['status' => 'error', 'details' => config('messages.property.error.accessDenied')]);
                    }

                    if ($childProperty->property_status != '951') {
                        return response()->json(['status' => 'error', 'details' => config('messages.property.error.notLeaseHold')]);
                    }
                    $masterPropertyId = $childProperty->property_master_id;
                    $masterProperty = PropertyMaster::find($masterPropertyId);
                    $colonyId = $masterProperty->old_colony_name;
                    $propertyType = $masterProperty->property_type;
                    $propertyTypeName = $masterProperty->propertyTypeName;
                    $area = $childProperty->area_in_sqm;
                } else {
                    $allowAccess = userHasAccessToProperty($propertyId);

                    if (!$allowAccess) {
                        return response()->json(['status' => 'error', 'details' => config('messages.property.error.accessDenied')]);
                    }

                    if ($masterProperty->status != '951') {
                        return response()->json(['status' => 'error', 'details' => config('messages.property.error.notLeaseHold')]);
                    }
                    $colonyId = $masterProperty->old_colony_name;
                    $propertyType = $masterProperty->property_type;
                    $propertyTypeName = $masterProperty->propertyTypeName;
                    $area = $masterProperty->propertyLeaseDetail->plot_area_in_sqm;
                }
            }
            if (!isset($area) || $area <= 0) {
                return response()->json(['status' => 'error', 'details' => config('messages.property.error.invalidArea')]);
            }
            $landRateType = config('constants.conversion_calculation_rate');
            // Config::get();
            $landRateService = new LandRateService();
            $landRateRow = $landRateService->getLandRates($landRateType, $propertyTypeName, $colonyId, date('Y-m-d'));
            if (isset($landRateRow['error'])) {
                return response()->json(['status' => 'error', 'details' => config('messages.landUseChange.error.dataNotAvailable')]);
            }
            if (empty($landRateRow)) {
                return response()->json(['status' => 'error', 'details' => config('messages.property.error.landRateNotFound')]);
            }
            $landRate = $landRateRow->land_rate;
            $conversionFormula = $this->getConversionFormula($propertyType, $area);
            if ($conversionFormula == 0) {
                $conversionCharges = 0;
                $additionalCharges = 0;
                $total = 0;
                $conversionFormula = '0 as area < 50 Sqm.';
            } else {
                $equation = str_replace(['P', 'R'], [$area, $landRate], $conversionFormula);
                $conversionCharges = eval("return $equation;");
                $additionalCharges = $remission == 1 ? 0.40 * $conversionCharges :  0;
                $total = $remission == 1 ? 0.60 * $conversionCharges :  $conversionCharges;
            }
            if ($chargesOnly) {
                return ['status' => 'success', 'amount' => $conversionCharges];
            }
            return response()->json([
                'status' => 'success',
                'propertyId' => isset($childProperty) ? $childProperty->old_peroperty_id : $masterProperty->old_propert_id,
                'colonyName' => $masterProperty->oldColony->name,
                'propertyArea' => $area,
                'landRate' => $landRate,
                'formula' => str_replace(['P', 'R', '*'], ['Plot Area', 'Land Rate', '&times;'], $conversionFormula),
                'equation' => isset($equation) ? str_replace('*', '&times;', $equation) : 0,
                'charges' => customNumFormat(round($conversionCharges, 2)),
                "propertyType" => $masterProperty->propertyTypeName,
                "propertySubtype" => $masterProperty->propertySubtypeName,
                // "additionalChargesLabel" => $remission == 1 ? 'Remission for Recorded Lessee' : 'Additional charges',
                "additionalChargesLabel" => 'Remission',
                "remission" => customNumFormat(round(0.4 * $conversionCharges, 2)),
                "chargesWithRemission" => customNumFormat(round(0.6 * $conversionCharges, 2)),
                'additionalCharges' =>  customNumFormat(round($additionalCharges, 2)),
                'additionalFormula' =>  $remission == 1 ? '40% of Conversion Charges' : 'Remission not given',
                'total' =>  customNumFormat(round($total, 2))
            ]);
        } else {
            return response()->json(['status' => 'error', 'details' => config('messages.landUseChange.error.invalidId')]);
        }
    } */

        public function conversionCharges($propertyId, $remission = 0, $surcharge = 0, $chargesOnly = true)
    {
        // dd($propertyId, $remission, $surcharge, $chargesOnly);
        if ($propertyId != "") {
            $colonyId = $propertyTypeName = $propertyType = $area = "";
            if (strpos($propertyId, '_')) {
                $id_arr = explode('_', $propertyId);
                $masterPropertyId = $id_arr[0];
                $childPropertyId = $id_arr[1];
                if ($masterPropertyId != "" && $childPropertyId != "") {
                    $masterProperty = PropertyMaster::find($masterPropertyId);
                    $childProperty = SplitedPropertyDetail::find($childPropertyId);
                    if (!empty($masterProperty) && !empty($childProperty)) {
                        $old_property_id = $childProperty->old_property_id;
                        $allowAccess = userHasAccessToProperty($old_property_id);

                        if (!$allowAccess) {
                            return response()->json(['status' => 'error', 'details' => config('messages.property.error.accessDenied')]);
                        }
                        if ($childProperty->property_status != '951') {
                            return response()->json(['status' => 'error', 'details' => 'Please select lease hold property']);
                        }
                        $colonyId = $masterProperty->old_colony_name;
                        $propertyType = $masterProperty->property_type;
                        $propertyTypeName = $masterProperty->propertyTypeName;
                        $area = $childProperty->area_in_sqm;
                    } else {
                        return response()->json(['status' => 'error', 'details' => config('messages.property.error.invalidId')]);
                    }
                } else {
                    return response()->json(['status' => 'error', 'details' => config('messages.property.error.invalidId')]);
                }
            } else {
                $masterProperty = PropertyMaster::where('old_propert_id', $propertyId)->first();
                if (empty($masterProperty)) {
                    $childProperty = SplitedPropertyDetail::where('old_property_id', $propertyId)->first();
                    if (empty($childProperty)) {
                        return response()->json(['status' => 'error', 'details' => config('messages.property.error.invalidId')]);
                    }
                    $allowAccess = userHasAccessToProperty($childProperty->old_property_id);

                    if (!$allowAccess) {
                        return response()->json(['status' => 'error', 'details' => config('messages.property.error.accessDenied')]);
                    }

                    if ($childProperty->property_status != '951') {
                        return response()->json(['status' => 'error', 'details' => config('messages.property.error.notLeaseHold')]);
                    }
                    $masterPropertyId = $childProperty->property_master_id;
                    $masterProperty = PropertyMaster::find($masterPropertyId);
                    $colonyId = $masterProperty->old_colony_name;
                    $propertyType = $masterProperty->property_type;
                    $propertyTypeName = $masterProperty->propertyTypeName;
                    $area = $childProperty->area_in_sqm;
                } else {
                    $allowAccess = userHasAccessToProperty($propertyId);

                    if (!$allowAccess) {
                        return response()->json(['status' => 'error', 'details' => config('messages.property.error.accessDenied')]);
                    }

                    if ($masterProperty->status != '951') {
                        return response()->json(['status' => 'error', 'details' => config('messages.property.error.notLeaseHold')]);
                    }
                    $colonyId = $masterProperty->old_colony_name;
                    $propertyType = $masterProperty->property_type;
                    $propertyTypeName = $masterProperty->propertyTypeName;
                    $area = $masterProperty->propertyLeaseDetail->plot_area_in_sqm;
                }
            }
            if (!isset($area) || $area <= 0) {
                return response()->json(['status' => 'error', 'details' => config('messages.property.error.invalidArea')]);
            }
            $landRateType = config('constants.conversion_calculation_rate');
            // Config::get();
            $landRateService = new LandRateService();
            $landRateRow = $landRateService->getLandRates($landRateType, $propertyTypeName, $colonyId, date('Y-m-d'));
            if (isset($landRateRow['error'])) {
                return response()->json(['status' => 'error', 'details' => config('messages.landUseChange.error.dataNotAvailable')]);
            }
            if (empty($landRateRow)) {
                return response()->json(['status' => 'error', 'details' => config('messages.property.error.landRateNotFound')]);
            }
            $landRate = $landRateRow->land_rate;
            $conversionFormula = $this->getConversionFormula($propertyType, $area);
            if ($conversionFormula == 0) {
                $conversionCharges = 0;
                $additionalCharges = 0;
                $additionalSurcharge = 0;
                $total = 0;
                $conversionFormula = '0 as area < 50 Sqm.';
            } else {
                $equation = str_replace(['P', 'R'], [$area, $landRate], $conversionFormula);
                $conversionCharges = eval("return $equation;");
                /*  $additionalCharges = $remission == 1 ? 0.40 * $conversionCharges :  0;
                $total = $remission == 1 ? 0.60 * $conversionCharges :  $conversionCharges; */
                $additionalCharges = 0;
                $additionalChargesLabel = '';
                $additionalChargesFormula = '';
                if ($remission == 1) {
                    $remissionAmount = 0.4 * $conversionCharges;
                    $chargesWithRemission = $conversionCharges - $remissionAmount;
                    $additionalCharges += - ($remissionAmount);
                    $additionalChargesLabel = 'Remission';
                    $additionalChargesFormula = '40% of conversion Charges';
                }
                if ($surcharge == 1) {
                    $surchargeAmount = 0.3333 * $conversionCharges;
                    $chargesWithSurcharge = $conversionCharges + $surchargeAmount;
                    $additionalCharges += $surchargeAmount;
                    $additionalChargesLabel = 'Surcharge';
                    $additionalChargesFormula = '33.33% of conversion Charges';
                }

                if (!$remission && !$surcharge) {
                    $additionalChargesLabel = 'Additional Charges';
                    $additionalChargesFormula = 'No remission or surcharge applied';
                }
                if ($remission && $surcharge) {
                    $additionalChargesLabel = 'Additional Charges';
                    $additionalChargesFormula = 'Surcharge(33.33% of conversion charges) - remission(40% of conversion charges)';
                }
                $total = $conversionCharges + $additionalCharges;
            }
            if ($chargesOnly) {
                return ['status' => 'success', 'amount' => $conversionCharges];
            }
            return response()->json([
                'status' => 'success',
                'propertyId' => isset($childProperty) ? $childProperty->old_peroperty_id : $masterProperty->old_propert_id,
                'colonyName' => $masterProperty->oldColony->name,
                'propertyArea' => $area,
                'landRate' => $landRate,
                'formula' => str_replace(['P', 'R', '*'], ['Plot Area', 'Land Rate', '&times;'], $conversionFormula),
                'equation' => isset($equation) ? str_replace('*', '&times;', $equation) : 0,
                'charges' => customNumFormat(round($conversionCharges, 2)),
                "propertyType" => $masterProperty->propertyTypeName,
                "propertySubtype" => $masterProperty->propertySubtypeName,
                // "additionalChargesLabel" => $remission == 1 ? 'Remission for Recorded Lessee' : 'Additional charges',
                // "additionalChargesLabel" => 'Remission',
                "additionalChargesLabel" => $additionalChargesLabel,
                //"remission" => customNumFormat(round(0.4 * $conversionCharges, 2)),
                "remission" => isset($remissionAmount) ? customNumFormat(round($remissionAmount, 2)) : 0,
                "surcharge" => isset($surchargeAmount) ? customNumFormat(round($surchargeAmount, 2)) : 0,
                // "chargesWithRemission" => customNumFormat(round(0.6 * $conversionCharges, 2)),
                "chargesWithRemission" => isset($chargesWithRemission) ? customNumFormat(round($chargesWithRemission)) : 0,
                "chargesWithSurcharge" => isset($chargesWithSurcharge) ? customNumFormat(round($chargesWithSurcharge)) : 0,
                'additionalCharges' =>  customNumFormat(round($additionalCharges, 2)),
                // 'additionalFormula' =>  $remission == 1 ? '40% of Conversion Charges' : 'Remission not given',
                'additionalFormula' =>  $additionalChargesFormula,
                'total' =>  customNumFormat(round($total, 2))
            ]);
        } else {
            return response()->json(['status' => 'error', 'details' => config('messages.landUseChange.error.invalidId')]);
        }
    }

    private function getConversionFormula($propertyType, $area)
    {
        $row = ConversionCharge::where('property_type', $propertyType)->where(function ($query) use ($area) {
            $query->whereNull('area_from')->orWhere(function ($q) use ($area) {
                $q->where('area_from', '<', $area)->where(function ($q1) use ($area) {
                    $q1->where('area_to', '>=', $area)->orWhereNull('area_to');
                });
            });
        })->first();
        if (empty($row)) {
            return response()->json(['status' => 'error', 'details' => config('messages.landUseChange.error.dataNotAvailable')]);
        }
        return $row->formula;
        // return '0.1*P*R';
    }
}