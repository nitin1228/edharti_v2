<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralFunctions;
use App\Http\Controllers\Controller;
use App\Models\ApplicantUserDetail;
use App\Models\Application;
use App\Models\ApplicationAppointmentLink;
use App\Models\Holiday;
use App\Models\NewlyAddedProperty;
use App\Models\OldColony;
use App\Models\PropertyMaster;
use App\Models\SplitedPropertyDetail;
use App\Models\PropertySectionMapping;
use App\Models\User;
use App\Models\UserProperty;
use App\Models\PropertyLeaseDetail;
use App\Models\TempDocument;
use App\Models\TempDocumentKey;
use App\Services\ColonyService;
use App\Services\MisService;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Mail;
use App\Mail\CommonMail;
use App\Services\CommunicationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use App\Models\InspectionRequest;
use App\Services\CommonService;
use App\Models\InspectionMovement;
use DB;
use App\Models\InspectionReport;
use App\Models\InspectionReportBreach;
use App\Models\InspectionReportUnauthorisedDetail;


class InspectionController extends Controller
{
    protected $communicationService;
    protected $settingsService;

    public function __construct(CommunicationService $communicationService, SettingsService $settingsService)
    {
        $this->communicationService = $communicationService;
        $this->settingsService = $settingsService;
    }
    public function inspectionRequest()
    {   if (Auth::user()->hasRole(['it-cell', 'technical-section'])) {
            $skipAccessCheck = 0;
        } else {
            $skipAccessCheck = 1;
        }
        return view('inspection.inspectionrequest',compact('skipAccessCheck'));
    }

// 		public function inspectionList(Request $request)
// 		{			//$userName = Auth::user()->name;
// 			$startDate = isset($request->start_date) && !empty($request->start_date) ? $request->start_date : null;
//        		$endDate = isset($request->end_date) && !empty($request->end_date) ? $request->end_date : null;  
//        		$inspectionid = isset($request->inspectionid) && !empty($request->inspectionid) ? $request->inspectionid : null;       		
// 		    $sectionCodes = Auth::user()->sections()->pluck('section_code')->toArray();
// 			$inspectionRequests = InspectionRequest::query()
// 								    ->leftJoin('current_lessee_details as cld', 'cld.old_property_id', '=', 'inspection_requests.property_id')
// 								    ->select('inspection_requests.*', 'cld.property_known_as')
// 								   ->when($startDate, function ($q) use ($startDate) {
// 							        $q->whereDate(
// 							            'inspection_requests.created_at',
// 							            '>=',
// 							            Carbon::createFromFormat('d-m-Y', $startDate)->format('Y-m-d')
// 							        );
// 							    })
// 							    ->when($endDate, function ($q) use ($endDate) {
// 							        $q->whereDate(
// 							            'inspection_requests.created_at',
// 							            '<=',
// 							            Carbon::createFromFormat('d-m-Y', $endDate)->format('Y-m-d')
// 							        );
// 							    })
// 							      ->when($inspectionid, function ($q) use ($inspectionid) {
// 							        $q->where('inspection_requests.inspection_id', $inspectionid);
// 							    });

// //				if (in_array('TECH', $sectionCodes)) {
// //				    $inspectionRequests->where('inspection_requests.status', '!=', 2);
// //				} else {
// //				    $inspectionRequests->where(function ($query) use ($sectionCodes) {
// //				        $query->whereHas('propertyMaster', function ($q) use ($sectionCodes) {
// //				            $q->whereIn('section_code', $sectionCodes);
// //				        })
// //				        ->orWhereHas('splittedProperty.propertyMaster', function ($q) use ($sectionCodes) {
// //				            $q->whereIn('section_code', $sectionCodes);
// //				        });
// //				    });
// //				}
//             $user = Auth::user();
// 			if (in_array('TECH', $sectionCodes) && $user->designation_id == 17) {

// 				$inspectionRequests->where('inspection_requests.status', '!=', 2);

// 				if (!empty($inspectionid)) {

// 					// Search case
// 					$inspectionRequests->where(function ($q) use ($user) {

// 						$q->whereRaw("
// 							JSON_UNQUOTE(
// 								JSON_EXTRACT(
// 									inspection_requests.communication_address,
// 									'$.second_address.inspection_officer'
// 								)
// 							) = ?
// 						", [$user->name])

// 						->orWhereRaw("
// 							JSON_UNQUOTE(
// 								JSON_EXTRACT(
// 									inspection_requests.communication_address,
// 									'$.second_address.inspection_officer'
// 								)
// 							) IS NULL
// 						")

// 						->orWhereRaw("
// 							JSON_UNQUOTE(
// 								JSON_EXTRACT(
// 									inspection_requests.communication_address,
// 									'$.second_address.inspection_officer'
// 								)
// 							) = ''
// 						");

// 					});

// 				} else {

// 					// Default listing
// 					$hasAssigned = InspectionRequest::whereRaw("
// 						JSON_UNQUOTE(
// 							JSON_EXTRACT(
// 								communication_address,
// 								'$.second_address.inspection_officer'
// 							)
// 						) = ?
// 					", [$user->name])->exists();

// 					if ($hasAssigned) {

// 						$inspectionRequests->whereRaw("
// 							JSON_UNQUOTE(
// 								JSON_EXTRACT(
// 									inspection_requests.communication_address,
// 									'$.second_address.inspection_officer'
// 								)
// 							) = ?
// 						", [$user->name]);
// 					}

// 				}

// 			} elseif (in_array('TECH', $sectionCodes)) {

// 				// AE/EE/SE etc.
// 				$inspectionRequests->where('inspection_requests.status', '!=', 2);

// 			} else {

// 				$inspectionRequests->where(function ($query) use ($sectionCodes) {

// 					$query->whereHas('propertyMaster', function ($q) use ($sectionCodes) {
// 						$q->whereIn('section_code', $sectionCodes);
// 					})
// 					->orWhereHas('splittedProperty.propertyMaster', function ($q) use ($sectionCodes) {
// 						$q->whereIn('section_code', $sectionCodes);
// 					});

// 				});

// 			}
// 						$inspectionRequests = $inspectionRequests->with(['inspectionReport','propertyMaster','splittedProperty.propertyMaster'])->latest()->get();
// 						return view('inspection.list',compact('inspectionRequests','request'));
// 					}

public function inspectionList(Request $request)
{
    $startDate = isset($request->start_date) && !empty($request->start_date) ? $request->start_date : null;
    $endDate = isset($request->end_date) && !empty($request->end_date) ? $request->end_date : null;
    $inspectionid = isset($request->inspectionid) && !empty($request->inspectionid) ? $request->inspectionid : null;

    $user = Auth::user();
    $sectionCodes = $user->sections()->pluck('section_code')->toArray();

    $inspectionRequests = InspectionRequest::query()
        ->leftJoin('current_lessee_details as cld', 'cld.old_property_id', '=', 'inspection_requests.property_id')
        ->select('inspection_requests.*', 'cld.property_known_as')

        ->when($startDate, function ($q) use ($startDate) {
            $q->whereDate(
                'inspection_requests.created_at',
                '>=',
                Carbon::createFromFormat('d-m-Y', $startDate)->format('Y-m-d')
            );
        })

        ->when($endDate, function ($q) use ($endDate) {
            $q->whereDate(
                'inspection_requests.created_at',
                '<=',
                Carbon::createFromFormat('d-m-Y', $endDate)->format('Y-m-d')
            );
        })

        ->when($inspectionid, function ($q) use ($inspectionid) {
            $q->where('inspection_requests.inspection_id', $inspectionid);
        });

    if (in_array('TECH', $sectionCodes)) {

        $inspectionRequests->where('inspection_requests.status', '!=', 2);
        // Only JE
       if ($user->designation_id == 17) {

    $inspectionRequests->where(function ($q) use ($user) {

        $q->where('inspection_requests.schedule_by', $user->id)
          ->orWhereNull('inspection_requests.schedule_by');

    });

}

    } else {

        $inspectionRequests->where(function ($query) use ($sectionCodes) {

            $query->whereHas('propertyMaster', function ($q) use ($sectionCodes) {
                $q->whereIn('section_code', $sectionCodes);
            })

            ->orWhereHas('splittedProperty.propertyMaster', function ($q) use ($sectionCodes) {
                $q->whereIn('section_code', $sectionCodes);
            });
        });
    }
    $inspectionRequests = $inspectionRequests
        ->with([
            'inspectionReport',
            'propertyMaster',
            'splittedProperty.propertyMaster'
        ])
        ->latest()
        ->get();
    return view('inspection.list', compact('inspectionRequests', 'request'));
}
	public function store(Request $request)
		{    
		$isSchedule = !empty($request->schedule_date);
		$isBreach = !empty($request->communication_address['breach_address']['bname']);		
			 if (!$isSchedule && !$isBreach) {
		   $request->validate(
						[
						    'resasonfor' => 'required',
						    'communication_address.main_address.cname' => 'required',
						    'communication_address.main_address.chouse' => 'required',
						    'communication_address.main_address.clocality' => 'required',
						    'communication_address.main_address.ccity' => 'required',
						    'communication_address.main_address.cpincode' => 'required',
						],
						[],
						[
						    'resasonfor' => 'Reason For',
						    'communication_address.main_address.cname' => 'Name',
						    'communication_address.main_address.chouse' => 'House No/Street Name',
						    'communication_address.main_address.clocality' => 'Locality',
						    'communication_address.main_address.ccity' => 'City',
						    'communication_address.main_address.cpincode' => 'Pincode',
						]
						);
			}
		    DB::beginTransaction();
		    try {		    	
		    	 if (!empty($request->inspectionId)) {
            $inspection = InspectionRequest::findOrFail($request->inspectionId); 
          	$address = [];
				if (!empty($inspection->communication_address)) {
				    $address = json_decode($inspection->communication_address, true);
				}
              if (!empty($request->schedule_date)) 
               { 
               $address['second_address'] = $request->communication_address['second_address'];
            		 $inspection->update([
   						 'communication_address' => json_encode($address),   						 
   						  'schedule_date' =>$request->schedule_date,
   						  'schedule_fromtime' =>$request->schedule_fromtime,
   						  'schedule_totime' =>$request->schedule_totime,
   						  'refused_by' =>$request->refused_by,
						   'schedule_by'=>auth()->id(),
   						  'reason' =>$request->reason,
   						 'schedule' => 1,
						]);
				}
				else if (!empty($request->communication_address['breach_address']['bname']))
				{ //dd($request->bremarks);
				
					$address['breach_address'] = $request->communication_address['breach_address'];
					$inspection->update([
						    'communication_address' => json_encode($address),
						    'bremarks' => $request->bremarks,
						    'bnotice' => 1,
						]);
				}
				else 
				{
				$address['main_address'] = $request->communication_address['main_address'];
				 $inspection->update([			
                'inspection_reason' =>$request->resasonfor,
                'communication_address'=> json_encode($address),
                'cremarks' => $request->cremarks,
             ]);	
				}
            DB::commit();
            return response()->json([
                'status' => true,
                'message' =>'Inspection Request Updated Successfully','data' => $inspection
            ]);
            }
		        $commonService = new CommonService;
		        $inspectionid = $commonService->getUniqueID(InspectionRequest::class,'INS','inspection_id');
		        $applicationdata = $this->getApplications($request->selectedOldPropertyId);
		        $inspection = InspectionRequest::create([
									            'inspection_id' => $inspectionid,
									            'property_id' => $request->selectedOldPropertyId,
									            'application_type' =>$applicationdata->service_type ?? 'N/A',
									            'application_no' =>$applicationdata->application_no ?? 'N/A',									            
									            'inspection_reason' => $request->resasonfor,
									            'communication_address'=>json_encode($request->communication_address),									            
									            'cremarks' => $request->cremarks,
									            'created_by' => auth()->id(),
									        ]);
		        $sendToRole = 26;
		        //dd($inspection->id);
		        $assignedToUser = Self::getUserIdBySectionCodeAndRole($sendToRole,'45');
		        $InspectionMovement = InspectionMovement::create([
										            'assigned_by' => Auth::user()->id,
										            'assigned_by_role' =>Auth::user()->roles[0]->id,
										            'assigned_to' => $assignedToUser,
										            'assigned_to_role' => $sendToRole,
										            'status' => 0,
										            'action' => getServiceType('RECOMMENDED'),
										            'inspection_id' => $inspection->id, 
										            'remarks' => $request->remark,
										        ]);
		        // Commit Transaction
		        DB::commit();
		        return response()->json([
		            'status' => true,
		            'message' =>'Inspection Request Saved Successfully',
		            'data' => $inspection
		        ]);
		    } catch (\Exception $e) {
		        DB::rollBack();
		        return response()->json([
		            'status' => false,
		            'message' => $e->getMessage()
		        ]);
		    }
	}
    function getUserIdBySectionCodeAndRole($roleId, $sectionId = null)
    {
        if ($roleId == 11) {
            $row = DB::table('model_has_roles')->where('role_id', $roleId)->first();
            return $row->model_id;
        }
        $assignedToUser = DB::table('section_user as su')
            ->join('model_has_roles as mhr', 'su.user_id', '=', 'mhr.model_id')
            ->where('mhr.role_id', $roleId)
            ->where('su.section_id', $sectionId)
            ->select('su.*', 'mhr.role_id') // Select specific columns if needed
            ->first();
        return !empty($assignedToUser) ? $assignedToUser->user_id : null;
    }
    public function getApplications($old_property_id)
		{
		    $userIds = UserProperty::where(
		            'old_property_id',
		            $old_property_id
		        )->pluck('user_id')->unique();
		    return Application::select(
		            'application_no',
		            'service_type'
		        )
		        ->whereIn('created_by', $userIds)
		        ->orderBy('created_at', 'desc')
		        ->first();
		}
		// Controller

