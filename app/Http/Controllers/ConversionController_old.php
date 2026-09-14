<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ConversionCharge;
use App\Models\Item;
use App\Models\PropertyMaster;
use App\Models\SplitedPropertyDetail;
use App\Services\ColonyService;
use App\Services\LandRateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PropertyMasterService;

class ConversionController extends Controller
{
    public function calculateConversionCharges(ColonyService $colonyService)
    {
        $data = [];
        $user = Auth::user();
        if ($user->user_type == 'applicant') {
            $data['isApplicant'] = true;
            $data['properties'] = $user->userProperties;
        } else {
            $data['colonies'] = $colonyService->misDoneForColonies();
        }
        return view('calculation.conversion', $data);
    }

    public function chargesForProperty(Request $request)
    {
        // dd($request->all());
        $propertyId = $request->propertyId;
        // $lesseeType = $request->lesseType;
        $remission = $request->remission == 'true';
        $surcharge = $request->surcharge == 'true';

        $pms = new PropertyMasterService();
        $conversonCharges = $pms->conversionCharges($propertyId, $remission, $surcharge, false);
        return $conversonCharges;
    }
}
