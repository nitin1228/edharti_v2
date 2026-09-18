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
use App\Services\ApplicationForwardService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PropertyLeaseDetail;
use App\Models\SplitedPropertyDetail;
use App\Models\CurrentLesseeDetail;
use App\Models\Section;
use App\Models\Designation;
use Exception;
use Carbon\Carbon;
use App\Services\DemandService;
use App\Models\ConversionApplication;
use App\Models\LandUseChangeApplication;
use App\Models\DeedOfApartmentApplication;
use App\Models\MutationApplication;
use App\Models\NocApplication;
use App\Models\Flat;

use function PHPUnit\Framework\isNull;
use Illuminate\Support\Facades\Validator;

class DemandController extends Controller
{
    protected $communicationService;
    protected $settingsService;
    protected $demandService;

    public function __construct(CommunicationService $communicationService, SettingsService $settingsService, DemandService $demandService)
    {
        $this->communicationService = $communicationService;
        $this->settingsService = $settingsService;
        $this->demandService = $demandService;
    }
    public function createDemandView(Request $request, ColonyService $colonyService)
    {
        if (!empty($request->all())) {
            $applicationNo = $request->applicationNo;
            $application = Application::where('application_no', $applicationNo)->first();
            $data['applicationData'] = $application->applicationData;
        }
        $data['colonies'] = $colonyService->misDoneForColonies(true);
        $data['withOutLeaseHold'] = 1;
        // $data['demandSubheads'] = Item::where('group_id', 7003)->where('is_active', 1)->orderBy('item_name')->get();
        return view('demand.input-form', $data);
    }
    public function EditDemand($demandId, $applicationData =  null)
    {
        $demand = Demand::find($demandId);
        if (empty($demand)) {
            return redirect()->back()->with('failure', "No data found!!");
        }
        $data = $this->getDemandData($demand);
        $data['demand'] = $demand;

        $propertyMasterId = $demand->property_master_id;
        if ($demand->flat_id) {
            $flat = Flat::find($demand->flat_id);
            $propertyContactDetails = (object) [
                'lessees_name' => $flat->present_occupant_name ?? '',
                'address'      => $flat->address ?? '',
                'email'        => $flat->email ?? '',
                'phone_no'     => $flat->phone ?? '',
            ];
        } else if ($demand->splited_property_detail_id) {
            $childId = $demand->splited_property_detail_id;
            $propertyContactDetails = PropertyContactDetail::where('property_master_id', $propertyMasterId)->where('splited_property_detail_id', $childId)->first();
        } else {
            $propertyContactDetails = PropertyContactDetail::where('property_master_id', $propertyMasterId)->first();
        }
        $data['propertyContactDetails'] = $propertyContactDetails;
        // $viewBlade = $demand->is_manual == 1 ? 'demand.manual-input-form' : 'demand.input-form';
        /* if ($demand->is_manual) {
            $data['demandSubheads'] = Item::where('group_id', 7003)->where('is_active', 1)->orderBy('item_name')->get();
        } */
        if (!is_null($applicationData)) {
            $data['applicationData'] = json_decode($applicationData);
        }
        $data['withOutLeaseHold'] = 1;
        return view('demand.input-form', $data);
    }

