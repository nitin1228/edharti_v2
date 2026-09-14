<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralFunctions;
use App\Models\Application;
use App\Models\CarriedDemandDetail;
use App\Models\Demand;
use App\Models\DemandDetail;
use App\Models\DemandFormula;
use App\Models\DemandHeadKey;
use App\Models\Item;
use App\Models\OldDemand;
use App\Models\OldDemandSubhead;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\PropertyMaster;
use App\Services\ColonyService;
use App\Services\PaymentService;
use App\Services\PropertyMasterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\UserProperty;
use App\Models\PropertyContactDetail;
use Illuminate\Support\Facades\Mail;
use App\Mail\CommonMail;
use App\Services\CommunicationService;
use App\Services\SettingsService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PropertyLeaseDetail;
use App\Models\SplitedPropertyDetail;
use Exception;

use function PHPUnit\Framework\isNull;

class DemandController extends Controller
{
    protected $communicationService;
    protected $settingsService;

    public function __construct(CommunicationService $communicationService, SettingsService $settingsService)
    {
        $this->communicationService = $communicationService;
        $this->settingsService = $settingsService;
    }
    public function createDemandView(Request $request, ColonyService $colonyService)
    {
        if (!empty($request->all())) {
            $applicationNo = $request->applicationNo;
            $application = Application::where('application_no', $applicationNo)->first();
            $data['applicationData'] = $application->applicationData;
        }
        $data['colonies'] = $colonyService->misDoneForColonies(true);
        // $data['demandSubheads'] = Item::where('group_id', 7003)->where('is_active', 1)->orderBy('item_name')->get();
        return view('demand.input-form', $data);
    }
    public function EditDemand($demandId)
    {
        $demand = Demand::find($demandId);
        if (empty($demand)) {
            return redirect()->back()->with('failure', "No data found!!");
        }
        $data = $this->getDemandData($demand);
        $data['demand'] = $demand;
        // $viewBlade = $demand->is_manual == 1 ? 'demand.manual-input-form' : 'demand.input-form';
        /* if ($demand->is_manual) {
            $data['demandSubheads'] = Item::where('group_id', 7003)->where('is_active', 1)->orderBy('item_name')->get();
        } */
        return view('demand.input-form', $data);
    }
    public function ViewDemand($demandId)
    {
        $demand = Demand::find($demandId);
        if (empty($demand)) {
            return redirect()->back()->with('failure', "No data found!!");
        }
        $data = $this->getDemandData($demand);
        $data['demand'] = $demand;
        /* $data['demandSubheads'] = Item::where('group_id', 7003)->where('is_active', 1)->orderBy('item_name')->orderBy('item_name')->get(); */
        $data['openInReadOnlyMode'] = true;
        $data['canEdit'] = getServiceCodeById($demand->status) == "DEM_DRAFT"; //ONLY WHEN DEMAND IS STILL IN DRAFT STAGE
        $data['canApprove'] = Auth::user()->hasAnyRole('deputy-lndo', 'super-admin') && getServiceCodeById($demand->status) == "DEM_DRAFT"; //deputy can approve draft demands
        // $viewBlade = $demand->is_manual == 1 ? 'demand.manual-input-form' : 'demand.input-form';
        /* if ($demand->is_manual) {
            $data['demandSubheads'] = Item::where('group_id', 7003)->where('is_active', 1)->orderBy('item_name')->get();
        } */
        return view('demand.input-form', $data);
    }

    private function getDemandData($demand)
    {
        $demandDetails = $demand->demandDetails;
        /** format demand details in form 'subhead_code'=>[íd, ámount] */
        $formattedDemandDetails = [];
        $carried = [];
        $penalties = [];
        $manualDemandSubheadIds = $demand->demandDetails->where('subhead_id', getServiceType('DEM_MANUAL'))->pluck('id')->toArray();
        foreach ($demandDetails as $i => $demandDetail) {
            if (is_null($demandDetail->carried_amount) || $demandDetail->carried_amount == 0) {
                $headCode = $demandDetail->subhead_code;
                if ($headCode != 'DEM_MANUAL') { //specially handle manual demand
                    if ($headCode == 'PNL_CHG') {
                        $penalties[] = $demandDetail;
                    } else {
                        $formattedDemandDetails[$headCode] = ['id' => $demandDetail->id, 'amount' => $demandDetail->total]; // in this manual demand will save last reord only so we need handle manual demand saperately
                    }
                } else {
                    $formattedDemandDetails[$headCode][] = ['id' => $demandDetail->id, 'amount' => $demandDetail->total];
                }
            } else {
                $carried[] = $demandDetail;
            }
        }
        if (count($carried) > 0) {
            $carriedDemand = CarriedDemandDetail::where('new_demand_id', $demand->id)->first();
            $data['carried'] = $carried;
            $data['carriedDemandId'] = $carriedDemand->oldDemand->unique_id;
        }
        $data['penalties'] = $penalties;
        /** get subhead input values */
        $selectedValues = DemandHeadKey::where('demand_id', $demand->id)->get();
        $formattedSelectedValues = [];
        if (!empty($selectedValues)) {
            foreach ($selectedValues as $sv) {
                $formattedSelectedValues[$sv->key] = $sv->value;
            }
        }
        // dd($formattedSelectedValues);
        $data['selectedValues'] = $formattedSelectedValues;

        /** get  allowed subheads for the property based on it is new allotment or not */
        $newAllotmentPropertyValue = $formattedSelectedValues['new_allotment_radio'] ?? 0;
        $data['newAllotment'] = $newAllotmentPropertyValue;
        $data['subheads'] = $this->getDemandHeads($newAllotmentPropertyValue, false);

        // $manualDemandSubheadIds = DemandDetail::whereIn('demand_id', $carriedDemands)->where('subhead_id', getServiceType('DEM_MANUAL'))->pluck('id')->toArray();
        $manualDemandKeys = DemandHeadKey::whereIn('head_id', $manualDemandSubheadIds)->get();
        foreach ($manualDemandKeys as $mdk) {
            $targetIndex = null;
            foreach ($formattedDemandDetails['DEM_MANUAL'] as $ddKey => $demandDetail) {
                if ($demandDetail['id'] == $mdk->head_id) {
                    $targetIndex = $ddKey;
                    break;
                }
            }
            if (!is_null($targetIndex)) {
                $formattedDemandDetails['DEM_MANUAL'][$targetIndex]['values'][$mdk->key] = $mdk->value;
            }
            //$formattedDemandDetails[getServiceType('DEM_MANUAL')]['values'][$mdk->head_id][$mdk->key] = $mdk->value;
        }
        $data['manualDemandKeys'] = $manualDemandKeys;

        $data['slectedSubheads'] = $formattedDemandDetails;
        /** get land vlue and land area in advance for displaying calculations */
        // dd($demand->splited_property_detail, $demand->property_master);
        $data['landValue'] = !is_null($demand->splited_property_detail)
            ? $demand->splited_property_detail->plot_value
            : (!is_null($demand->property_master) && !is_null($demand->property_master->propertyLeaseDetail)
                ? $demand->property_master->propertyLeaseDetail->plot_value
                : null);
        $data['landArea'] = !is_null($demand->splited_property_detail)
            ? $demand->splited_property_detail->area_in_sqm
            : (!is_null($demand->property_master) && !is_null($demand->property_master->propertyLeaseDetail)
                ? $demand->property_master->propertyLeaseDetail->plot_area_in_sqm
                : null);
        if ($demand->includedOldDemands->count() > 0) {
            $data['oldDemands'] = $this->oldDemandData($demand->id, 'new_demand_id', true);
        }
        // dd($data);
        return $data;
    }

