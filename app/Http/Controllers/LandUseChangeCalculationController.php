<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LandUseChangeMatrix;
use App\Models\LandUseChangeApplication;
use App\Services\ColonyService;
use App\Services\LandRateService;
use App\Services\PropertyMasterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LandUseChangeCalculationController extends Controller
{
    public function calculateLandUseChangeCharges(ColonyService $colonyService)
    {
        $data = [];
        $user = Auth::user();
        if ($user->user_type == 'applicant') {
            $data['isApplicant'] = true;
            $data['properties'] = $user->userProperties;
        } else {
            $data['colonies'] = $colonyService->misDoneForColonies();
        }
        return view('calculation.land-use-change', $data);
    }

    public function propertyTypeOptions($propertyId)
    {
        if ($propertyId != "") {
            $propertyMasterService = new PropertyMasterService();
            $propertyFromId = $propertyMasterService->propertyFromSelected($propertyId);
            if ($propertyFromId['status'] == 'error') {
                return response()->json($propertyFromId);
            } else {
                $masterProperty = $propertyFromId['masterProperty'];
                $childProperty = $propertyFromId['childProperty'] ?? null;
                if (!empty($masterProperty)) {
                    $propertyType = $masterProperty->property_type;
                    $propertySubtype = $masterProperty->property_sub_type;
                    $lcm = new LandUseChangeMatrix();
                    $matrixFilteredData = $lcm->getAllowedOptions($propertyType, $propertySubtype);

                    $landRateService = new LandRateService();
                    $landRateRow = $landRateService->getLandRates(config('constants.conversion_calculation_rate'), $masterProperty->propertyTypeName, $masterProperty->old_colony_name, date('Y-m-d'));
                    if (isset($landRateRow['error'])) {
                        return response()->json(['status' => 'error', 'details' => config('messages.landUseChange.error.dataNotAvailable')]);
                    }
                    $landRate = !empty($landRateRow) ? $landRateRow->land_rate : null;
                    $area = $childProperty ? $childProperty->area_in_sqm : $masterProperty->propertyLeaseDetail->plot_area_in_sqm;
                    $landValue = ($landRate && $area > 0) ? round($landRate * $area, 2) : 0;
                    $propertyDetails = [
                        'old_property_id' => !is_null($childProperty) ? $childProperty->old_property_id : $masterProperty->old_propert_id,
                        'land_rate' => $landRate,
                        'area' => $area,
                        'land_value' => $landValue,
                        'address' => !is_null($childProperty) ? $childProperty->presently_known_as : $masterProperty->propertyLeaseDetail->presently_known_as,
                        'property_type' => $masterProperty->propertyTypeName,
                        'property_subtype' => $masterProperty->propertySubtypeName,
                    ];
                    return ['status' => 'success', 'landUseChangeData' => $matrixFilteredData, 'propertyDetails' => $propertyDetails];
                } else {
                    return response()->json(['status' => 'error', 'details' => 'Property not Found']);
                }
            }
        } else {
            return response()->json(['status' => 'error', 'details' => 'Invalid property id given']);
        }
    }

    public function getSaughtLandValue(Request $request)
    {
        $propertyId = $request->propertyId;
        $saughtPropertyType = $request->saughtPropertyType;
        if (is_null($saughtPropertyType)) {
            $landUseChangeApplication = LandUseChangeApplication::join('applications as app', 'land_use_change_applications.application_no', '=', 'app.application_no')
                ->whereNotIn('app.status', [getServiceType('APP_WD'), getServiceType('APP_CAN'), getServiceType('APP_REJ'), getServiceType('APP_APR')])
                ->where('land_use_change_applications.old_property_id', $propertyId)
                ->first();
            if (!empty($landUseChangeApplication)) {
                $saughtPropertyType = $landUseChangeApplication->property_type_change_to;
            } else {
                return response()->json(['status' => 'error', 'details' => 'Land use change application not found for given property']);
            }
        }
        $saughtPropertyTypeName = strtolower(getServiceNameById($saughtPropertyType));

        if ($propertyId != "") {
            $propertyMasterService = new PropertyMasterService();
            $propertyFromId = $propertyMasterService->propertyFromSelected($propertyId);
            if ($propertyFromId['status'] == 'error') {
                return response()->json($propertyFromId);
            } else {
                $masterProperty = $propertyFromId['masterProperty'];
                if (!empty($masterProperty)) {
                    $colony_id = $masterProperty->new_colony_name;
                    $colony_name = $masterProperty->newColony->name;
                    $lrs = new LandRateService();
                    $commercialLandRates = $lrs->getLandRates('lndo', $saughtPropertyTypeName, $colony_id, date('Y-m-d'));
                    $commercialLandRates->saughtPropertyTypeName = ucfirst($saughtPropertyTypeName);
                    if (isset($commercialLandRates['status'])) {
                        return response()->json($commercialLandRates);
                    }
                    $commercialLandRates->colonyName = $colony_name;

                    return $commercialLandRates;
                } else {
                    return response()->json(['status' => 'error', 'details' => 'Property not Found']);
                }
            }
        } else {
            return response()->json(['status' => 'error', 'details' => 'Invalid property id given']);
        }
    }
  /* public function getCommercialLandValue($propertyId)
    {
        if ($propertyId != "") {
            $propertyMasterService = new PropertyMasterService();
            $propertyFromId = $propertyMasterService->propertyFromSelected($propertyId);
            if ($propertyFromId['status'] == 'error') {
                return response()->json($propertyFromId);
            } else {
                $masterProperty = $propertyFromId['masterProperty'];
                if (!empty($masterProperty)) {
                    $colony_id = $masterProperty->new_colony_name;
                    $colony_name = $masterProperty->newColony->name;
                    $lrs = new LandRateService();
                    $commercialLandRates = $lrs->getLandRates('lndo', 'commercial', $colony_id, date('Y-m-d'));
                    if (isset($commercialLandRates['status'])) {
                        return response()->json($commercialLandRates);
                    }
                    $commercialLandRates->colonyName = $colony_name;

                    return $commercialLandRates;
                } else {
                    return response()->json(['status' => 'error', 'details' => 'Property not Found']);
                }
            }
        } else {
            return response()->json(['status' => 'error', 'details' => 'Invalid property id given']);
        }
    } */
}