	public function getInspectionStatus(Request $request)
		{

		    $inspection = InspectionRequest::where('property_id',$request->property_id)->latest()->first();		    
		    if (!$inspection) {
		        return response()->json(['status' => false]);
		    }
		    if ($inspection->status == 0) {
		        return response()->json([
		            'status' => true,
		            'type' => 'already_created',
		            'message' => 'Inspection already created for this property.'
		        ]);
		    }
		    if ($inspection->status == 1) {
		        return response()->json([
		            'status' => true,
		            'type' => 'reopen_or_new',
		            'inspection_id' => $inspection->id,
		            'message' => 'Inspection already exists. Do you want to reopen or create new?'
		        ]);
		    }
	}
	public function inspectionAction(Request $request, $id)
		{
		    $skipAccessCheck = Auth::user()->hasRole(['technical-section']) ? 0 : 1;
		    $type = $request->type;
		    $inspectionQuery = InspectionRequest::leftJoin(
								            'current_lessee_details as cld',
								            'cld.old_property_id','=','inspection_requests.property_id')
								        ->leftJoin('section_user as su','su.user_id', '=','inspection_requests.created_by')
								        ->leftJoin('sections as s', 's.id', '=', 'su.section_id')
								        ->leftJoin('users as u', 'u.id', '=', 'su.user_id')
								        ->select(
								            'inspection_requests.*',
								            'cld.property_known_as',
								            's.section_code',
								            's.name',
								            'u.name as section_user_name'
								        );
		    if (in_array($type, ['noting', 'innoting', 'innotice'])) 
		    {
		        $inspection = (clone $inspectionQuery)
		            ->where('inspection_requests.id', $id)
		            ->firstOrFail();
		        $viewName = match ($type) {
		            'noting'   => 'inspection.noting',
		            'innoting' => 'inspection.innoting',
		            'innotice' => 'inspection.innotice',
		        };
		        $fileName = ($type == 'innotice')
		            ? 'Notice-' . $inspection->inspection_id . '.pdf'
		            : 'Noting-' . $inspection->inspection_id . '.pdf';
		        $pdf = Pdf::loadView($viewName, compact('inspection'));
		        return $pdf->download($fileName);
		       }
		       if ($type == 'finalreport' || $type == 'breachnoticep') {
		       	
			    $inspection = (clone $inspectionQuery)
			        ->where('inspection_requests.id', $id)
			        ->firstOrFail();
                 $commonController = app(\App\Http\Controllers\CommonController::class);
									$propertyResponse = $commonController->propertyBasicdetail(
									    new \Illuminate\Http\Request([
									        'property_id' => $inspection->property_id,
									        'skipAccessCheck' => 0
									    ])
									);
                 $propertyData = $propertyResponse['data'] ?? [];                
			    $inspectionReportBind = InspectionReport::with([
			        'breaches',
			        'unauthorisedDetails',
					'signingAuthority','seniorofficer'
			    ])->where('inspection_id', $inspection->id)
			      ->first();
                 
			    $pdf = Pdf::loadView(
			        'inspection.inreport',
			        compact('inspection', 'inspectionReportBind','propertyData','type')
			    );
			    if($type == 'breachnoticep'){
			    		return $pdf->download('Breach-Notice-' . $inspection->inspection_id . '.pdf');
			    } 
			    else
			    {
			    //$pdf->setPaper('a4', 'landscape');
                  if($inspectionReportBind->mark_final == 0)
                  {
                  	return $pdf->download('Draft-Report-' . $inspection->inspection_id . '.pdf');
                  	}
                  	else {
                  			return $pdf->download('Final-Report-' . $inspection->inspection_id . '.pdf');
                  	}
			}
			    
			}
		    if ($type == 'cancel') {
		        $inspection = InspectionRequest::findOrFail($id);
		        $inspection->update([
		                       'status' => 2,
		                       'reason_re_can' => $request->reason
		                           ]);
		        return redirect()
		            ->back()
		            ->with('success', 'Inspection Request Cancelled Successfully');
		    }
		    if ($type == 'edit') {
		        $inspection = InspectionRequest::findOrFail($id);
		        return view(
		            'inspection.inspectionrequest',  compact('inspection', 'skipAccessCheck')
		        );
		    }
		    if ($type == 'reopen') {
		        $inspection = InspectionRequest::findOrFail($id);
		        $inspection->update([
		            'status' => 0,
		            'reopen' => 1,
		            'reason_re_can' => $request->reason
		        ]);
		        InspectionReport::where('inspection_id',$inspection->id  )->update([ 'mark_final' => 0]);
		        return redirect() ->back()->with('success', 'Inspection Request Reopened Successfully');
//		        return view(
//		            'inspection.inspectionrequest', compact('inspection','skipAccessCheck')
//		        );
		    }
		    if ($type == 'breachnotice') {
		    	// $sectionCodes = Auth::user()->sections()->pluck('section_id')->toArray();
				$inspection = InspectionRequest::findOrFail($id);
				$sectionCode = DB::table('property_masters')
						    ->where('old_propert_id', $inspection->property_id)
						    ->value('section_code');
						if (!$sectionCode) {
						    $sectionCode = DB::table('splited_property_details as sp')
						        ->join('property_masters as pm', 'pm.id', '=', 'sp.property_master_id')
						        ->where('sp.old_property_id', $inspection->property_id)
						        ->value('pm.section_code');
						}
						$sectionId = DB::table('sections')
						    ->where('section_code', $sectionCode)
						    ->value('id');

		    	// dd($sectionCodes);
		    	 $assignedToUser = DB::table('section_user as su')
            ->join('model_has_roles as mhr', 'su.user_id', '=', 'mhr.model_id')
              ->join('users as u', 'su.user_id', '=', 'u.id')
            ->where('mhr.role_id', 10)
            ->where('su.section_id', $sectionId)
			->where('u.status','1')
            ->select('su.*', 'mhr.role_id','u.name') // Select specific columns if needed
            ->first(); 
		        $inspection = InspectionRequest::findOrFail($id);
		       		        return view(
		            'inspection.inspectionrequest', compact('inspection','skipAccessCheck','type','assignedToUser')
		        );
		    }
		     if ($type == 'inspectionreport') {
		        $inspection = InspectionRequest::findOrFail($id);	
				$signingAuthorities = User::where('designation_id', 16)->orderBy('name')->get(['id', 'name']);
		        $inspectionreport = 'inspectionreport';	       
		        return view('inspection.inspectionrequest', compact('inspection','inspectionreport', 'skipAccessCheck','signingAuthorities'));
		    }
		     if ($type == 'inspectionreportedit') {
		        $inspection = InspectionRequest::findOrFail($id);	
				$signingAuthorities = User::where('designation_id', 16)->orderBy('name')->get(['id', 'name']);
		        $inspectionReportBind = InspectionReport::with([
								    'breaches',
								    'unauthorisedDetails'
								])->where('inspection_id', $inspection->id)->first();
		        $inspectionreport = 'inspectionreport';	       
		        return view('inspection.inspectionrequest', compact('inspection','inspectionreport', 'skipAccessCheck','inspectionReportBind','signingAuthorities'));
		    }
		    if (in_array($type, ['schedule', 'reschedule'])) {
		        $inspection = InspectionRequest::findOrFail($id);
		        $disabled = 'readonly';
		        $schedule = $reschedule = null;    
		        if ($type == 'schedule') {
		            $schedule = 'schedule';
		        }
		        if ($type == 'reschedule') {
		            $reschedule = 'reschedule';
		        }
		        return view('inspection.inspectionrequest', compact('inspection', 'skipAccessCheck', 'disabled', 'schedule', 'reschedule')
		        );
		    }
}
	
// 	public function storeReport(Request $request)
// 		{
			
// 		    DB::beginTransaction();
// 		    try
// 		    {
// 		        $report = $this->saveReportHeader($request);
				
// 		       if ($request->mark_final == 1) {		       
// 				    $lastMovement = InspectionMovement::where('inspection_id', $request->inspectionId)
// 				                    ->latest('id')
// 				                    ->first();				                     
// 				    if ($lastMovement) {
// 				        InspectionMovement::create([
// 				            'assigned_by'      => Auth::id(),
// 				            'assigned_by_role' => Auth::user()->roles[0]->id,
// 				            'assigned_to'      => $lastMovement->assigned_by,
// 				            'assigned_to_role' => $lastMovement->assigned_by_role,
// 				            'status'           => 1,
// 				            'action' => getServiceType('RECOMMENDED'),
// 				            'inspection_id'    => $request->inspectionId,
// 				            'remarks'          => 'Inspection Report Finalized',
// 				        ]);
// 				    }
// 					//dd($request->inspectionId);
// 				    InspectionRequest::where('id', $request->inspectionId)
// 							            ->update([
// 							                'status' => 1,
// 							                'reopen'=>0
// 							            ]);
// 				}						        
// //		        InspectionReportBreach::where(
// //		            'inspection_report_id',
// //		            $report->id
// //		        )->delete();
// // 		       if (!empty($request->breachDetails)) {

// // 				    foreach ($request->breachDetails as $row) {
// // 				        // Existing row updated
// // 				        if (!empty($row['id']) && !empty($row['is_changed']) && $row['is_changed'] == 1) {

// // 				            InspectionReportBreach::where('id', $row['id'])->update([

// // 				                'inspected_type'         => $row['inspected_type'] ?? null,
// // 				                'breach_type'            => $row['breach_type'] ?? null,
// // 				                'breach_on'              => $row['breach_on'] ?? null,
// // 				                'breach_floor'           => $row['breach_floor'] ?? null,
// // 				                'breach_use'             => $row['breach_use'] ?? null,
// // 				                'location_details'       => $row['location_details'] ?? null,
// // 				                'location_details_brief' => $row['location_details_brief'] ?? null,
// // 				                'length'                 => $row['length'] ?? null,
// // 				                'lunit'                  => $row['lunit'] ?? null,
// // 				                'breadth'                => $row['breadth'] ?? null,
// // 				                'bunit'                  => $row['bunit'] ?? null,
// // 				                'area'                   => $row['area'] ?? null,
// // 				                'aunit'                  => $row['aunit'] ?? null,
// // 				                'objectionable'          => $row['objectionable'] ?? null,
// // 				                'reason'                 => $row['reason'] ?? null,

// // 				            ]);

// // 				            continue;
// // 				        }

// // 				        // Existing row unchanged
// // 				        if (!empty($row['id'])) {
// // 				            continue;
// // 				        }

// // 				        // New row
// // 				        InspectionReportBreach::create([

// // 				            'inspection_report_id'   => $report->id,
// // 				            'inspected_type'         => $row['inspected_type'] ?? null,
// // 				            'breach_type'            => $row['breach_type'] ?? null,
// // 				            'breach_on'              => $row['breach_on'] ?? null,
// // 				            'breach_floor'           => $row['breach_floor'] ?? null,
// // 				            'breach_use'             => $row['breach_use'] ?? null,
// // 				            'location_details'       => $row['location_details'] ?? null,
// // 				            'location_details_brief' => $row['location_details_brief'] ?? null,
// // 				            'length'                 => $row['length'] ?? null,
// // 				            'lunit'                  => $row['lunit'] ?? null,
// // 				            'breadth'                => $row['breadth'] ?? null,
// // 				            'bunit'                  => $row['bunit'] ?? null,
// // 				            'area'                   => $row['area'] ?? null,
// // 				            'aunit'                  => $row['aunit'] ?? null,
// // 				            'objectionable'          => $row['objectionable'] ?? null,
// // 				            'reason'                 => $row['reason'] ?? null,

// // 				        ]);
// // 				    }
// // 				}
// // //		        InspectionReportUnauthorisedDetail::where(
// // //		            'inspection_report_id',
// // //		            $report->id
// // //		        )->delete();
// //              // dd($request->unauthorisedDetails);
			
// // 		        if (!empty($request->unauthorisedDetails)) {

// //     foreach ($request->unauthorisedDetails as $row) {

// //         // Existing row aur koi change nahi hua
// //         // if (!empty($row['id']) && ($row['is_changed'] ?? 0) == 0) {
// //         //     continue;
// //         // }
// //         InspectionReportUnauthorisedDetail::updateOrCreate(

// //             [
// //                 'id' => !empty($row['id']) ? $row['id'] : null
// //             ],

// //             [
// //                 'inspection_report_id'   => $report->id,
// //                 'inspected_type'         => $row['inspected_type'] ?? null,
// //                 'floor_breach'           => $row['floor_breach'] ?? null,
// //                 'floor_breach_use'       => $row['floor_breach_use'] ?? null,
// //                 'location_details'       => $row['location_details'] ?? null,
// //                 'location_details_brief' => $row['location_details_brief'] ?? null,
// //                 'length'                 => $row['length'] ?? null,
// //                 'lunit'                  => $row['lunit'] ?? null,
// //                 'breadth'                => $row['breadth'] ?? null,
// //                 'bunit'                  => $row['bunit'] ?? null,
// //                 'area'                   => $row['area'] ?? null,
// //                 'aunit'                  => $row['aunit'] ?? null,
// //                 'wpl'                    => $row['wpl'] ?? null,
// //                 'bpl'                    => $row['bpl'] ?? null,
// //                 'objectionable'          => $row['objectionable'] ?? null,
// //                 'reason'                 => $row['reason'] ?? null,
// //             ]
// //         );
// //     }
// // }
// 		        DB::commit();

// 		        return response()->json([
// 		            'status'  => true,
// 		            'message' => 'Inspection Report Saved Successfully'
// 		        ]);

// 		    } catch (\Exception $e) {

// 		        DB::rollBack();
// 		        return response()->json([
// 		            'status'  => false,
// 		            'message' => $e->getMessage()
// 		        ], 500);
// 		    }
// 			}
	public function storeReport(Request $request)
		{			
		    DB::beginTransaction();
		    try
		    {
		        $report = $this->saveReportHeader($request);
		       if ($request->mark_final == 1) {		       
				    $lastMovement = InspectionMovement::where('inspection_id', $request->inspectionId)
				                    ->latest('id')
				                    ->first();				                     
				    if ($lastMovement) {
				        InspectionMovement::create([
				            'assigned_by'      => Auth::id(),
				            'assigned_by_role' => Auth::user()->roles[0]->id,
				            'assigned_to'      => $lastMovement->assigned_by,
				            'assigned_to_role' => $lastMovement->assigned_by_role,
				            'status'           => 1,
				            'action' => getServiceType('RECOMMENDED'),
				            'inspection_id'    => $request->inspectionId,
				            'remarks'          => 'Inspection Report Finalized',
				        ]);
				    }
				    InspectionRequest::where('id', $request->inspectionId)
							            ->update([
							                'status' => 1,
							                'reopen'=>0,
							            ]);
				}
		        DB::commit();
		        return response()->json([
		            'status'  => true,
		            'message' => 'Inspection Report Saved Successfully'
		        ]);

		    } catch (\Exception $e) {

		        DB::rollBack();
		        return response()->json([
		            'status'  => false,
		            'message' => $e->getMessage()
		        ], 500);
		    }
		}
		private function saveReportHeader(Request $request)
		{   
		    return InspectionReport::updateOrCreate(
		        ['inspection_id' => $request->inspectionId],
		        [
		            'inspection_officer'   => $request->inspection_officer,
		            'senior_officer'       => $request->senior_officer,
		            'inspection_date'      => Carbon::parse($request->inspection_date)->format('Y-m-d'),
		            'book_number'          => $request->book_number,
		            'page_number'          => $request->page_number,
		            'construction_tallies' => $request->construction_tallies,
		            'sanction_plan_ref_no' => $request->sanction_plan_ref_no,
		            'sanction_plan_date'   => $request->sanction_plan_date,
		            'property_status'      => $request->property_status,
		            'total_floors'         => $request->total_floors,
		            'lease_deed_plot_area' => $request->lease_deed_plot_area,
		            'plot_area_matches'    => $request->plot_area_matches,
		            'plan_plot_area'       => $request->plan_plot_area,
		            'construction_area'    => $request->construction_area,
		            'tree_exists'          => $request->tree_exists,
		            'tree_count'           => $request->tree_count,
		            'remarks'              => $request->remarks,
		            'signing_authority'    => $request->signing_authority,
					'mark_final' => $request->has('mark_final')? $request->mark_final: 0,
		            'created_by'           => auth()->id(),
		        ]
		    );
		}
		