    public function getExistingPropertyDemand($oldPropertyId, $formatToJson = true, $checkApiDemand = true)
    {
        /** get unpaid or partiallly paid demands for property
        if first demand record is not created then - 
        check for pending demands in old application */
        $isFirstDemand = !Demand::where('old_property_id', $oldPropertyId)->exists();
        if ($checkApiDemand && $isFirstDemand) {

            $pms = new PropertyMasterService();
            $oldDemandData = $pms->getPreviousDemands($oldPropertyId);
            // dd($oldDemandData);
            if ($oldDemandData && is_array($oldDemandData) && $oldDemandData['status']) {
                return response()->json(['status' => false, 'details' => 'Could not fetch old demand details. Please try again.']);
            }
            if ($oldDemandData) {
                /** handle the case when user checks previous pending dues but do not proceed with creating new demand -- 
                 * delete the previous saved demands and subheads
                 */
                $previousSavedDemands = OldDemand::where('property_id', $oldPropertyId)->get();
                if ($previousSavedDemands->isNotEmpty()) {
                    foreach ($previousSavedDemands as $psd) {
                        OldDemandSubhead::where('DemandID', $psd->demand_id)->delete();
                    }
                    OldDemand::where('property_id', $oldPropertyId)->delete();
                }
                /** get old Demand data */
                $demands = $oldDemandData->LatestDemanddetails;
                $previousDemandData = [];
                foreach ($demands as $demand) {
                    // $paidKey = collect($demand)->keys()->first(fn($key) => str_ends_with($key, 'Paid'));'
                    $paidAmount = $demand->Amount - $demand->Outstanding;
                    $demandData = collect($demand)->merge(['PaidAmount' => $paidAmount])->only([
                        'PropertyID',
                        'DemandID',
                        'Amount',
                        'PaidAmount',
                        'Outstanding',
                        'DemandDate'
                    ])->mapWithKeys(function ($value, $key) {
                        return [
                            match ($key) {
                                'DemandID' => 'demand_id',
                                'PropertyID' => 'property_id',
                                'Amount' => 'amount',
                                'PaidAmount' => 'paid_amount',
                                'Outstanding' => 'outstanding',
                                'DemandDate' => 'demand_date',
                                default => $key
                            } => $value
                        ];
                    })->toArray();
                    $demandData['property_status'] = getProperyStatusFromOldPropetyId($oldPropertyId);
                    OldDemand::create($demandData);
                    $previousDemandData[] = $demandData;
                }
                $demandSubheads = $oldDemandData->SubHeadwiseBreakup;
                foreach ($demandSubheads as $oldSubhead) {
                    $oldSubheadData = collect($oldSubhead)->all();
                    OldDemandSubhead::create($oldSubheadData);
                }
                $previousDues =  ['previousDemands' => $previousDemandData, 'dues' => collect($previousDemandData)->sum('outstanding')/* , 'demandSubheads' => $demandSubheads */];
            }
        }
        /** 
         get previous unpaid demand on this aplication 
         */

        $proeprtyMasterService = new PropertyMasterService();
        $findProperty = $proeprtyMasterService->propertyFromSelected($oldPropertyId);
        if ($findProperty['status'] == 'error') {
            return $formatToJson ? response()->json([
                'status' => false,
                'details' => $findProperty['details']
            ]) : false;
        } else {
            $masterProperty = $findProperty['masterProperty'];
            $propertyMasterId = $masterProperty->id;
            $childProperty = isset($findProperty['childProperty']) ? $findProperty['childProperty'] : null;
            $childId = is_null($childProperty) ? null : $childProperty->id;
            if (isset($previousDues)) {
                return  $formatToJson ? response()->json([
                    'status' => true,
                    'data' => $previousDues
                ]) : ['propertyMasterId' => $propertyMasterId, 'childId' => $childId, 'dues' => $previousDues['dues'], 'previousDemands' => $previousDues['previousDemands']];
            } else {
                $existingDemand = Demand::where('property_master_id', $propertyMasterId)
                    ->where(function ($query) use ($childId) {
                        if (is_null($childId)) {
                            return $query->whereNull('splited_property_detail_id');
                        } else {
                            return $query->where('splited_property_detail_id', $childId);
                        }
                    })
                    ->where(function ($query) {
                        return $query->whereNull('model')->orWhere('model', '<>', 'PropertyRevivisedGroundRent'); // do  not consider rgr demands
                    })
                    ->whereIn('status', [getServiceType('DEM_DRAFT'), getServiceType('DEM_PENDING'), getServiceType('DEM_PART_PAID')])
                    ->first();
                return $formatToJson ? response()->json([
                    'status' => true,
                    'data' => ['demand' => $existingDemand, 'demandDetails' => !empty($existingDemand) ? $existingDemand->demandDetails : null, 'applicationData' => $this->getActiveApplicationData($oldPropertyId)]
                ]) : ['propertyMasterId' => $propertyMasterId, 'childId' => $childId, 'demand' => $existingDemand];
            }
        }
    }