    public function demandLetterPdf($id)
    {
        $demand = Demand::findOrFail($id);
        $flat = null;
        if ($demand->flat_id) {
            $flat = Flat::find($demand->flat_id);
            $name = $flat->present_occupant_name ?? "Sir/Madam";
            $address = $flat->address ?? 'N/A';
        } else if (is_null($demand->splited_property_detail_id)) {
            $contactDetails = PropertyContactDetail::where('property_master_id', $demand->property_master_id)->first();
            if ($contactDetails) {
                $name = $contactDetails->lessees_name ?? "Sir/Madam";
                $address = $contactDetails->address;
            } else {
                $userProperty = UserProperty::where('new_property_id', $demand->property_master_id)->first();
                if ($userProperty) {
                    $user = User::find($userProperty->user_id);
                    $name = $user->name;
                }
                // Address from property_lease_details
                $leaseDetail = PropertyLeaseDetail::where('property_master_id', $demand->property_master_id)->first();
                $address = $leaseDetail?->presently_known_as ?? 'N/A';
            }


            $getUniquePropertyId = PropertyMaster::where('id', $demand->property_master_id)->first();
            $uniquePropertyId = $getUniquePropertyId?->unique_propert_id ?? 'N/A';
        } else {
            // Split property
            $splitId = $demand->splited_property_detail_id;

            $contactDetails = PropertyContactDetail::where('property_master_id', $demand->property_master_id)->where('splited_property_detail_id', $splitId)->first();
            if ($contactDetails) {
                $name = $contactDetails->lessees_name ?? "Sir/Madam";
                $address = $contactDetails->address;
            } else {
                $userProperty = UserProperty::where('splitted_property_id', $splitId)->first();
                if ($userProperty) {
                    $user = User::find($userProperty->user_id);
                    $name = $user->name;
                }
                // Address from splited_property_details
                $splitDetail = SplitedPropertyDetail::find($splitId);
                $address = $splitDetail?->presently_known_as ?? 'N/A';
            }


            $uniquePropertyId = $splitDetail?->child_prop_id ?? 'N/A';
        }

        // Fetch related data
        $demandDetails = DemandDetail::where('demand_id', $demand->id)->get();
        $items = Item::where('group_id', 7003)
            ->whereIn('id', $demandDetails->pluck('subhead_id'))
            ->pluck('item_name', 'id');
        $formulas = DemandFormula::whereIn('id', $demandDetails->pluck('formula_id')->filter())
            ->pluck('formula', 'id');

        //fetching approved_by data
        $approvedBy = 'N/A'; // Default

        $propertyMaster = PropertyMaster::find($demand->property_master_id);
        $splittedProperty = !is_null($demand->splited_property_detail_id) ? SplitedPropertyDetail::find($demand->splited_property_detail_id) : null;

        if ($propertyMaster) {
            $sectionCode = $propertyMaster->section_code;

            // Assuming section_code maps to Sections.code
            $section = Section::where('section_code', $sectionCode)->first();


            if ($section) {
                // Get the deputy-lndo role id
                $designation = Designation::where('name', 'DEPUTY L&amp;DO')->first();


                if ($designation) {
                    $approvedByDesignation = $designation->name;
                    $sectionUser = DB::table('section_user')
                        ->where('section_id', $section->id)
                        ->where('designation_id', $designation->id)
                        ->first();

                    if ($sectionUser) {
                        $approvedUser = User::find($sectionUser->user_id);

                        if ($approvedUser) {
                            $approvedBy = $approvedUser->name;
                        }
                    }
                }
            }
        }

        $applicationName = getServiceNameById($demand->application?->service_type);
        $applicationNo = $demand->application?->application_no;
        $applicationDate = $demand->application?->created_at;

        // dd($demandDetails);
        // // Pass demand, name, and address to the view
        // $pdf = Pdf::loadView('demand.demand_letter_pdf', compact('demand', 'name', 'address', 'demandDetails', 'items', 'formulas', 'approvedBy', 'approvedByDesignation'));
        $pdf = Pdf::loadView('demand.demand_letter_pdf', compact('demand', 'name', 'address', 'demandDetails', 'propertyMaster', 'splittedProperty', 'items', 'formulas', 'approvedBy', 'approvedByDesignation', 'flat'));

        return $pdf->stream('demand_letter.pdf');
    }
    public function ViewDemand($demandId)
    {
        $demand = Demand::find($demandId);
        if (empty($demand)) {
            return redirect()->back()->with('failure', "No data found!!");
        }
        // dd($demand->is_lease_hold);
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
        $propertyMasterId = $demand->property_master_id;

        if ($demand->flat_id) {
            $flat = Flat::find($demand->flat_id);
            $propertyContactDetails = (object) [
                'lessees_name' => $flat->present_occupant_name ?? '',
                'address'      => $flat->address ?? '',
                'email'        => $flat->email ?? '',
                'phone_no'     => $flat->phone ?? '',
            ];
        } else if ($demand->splited_property_detail_id) {
            $childId = $demand->splited_property_detail_id;
            $propertyContactDetails = PropertyContactDetail::where('property_master_id', $propertyMasterId)->where('splited_property_detail_id', $childId)->first();
        } else {
            $propertyContactDetails = PropertyContactDetail::where('property_master_id', $propertyMasterId)->first();
        }
        $data['propertyContactDetails'] = $propertyContactDetails;
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
        $settledSubheadIds = $demand->demandDetails->where('subhead_id', getServiceType('DEM_SETTLED_AMOUNT'))->pluck('id')->toArray();
        foreach ($demandDetails as $i => $demandDetail) {
            if (is_null($demandDetail->carried_amount) || $demandDetail->carried_amount == 0) {
                $headCode = $demandDetail->subhead_code;
                // if ($headCode != 'DEM_MANUAL') { //specially handle manual demand and settled amounts
                if (!in_array($headCode, ['DEM_MANUAL', 'DEM_SETTLED_AMOUNT'])) { //specially handle manual demand and settled amounts
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
        $allocationType = $formattedSelectedValues['allocation_type_radio'] ?? 0;
        $data['newAllotment'] = $newAllotmentPropertyValue;
        $data['allocationType'] = $allocationType;
        $data['subheads'] = $this->getDemandHeads($newAllotmentPropertyValue, false);

        // $manualDemandSubheadIds = DemandDetail::whereIn('demand_id', $carriedDemands)->where('subhead_id', getServiceType('DEM_MANUAL'))->pluck('id')->toArray();
        $manualDemandKeys = DemandHeadKey::whereIn('head_id', $manualDemandSubheadIds)->get();
        $settledHeadKeys = DemandHeadKey::whereIn('head_id', $settledSubheadIds)->get();
        foreach ($manualDemandKeys as $mdk) {
            $manualTargetIndex = null;
            foreach ($formattedDemandDetails['DEM_MANUAL'] as $ddKey => $demandDetail) {
                if ($demandDetail['id'] == $mdk->head_id) {
                    $manualTargetIndex = $ddKey;
                    break;
                }
            }
            if (!is_null($manualTargetIndex)) {
                $formattedDemandDetails['DEM_MANUAL'][$manualTargetIndex]['values'][$mdk->key] = $mdk->value;
            }
            //$formattedDemandDetails[getServiceType('DEM_MANUAL')]['values'][$mdk->head_id][$mdk->key] = $mdk->value;
        }
        // dd($demandDetail, $settledHeadKeys);
        foreach ($settledHeadKeys as $shk) {
            // dd($formattedDemandDetails['DEM_SETTLED_AMOUNT'], $demandDetail);
            $settledTargetIndex = null;
            foreach ($formattedDemandDetails['DEM_SETTLED_AMOUNT'] as $shKey => $demandDetail) {
                if ($demandDetail['id'] == $shk->head_id) {
                    $settledTargetIndex = $shKey;
                    break;
                }
            }
            if (!is_null($settledTargetIndex)) {
                $formattedDemandDetails['DEM_SETTLED_AMOUNT'][$settledTargetIndex]['values'][$shk->key] = $shk->value;
            }
            //$formattedDemandDetails[getServiceType('DEM_MANUAL')]['values'][$mdk->head_id][$mdk->key] = $mdk->value;
        }
        $data['manualDemandKeys'] = $manualDemandKeys;
        $data['settledHeadKeys'] = $settledHeadKeys;

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

    /* public function getExistingPropertyDemand($oldPropertyId, $formatToJson = true, $checkApiDemand = true)
    {
        /** get unpaid or partiallly paid demands for property
        if first demand record is not created then - 
        check for pending demands in old application /
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
                 /
                $previousSavedDemands = OldDemand::where('property_id', $oldPropertyId)->get();
                if ($previousSavedDemands->isNotEmpty()) {
                    foreach ($previousSavedDemands as $psd) {
                        OldDemandSubhead::where('DemandID', $psd->demand_id)->delete();
                    }
                    OldDemand::where('property_id', $oldPropertyId)->delete();
                }
                /** get old Demand data /
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
                $previousDues =  ['previousDemands' => $previousDemandData, 'dues' => collect($previousDemandData)->sum('outstanding')/* , 'demandSubheads' => $demandSubheads /];
            }
        }
        /** 
         get previous unpaid demand on this aplication 
         /

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
    } */

    public function getExistingPropertyDemand($oldPropertyId, $flatId = null, $formatToJson = true, $checkApiDemand = true)
    {
        /** get unpaid or partiallly paid demands for property
        if first demand record is not created then - 
        check for pending demands in old application */
        $isFirstDemand = !Demand::where('old_property_id', $oldPropertyId)->when(!is_null($flatId), function ($query) use ($flatId) {
            return $query->where('flat_id', $flatId);
        })->exists();
        if ($checkApiDemand && $isFirstDemand) {

            //if flat id not available than check for edharti 1.0 demands - SOURAV CHAUHAN (25 May 2026)
            if (is_null($flatId)) {
                $oldDemandData = OldDemand::where('property_id', $oldPropertyId)->exists();
                if ($oldDemandData) {
                    $previousDemandData = OldDemand::where('property_id', $oldPropertyId)->get();
                    $previousDemandData = $previousDemandData->toArray();
                    $previousDues =  ['previousDemands' => $previousDemandData, 'dues' => collect($previousDemandData)->sum('outstanding')/* , 'demandSubheads' => $demandSubheads */];
                }
            }
            // dd($previousDues);


            //commented as mow we are fetching demand on basis of flats if available
            // $oldDemandData = OldDemand::where('property_id', $oldPropertyId)->exists();
            // if ($oldDemandData) {
            //     $previousDemandData = OldDemand::where('property_id', $oldPropertyId)->get();
            //     $previousDemandData = $previousDemandData->toArray(); 

            //     $previousDues =  ['previousDemands' => $previousDemandData, 'dues' => collect($previousDemandData)->sum('outstanding')/* , 'demandSubheads' => $demandSubheads */];
            // }
        }
        /** 
         get previous unpaid demand on this application 
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
            if ($flatId) {
                $flat = Flat::find($flatId);
                $propertyContactDetails = (object) [
                    'lessees_name' => $flat->present_occupant_name ?? '',
                    'address'      => $flat->address ?? '',
                    'email'        => $flat->email ?? '',
                    'phone_no'     => $flat->phone ?? '',
                ];
            } else {
                if (is_null($childProperty)) {
                    $propertyContactDetails = PropertyContactDetail::where('property_master_id', $propertyMasterId)->first();
                    $currentLesseeDetails = CurrentLesseeDetail::where('property_master_id', $propertyMasterId)->first();
                } else {
                    $propertyContactDetails = PropertyContactDetail::where('property_master_id', $propertyMasterId)->where('splited_property_detail_id', $childId)->first();
                    $currentLesseeDetails = CurrentLesseeDetail::where('property_master_id', $propertyMasterId)->where('splited_property_detail_id', $childId)->first();
                }
                $propertyContactDetails['lessees_name'] = $currentLesseeDetails->lessees_name ?? null;
                $propertyContactDetails['contact_record_id'] = $propertyContactDetails->id ?? null;
            }
            if (isset($previousDues)) {

                // dd("inside");
                $previousDues['propertyContactDetails'] = $propertyContactDetails;
                return  $formatToJson ? response()->json([
                    'status' => true,
                    'data' => $previousDues
                ]) : ['propertyMasterId' => $propertyMasterId, 'childId' => $childId, 'dues' => $previousDues['dues'], 'previousDemands' => $previousDues['previousDemands']];
            } else {

                $includeRGR = url()->previous() == route('leaseHoldDemands');
                $existingDemand = Demand::where('property_master_id', $propertyMasterId)
                    ->where(function ($query) use ($childId) {
                        if (is_null($childId)) {
                            return $query->whereNull('splited_property_detail_id');
                        } else {
                            return $query->where('splited_property_detail_id', $childId);
                        }
                    })

                    ->when(!is_null($flatId), function ($query) use ($flatId) {
                        return $query->where('flat_id', $flatId);
                    })
                    ->when(!$includeRGR, function ($query) {
                        return $query->where(function ($q) {
                            $q->whereNull('model')
                                ->orWhere('model', '<>', 'PropertyRevivisedGroundRent');
                        });
                    })
                    ->whereIn('status', [getServiceType('DEM_DRAFT'), getServiceType('DEM_PENDING'), getServiceType('DEM_PART_PAID')])
                    ->first();
                return $formatToJson ? response()->json([
                    'status' => true,
                    'data' => ['demand' => $existingDemand, 'demandDetails' => !empty($existingDemand) ? $existingDemand->demandDetails : null, 'propertyContactDetails' => $propertyContactDetails, 'applicationData' => $this->getActiveApplicationData($oldPropertyId)]
                ]) : ['propertyMasterId' => $propertyMasterId, 'propertyContactDetails' => $propertyContactDetails, 'childId' => $childId, 'demand' => $existingDemand];
            }
        }
    }

    public function storeDemand(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'demand_to' => 'required|string|max:255',
                'demand_to_address' => 'required|string|max:500',
                'demand_to_email' => 'required|email|max:255',
                'demand_to_phone' => 'required|digits:10',
            ],
            [
                'demand_to.required' => 'Name is required in To',
                'demand_to_email.email' => 'Enter a valid email',
                'demand_to_email.required' => 'Email is required',
                'demand_to_phone.digits' => 'Phone must be 10 digits',
                'demand_to_phone.required' => 'Phone is required',
                'demand_to_address.required' => 'Address is required',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'details' => $validator->errors()->first(), // first error message
                'data' => 0
            ]);
        }
        // dd($request->all());
        // try {
        return DB::transaction(function () use ($request) {
            $oldPropertyId = $request->oldPropertyId;
            // if($oldPropertyId = "10397"){
            //     dd('Demand called');
            // }

            // $manualDemand = null;
            $flatId = (isset($request->flat_id) && $request->flat_id != '') ? $request->flat_id : null;
            $demandId = (isset($request->id) && $request->id != "") ? $request->id : null;
            if ($oldPropertyId || $demandId) {
                if (!$demandId) {
                    $prevDues = $prevDuesDemandId = null; //initiallize to store demand subheads

                    //after latest update no need to check Old demad here - 07-04-2025
                    $existingDemandData = $this->getExistingPropertyDemand($oldPropertyId, $flatId, false, false);
                    // dd($existingDemandData);
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
                    $settledAmounts = isset($amounts['DEM_SETTLED_AMOUNT']) ? $amounts['DEM_SETTLED_AMOUNT'] : false;
                    // dd($demand, $carriedAmount, $manualDemandAmounts);
                    unset($amounts['DEM_MANUAL']);
                    unset($amounts['DEM_SETTLED_AMOUNT']);

                    // $total = array_sum($amounts) + ($manualDemandAmounts !== false ? array_sum($manualDemandAmounts) : 0);

                    $total = array_sum($amounts)
                        + ($manualDemandAmounts !== false ? array_sum($manualDemandAmounts) : 0)
                        + ($settledAmounts !== false ? array_sum($settledAmounts) : 0);


                    if ($manualDemandAmounts !== false)
                        $amounts['DEM_MANUAL'] = $manualDemandAmounts;

                    if ($settledAmounts !== false) {
                        $amounts['DEM_SETTLED_AMOUNT'] = $settledAmounts;
                    }
                    // dd($total, array_sum($amounts), array_sum($manual));
                }
                if (!isset($amounts)) {
                    return json_encode(['status' => false, 'details' => "Can not create a demand. Invalid demand amount", 'data' => 0]);
                }
                foreach ($amounts as $k => $am) {
                    if (!(array_key_exists($k, $request->all()) || in_array($k, ["DEM_MANUAL", "DEM_SETTLED_AMOUNT"]))) {
                        unset($amounts[$k]);
                    }
                }

                if ($total == 0) {
                    $response = ['status' => false, 'details' => "Can not create a demand. Invalid demand amount", 'data' => 0];
                    return json_encode($response);
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


                //For linking the demand with application if application available - SOURAV CHAUHAN (20 Feb 2026)
                $propertyId = $oldPropertyId ?? $demand->old_property_id;
                $applicationNo = $request->application_no ?? null;
                if (!$request->application_no) {
                    $statusIds = [1487, 1488];
                    $models = [
                        ConversionApplication::class,
                        DeedOfApartmentApplication::class,
                        LandUseChangeApplication::class,
                        MutationApplication::class,
                        NocApplication::class,
                    ];

                    $applicationNew = null;
                    //For attaching the application with flats if available else with the property - SOURAV CHAUHAN (01 June 2026)
                    foreach ($models as $model) {
                        $query = $model::whereNotIn('status', $statusIds);

                        if (!empty($flatId)) {
                            // For flats
                            $query->where('flat_id', $flatId);
                        } else {
                            // For properties
                            $query->where('old_property_id', $propertyId);
                        }
                        $applicationNew = $query->latest()->first();
                        if ($applicationNew) {
                            break;
                        }
                    }
                    // foreach ($models as $model) {
                    //     $applicationNew = $model::where('old_property_id', $propertyId)->whereNotIn('status', $statusIds)->first();
                    //     if ($applicationNew) {
                    //         break;
                    //     }
                    // }

                    $applicationNo = $applicationNew['application_no'] ?? '';
                }
                $demandData = [
                    'property_master_id' => $existingDemandData['propertyMasterId'] ?? $demand->property_master_id,
                    'splited_property_detail_id' =>  $existingDemandData['childId'] ?? $demand->splited_property_detail_id ?? null,
                    'flat_id' => $flatId, //will be changed later
                    'old_property_id' => $propertyId,
                    'app_no' => $applicationNo,
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
                    'updated_by' => Auth::id(),
                    'approved_at' => now()
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
                                        'flat_id' => $flatId, //will be changed later
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

                    /** If previous demand for the application */
                    if (isset($request->oldDemandId) && count($request->oldDemandId) > 0) {
                        foreach ($request->oldDemandId as $oldDemandIndex => $oldDemandId) {
                            $oldDemandCheck = OldDemand::where('demand_id', $oldDemandId)->first();
                            if ($oldDemandCheck) {
                                if ($oldDemandCheck['outstanding'] != '0' || $oldDemandCheck['outstanding'] != '0.0' || $oldDemandCheck['outstanding'] != '0.00') {
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

                                                DemandDetail::create([
                                                    'demand_id' => $newDemandId,
                                                    'property_master_id' => $newDemand->property_master_id,
                                                    'splited_property_detail_id' => $newDemand->splited_property_detail_id,
                                                    'flat_id' => $flatId, //will be changed later
                                                    'subhead_id' => getServiceType('DEM_PREV_EXTRA_AMT'),
                                                    'total' => $subheadAmount,
                                                    'net_total' => $subheadAmount,
                                                    'paid_amount' => null,
                                                    'balance_amount' => $subheadAmount,
                                                    'carried_amount' => null,
                                                    /* 'duration_from' =>  $request->duration_from[$i],
                                                                            duration_to' => $request->duration_to[$i], */
                                                    'fy' => $fy,
                                                    'remarks' => "Extra amount paid by aplicant in previous demand" . (count($request->oldDemandId) > 1 ? 's -' : ' -') . (implode(', ', $request->oldDemandId)),
                                                    'created_by' => Auth::id(),
                                                    'updated_by' => Auth::id()
                                                ]);
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
                            }
                        }
                        DemandDetail::where('demand_id', $newDemandId)->where('subhead_id', getServiceType("PREV_DUE"))->delete();
                        if ($previousDues > 0) {
                            $demandDetail = DemandDetail::create([
                                'demand_id' => $newDemandId,
                                'property_master_id' => $newDemand->property_master_id,
                                'splited_property_detail_id' => $newDemand->splited_property_detail_id,
                                'flat_id' => $flatId, //will be changed later
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
                    // dd($requestSubheads);
                    foreach ($subheadKeysConfig as $subheadCode => $inputs) {
                        if (!in_array($subheadCode, $requestSubheads)) continue;

                        // $isManual = $subheadCode === 'DEM_MANUAL';
                        $isManual = in_array($subheadCode, [
                            'DEM_MANUAL',
                            'DEM_SETTLED_AMOUNT',
                            'DEM_ENCH_CHG'
                        ]);
                        if ($subheadCode == "DEM_ENCH_CHG") {
                            $amounts[$subheadCode] = $request->ench_amount;
                        }
                        // $rows = $isManual ? ($amounts['DEM_MANUAL'] ?? []) : [$amounts[$subheadCode]];
                        $rows = $isManual
                            ? ($amounts[$subheadCode] ?? [])
                            : [$amounts[$subheadCode]];
                        // dd($amounts, $rows);



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
                    $noDemandHeads = $subheadKeysConfig['no_demand_head'];
                    // dd($singlePropertyCheck);
                    /*  $key = $singlePropertyCheck['key'];
                        // if (in_array($key, array_keys((array) $request)) && ($request->{$key} == 0 || $request->{$key} == 1)) {
                        if (isset($request->{$key}) && ($request->{$key} == 0 || $request->{$key} == 1)) {
                            DemandHeadKey::create([
                                'demand_id' => $newDemandId,
                                'key' => $key,
                                'value' => $request->{$key}
                            ]);
                            unset($subheadKeysConfig['no_demand_head']);
                        } */
                    // dd($request->all());
                    // $counter = 0;
                    foreach ($noDemandHeads as $key => $head) {
                        // ++$counter;
                        if (in_array($head['key'], array_keys((array) $request->all())) && ($request->{$head['key']} != '')) {
                            // echo ('in request==' . $head['key'] . '...' . $counter . '&val=' . $request->{$head['key']} . '<br>');
                            $value = $request->input($head['key']);

                            if ($value !== "" && $value !== null) {
                                DemandHeadKey::create([
                                    'demand_id' => $newDemandId,
                                    'key' => $head['key'],
                                    'value' => $value
                                ]);
                            }
                        } /* else {
                                echo (' not in request--->' . $head['key'] . '%%' . $counter . '<br>');
                            } */
                    }
                    // dd('loop completed');
                    unset($subheadKeysConfig['no_demand_head']);

                    foreach ($subheadKeysConfig as $subheadCode => $inputs) {
                        if (!in_array($subheadCode, $requestSubheads)) continue;

                        /*  $isManual = $subheadCode === 'DEM_MANUAL';
                            $rows = $isManual ? ($amounts['DEM_MANUAL'] ?? []) : [$amounts[$subheadCode]]; */

                        $isManual = in_array($subheadCode, [
                            'DEM_MANUAL',
                            'DEM_SETTLED_AMOUNT',
                            'DEM_ENCH_CHG'
                        ]);
                        if ($subheadCode == "DEM_ENCH_CHG") {
                            $amounts[$subheadCode] = $request->ench_amount;
                        }
                        $rows = $isManual
                            ? ($amounts[$subheadCode] ?? [])
                            : [$amounts[$subheadCode]];

                        foreach ($rows as $ind => $amount) {
                            $demandDetail = DemandDetail::updateOrCreate([
                                'id' => $isManual
                                    ? ($request->detail_id[$subheadCode][$ind] ?? 0)
                                    : ($request->detail_id[$subheadCode] ?? 0),
                            ], [
                                'demand_id' => $newDemandId,
                                'property_master_id' => $newDemand->property_master_id,
                                'splited_property_detail_id' => $newDemand->splited_property_detail_id,
                                'flat_id' => $flatId,
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

                    if (!isset($request->id)) {
                        /** check active applications */
                        $activeApplication = $this->getActiveApplicationData($oldPropertyId, true);
                        if (count($activeApplication) > 0) {
                            $app = $activeApplication[0];
                            $req = [
                                'forwardTo' => 'deputy-lndo',
                                'forwardRemark' => 'Created demand for application. Please approve the demand',
                                'serviceType' => getServiceCodeById($app->service_type),
                                'modalId' => $app->model_id,
                                'applicantNo' => $app->application_no,
                                'isNewDemand' => 1
                            ];
                            $service = new ApplicationForwardService();
                            $response = $service->forward($req);
                            $statusCode = $response['code'];
                            if ($statusCode == 500) {
                                return response()->json(['status' => false, 'details' => 'Application can not be forwarded']);
                            }
                        }
                    }

                    $this->updatePropertyContactDetailsDuringDemand($request);


                    return response()->json(['status' => true, 'message' => 'Demand created successfully']);
                } else {
                    return response()->json(['status' => false, 'details' => 'Demand not created.']);
                }
            } else {
                return response()->json(['status' => false, 'details' => config('messages.property.error.notFound')]);
            }
            $this->updatePropertyContactDetailsDuringDemand($request);
            return response()->json(['status' => true, 'message' => 'Demand created successfully']);
        });
        /* } catch (\Exception $e) {
            Log::info($e->getMessage());
            $response = ['status' => false, 'details' => $e->getMessage(), 'data' => 0];
            return json_encode($response);
        } */
    }



    public function updatePropertyContactDetailsDuringDemand($request)
    {
        // dd($request);
        if ($request->flat_id) {
            $flatContactDetails = Flat::find($request->flat_id);
            $flatContactDetails->present_occupant_name = $request->demand_to;
            $flatContactDetails->address = $request->demand_to_address;
            $flatContactDetails->email = $request->demand_to_email;
            $flatContactDetails->phone = $request->demand_to_phone;
            $flatContactDetails->save();
        } else {
            $propertyContactDetail = PropertyContactDetail::find($request->demandcontactRecordId);
            $propertyContactDetail->lessees_name = $request->demand_to;
            $propertyContactDetail->address = $request->demand_to_address;
            $propertyContactDetail->email = $request->demand_to_email;
            $propertyContactDetail->phone_no = $request->demand_to_phone;
            $propertyContactDetail->save();
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
    /*  public function ApproveDemand($demandId)
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
    } */

    public function ApproveDemand($demandId)
    {
        $communicationService = new CommunicationService;
        $demand = Demand::find($demandId);

        if ($demand->status == getServiceType('DEM_DRAFT')) {

            $email = null;
            $mobile = null;
            $action = 'DEMAND_GEN';
            $address = null;
            $name = 'Sir/Madam';
            $flat = null;

            $propertyMaster = PropertyMaster::find($demand->property_master_id);
            $blockNo = $propertyMaster->block_no ?? 'N/A';
            $plotNo = $propertyMaster->plot_or_property_no  ?? 'N/A';


            if ($demand->flat_id) {
                $flat = Flat::find($demand->flat_id);
                $name = $flat->present_occupant_name ?? "Sir/Madam";
                $address = $flat->address ?? 'N/A';
                $email = $flat->email ?? null;
                $mobile = $flat->phone ?? null;
                $uniquePropertyId = $flat->unique_flat_id ?? 'N/A';
            } else if (is_null($demand->splited_property_detail_id)) {
                // Non-split property

                $contactDetails = PropertyContactDetail::where('property_master_id', $demand->property_master_id)->first();
                if ($contactDetails) {
                    $name = $contactDetails->lessees_name ?? "Sir/Madam";
                    $email = $contactDetails->email ?? null;
                    $mobile = $contactDetails->phone_no ?? null;
                    $address = $contactDetails->address ?? null;
                } else {
                    $userProperty = UserProperty::where('new_property_id', $demand->property_master_id)->first();
                    if ($userProperty) {
                        $user = User::find($userProperty->user_id);
                        $name = $user->name;
                        $email = $user->email;
                        $mobile = $user->mobile_no;
                        $leaseDetail = PropertyLeaseDetail::where('property_master_id', $demand->property_master_id)->first();
                        $address = $leaseDetail?->presently_known_as ?? 'N/A';
                    }
                }


                $getUniquePropertyId = PropertyMaster::where('id', $demand->property_master_id)->first();
                $uniquePropertyId = $getUniquePropertyId?->unique_propert_id ?? 'N/A';
            } else {
                // Split property
                $splitId = $demand->splited_property_detail_id;

                $contactDetails = PropertyContactDetail::where('property_master_id', $demand->property_master_id)->where('splited_property_detail_id', $splitId)->first();
                if ($contactDetails) {
                    $name = $contactDetails->lessees_name ?? "Sir/Madam";
                    $email = $contactDetails->email ?? null;
                    $mobile = $contactDetails->phone_no ?? null;
                    $address = $contactDetails->address ?? null;
                } else {
                    $userProperty = UserProperty::where('splitted_property_id', $splitId)->first();
                    if ($userProperty) {
                        $user = User::find($userProperty->user_id);
                        $name = $user->name;
                        $email = $user->email;
                        $mobile = $user->mobile_no;
                        $splitDetail = SplitedPropertyDetail::find($splitId);
                        $address = $splitDetail?->presently_known_as ?? 'N/A';
                    }
                }
                $splitDetail = SplitedPropertyDetail::find($splitId);






                // $userProperty = UserProperty::where('splitted_property_id', $splitId)->first();

                // if ($userProperty) {
                //     $user = User::find($userProperty->user_id);
                //     $name = $user->name;
                //     $email = $user->email;
                //     $mobile = $user->mobile_no;
                // } else {
                //     $contactDetails = CurrentLesseeDetail::where('property_master_id', $demand->property_master_id)->get();
                //     $matchedContact = $contactDetails->firstWhere('splited_property_detail_id', $splitId);
                //     $name = $matchedContact->lessees_name ?? "Sir/Madam";

                //     $contactDetails = PropertyContactDetail::where('property_master_id', $demand->property_master_id)->get();
                //     $matchedContact = $contactDetails->firstWhere('splited_property_detail_id', $splitId);
                //     if ($matchedContact) {
                //         $email = $matchedContact->email ?? null;
                //         $mobile = $matchedContact->phone_no ?? null;
                //     }
                // }

                if ($splitDetail) {
                    $plotNo = $splitDetail->plot_flat_no ?? $plotNo;
                }
                $uniquePropertyId = $splitDetail?->child_prop_id ?? 'N/A';
            }

            // Get approval info
            $approvedBy = 'N/A';
            $approvedByDesignation = 'N/A';

            $propertyMaster = PropertyMaster::find($demand->property_master_id);
            $splittedProperty = !is_null($demand->splited_property_detail_id) ? SplitedPropertyDetail::find($demand->splited_property_detail_id) : null;
            // dd($propertyMaster);
            if ($propertyMaster) {
                $section = Section::where('section_code', $propertyMaster->section_code)->first();
                if ($section) {
                    $designation = Designation::where('name', 'DEPUTY L&amp;DO')->first();
                    if ($designation) {
                        $sectionUser = DB::table('section_user')
                            ->where('section_id', $section->id)
                            ->where('designation_id', $designation->id)
                            ->first();
                        if ($sectionUser) {
                            $approvedUser = User::find($sectionUser->user_id);
                            if ($approvedUser) {
                                $approvedBy = $approvedUser->name;
                                $approvedByDesignation = $designation->name == 'DEPUTY L&DO' ? 'DEPUTY L&amp;DO' : $designation->name;
                            }
                        }
                    }
                }
            }
            // dd($demand);
            // dd([
            //     'app_no_on_demand' => $demand->app_no,
            //     'application_model' => $demand->application,
            // ]);

            // dd($demand->application);
            $application_name = getServiceNameById($demand->application?->service_type);
            $application_no = $demand->application?->application_no;
            $application_date = $demand->application?->created_at;
            // dd($application_name);

            // Prepare notification data
            $notificationData = [
                'demand_id' => $demand->unique_id,
                'amount' => $demand->net_total,
                'amountinwords' => ucfirst(convertNumberToWords($demand->net_total)),
                'property_id' => $demand->splited_property_detail_id !== null ? $demand->splited_property_detail_id : $demand->old_property_id,
                'demand_date' => now()->format('d-m-Y'),
                'name' => $name,
                'address' => $address,
                'approvedBy' => $approvedBy,
                'approvedByDesignation' => $approvedByDesignation,
                'blockNo' => $blockNo,
                'plotNo' => $flat ? ($plotNo . "(Flat No: " . $flat->flat_number . ")") : $plotNo,
                'applicationName' => $application_name,
                'applicationNo' => $application_no,
            ];

            // $email = 'nitinrag@gmail.com'; // override for testing
            // $email = 'swati180296@gmail.com';

            if ($email || $mobile) {

                // dd($demand);
                $demand->update([
                    'status' => getServiceType('DEM_PENDING'),
                    'updated_by' => Auth::id(),
                    'approved_at' => date('Y-m-d H:i:s')
                ]);

                // Fetch related data
                $demandDetails = DemandDetail::where('demand_id', $demand->id)->get();
                $items = Item::where('group_id', 7003)
                    ->whereIn('id', $demandDetails->pluck('subhead_id'))
                    ->pluck('item_name', 'id');
                $formulas = DemandFormula::whereIn('id', $demandDetails->pluck('formula_id')->filter())
                    ->pluck('formula', 'id');

                // Generate PDF
                $pdf = Pdf::loadView('demand.demand_letter_pdf', [
                    'demand' => $demand,
                    'name' => $name,
                    'approvedBy' => $approvedBy,
                    'approvedByDesignation' => $approvedByDesignation,
                    'applicationNo' => $application_no,
                    'demandDetails' => $demandDetails,
                    'items' => $items,
                    'formulas' => $formulas,
                    'address' => $address,
                    'applicationDate' => $application_date,
                    'uniquePropertyId' => $uniquePropertyId,
                    'splittedProperty' => $splittedProperty,
                    'propertyMaster' => $propertyMaster,
                    'flat' => $flat,
                ]);

                $timestamp = now()->setTimezone('Asia/Kolkata')->format('d-m-Y_H-i-s');
                $filename = "{$demand->unique_id}_{$timestamp}.pdf";
                $path = "Demand/{$filename}";

                Storage::put("public/{$path}", $pdf->output());
                $attachment = "storage/public/{$path}";

                $demand->update([
                    'demand_path' => $path,
                ]);
                // dd($path, $attachment);
                // $this->settingsService->applyMailSettings($action);

                if ($email) {
                    // try {
                    //     Mail::to($email)->send(new CommonMail($notificationData, $action));
                    // } catch (\Exception $e) {
                    //     Log::error("Failed to send demand email: " . $e->getMessage());
                    // }
                    try {
                        $mailSettings = app(SettingsService::class)->getMailSettings($action);
                        $mailer = new \App\Mail\CommonPHPMail($notificationData, $action, $communicationTrackingId ?? null, $attachment);
                        $mailResponse = $mailer->send($email, $mailSettings);

                        Log::info("Email sent successfully.", [
                            'action' => $action,
                            'email'  => $email,
                            'data'   => $notificationData,
                        ]);
                    } catch (\Exception $e) {
                        Log::error("Email sending failed.", [
                            'action' => $action,
                            'email'  => $email,
                            'error'  => $e->getMessage(),
                        ]);
                    }
                }

                if ($mobile) {
                    // try {
                    //     $this->communicationService->sendSmsMessage($notificationData, $mobile, $action);
                    // } catch (\Exception $e) {
                    //     Log::error("Failed to send demand SMS: " . $e->getMessage());
                    // }
                    try {
                        $isSmsSuccess = $communicationService->sendSmsMessage(
                            $notificationData,
                            $mobile,
                            $action
                        );

                        if ($isSmsSuccess) {
                            Log::info("SMS sent successfully.", [
                                'mobile'      => $mobile,
                                // 'countryCode' => $appointment->country_code,
                                'action'      => $action,
                            ]);
                        } else {
                            Log::warning("SMS sending failed.", [
                                'mobile'      => $mobile,
                                // 'countryCode' => $appointment->country_code,
                                'action'      => $action,
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error("SMS sending threw exception.", [
                            'mobile'      => $mobile,
                            // 'countryCode' => $appointment->country_code,
                            'action'      => $action,
                            'error'       => $e->getMessage(),
                        ]);
                    }

                    // try {
                    //     $this->communicationService->sendWhatsAppMessage($notificationData, $mobile, $action);
                    // } catch (\Exception $e) {
                    //     Log::error("Failed to send demand WhatsApp: " . $e->getMessage());
                    // }

                    // --- WHATSAPP ---
                    try {
                        $isWhatsAppSuccess = $communicationService->sendWhatsAppMessage(
                            $notificationData,
                            $mobile,
                            $action
                        );

                        if ($isWhatsAppSuccess) {
                            Log::info("WhatsApp sent successfully.", [
                                'mobile'      => $mobile,
                                // 'countryCode' => $appointment->country_code,
                                'action'      => $action,
                            ]);
                        } else {
                            Log::warning("WhatsApp sending failed.", [
                                'mobile'      => $mobile,
                                // 'countryCode' => $appointment->country_code,
                                'action'      => $action,
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error("WhatsApp sending threw exception.", [
                            'mobile'      => $mobile,
                            // 'countryCode' => $appointment->country_code,
                            'action'      => $action,
                            'error'       => $e->getMessage(),
                        ]);
                    }
                }

                /** revert application if demand was created for demand Id */
                $activeApplication = $this->getActiveApplicationData($demand->old_property_id, true);
                if (count($activeApplication) > 0) {
                    $app = $activeApplication[0];
                    $req = [
                        'revertRemark' => 'required|string',
                        'serviceType' => getServiceCodeById($app->service_type),
                        'modalId' => $app->model_id,
                        'applicantNo' => $app->application_no,
                    ];
                    $service = new ApplicationForwardService();
                    $response = $service->revert($req);
                    $statusCode = $response['code'];
                    if ($statusCode == 500) {
                        return response()->json(['status' => false, 'details' => 'Application can not be forwarded']);
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


    // public function withdrawDemand($demandId)
    // {
    //     $demand = Demand::find($demandId);
    //     if (empty($demand)) {
    //         return redirect()->back()->with('failure', "No data found!!");
    //     }
    //     if ($demand->carried_amount && $demand->carried_amount > 0) {
    //         $carriedDetails = CarriedDemandDetail::where('new_demand_id', $demandId)->first();
    //         if (!empty($carriedDetails)) {
    //             $oldDemand = Demand::find($carriedDetails->old_demand_id);
    //             if (!empty($oldDemand)) {
    //                 $statusToUpdate = getServiceType('DEM_PART_PAID');
    //                 if ($oldDemand->net_total == $oldDemand->balance_amount) {
    //                     $statusToUpdate = getServiceType('DEM_PENDING');
    //                 }
    //                 $oldDemand->update(['status' => $statusToUpdate, 'updated_by' => Auth::id()]);
    //             } else {
    //                 return redirect()->back()->with('failure', "Something went wrong. Required data is missing");
    //             }
    //         } else {
    //             return redirect()->back()->with('failure', "Something went wrong. Required data is missing");
    //         }
    //     }
    //     $demand->update(['status' => getServiceType('DEM_WD'), 'updated_by' => Auth::id()]);
    //     return redirect()->back()->with('success', "Demand withdrawn successfully");
    // }
    public function withdrawDemand($demandId)
    {
        $result = $this->demandService->withdrawDemand($demandId, Auth::id(), false);
        if ($result['status']) {
            return redirect()->back()->with('success', $result['message']);
        }
        return redirect()->back()->with('failure', $result['message']);
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

    // public function applicantPayForDemand($demandId)
    // {
    //     $demand = Demand::find($demandId);
    //     if (empty($demand)) {
    //         return redirect()->back()->with('failure', "No data found!!");
    //     }
    //     if (in_array(getServiceCodeById($demand->status), ["DEM_PART_PAID", "DEM_PENDING"])) {

    //         $addressDropdownData = getAddressDropdownData();

    //         return view('demand.applicant.payment-form', ['demand' => $demand, ...$addressDropdownData]);
    //     } else {
    //         return redirect()->back()->with('failure', "Can not make payment for this demand");
    //     }
    // }
    public function applicantPayForDemand($demandId)
    {
        $demand = Demand::find($demandId);
        $olddemand = 0;
        if (empty($demand)) {
            return redirect()->back()->with('failure', "No data found!!");
        }
        if (in_array(getServiceCodeById($demand->status), ["DEM_PART_PAID", "DEM_PENDING"])) {

            $addressDropdownData = getAddressDropdownData();

            return view('demand.applicant.payment-form', ['olddemand' => $olddemand, 'demand' => $demand, ...$addressDropdownData]);
        } else {
            return redirect()->back()->with('failure', "Can not make payment for this demand");
        }
    }

    public function applicantDemandPayment(Request $request, PaymentService $paymentService)
    {
        $modelName = $modelId = $applicationNo = '';
        if (!empty($request->demand_id)) {
            $demandId = $request->demand_id;
            $demand = Demand::find($demandId);
            if (empty($demand)) {
                return redirect()->back()->with('faliure', 'Given demand not found');
            }
            //If demand payment done for application (24/Feb/2026)
            if (!empty($demand->app_no)) {
                $getApplicationDetails = Application::where('application_no', $demand->app_no)->first();
                $modelName = $getApplicationDetails->model_name;
                $modelId = $getApplicationDetails->model_id;
                $applicationNo = $demand->app_no;
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
            'application_no' => $applicationNo ?? '',
            'model' => $modelName ?? '',
            'model_id' => $modelId ? (int)$modelId : 0,
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
                'order_content' => '15777',
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


    private function getActiveApplicationData($oldPropertyId, $excludeObjected = false)
    {
        $statusArray = [
            getServiceType('APP_NEW'),
            getServiceType('APP_PEN'),
            getServiceType('APP_IP')
        ];
        if (!$excludeObjected) {
            array_push($statusArray, getServiceType('APP_OBJ'));
        }
        $data =  DB::table('applications')
            ->whereIn('applications.status', $statusArray)
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

    // public function demandSummary(Request $request)
    // {
    //     $user = Auth::user();
    //     $sections = $user->hasAnyRole(['super-admin', 'lndo', 'minister']) ? getRequiredSections() : $user->sections;
    //     $filters = $request->all(); 
    //     $filterSectionIds = [];

    //     if (isset($filters['section_id'])) {
    //         $filterSectionIds = [$filters['section_id']];
    //     } else if (!$user->hasAnyRole(['super-admin', 'lndo', 'minister'])) {
    //         $filterSectionIds = $user->sections->pluck('id')->toArray();
    //     }       

    //     $deputlndousers = User::role('deputy-lndo')->with(['sections', 'designation'])->get();

    //     $filterDateFrom = $filters['startDate'] ?? null;
    //     $filterDateTo = $filters['endDate'] ?? null;
    //     $demandType = $filters['demandType'] ?? 0;

    //     $sectionCodes = [];
    //     $selectedId = 0;
    //     $deputyUser = null;

    //     if (!empty($filters['filterType'])) {
    //         if ($filters['filterType'] === 'dyLDoWise') {
    //             $selectedId = $filters['selectedId'] ?? 0;
    //             $deputyUser = User::with('sections')->find($selectedId);
    //             if ($deputyUser) {
    //                 $sectionCodes = $deputyUser->sections->pluck('section_code')->toArray();
    //             }
    //         } elseif ($filters['filterType'] === 'sectionWise') {
    //             $selectedId = $filters['selectedId'] ?? 0;
    //             $sectionCodes = Section::where('id', $selectedId)->pluck('section_code')->toArray(); 
    //         } elseif ($filters['filterType'] === 'yearWise') {
    //             $selectedId = $filters['selectedId'] ?? 0;
    //         }
    //     }

    //     $filtertype = $filters['filterType'] ?? 0; 
    //     if ($demandType == 0) {
    //         $latestDemandsSub = Demand::query()
    //             ->whereNotIn('status', [getServiceType('DEM_DRAFT'), getServiceType('DEM_WD')])
    //             ->selectRaw('MAX(id) as latest_id')
    //             ->groupBy('property_master_id');
    //     } else {
    //         // When demandType != 0, just use a normal Demand query without groupBy or MAX id
    //         $latestDemandsSub = Demand::query()
    //             ->whereNotIn('status', [getServiceType('DEM_DRAFT'), getServiceType('DEM_WD')]);
    //     }

    //     // Build main query depending on demandType (joinSub only if demandType==0)
    //     $query = Demand::query()
    //         ->join('property_masters', 'demands.property_master_id', '=', 'property_masters.id')
    //         ->when(!empty($sectionCodes), function ($query) use ($sectionCodes) {
    //             return $query->whereIn('property_masters.section_code', $sectionCodes);
    //         })
    //         ->when($filterDateFrom, function ($query) use ($filterDateFrom) {
    //             return $query->whereDate('demands.created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
    //         })
    //         ->when($filterDateTo, function ($query) use ($filterDateTo) {
    //             return $query->whereDate('demands.created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
    //         });

    //     if ($demandType == 0) {
    //         $query->joinSub($latestDemandsSub, 'latest_demands', function ($join) {
    //             $join->on('demands.id', '=', 'latest_demands.latest_id');
    //         });
    //     } else {        
    //         $query->whereNotIn('demands.status', [getServiceType('DEM_DRAFT'), getServiceType('DEM_WD')]);
    //     }
    //     $queryResult = $query->select('demands.*', 'property_masters.old_propert_id', 'property_masters.unique_propert_id', 'property_masters.unique_file_no')->get();

    //     $totalSummary = null;
    //     $dyLdoWiseSummary = [];
    //     $sectionWiseSummary = [];
    //     $dySectionWiseSummary = [];    
    //     if ($filtertype === 'dyLDoWise' && $deputyUser) {
    //         foreach ($deputyUser->sections as $section) {
    //             $sectionCode = $section->section_code;
    //             $secQuery = Demand::query()
    //                 ->join('property_masters', 'demands.property_master_id', '=', 'property_masters.id')
    //                 ->where('property_masters.section_code', $sectionCode)
    //                 ->when($filterDateFrom, function ($q) use ($filterDateFrom) {
    //                     return $q->whereDate('demands.created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
    //                 })
    //                 ->when($filterDateTo, function ($q) use ($filterDateTo) {
    //                     return $q->whereDate('demands.created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
    //                 });

    //             if ($demandType == 0) {
    //                 $secQuery->joinSub($latestDemandsSub, 'latest_demands', function ($join) {
    //                     $join->on('demands.id', '=', 'latest_demands.latest_id');
    //                 });
    //             } else {
    //                 $secQuery->whereNotIn('demands.status', [getServiceType('DEM_DRAFT'), getServiceType('DEM_WD')]);
    //             }

    //             $secSummary = $secQuery->selectRaw('
    //                 COUNT(demands.id) as total_demands,
    //                 SUM(demands.net_total) as total_amount,
    //                 SUM(demands.paid_amount) as total_paid,
    //                 SUM(demands.balance_amount) as total_balance
    //             ')->first();

    //             $dySectionWiseSummary[] = [
    //                 'section_name' => $section->name,
    //                 'section_code' => $sectionCode,
    //                 'total_demands' => $secSummary->total_demands ?? 0,
    //                 'total_amount' => $secSummary->total_amount ?? 0,
    //                 'total_paid' => $secSummary->total_paid ?? 0,
    //                 'total_balance' => $secSummary->total_balance ?? 0,
    //             ];
    //         }
    //     }
    //     if ($filtertype === 'yearWise') {
    //         $totalQuery = Demand::query()
    //             ->join('property_masters', 'demands.property_master_id', '=', 'property_masters.id')
    //             ->when($filterDateFrom, function ($query) use ($filterDateFrom) {
    //                 return $query->whereDate('demands.created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
    //             })
    //             ->when($filterDateTo, function ($query) use ($filterDateTo) {
    //                 return $query->whereDate('demands.created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
    //             });

    //         if ($demandType == 0) {
    //             $totalQuery->joinSub($latestDemandsSub, 'latest_demands', function ($join) {
    //                 $join->on('demands.id', '=', 'latest_demands.latest_id');
    //             });
    //         } else {
    //             $totalQuery->whereNotIn('demands.status', [getServiceType('DEM_DRAFT'), getServiceType('DEM_WD')]);
    //         }
    //         $totalSummary = $totalQuery->selectRaw('
    //             GROUP_CONCAT(DISTINCT property_masters.section_code ORDER BY property_masters.section_code) as section_codes,
    //             COUNT(demands.id) as total_demands,
    //             SUM(demands.net_total) as total_amount,
    //             SUM(demands.paid_amount) as total_paid,
    //             SUM(demands.balance_amount) as total_balance
    //         ')->first();
    //         foreach ($deputlndousers as $dyUser) {
    //             $userSections = $dyUser->sections->pluck('section_code')->toArray();
    //             if (empty($userSections)) continue;

    //             $dyQuery = Demand::query()
    //                 ->join('property_masters', 'demands.property_master_id', '=', 'property_masters.id')
    //                 ->whereIn('property_masters.section_code', $userSections)
    //                 ->when($filterDateFrom, function ($query) use ($filterDateFrom) {
    //                     return $query->whereDate('demands.created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
    //                 })
    //                 ->when($filterDateTo, function ($query) use ($filterDateTo) {
    //                     return $query->whereDate('demands.created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
    //                 });

    //             if ($demandType == 0) {
    //                 $dyQuery->joinSub($latestDemandsSub, 'latest_demands', function ($join) {
    //                     $join->on('demands.id', '=', 'latest_demands.latest_id');
    //                 });
    //             } else {
    //                 $dyQuery->whereNotIn('demands.status', [getServiceType('DEM_DRAFT'), getServiceType('DEM_WD')]);
    //             }

    //             $dySummary = $dyQuery->selectRaw('
    //                 COUNT(demands.id) as total_demands,
    //                 SUM(demands.net_total) as total_amount,
    //                 SUM(demands.paid_amount) as total_paid,
    //                 SUM(demands.balance_amount) as total_balance
    //             ')->first();

    //             $dyLdoWiseSummary[] = [
    //                 'dyassingsection' => implode(',', $userSections),
    //                 'user' => $dyUser->name,
    //                 'user_id' => $dyUser->id,
    //                 'designation' => $dyUser->designation->name ?? 'N/A',
    //                 'total_demands' => $dySummary->total_demands ?? 0,
    //                 'total_amount' => $dySummary->total_amount ?? 0,
    //                 'total_paid' => $dySummary->total_paid ?? 0,
    //                 'total_balance' => $dySummary->total_balance ?? 0,
    //             ];
    //         }
    //         $assignedSectionCodes = $deputlndousers
    //             ->pluck('sections')
    //             ->flatten()
    //             ->pluck('section_code')
    //             ->unique()
    //             ->toArray();

    //         foreach ($assignedSectionCodes as $sectionCode) {
    //             $section = Section::where('section_code', $sectionCode)->first();
    //             if (!$section) continue;
    //             $secQuery = Demand::query()
    //                 ->join('property_masters', 'demands.property_master_id', '=', 'property_masters.id')
    //                 ->where('property_masters.section_code', $sectionCode)
    //                 ->when($filterDateFrom, function ($q) use ($filterDateFrom) {
    //                     return $q->whereDate('demands.created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
    //                 })
    //                 ->when($filterDateTo, function ($q) use ($filterDateTo) {
    //                     return $q->whereDate('demands.created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
    //                 });

    //             if ($demandType == 0) {
    //                 $secQuery->joinSub($latestDemandsSub, 'latest_demands', function ($join) {
    //                     $join->on('demands.id', '=', 'latest_demands.latest_id');
    //                 });
    //             } else {
    //                 $secQuery->whereNotIn('demands.status', [getServiceType('DEM_DRAFT'), getServiceType('DEM_WD')]);
    //             }
    //             $secSummary = $secQuery->selectRaw('
    //                 COUNT(demands.id) as total_demands,
    //                 SUM(demands.net_total) as total_amount,
    //                 SUM(demands.paid_amount) as total_paid,
    //                 SUM(demands.balance_amount) as total_balance
    //             ')->first();

    //             $sectionWiseSummary[] = [
    //                 'section_name' => $section->name,
    //                 'section_code' => $section->section_code,
    //                 'total_demands' => $secSummary->total_demands ?? 0,
    //                 'total_amount' => $secSummary->total_amount ?? 0,
    //                 'total_paid' => $secSummary->total_paid ?? 0,
    //                 'total_balance' => $secSummary->total_balance ?? 0,
    //             ];
    //         }
    //     }
    //     $data['sections'] = $sections;
    //     $data['deputlndousers'] = $deputlndousers;
    //     $data['queryResult'] = $queryResult;
    //     $data['filtertype'] = $filtertype;
    //     $data['totalSummary'] = $totalSummary;
    //     $data['dyLdoWiseSummary'] = $dyLdoWiseSummary;
    //     $data['sectionWiseSummary'] = $sectionWiseSummary;
    //     $data['dySectionWiseSummary'] = $dySectionWiseSummary;
    //     $data['selectedDyUser'] = $deputyUser;
    //     $data['demandType'] = $demandType;
    //     $data['filterDateFrom']= $filterDateFrom;
    //     $data['filterDateTo']= $filterDateTo;
    //     if ($request->ajax()) {
    //         $html = view('include.partials.filtered_demand_data', $data)->render();
    //         return response()->json(['html' => $html]);
    //     }

    //     return empty($filters) 
    //         ? view('demand.demand-summary', $data) 
    //         : response()->json(['success' => true, 'data' => $data]);
    // }

    public function demandSummary(Request $request)
    {
        $user = Auth::user();
        //$sections = $user->hasAnyRole(['super-admin', 'lndo', 'minister']) ? getRequiredSections() : $user->sections;
        $sections = $user->hasAnyRole(['super-admin', 'lndo', 'minister'])
            ? Section::where('has_property', 1)->get()
            : $user->sections()->where('has_property', 1)->get();
        //dd(Section::where('has_property', 1)->get());
        $filters = $request->all();
        $filterSectionIds = [];

        if (isset($filters['section_id'])) {
            $filterSectionIds = [$filters['section_id']];
        } else if (!$user->hasAnyRole(['super-admin', 'lndo', 'minister'])) {
            $filterSectionIds = $user->sections->pluck('id')->toArray();
        }
        $deputlndousers = User::role('deputy-lndo')
            ->whereHas('sections', function ($query) {
                $query->where('has_property', 1);
            })
            ->with([
                'sections' => function ($query) {
                    $query->where('has_property', 1);
                },
                'designation'
            ])
            ->get();
        $filterDateFrom = $filters['startDate'] ?? null;
        $filterDateTo = $filters['endDate'] ?? null;
        $demandType = $filters['demandType'] ?? 0;

        $sectionCodes = [];
        $selectedId = 0;
        $deputyUser = null;

        if (!empty($filters['filterType'])) {
            if ($filters['filterType'] === 'dyLDoWise') {
                $selectedId = $filters['selectedId'] ?? 0;
                $deputyUser = User::with(['sections' => function ($q) {
                    $q->where('has_property', 1);
                }])->find($selectedId);
                $sectionCodes = [];
                if ($deputyUser) {
                    // Collection now already only has sections with has_property = 1
                    $sectionCodes = $deputyUser->sections->pluck('section_code')->toArray();
                }
            } elseif ($filters['filterType'] === 'sectionWise') {
                $selectedId = $filters['selectedId'] ?? 0;
                $sectionCodes = Section::where('id', $selectedId)->pluck('section_code')->toArray();
            } elseif ($filters['filterType'] === 'yearWise') {
                $selectedId = $filters['selectedId'] ?? 0;
            }
        }

        $filtertype = $filters['filterType'] ?? 0;
        if ($demandType == 0) {
            $latestDemandsSub = Demand::query()
                ->whereNotIn('status', [getServiceType('DEM_DRAFT'), getServiceType('DEM_WD')])
                ->selectRaw('MAX(id) as latest_id')
                ->groupBy('property_master_id');
        } else {
            // When demandType != 0, just use a normal Demand query without groupBy or MAX id
            $latestDemandsSub = Demand::query()
                ->whereNotIn('status', [getServiceType('DEM_DRAFT'), getServiceType('DEM_WD')]);
        }

        // Build main query depending on demandType (joinSub only if demandType==0)
        $query = Demand::query()
            ->join('property_masters', 'demands.property_master_id', '=', 'property_masters.id')
            ->when(!empty($sectionCodes), function ($query) use ($sectionCodes) {
                return $query->whereIn('property_masters.section_code', $sectionCodes);
            })
            ->when($filterDateFrom, function ($query) use ($filterDateFrom) {
                return $query->whereDate('demands.created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
            })
            ->when($filterDateTo, function ($query) use ($filterDateTo) {
                return $query->whereDate('demands.created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
            });

        if ($demandType == 0) {
            $query->joinSub($latestDemandsSub, 'latest_demands', function ($join) {
                $join->on('demands.id', '=', 'latest_demands.latest_id');
            });
        } else {
            $query->whereNotIn('demands.status', [getServiceType('DEM_DRAFT'), getServiceType('DEM_WD')]);
        }
        $queryResult = $query->select('demands.*', 'property_masters.old_propert_id', 'property_masters.unique_propert_id', 'property_masters.section_code', 'property_masters.unique_file_no')->get();

        $totalSummary = null;
        $dyLdoWiseSummary = [];
        $sectionWiseSummary = [];
        $dySectionWiseSummary = [];
        if ($filtertype === 'dyLDoWise' && $deputyUser) {
            foreach ($deputyUser->sections as $section) {
                $sectionCode = $section->section_code;
                $secQuery = Demand::query()
                    ->join('property_masters', 'demands.property_master_id', '=', 'property_masters.id')
                    ->where('property_masters.section_code', $sectionCode)
                    ->when($filterDateFrom, function ($q) use ($filterDateFrom) {
                        return $q->whereDate('demands.created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
                    })
                    ->when($filterDateTo, function ($q) use ($filterDateTo) {
                        return $q->whereDate('demands.created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
                    });

                if ($demandType == 0) {
                    $secQuery->joinSub($latestDemandsSub, 'latest_demands', function ($join) {
                        $join->on('demands.id', '=', 'latest_demands.latest_id');
                    });
                } else {
                    $secQuery->whereNotIn('demands.status', [getServiceType('DEM_DRAFT'), getServiceType('DEM_WD')]);
                }

                $secSummary = $secQuery->selectRaw('
                COUNT(demands.id) as total_demands,
                SUM(demands.net_total) as total_amount,
                SUM(demands.paid_amount) as total_paid,
                SUM(demands.balance_amount) as total_balance
            ')->first();

                $dySectionWiseSummary[] = [
                    'section_name' => $section->name,
                    'section_code' => $sectionCode,
                    'total_demands' => $secSummary->total_demands ?? 0,
                    'total_amount' => $secSummary->total_amount ?? 0,
                    'total_paid' => $secSummary->total_paid ?? 0,
                    'total_balance' => $secSummary->total_balance ?? 0,
                ];
            }
        }
        if ($filtertype === 'yearWise') {
            $totalQuery = Demand::query()
                ->join('property_masters', 'demands.property_master_id', '=', 'property_masters.id')
                ->when($filterDateFrom, function ($query) use ($filterDateFrom) {
                    return $query->whereDate('demands.created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
                })
                ->when($filterDateTo, function ($query) use ($filterDateTo) {
                    return $query->whereDate('demands.created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
                });

            if ($demandType == 0) {
                $totalQuery->joinSub($latestDemandsSub, 'latest_demands', function ($join) {
                    $join->on('demands.id', '=', 'latest_demands.latest_id');
                });
            } else {
                $totalQuery->whereNotIn('demands.status', [getServiceType('DEM_DRAFT'), getServiceType('DEM_WD')]);
            }
            $totalSummary = $totalQuery->selectRaw('
            GROUP_CONCAT(DISTINCT property_masters.section_code ORDER BY property_masters.section_code) as section_codes,
            COUNT(demands.id) as total_demands,
            SUM(demands.net_total) as total_amount,
            SUM(demands.paid_amount) as total_paid,
            SUM(demands.balance_amount) as total_balance
        ')->first();

            foreach ($deputlndousers as $dyUser) {
                $userSections = $dyUser->sections->pluck('section_code')->toArray();
                if (empty($userSections)) continue;

                $dyQuery = Demand::query()
                    ->join('property_masters', 'demands.property_master_id', '=', 'property_masters.id')
                    ->whereIn('property_masters.section_code', $userSections)
                    ->when($filterDateFrom, function ($query) use ($filterDateFrom) {
                        return $query->whereDate('demands.created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
                    })
                    ->when($filterDateTo, function ($query) use ($filterDateTo) {
                        return $query->whereDate('demands.created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
                    });

                if ($demandType == 0) {
                    $dyQuery->joinSub($latestDemandsSub, 'latest_demands', function ($join) {
                        $join->on('demands.id', '=', 'latest_demands.latest_id');
                    });
                } else {
                    $dyQuery->whereNotIn('demands.status', [getServiceType('DEM_DRAFT'), getServiceType('DEM_WD')]);
                }

                $dySummary = $dyQuery->selectRaw('
                COUNT(demands.id) as total_demands,
                SUM(demands.net_total) as total_amount,
                SUM(demands.paid_amount) as total_paid,
                SUM(demands.balance_amount) as total_balance
            ')->first();

                $dyLdoWiseSummary[] = [
                    'dyassingsection' => implode(',', $userSections),
                    'user' => $dyUser->name,
                    'user_id' => $dyUser->id,
                    'designation' => $dyUser->designation->name ?? 'N/A',
                    'total_demands' => $dySummary->total_demands ?? 0,
                    'total_amount' => $dySummary->total_amount ?? 0,
                    'total_paid' => $dySummary->total_paid ?? 0,
                    'total_balance' => $dySummary->total_balance ?? 0,
                ];
            }
            $assignedSectionCodes = $deputlndousers
                ->pluck('sections')
                ->flatten()
                ->pluck('section_code')
                ->unique()
                ->toArray();
            //dd($assignedSectionCodes);
            foreach ($assignedSectionCodes as $sectionCode) {
                $section = Section::where('section_code', $sectionCode)->first();
                if (!$section) continue;
                $secQuery = Demand::query()
                    ->join('property_masters', 'demands.property_master_id', '=', 'property_masters.id')
                    ->where('property_masters.section_code', $sectionCode)
                    ->when($filterDateFrom, function ($q) use ($filterDateFrom) {
                        return $q->whereDate('demands.created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
                    })
                    ->when($filterDateTo, function ($q) use ($filterDateTo) {
                        return $q->whereDate('demands.created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
                    });

                if ($demandType == 0) {
                    $secQuery->joinSub($latestDemandsSub, 'latest_demands', function ($join) {
                        $join->on('demands.id', '=', 'latest_demands.latest_id');
                    });
                } else {
                    $secQuery->whereNotIn('demands.status', [getServiceType('DEM_DRAFT'), getServiceType('DEM_WD')]);
                }
                $secSummary = $secQuery->selectRaw('
                COUNT(demands.id) as total_demands,
                SUM(demands.net_total) as total_amount,
                SUM(demands.paid_amount) as total_paid,
                SUM(demands.balance_amount) as total_balance
            ')->first();

                $sectionWiseSummary[] = [
                    'section_name' => $section->name,
                    'section_code' => $section->section_code,
                    'total_demands' => $secSummary->total_demands ?? 0,
                    'total_amount' => $secSummary->total_amount ?? 0,
                    'total_paid' => $secSummary->total_paid ?? 0,
                    'total_balance' => $secSummary->total_balance ?? 0,
                ];
            }
        }
        $data['sections'] = $sections;
        $data['deputlndousers'] = $deputlndousers;
        $data['queryResult'] = $queryResult;
        $data['filtertype'] = $filtertype;
        $data['totalSummary'] = $totalSummary;
        $data['dyLdoWiseSummary'] = $dyLdoWiseSummary;
        $data['sectionWiseSummary'] = $sectionWiseSummary;
        $data['dySectionWiseSummary'] = $dySectionWiseSummary;
        $data['selectedDyUser'] = $deputyUser;
        $data['demandType'] = $demandType;
        $data['filterDateFrom'] = $filterDateFrom;
        $data['filterDateTo'] = $filterDateTo;
        if ($request->ajax()) {
            $html = view('include.partials.filtered_demand_data', $data)->render();
            return response()->json(['html' => $html]);
        }

        return empty($filters)
            ? view('demand.demand-summary', $data)
            : response()->json(['success' => true, 'data' => $data]);
    }


    public function demandSummaryDetails(Request $request)
    {
        //    $serviceType = $request->type;
        //    $filterDateFrom = $request->from;
        //    $filterDateTo = $request->to;
        //    $sectionCodes = explode(',', $request->service);
        if ($request->has('data')) {
            $decoded = base64_decode($request->get('data'));
            parse_str($decoded, $params);
            $serviceType = $params['type'] ?? 0;
            $filterDateFrom = $params['from'] ?? null;
            $filterDateTo = $params['to'] ?? null;
            $sectionCodes = isset($params['service']) ? explode(',', $params['service']) : [];
        } else {
            $serviceType = $request->type ?? 0;
            $filterDateFrom = $request->from ?? null;
            $filterDateTo = $request->to ?? null;
            $sectionCodes = $request->has('service') ? explode(',', $request->service) : [];
        }
        if ($serviceType == 0) {
            // Latest demands only
            $latestDemandsSub = Demand::query()
                ->whereNotIn('demands.status', [getServiceType('DEM_DRAFT'), getServiceType('DEM_WD')])
                ->selectRaw('MAX(id) as latest_id')
                ->groupBy('property_master_id');

            $queryResult = Demand::query()
                ->joinSub($latestDemandsSub, 'latest_demands', function ($join) {
                    $join->on('demands.id', '=', 'latest_demands.latest_id');
                })
                ->join('property_masters', 'demands.property_master_id', '=', 'property_masters.id')
                ->when(!empty($sectionCodes), function ($query) use ($sectionCodes) {
                    return $query->whereIn('property_masters.section_code', $sectionCodes);
                })
                ->when($filterDateFrom, function ($q) use ($filterDateFrom) {
                    return $q->whereDate('demands.created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
                })
                ->when($filterDateTo, function ($q) use ($filterDateTo) {
                    return $q->whereDate('demands.created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
                })
                ->select('demands.*', 'property_masters.old_propert_id', 'property_masters.unique_propert_id', 'property_masters.section_code', 'property_masters.unique_file_no')
                ->get();
        } else {
            $queryResult = Demand::query()
                ->whereNotIn('demands.status', [getServiceType('DEM_DRAFT'), getServiceType('DEM_WD')])
                ->join('property_masters', 'demands.property_master_id', '=', 'property_masters.id')
                ->when(!empty($sectionCodes), function ($query) use ($sectionCodes) {
                    return $query->whereIn('property_masters.section_code', $sectionCodes);
                })
                ->when($filterDateFrom, function ($q) use ($filterDateFrom) {
                    return $q->whereDate('demands.created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
                })
                ->when($filterDateTo, function ($q) use ($filterDateTo) {
                    return $q->whereDate('demands.created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
                })
                ->select('demands.*', 'property_masters.old_propert_id', 'property_masters.unique_propert_id', 'property_masters.unique_file_no')
                ->get();
        }

        $data['queryResult'] = $queryResult;

        return view('demand.demand-summary-details', $data);
    }
    public function getBreakup(Request $request)
    {
        $demand_id = $request->demand_id;
        $details = DemandDetail::select(
            'demand_details.*',
            'demands.unique_id as demand_unique_id',
            'property_masters.old_propert_id',
            'property_masters.unique_propert_id'
        )
            ->leftJoin('demands', 'demands.id', '=', 'demand_details.demand_id')
            ->join('property_masters', 'demands.property_master_id', '=', 'property_masters.id')
            ->where('demand_details.demand_id', $demand_id)
            ->get();
        $demandUniqueId = $details->first()?->demand_unique_id;
        $oldPropertyId  = $details->first()?->old_propert_id;
        $newPropertyId  = $details->first()?->unique_propert_id;
        return view('include.partials.demand_breakup', compact('details', 'demandUniqueId', 'oldPropertyId', 'newPropertyId'));
    }
    public function getolddemandbreakup(Request $request)
    {
        $demand_id = $request->demand_id;
        $paymentSub = DB::table('old_payments')
            ->select('DemandID', DB::raw('SUM(amount) as total_payment'))
            ->groupBy('DemandID');
        $details = OldDemandSubhead::select(
            'old_demand_subheads.DemandID',
            'old_demand_subheads.ComputerCode',
            'old_demand_subheads.Subhead',
            'old_demands.demand_date',
            'property_masters.old_propert_id',
            'property_masters.unique_propert_id',
            DB::raw("SUM(CASE WHEN old_demand_subheads.PaymentStatus = 'N' 
									            THEN old_demand_subheads.Amount ELSE 0 END) as demand_amount"),
            DB::raw("SUM(CASE WHEN old_demand_subheads.PaymentStatus = 'Y' 
									            THEN old_demand_subheads.Amount ELSE 0 END) as paid_amount"),
            DB::raw("COALESCE(p.total_payment,0) as total_payment")
        )
            ->leftJoin('old_demands', 'old_demands.demand_id', '=', 'old_demand_subheads.DemandID')
            ->join('property_masters', 'old_demands.property_id', '=', 'property_masters.old_propert_id')
            ->leftJoinSub($paymentSub, 'p', function ($join) {
                $join->on('p.DemandID', '=', 'old_demand_subheads.DemandID');
            })
            ->where('old_demand_subheads.DemandID', $demand_id)
            ->groupBy(
                'old_demand_subheads.DemandID',
                'old_demand_subheads.ComputerCode',
                'old_demand_subheads.Subhead',
                'p.total_payment',
                'old_demands.demand_date',
                'property_masters.old_propert_id',
                'property_masters.unique_propert_id'
            )
            ->get();
        $oldPropertyId  = $details->first()?->old_propert_id;
        $newPropertyId  = $details->first()?->unique_propert_id;
        //dd($newPropertyId);
        return view('include.partials.demand_oldbreakup', compact('details', 'oldPropertyId', 'newPropertyId'));
    }

    //updated by Swati on 22062026 for old demands
    public function oldDemandList()
    {
        // OldDemand::where('outstanding', '<=', 0)
        //     ->where(function ($query) {
        //         $query->where('email_sent_count', '>', 0)
        //             ->orWhereNotNull('last_reminder_sent_at');
        //     })
        //     ->update([
        //         'email_sent_count' => 0,
        //         'last_reminder_sent_at' => null,
        //     ]);

        [$filterUserSections, $userSectionIdList] = getUserAssignedSections();
        $user = Auth::user();

        if ($user->hasRole('assistant-section-officer')) {
            $filterUserSections = true;
        }

        $oldDemandQuery = OldDemand::orderBy('demand_date', 'desc');

        if ($filterUserSections) {
            $sectionCodes = Section::whereIn('id', $userSectionIdList)
                ->pluck('section_code')
                ->toArray();

            $oldDemandQuery->whereIn('section', $sectionCodes);
        }

        $oldDemands = $oldDemandQuery->get();

        $totalDemands = $oldDemands->count();

        $paidDemands = $oldDemands->where('outstanding', '<=', 0)->count();

        $pendingDemands = $oldDemands->where('outstanding', '>', 0)->count();

        $emailSentDemands = $oldDemands
            ->where('email_sent_count', '>', 0)
            ->count();

        return view('demand.old-demand-index', compact(
            'oldDemands',
            'totalDemands',
            'paidDemands',
            'pendingDemands',
            'emailSentDemands'
        ));
    }

    public function updateOldDemandSections()
    {
        OldDemand::whereNull('section')
            ->orderBy('id')
            ->chunkById(500, function ($oldDemands) {

                foreach ($oldDemands as $oldDemand) {

                    $section = getPropertySectionDetails($oldDemand->property_id);

                    if ($section) {
                        $oldDemand->section = $section['section_code'];
                        $oldDemand->save();
                    }
                }
            });

        return "Old demand sections updated successfully.";
    }

    private function getOldDemandContactDetails($oldDemand)
    {
        $propertyMasterService = new PropertyMasterService();
        $propertyData = $propertyMasterService->propertyFromSelected($oldDemand->property_id);

        if ($propertyData['status'] === 'error') {
            return [
                'status' => false,
                'message' => 'Property details not found.'
            ];
        }

        $propertyMaster = $propertyData['masterProperty'];
        $childProperty = $propertyData['childProperty'] ?? null;

        if (is_null($childProperty)) {
            $contactDetails = PropertyContactDetail::where('property_master_id', $propertyMaster->id)->first();
        } else {
            $contactDetails = PropertyContactDetail::where('property_master_id', $propertyMaster->id)
                ->where('splited_property_detail_id', $childProperty->id)
                ->first();
        }

        return [
            'status' => true,
            'propertyMaster' => $propertyMaster,
            'childProperty' => $childProperty,
            'contactDetails' => $contactDetails,
            'name' => $contactDetails->lessees_name ?? '',
            'email' => $contactDetails->email ?? '',
            'address' => $contactDetails->address ?? 'N/A',
        ];
    }

    public function oldDemandReminderDetails($demandId)
    {
        $oldDemand = OldDemand::where('demand_id', $demandId)->first();

        if (!$oldDemand) {
            return response()->json([
                'status' => false,
                'message' => 'Old demand not found.'
            ]);
        }

        if ($oldDemand->outstanding <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'This demand is already paid.'
            ]);
        }

        $contactData = $this->getOldDemandContactDetails($oldDemand);

        if (!$contactData['status']) {
            return response()->json($contactData);
        }

        return response()->json([
            'status' => true,
            'email_sent_count' => $oldDemand->email_sent_count ?? 0,
            'last_reminder_sent_at' => $oldDemand->last_reminder_sent_at
                ? Carbon::parse($oldDemand->last_reminder_sent_at)->format('d-m-Y h:i A')
                : 'N/A',
            'name' => $contactData['name'],
            'email' => $contactData['email'],
            'address' => $contactData['address'],
        ]);
    }

    public function sendOldDemandReminder(Request $request)
    {
        $request->validate([
            'demand_id' => 'required',
            'name' => 'nullable|string',
            'email' => 'required|email',
            'address' => 'nullable|string',
        ]);

        $oldDemand = OldDemand::where('demand_id', $request->demand_id)->first();

        if (!$oldDemand) {
            return response()->json([
                'status' => false,
                'message' => 'Old demand not found.'
            ]);
        }

        if ($oldDemand->outstanding <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'This demand is already paid.'
            ]);
        }

        $contactData = $this->getOldDemandContactDetails($oldDemand);

        if (!$contactData['status']) {
            return response()->json($contactData);
        }

        if (is_null($contactData['childProperty'])) {
            $contactDetails = PropertyContactDetail::firstOrNew([
                'property_master_id' => $contactData['propertyMaster']->id,
            ]);
        } else {
            $contactDetails = PropertyContactDetail::firstOrNew([
                'property_master_id' => $contactData['propertyMaster']->id,
                'splited_property_detail_id' => $contactData['childProperty']->id,
            ]);
        }

        $contactDetails->lessees_name = trim($request->name);
        $contactDetails->email = $request->email;
        $contactDetails->address = $request->address ?? 'N/A';
        $contactDetails->save();

        $action = 'OLD_DEM_REM';

        $notificationData = [
            'name' => $contactDetails->lessees_name,
            'property_id' => $oldDemand->property_id,
            'demand_id' => $oldDemand->demand_id,
            'demand_date' => !empty($oldDemand->demand_date)
                ? Carbon::parse($oldDemand->demand_date)->format('d-m-Y')
                : 'N/A',
            'outstanding' => $oldDemand->outstanding,
            'amount' => $oldDemand->outstanding,
            'amountinwords' => ucfirst(convertNumberToWords($oldDemand->outstanding)),
            'property_known_as' => $contactDetails->address,
        ];

        try {
            $mailSettings = app(SettingsService::class)->getMailSettings($action);

            $mailer = new \App\Mail\CommonPHPMail(
                $notificationData,
                $action,
                null,
                null
            );

            $mailer->send($contactDetails->email, $mailSettings);

            $oldDemand->email_sent_count += 1;
            $oldDemand->last_reminder_sent_at = now();
            $oldDemand->save();

            return response()->json([
                'status' => true,
                'message' => 'Email sent successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error("Old demand reminder email sending failed.", [
                'action' => $action,
                'email' => $contactDetails->email,
                'demand_id' => $oldDemand->demand_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Email sending failed.'
            ]);
        }
    }
}