		public function saveUnauthorisedRow(Request $request)
				{
				    DB::beginTransaction();
				    try {
				        $report = $this->saveReportHeader($request);
				        $unauthorised = InspectionReportUnauthorisedDetail::updateOrCreate(
				            [
				                'id' => $request->id
				            ],
				            [
				                'inspection_report_id'   => $report->id,
				                'inspected_type'         => $request->inspected_type,
				                'floor_breach'           => $request->floor_breach,
				                'floor_breach_use'       => $request->floor_breach_use,
				                'location_details'       => $request->location_details,
				                'location_details_brief' => $request->location_details_brief,
				                'length'                 => $request->length,
				                'lunit'                  => $request->lunit,
				                'breadth'                => $request->breadth,
				                'bunit'                  => $request->bunit,
				                'area'                   => $request->area,
				                'aunit'                  => $request->aunit,
				                'wpl'                    => $request->wpl,
				                'bpl'                    => $request->bpl,
				                'objectionable'          => $request->objectionable,
				                'reason'                 => $request->reason,
				            ]
				        );
				        DB::commit();
				        return response()->json([
				            'status'  => true,
				            'id'      => $unauthorised->id,
				            'message' => 'Unauthorised row saved successfully.'
				        ]);
				    } catch (\Exception $e) {
				        DB::rollBack();
				        return response()->json([
				            'status'  => false,
				            'message' => $e->getMessage()
				        ], 500);

				    }
			}
     public function saveBreachRow(Request $request)
	{
    DB::beginTransaction();
    try {
  		$report = $this->saveReportHeader($request);
        $breach = InspectionReportBreach::updateOrCreate(
            [
                'id' => $request->id
            ],
            [
                'inspection_report_id'   => $report->id,
                'inspected_type'         => $request->inspected_type,
                'breach_type'            => $request->breach_type,
                'breach_on'              => $request->breach_on,
                'breach_floor'           => $request->breach_floor,
                'breach_use'             => $request->breach_use,
                'location_details'       => $request->location_details,
                'location_details_brief' => $request->location_details_brief,
                'length'                 => $request->length,
                'lunit'                  => $request->lunit,
                'breadth'                => $request->breadth,
                'bunit'                  => $request->bunit,
                'area'                   => $request->area,
                'aunit'                  => $request->aunit,
                'objectionable'          => $request->objectionable,
                'reason'                 => $request->reason,
            ]
        );
        DB::commit();

        return response()->json([
            'status' => true,
            'id'     => $breach->id,
            'message'=> 'Row Saved Successfully'
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message'=> $e->getMessage()
        ],500);
    }
 }


 public function deleteRow(Request $request)
		{  // dd($request->type);
		    if ($request->type == 'breach') {
		        InspectionReportBreach::where('id', $request->id)->delete();
		    } elseif ($request->type == 'unauthorised') {
		        InspectionReportUnauthorisedDetail::where('id', $request->id)->delete();
		    }
		    return response()->json([
		        'status' => true
		    ]);
		}

           
   }
    