    public function storeDemand(Request $request)
    {
        // dd($request->all());
        try {
            return DB::transaction(function () use ($request) {
                $oldPropertyId = $request->oldPropertyId;

                // $manualDemand = null;
                $demandId = (isset($request->id) && $request->id != "") ? $request->id : null;
                if ($oldPropertyId || $demandId) {
                    if (!$demandId) {
                        $prevDues = $prevDuesDemandId = null; //initiallize to store demand subheads

                        //after latest update no need to check Old demad here - 07-04-2025
                        $existingDemandData = $this->getExistingPropertyDemand($oldPropertyId, false, false);
                        /* if (isset($existingDemandData['dues'])) {
                            // previous dues logic is to berevised so commented on 26 March 2025
                           
                        } else { */
                        $oldDemand = $existingDemandData['demand'];
                        if ($oldDemand && $oldDemand->status_code !== "DEM_DRAFT") {
                            $carriedAmount = $oldDemand->balance_amount;
                        }
                        /* } */
                        $carriedAmount = (isset($oldDemand) && $oldDemand && $oldDemand->status_code !== "DEM_DRAFT") ? $oldDemand->balance_amount : 0;
                    } else {
                        $demand = $oldDemand = Demand::find($demandId);
                        $carriedAmount = !is_null($demand->carried_amount) ? $demand->carried_amount : 0;
                    }
                    // $amounts = $request->amount;
                    $manualDemandAmounts = 0;
                    $total = 0;
                    if (isset($request->demand_amount)) {
                        $amounts = $request->demand_amount;
                        $manualDemandAmounts = isset($amounts['DEM_MANUAL']) ? $amounts['DEM_MANUAL'] : false;
                        // dd($demand, $carriedAmount, $manualDemandAmounts);
                        unset($amounts['DEM_MANUAL']);
                        $total = array_sum($amounts) + ($manualDemandAmounts !== false ? array_sum($manualDemandAmounts) : 0);
                        if ($manualDemandAmounts !== false)
                            $amounts['DEM_MANUAL'] = $manualDemandAmounts;
                        // dd($total, array_sum($amounts), array_sum($manual));
                    }
                    foreach ($amounts as $k => $am) {
                        if (!(array_key_exists($k, $request->all()) || $k == "DEM_MANUAL")) {
                            unset($amounts[$k]);
                        }
                    }
                    /* if (isset($request->amount)) {
                        $manualDemand = true;
                        $amounts = $request->amount;
                    } */

                    //$total = array_sum($amounts) + (isset($manualDemandAmounts) ? array_sum($manualDemandAmounts) : 0);
                    $previousDues = 0;
                    $previousDuesDemand = 0;
                    $netTotal = $total + $carriedAmount;
                    //create new demand
                    $fy = getFinancialYear();
                    $demandData = [
                        'property_master_id' => $existingDemandData['propertyMasterId'] ?? $demand->property_master_id,
                        'splited_property_detail_id' =>  $existingDemandData['childId'] ?? $demand->splited_property_detail_id ?? null,
                        'flat_id' => null, //will be changed later
                        'old_property_id' => $oldPropertyId ?? $demand->old_property_id,
                        'app_no' => $request->application_no ?? null,
                        'total' => round($total, 2),
                        'net_total' => round($netTotal, 2),
                        'balance_amount' => round($netTotal, 2),
                        'paid_amount' => 0,
                        'carried_amount' => $carriedAmount > 0 ? round($carriedAmount, 2) : null,
                        'fy_prev_demand' => $oldDemand->current_fy ?? $demand->fy_prev_demand ?? null,
                        'current_fy' => $fy,
                        'status' => getServiceType('DEM_DRAFT'), //at first demand status is draft
                        //'is_manual' => $manualDemand,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id()
                    ];
                    if (!isset($request->id)) {
                        $demandData['unique_id'] = GeneralFunctions::createUniqueDemandId($oldPropertyId);
                    }
                    $newDemand =  Demand::updateOrCreate(['id' => $request->id ?? 0,], $demandData);
                    if ($newDemand) {
                        $newDemandId = $newDemand->id;
                        $idsToKeep = [];
                        if (isset($request->detail_id)) {
                            // Removes null values from the array
                            $notNullIds = array_filter($request->detail_id);
                            $idsToKeep = Arr::flatten($notNullIds);
                        }
                        /** If carried amount is not null then data in forwarded from old demand so not avaialble in request */
                        DemandDetail::where('demand_id', $newDemandId)->where(function ($query) {
                            return $query->whereNull('carried_amount')->orWhere('carried_amount', 0);
                        })->whereNotIn('id', $idsToKeep)->delete();
                        DemandHeadKey::where('demand_id', $newDemandId)->whereNotIn('head_id', $idsToKeep)->delete();

                        if (isset($oldDemand) && $oldDemand->status_code !== "DEM_DRAFT") {
                            $preveiousDemand = Demand::find($oldDemand->id);
                            if (in_array($preveiousDemand->status, [getServiceType('DEM_PENDING'), getServiceType('DEM_PART_PAID')])) { //check the status of previous demand is pending or partially paid. if yes then forward the demand to new demand, and subheads to new demand, add the remaining amount of old demand to new demand
                                $preveiousDemand->update(['status' => getServiceType('DEM_CR_FRW'), 'updated_by' => Auth::id()]); //update status of old Demand

                                //add data in carried demand Detail Table
                                CarriedDemandDetail::create([
                                    'new_demand_id' => $newDemandId,
                                    'old_demand_id' => $oldDemand->id,
                                    'carried_amount' => $carriedAmount
                                ]);

                                // create subheads for carried demand
                                $oldSubheads = $preveiousDemand->demandDetails;
                                foreach ($oldSubheads as $i => $osh) {
                                    if ($osh->balance_amount > 0) {
                                        DemandDetail::create([
                                            'demand_id' => $newDemandId,
                                            'property_master_id' => $existingDemandData['propertyMasterId'],
                                            'splited_property_detail_id' => $existingDemandData['childId'],
                                            'flat_id' => null, //will be changed later
                                            'subhead_id' => $osh->subhead_id,
                                            'total' => 0,
                                            'net_total' => $osh->balance_amount,
                                            'paid_amount' => null,
                                            'balance_amount' => $osh->balance_amount,
                                            'carried_amount' => $osh->balance_amount,
                                            'duration_from' =>  $osh->duration_from,
                                            'duration_to' => $osh->duration_to,
                                            'formula_id' => $osh->formula_id ?? null,
                                            'fy' => $osh->fy,
                                            'remarks' => $osh->remark,
                                            'created_by' => Auth::id(),
                                            'updated_by' => Auth::id()
                                        ]);
                                    }
                                }
                            }
                        }

                        //previous dues logic is to be revised - commented on 26-03-2025
                        /* if (isset($prevDues)) {
                            if ($prevDues > 0) {
                                DemandDetail::create([
                                    'demand_id' => $newDemandId,
                                    'property_master_id' => $newDemand->property_master_id,
                                    'splited_property_detail_id' => $newDemand->property_master_id,
                                    'flat_id' => null, //will be changed later
                                    'subhead_id' => getServiceType('PREV_DUE'),
                                    'total' => $prevDues,
                                    'net_total' => $prevDues,
                                    'paid_amount' => null,
                                    'balance_amount' => $prevDues,
                                    'carried_amount' => null,
                                    'duration_from' =>  null,
                                    'duration_to' => null,
                                    'fy' => null,
                                    'remarks' => 'previous dues- Demand Id = ' . $prevDuesDemandId,
                                    'created_by' => Auth::id(),
                                    'updated_by' => Auth::id()
                                ]);
                                Demand::find($newDemandId)->update([
                                    'total' => $newDemand->total + $prevDues,
                                    'net_total' => $newDemand->net_total + $prevDues,
                                    'balance_amount' => $newDemand->balance_amount + $prevDues
                                ]);
                            }

                            OldDemand::where('demand_id', $prevDuesDemandId)->update(['new_demand_id' => $newDemandId]);
                        } */
                        /* foreach ($request->subhead as $i => $sh) {

                            $demandDetail = DemandDetail::updateOrCreate([
                                'id' => $request->detail_id[$i] ?? 0
                            ], [
                                'demand_id' => $newDemandId,
                                'property_master_id' => $newDemand->property_master_id,
                                'splited_property_detail_id' => $newDemand->property_master_id,
                                'flat_id' => null, //will be changed later
                                'subhead_id' => $sh,
                                'total' => $amounts[$i],
                                'net_total' => $amounts[$i],
                                'paid_amount' => null,
                                'balance_amount' => $amounts[$i],
                                'carried_amount' => null,
                                'duration_from' =>  $request->duration_from[$i],
                                'duration_to' => $request->duration_to[$i],
                                'fy' => $fy,
                                'remarks' => $request->remark[$i],
                                'created_by' => Auth::id(),
                                'updated_by' => Auth::id()
                            ]);
                        } */

                        /** If previous demand for the application */
                        if (isset($request->oldDemandId) && count($request->oldDemandId) > 0) {
                            foreach ($request->oldDemandId as $oldDemandIndex => $oldDemandId) {
                                OldDemandSubhead::where('DemandID', $oldDemandId)->update(['is_added_to_new_demand' => 0]);
                                $subHeadKeys = $request->oldDemandSubheadkey[$oldDemandIndex];
                                if (isset($request->check[$oldDemandIndex])) {
                                    $selectedSubheads = array_keys($request->check[$oldDemandIndex]);
                                    foreach ($subHeadKeys as $subheadName => $subheadAmount) {

                                        if (in_array($subheadName, $selectedSubheads)) {
                                            // echo ($subheadName . '>>>');
                                            $previousDuesDemand += $subheadAmount;
                                            // echo ($previousDuesDemand . '>>><br>');
                                            OldDemandSubhead::where('DemandID', $oldDemandId)
                                                ->where('Subhead', $subheadName)
                                                ->update(['is_added_to_new_demand' => 1]);
                                        }
                                    }
                                }

                                OldDemand::where('demand_id', $oldDemandId)->update(['new_demand_id' => $newDemandId]);
                                $newDemand->update([
                                    'total' => $newDemand->total + $previousDuesDemand,
                                    'net_total' => $newDemand->net_total + $previousDuesDemand,
                                    'balance_amount' => $newDemand->balance_amount + $previousDuesDemand,
                                ]);
                                $previousDues += $previousDuesDemand;
                                $previousDuesDemand = 0;
                            }
                            DemandDetail::where('demand_id', $newDemandId)->where('subhead_id', getServiceType("PREV_DUE"))->delete();
                            if ($previousDues > 0) {
                                $demandDetail = DemandDetail::create([
                                    'demand_id' => $newDemandId,
                                    'property_master_id' => $newDemand->property_master_id,
                                    'splited_property_detail_id' => $newDemand->splited_property_detail_id,
                                    'flat_id' => null, //will be changed later
                                    'subhead_id' => getServiceType('PREV_DUE'),
                                    'total' => $previousDues,
                                    'net_total' => $previousDues,
                                    'paid_amount' => null,
                                    'balance_amount' => $previousDues,
                                    'carried_amount' => null,
                                    /* 'duration_from' =>  $request->duration_from[$i],
                                        duration_to' => $request->duration_to[$i], */
                                    'fy' => $fy,
                                    'remarks' => "Previous pending dues for demand" . (count($request->oldDemandId) > 1 ? 's -' : ' -') . (implode(', ', $request->oldDemandId)),
                                    'created_by' => Auth::id(),
                                    'updated_by' => Auth::id()
                                ]);
                            }
                        }
                        // dd(__LINE__);

                        $validationErrors = [];
                        $subheadKeysConfig = config('demandHeadKeys');
                        $requestSubheads = array_keys(array_filter($amounts, fn($val) => !is_null($val)));
                        // dd($amounts, $requestSubheads);
                        foreach ($subheadKeysConfig as $subheadCode => $inputs) {
                            if (!in_array($subheadCode, $requestSubheads)) continue;

                            $isManual = $subheadCode === 'DEM_MANUAL';
                            $rows = $isManual ? ($amounts['DEM_MANUAL'] ?? []) : [$amounts[$subheadCode]];

                            foreach ($rows as $ind => $value) {
                                foreach ($inputs as $input) {
                                    $key = $input['key'];

                                    // Determine value based on type (manual or regular)
                                    $inputValue = $isManual
                                        ? ($request->{$key}[$ind] ?? null)
                                        : ($request->{$key} ?? null);
                                    // dd($isManual, $input, $key, $inputValue);

                                    // Required validation
                                    if (!empty($input['required']) && ($inputValue === null || $inputValue === '')) {
                                        $label = $input['label'] ?? str_replace('_', ' ', $key);
                                        $validationErrors[] = "$label is required" . ($isManual ? " for row " . ($ind + 1) : "");
                                    }

                                    // RequiredIf logic
                                    if (!empty($input['requiredIf'])) {
                                        $reqArray = explode('=', $input['requiredIf']);
                                        $conditionKey = $reqArray[0];
                                        $conditionValue = $reqArray[1] ?? null;
                                        $conditionActualValue = $isManual
                                            ? ($request->{$conditionKey}[$ind] ?? null)
                                            : ($request->{$conditionKey} ?? null);

                                        if (
                                            $conditionActualValue !== null &&
                                            ($conditionValue === '' || $conditionActualValue == $conditionValue) &&
                                            ($inputValue === null || $inputValue === '')
                                        ) {
                                            $label = $input['label'] ?? str_replace('_', ' ', $key);
                                            $conditionLabel = str_replace('_', ' ', $conditionKey);
                                            $validationErrors[] = "$label is required when $conditionLabel is checked" . ($isManual ? " for row " . ($ind + 1) : "");
                                        }
                                    }

                                    // Type validation
                                    if (!empty($input['type']) && $inputValue !== null && $inputValue !== '') {
                                        if ($input['type'] === 'date' && !isValidDate($inputValue)) {
                                            $label = $input['label'] ?? str_replace('_', ' ', $key);
                                            $validationErrors[] = "Invalid date in $label" . ($isManual ? " for row " . ($ind + 1) : "");
                                        }
                                        if ($input['type'] === 'number' && !is_numeric($inputValue)) {
                                            $label = $input['label'] ?? str_replace('_', ' ', $key);
                                            $validationErrors[] = "Invalid number in $label" . ($isManual ? " for row " . ($ind + 1) : "");
                                        }
                                    }
                                }
                            }
                        }

                        if (!empty($validationErrors)) {
                            // return response()->json(['status' => false, 'details' => $validationErrors]);
                            throw new Exception(json_encode([
                                'status' => false,
                                'details' => $validationErrors
                            ]));
                        }


                        // Delete old keys
                        DemandHeadKey::where('demand_id', $newDemandId)->delete();

                        // Handle 'no_demand_head' special case
                        $singlePropertyCheck = $subheadKeysConfig['no_demand_head'][0];
                        // dd($singlePropertyCheck);
                        $key = $singlePropertyCheck['key'];
                        // if (in_array($key, array_keys((array) $request)) && ($request->{$key} == 0 || $request->{$key} == 1)) {
                        if (isset($request->{$key}) && ($request->{$key} == 0 || $request->{$key} == 1)) {
                            DemandHeadKey::create([
                                'demand_id' => $newDemandId,
                                'key' => $key,
                                'value' => $request->{$key}
                            ]);
                            unset($subheadKeysConfig['no_demand_head']);
                        }

                        foreach ($subheadKeysConfig as $subheadCode => $inputs) {
                            if (!in_array($subheadCode, $requestSubheads)) continue;

                            $isManual = $subheadCode === 'DEM_MANUAL';
                            $rows = $isManual ? ($amounts['DEM_MANUAL'] ?? []) : [$amounts[$subheadCode]];

                            foreach ($rows as $ind => $amount) {
                                $demandDetail = DemandDetail::updateOrCreate([
                                    'id' => $isManual
                                        ? ($request->detail_id[$subheadCode][$ind] ?? 0)
                                        : ($request->detail_id[$subheadCode] ?? 0),
                                ], [
                                    'demand_id' => $newDemandId,
                                    'property_master_id' => $newDemand->property_master_id,
                                    'splited_property_detail_id' => $newDemand->splited_property_detail_id,
                                    'flat_id' => null,
                                    'subhead_id' => getServiceType($subheadCode),
                                    'total' => $amount,
                                    'net_total' => $amount,
                                    'paid_amount' => null,
                                    'balance_amount' => $amount,
                                    'carried_amount' => null,
                                    'fy' => $fy,
                                    'formula_id' => $this->getDemandFormula($subheadCode),
                                    'created_by' => Auth::id(),
                                    'updated_by' => Auth::id()
                                ]);

                                if ($demandDetail) {
                                    $insertData = [];
                                    foreach ($inputs as $input) {
                                        $key = $input['key'];
                                        $value = $isManual
                                            ? ($request->{$key}[$ind] ?? null)
                                            : ($input['type'] === 'checkbox' ? isset($request->{$key}) : ($request->{$key} ?? null));

                                        $insertData[] = [
                                            'demand_id' => $newDemandId,
                                            'head_id' => $demandDetail->id,
                                            'key' => $key,
                                            'value' => $value,
                                        ];
                                    }
                                    DemandHeadKey::insert($insertData);
                                }
                            }
                        }


                        // }
                        /* if ($manualDemand) {
                            foreach ($amounts as $i => $amount) {
                                $demandDetail = DemandDetail::updateOrCreate([
                                    'id' => $request->detail_id[$i] ?? 0
                                ], [
                                    'demand_id' => $newDemandId,
                                    'property_master_id' => $newDemand->property_master_id,
                                    'splited_property_detail_id' => $newDemand->property_master_id,
                                    'flat_id' => null, //will be changed later
                                    'subhead_id' => $request->subhead[$i],
                                    'total' => $amount,
                                    'net_total' => $amount,
                                    'paid_amount' => null,
                                    'balance_amount' => $amount,
                                    'carried_amount' => null,
                                    'duration_from' =>  $request->duration_from[$i],
                                    'duration_to' => $request->duration_to[$i],
                                    'fy' => $fy,
                                    'remarks' => $request->remark[$i],
                                    'created_by' => Auth::id(),
                                    'updated_by' => Auth::id()
                                ]);
                            }
                        } */


                        return response()->json(['status' => true, 'message' => 'Demand created successfullly']);
                    } else {
                        return response()->json(['status' => false, 'details' => 'Demand not created.']);
                    }
                } else {
                    return response()->json(['status' => false, 'details' => config('messages.property.error.notFound')]);
                }
                return response()->json(['status' => true, 'message' => 'Demand created successfullly']);
            });
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            $response = ['status' => false, 'message' => $e->getMessage(), 'data' => 0];
            return json_encode($response);
        }
    }

    // public function ApproveDemand($demandId)
    // {
    //     $demand = Demand::find($demandId);
    //     if (empty($demand)) {
    //         return redirect()->back()->with('failure', "No data found!!");
    //     }
    //     if ($demand->status == getServiceType('DEM_DRAFT')) {
    //         $demand->update(['status' => getServiceType('DEM_PENDING'), 'updated_by' => Auth::id()]);

    //         //send email to peoprty owner abt new demand created
    //         return redirect()->route('demandList')->with('success', "Demand approved successfully");
    //     } else {
    //         return redirect()->back()->with('failure', "Can not approve this demand");
    //     }
    // }

    //Updated by Swati Mishra to integrate mail, sms, whatsapp while approving demand on 17-04-2025
    public function ApproveDemand($demandId)
    {
        $demand = Demand::find($demandId);

        // if (empty($demand)) {
        //     return redirect()->back()->with('failure', "No data found!!");
        // }

        if ($demand->status == getServiceType('DEM_DRAFT')) {
            $notificationData = [
                'demand_id' => $demand->unique_id,
                'amount' => $demand->net_total
            ];

            $email = null;
            $mobile = null;
            $action = 'DEMAND_GEN';
            $address = null;

            if (is_null($demand->splited_property_detail_id)) {
                $userProperty = UserProperty::where('new_property_id', $demand->property_master_id)->first();

                if ($userProperty) {
                    $user = User::find($userProperty->user_id);
                    $email = $user->email;
                    $mobile = $user->mobile_no;
                } else {
                    $contactDetails = PropertyContactDetail::where('property_master_id', $demand->property_master_id)->first();
                    $email = $contactDetails->email ?? null;
                    $mobile = $contactDetails->mobile_no ?? null;
                }
                // Get address from property_lease_details
                $leaseDetail = PropertyLeaseDetail::where('property_master_id', $demand->property_master_id)->first();
                $address = $leaseDetail?->presently_known_as ?? 'N/A';
            } else {
                $splitId = $demand->splited_property_detail_id;

                $userProperty = UserProperty::where('new_property_id', $splitId)->first();

                if ($userProperty) {
                    $user = User::find($userProperty->user_id);
                    $email = $user->email;
                    $mobile = $user->mobile_no;
                } else {
                    $contactDetails = PropertyContactDetail::where('property_master_id', $demand->property_master_id)->get();
                    $matchedContact = $contactDetails->firstWhere('splited_property_detail_id', $splitId);

                    if ($matchedContact) {
                        $email = $matchedContact->email ?? null;
                        $mobile = $matchedContact->mobile_no ?? null;
                    }
                }
                // Get address from splited_property_details
                $splitDetail = SplitedPropertyDetail::find($splitId);
                $address = $splitDetail?->presently_known_as ?? 'N/A';
            }
            $email = 'nitinrag@gmail.com';
            if ($email || $mobile) {
                $demand->update(['status' => getServiceType('DEM_PENDING'), 'updated_by' => Auth::id(), 'approved_at' => date('Y-m-d H:i:s')]); //updating demand only if contact details are available   //approved at added by nitin on 30-04-2025

                // Fetch related data
                $demandDetails = DemandDetail::where('demand_id', $demand->id)->get();
                $items = Item::where('group_id', 7003)
                    ->whereIn('id', $demandDetails->pluck('subhead_id'))
                    ->pluck('item_name', 'id');
                $formulas = DemandFormula::whereIn('id', $demandDetails->pluck('formula_id')->filter())
                    ->pluck('formula', 'id');

                // Generate PDF
                $pdf = Pdf::loadView('demand.demand_pdf', [
                    'demand' => $demand,
                    'demandDetails' => $demandDetails,
                    'items' => $items,
                    'formulas' => $formulas,
                    'address' => $address
                ]);

                $timestamp = now()->setTimezone('Asia/Kolkata')->format('d-m-Y_H-i-s');
                $filename = "{$demand->unique_id}_{$timestamp}.pdf";
                $path = "Demand/{$filename}";

                Storage::put("public/{$path}", $pdf->output());
                $notificationData['attachment'] = Storage::path("public/{$path}");

                $this->settingsService->applyMailSettings($action);

                if ($email) {
                    try {
                        Mail::to($email)->send(new CommonMail($notificationData, $action));
                    } catch (\Exception $e) {
                        Log::error("Failed to send demand email: " . $e->getMessage());
                    }
                }

                if ($mobile) {
                    try {
                        $this->communicationService->sendSmsMessage($notificationData, $mobile, $action);
                    } catch (\Exception $e) {
                        Log::error("Failed to send demand SMS: " . $e->getMessage());
                    }

                    try {
                        $this->communicationService->sendWhatsAppMessage($notificationData, $mobile, $action);
                    } catch (\Exception $e) {
                        Log::error("Failed to send demand WhatsApp: " . $e->getMessage());
                    }
                }

                return redirect()->route('demandList')->with('success', "Demand approved successfully");
            } else {
                Log::info("Demand not approved. No contact details found.");
                return redirect()->back()->with('failure', "Cannot approve demand — no contact details found, please update it.");
            }
        } else {
            return redirect()->back()->with('failure', "Can not approve this demand");
        }
    }


    public function withdrawDemand($demandId)
    {
        $demand = Demand::find($demandId);
        if (empty($demand)) {
            return redirect()->back()->with('failure', "No data found!!");
        }
        if ($demand->carried_amount && $demand->carried_amount > 0) {
            $carriedDetails = CarriedDemandDetail::where('new_demand_id', $demandId)->first();
            if (!empty($carriedDetails)) {
                $oldDemand = Demand::find($carriedDetails->old_demand_id);
                if (!empty($oldDemand)) {
                    $statusToUpdate = getServiceType('DEM_PART_PAID');
                    if ($oldDemand->net_total == $oldDemand->balance_amount) {
                        $statusToUpdate = getServiceType('DEM_PENDING');
                    }
                    $oldDemand->update(['status' => $statusToUpdate, 'updated_by' => Auth::id()]);
                } else {
                    return redirect()->back()->with('failure', "Something went wrong. Required data is missing");
                }
            } else {
                return redirect()->back()->with('failure', "Something went wrong. Required data is missing");
            }
        }
        $demand->update(['status' => getServiceType('DEM_WD'), 'updated_by' => Auth::id()]);
        return redirect()->back()->with('success', "Demand withdrawn successfully");
    }


    public function index()
    {
        if (Auth::user()->hasAnyRole('lndo', 'super-admin'))
            $demandQuery = Demand::latest();
        else {
            $pms = new PropertyMasterService();
            $userSectonProperties = $pms->userSectionProperties();
            $demandQuery = Demand::whereIn('old_property_id', $userSectonProperties)->latest();
        }
        $demands = $demandQuery->where(function ($query) {
            return $query->whereNull('model')->orWhere('model', '<>', 'PropertyRevivisedGroundRent');
        })->get();
        return view('demand.index', compact('demands'));
    }

    public function applicantPendingDemands()
    {
        $pendingDemands = GeneralFunctions::getUserDemandData(false, true);
        return view('demand.applicant.index', ['demands' => $pendingDemands]);
    }
    public function applicantViewDemand($demandId)
    {
        $demand = Demand::find($demandId);
        if (empty($demand)) {
            return redirect()->back()->with('failure', "No data found!!");
        }
        return view('demand.applicant.view', ['demand' => $demand]);
    }

    public function applicantPayForDemand($demandId)
    {
        $demand = Demand::find($demandId);
        if (empty($demand)) {
            return redirect()->back()->with('failure', "No data found!!");
        }
        if (in_array(getServiceCodeById($demand->status), ["DEM_PART_PAID", "DEM_PENDING"])) {

            $addressDropdownData = getAddressDropdownData();

            return view('demand.applicant.payment-form', ['demand' => $demand, ...$addressDropdownData]);
        } else {
            return redirect()->back()->with('failure', "Can not make payment for this demand");
        }
    }

    public function applicantDemandPayment(Request $request, PaymentService $paymentService)
    {
        if (!empty($request->demand_id)) {
            $demandId = $request->demand_id;
            $demand = Demand::find($demandId);
            if (empty($demand)) {
                return redirect()->back()->with('faliure', 'Given demand not found');
            }
        } else {
            return redirect()->back()->with('faliure', 'Demand not given');
        }
        $propertyMasterId = $demand->property_master_id;
        if (is_null($demand->splited_property_detail_id)) {
            $master_old_property_id = $demand->old_property_id;
            $splited_old_property_id = null;
        } else {
            $masterProperty = PropertyMaster::find($propertyMasterId);
            $master_old_property_id = $masterProperty->old_propert_id;
            $splited_old_property_id = $demand->old_property_id;
        }
        $paidAmount = array_sum($request->paid_amount);
        $uniquePayemntId = 'DEM' . date('YmdHis');
        $payment = Payment::create([
            'property_master_id' => $propertyMasterId,
            'type' => getServiceType('PAY_DEMAND'),
            'demand_id' => $demandId,
            'payment_mode' => getServiceType($request->payment_mode),
            'unique_payment_id' => $uniquePayemntId,
            'splited_property_detail_id' => $demand->splited_property_detail_id,
            'master_old_property_id' => $master_old_property_id,
            'splited_old_property_id' => $splited_old_property_id,
            'amount' => $paidAmount,
            'status' => 1,
            'created_by' => Auth::check() ? Auth::id() : null
        ]);

        if ($payment) {

            //save payer details
            GeneralFunctions::savePayerDetails($request->all(), $payment->id);

            //save payment subheads
            foreach ($request->subhead_id as $i => $subhead) {
                if (isset($request->paid_amount[$i]) && $request->paid_amount[$i] != "") {
                    $saveDetail = PaymentDetail::create([
                        'payment_id' => $payment->id,
                        'demand_id' => $demandId,
                        'subhead_id' => $subhead,
                        'paid_amount' => $request->paid_amount[$i],
                    ]);
                }
            }
            // Payment 
            list($countryName, $stateName, $cityName) =  GeneralFunctions::getAddressNames($request->only('country', 'state', 'city'));

            $orderCode = $uniquePayemntId;
            // $orderCode = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
            $payementData = [
                'order_code' => $orderCode,
                'merchant_batch_code' => $orderCode,
                'installation_id' => '11136',
                'amount' => $paidAmount,
                'currency_code' => "INR",
                'order_content' => '23092',
                'payemnt_type_id' => config('constants.payment_type_id'),
                'code' => getServiceNameByCode($request->payment_mode),
                'email' => $request->payer_email,
                'first_name' => $request->payer_first_name,
                'last_name' => $request->payer_last_name,
                'mobile' => $request->payer_mobile,
                'address_1' => $request->address_1,
                'address_2' => $request->address_2,
                'postal_code' => $request->postal_code,
                'region' => $request->region,
                'city' => $cityName,
                'state' => $stateName,
                'country' => $countryName,
            ];
            // dd($payementData);
            $transaction = $paymentService->makePayemnt($payementData);
            // return redirect()->back()->with('success', 'Data saved successfully');
        }
    }

    /** this function returns displays the data of old applications demands  */

    public function oldDemandData($demandIds, $searchColumn = 'demand_id', $dataOnly = false)
    {
        if (strlen($demandIds) > 0) {
            $demandIdArray = explode(',', $demandIds);
            $oldDeamands = OldDemand::whereIn($searchColumn, $demandIdArray)->get();
            // dd($oldDeamands, $demandIdArray);
            if ($oldDeamands->count() > 0) {
                foreach ($oldDeamands as $i => $demand) {
                    $oldDeamands[$i]->subheadwiseBreakup = $this->getSubheadwiseBreakup($demand);
                }
            }
        }
        if ($dataOnly) {
            return isset($oldDeamands) ? $oldDeamands : [];
        }
        $data['oldDemands'] = isset($oldDeamands) ? $oldDeamands : [];
        return view('include.parts.old-demand-details', $data);
    }

    public function getSubheadwiseBreakup($demand)
    {
        $subheads = OldDemandSubhead::where('DemandID', $demand->demand_id)->get();
        $subheadwiseBreakup = [];
        if ($subheads->count() > 0) {
            foreach ($subheads as $demandHead) {
                if (!isset($subheadwiseBreakup[$demandHead->Subhead])) {
                    $subheadwiseBreakup[$demandHead->Subhead] = ['subhedName' => $demandHead->Subhead, 'demand_amount' => 0, 'paid_amount' => 0, 'checked' => $demandHead->is_added_to_new_demand == 1];
                }
                if (trim($demandHead->PaymentStatus) == "N")
                    $subheadwiseBreakup[$demandHead->Subhead]['demand_amount'] += $demandHead->Amount;
                if (trim($demandHead->PaymentStatus) == "Y")
                    $subheadwiseBreakup[$demandHead->Subhead]['paid_amount'] += $demandHead->Amount;
            }
        }
        return $subheadwiseBreakup;
    }

    public function getDemandHeads($newAllotment, $formatToJson = true)
    {
        if ($newAllotment == 1) {
            $dataQuery = DemandFormula::whereIn('for_allotment_type', [1, 2]);
        } else {
            $dataQuery = DemandFormula::whereIn('for_allotment_type', [0, 2]);
        }
        $formulae = $dataQuery->where(function ($query) {
            return $query->whereDate('date_from', '<=', date('Y-m-d'))->whereDate('date_to', '>=', date('Y-m-d'))->orWhere(
                function ($q1) {
                    return $q1->whereNull('date_from')->whereDate('date_to', '>=', date('Y-m-d'));
                }
            )->orWhere(function ($q2) {
                return $q2->whereNull('date_to')->whereDate('date_from', '<=', date('Y-m-d'));
            });
        })->select('head_code', 'formula', 'description')->get()->mapWithKeys(function ($val) {
            return [$val->head_code => ['formula' => $val->formula, 'description' => $val->description]];
        });
        $sendItemCodes = array_merge($formulae->keys()->toArray(), ['DEM_OTHER', 'DEM_MANUAL']);
        // dd($formulae['DEM_AF_P']);
        $sendItems = Item::whereIn('item_code', $sendItemCodes)->where('is_active', 1)->orderBY('item_order')->get();
        foreach ($sendItems as $sendItem) {
            if (isset($formulae[$sendItem->item_code])) {
                $sendItem->formula = $formulae[$sendItem->item_code]['formula'];
                $sendItem->description = $formulae[$sendItem->item_code]['description'];
            }
        }
        return ($formatToJson) ?  response()->json($sendItems) : $sendItems;
    }


    private function getActiveApplicationData($oldPropertyId)
    {
        $data =  DB::table('applications')
            ->whereIn('applications.status', [
                getServiceType('APP_NEW'),
                getServiceType('APP_PEN'),
                getServiceType('APP_IP'),
                getServiceType('APP_OBJ')
            ])
            ->leftJoin('mutation_applications as ma', function ($join) {
                $join->on('ma.application_no', '=', 'applications.application_no')
                    ->where('applications.model_name', '=', 'MutationApplication');
            })
            ->leftJoin('conversion_applications as ca', function ($join) {
                $join->on('ca.application_no', '=', 'applications.application_no')
                    ->where('applications.model_name', '=', 'ConversionApplication');
            })
            ->leftJoin('deed_of_apartment_applications as doa', function ($join) {
                $join->on('doa.application_no', '=', 'applications.application_no')
                    ->where('applications.model_name', '=', 'DeedOfApartmentApplication');
            })
            ->leftJoin('land_use_change_applications as luc', function ($join) {
                $join->on('luc.application_no', '=', 'applications.application_no')
                    ->where('applications.model_name', '=', 'LandUseChangeApplication');
            })
            ->leftJoin('noc_applications as noc', function ($join) {
                $join->on('noc.application_no', '=', 'applications.application_no')
                    ->where('applications.model_name', '=', 'NocApplication');
            })
            ->where(function ($query) use ($oldPropertyId) {
                $query->where('ma.old_property_id', $oldPropertyId)
                    ->orWhere('ca.old_property_id', $oldPropertyId)
                    ->orWhere('doa.old_property_id', $oldPropertyId)
                    ->orWhere('luc.old_property_id', $oldPropertyId)
                    ->orWhere('noc.old_property_id', $oldPropertyId);
            })
            ->select('applications.*')
            ->get();
        foreach ($data as $key => $row) {
            $data[$key]->statusName = getServiceNameById((int)$row->status);
            $data[$key]->appliedFor = getServiceNameById($row->service_type);
        }
        return $data;
    }

    public function manualDemandCreate(ColonyService $colonyService)
    {
        /* if (!empty($request->all())) {
            $applicationNo = $request->applicationNo;
            $application = Application::where('application_no', $applicationNo)->first();
            $data['applicationData'] = $application->applicationData;
        } */
        $data['colonies'] = $colonyService->misDoneForColonies(true);
        $data['demandSubheads'] = Item::where('group_id', 7003)->where('is_active', 1)->orderBy('item_name')->get();
        return view('demand.manual-input-form', $data);
    }

    private function getDemandFormula($subheadCode, $date = null)
    {
        if (is_null($date)) {
            $date = date('Y-m-d');
        }
        $formula = DemandFormula::where('head_code', $subheadCode)->where(function ($query) use ($date) {

            return $query->whereDate('date_from', '<=', $date)->whereDate('date_to', '>=', $date)->orWhere(
                function ($q1) use ($date) {
                    return $q1->whereNull('date_from')->whereDate('date_to', '>=', $date);
                }
            )->orWhere(function ($q2) use ($date) {
                return $q2->whereNull('date_to')->whereDate('date_from', '<=', $date);
            });
        })->first();
        return !empty($formula) ? $formula->id : null;
    }

    public function oldDemandBreakUp($oldDemandId)
    {
        if (!is_null($oldDemandId)) {
            $data['subheads'] = OldDemandSubhead::where('DemandID', $oldDemandId)->get();
            return view('include.parts.old-demand-subheads', $data);
        } else {
            return back()->with('failure', 'Invalid Id given.');
        }
    }

    public function outStandingDuesList($propertyStatus = null)
    {
        $data = OldDemand::join('items', 'old_demands.property_status', '=', 'items.id')
            ->when(!is_null($propertyStatus), function ($query) use ($propertyStatus) {
                return $query->where('property_status', $propertyStatus);
            })
            ->select('old_demands.*', 'items.item_name as propertyStatus')->paginate(3);
        return view('demand.outstanding-dues-list', compact('data'));
    }
}
