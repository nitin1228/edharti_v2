<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReportService;
use App\Services\MisService;
use App\Services\ColonyService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DetailExport;
use App\Jobs\CustomizedReportExport;
use App\Jobs\DetailedReportExport;
use App\Jobs\ReportExport as JobsReportExport;
use App\Models\PropertyMaster;
use App\Models\UserProperty;
use App\Models\Item;
//use App\Models\User;
use App\Models\OldColony;
use App\Models\Application;
use App\Models\CurrentLesseeDetail;
use App\Models\Payment;
//use App\Models\PropertyRevivisedGroundRent;
use App\Models\UnallottedPropertyDetail;
use App\Models\PropertyTransferredLesseeDetail;
use App\Models\SplitedPropertyDetail;
use App\Models\PropertyLeaseDetail;
use App\Models\PropertySectionMapping;
use App\Models\Section;
use Auth;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Storage;
// use DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Demand;
use App\Services\PropertyMasterService;
use Illuminate\Support\Carbon;
use App\Helpers\UserActionLogHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\DataTables;
use App\Models\SurveyDetail;
use App\Models\Flat;

class MasterReportController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view reports', ['only' => ['index', 'tabularRecord']]);
    }

    public function propertySummarizeReport()
    {        
       
        $data['colonies'] = $this->misDoneForColonies(false);        
        $data['sections'] = Section::where('has_property', 1)->get();
        return view('report.index', $data);
    }
      public function misDoneForColonies()
    {
        $colonyIds = PropertyMaster::select('old_colony_name')->distinct()->pluck('old_colony_name')->toArray();
        $foundColonies = collect();
        if (count($colonyIds) > 0) {           
            $foundColonies = OldColony::selectRaw("CASE 
                    WHEN new_name IS NOT NULL AND new_name != '' 
                    THEN CONCAT(name, ' (', new_name, ')') 
                    ELSE name 
		            END AS name, id
		            ")
	                ->whereIn('id', $colonyIds)
	                ->where('colony_stats', '=', 'Y')
	                ->orderBy('name', 'asc')
	                ->get();
        }
        return $foundColonies;
    }
   private function getApplicationsByMultipleOldPropertyIds(array $oldPropertyIds)
				{
				    $userProperties = UserProperty::whereIn('old_property_id', $oldPropertyIds)->get();

				    $userIds = $userProperties->pluck('user_id')->unique();

				    $applications = Application::whereIn('created_by', $userIds)
				        ->with(['serviceTypeItem', 'applicationStatuses'])
				        ->orderBy('created_at', 'desc')
				        ->get();
				    $applicationNos = $applications->pluck('application_no');

				    $movements = DB::table('application_movements')
				        ->whereIn('application_no', $applicationNos)
				        ->orderBy('created_at', 'desc')
				        ->get()
				        ->groupBy('application_no');

				    foreach ($applications as $app) {
				        $app->movements = $movements[$app->application_no] ?? collect();
				    }
				    return $applications;
				}
	
	private function getDemandsByMultipleOldPropertyIds(array $oldPropertyIds)
			{
			    
			    $demands = Demand::whereIn('old_property_id', $oldPropertyIds)
			        ->orderBy('created_at', 'desc')
			        ->get();
			    if ($demands->isEmpty()) {
			        return collect();
			    }
			    $demandIds = $demands->pluck('id')->toArray();
			    $payments = Payment::whereIn('demand_id', $demandIds)
			        ->where('status','1546')
			        ->orderBy('created_at', 'desc')
			        ->get()
			        ->groupBy('demand_id');
			    $demands = $demands->map(function ($demand) use ($payments) {
			        $demand->payments = $payments->get($demand->id, collect());
			        return $demand;
			    });

			    return $demands;
			}
	public function propertyAlldetail(Request $request)
					{
					    $searchedId = $request->property_id;
					    if (empty($searchedId)) {
					        return response()->json(['status' => 'error', 'message' => 'Property ID required']);
					    }
					    $propertyData = [];
					    $propertyMaster = null;
					    $splitProperties = collect();
					    /*
					    |--------------------------------------------------------------------------
					    | STEP 1: Check in Property Master by old_propert_id
					    |--------------------------------------------------------------------------
					    */
					   $propertyMaster = PropertyMaster::with([
										'propertyTransferredLesseeDetails:property_master_id,process_of_transfer,transferDate,lessee_name,splited_property_detail_id',
										'propertyLeaseDetail:property_master_id,type_of_lease,lease_no,date_of_expiration,doe,doa,date_of_conveyance_deed'
										])
										->select(
										    'property_masters.*',
										    'old_colonies.name',
										    'old_colonies.new_name',
										    'cld.lessees_name',
										    'cld.property_known_as',
										    'cld.area',
										    'cld.unit',
										    'cld.area_in_sqm',
										    'pcd.address',
										    'pcd.phone_no',
										    'pcd.email',
										    'pcd.as_on_date',
										     'pidd.last_inspection_ir_date',
									        'pidd.last_demand_letter_date',
									        'pidd.last_demand_id',
									        'pidd.last_demand_amount',
									        'pidd.last_amount_received',
									        'pidd.last_amount_received_date',
									        'pidd.total_dues',										    
										    DB::raw('(SELECT COUNT(*) 
										              FROM property_scanned_files 
										              WHERE property_scanned_files.old_property_id = property_masters.old_propert_id
										             ) as scanned_files_count')
										)
										->leftJoin('old_colonies', 'old_colonies.id', '=', 'property_masters.new_colony_name')
										->leftJoin('current_lessee_details as cld', 'cld.property_master_id', '=', 'property_masters.id')
										->leftJoin('property_contact_details as pcd', 'pcd.property_master_id', '=', 'property_masters.id')																									->leftJoin('property_inspection_demand_details as pidd', 'pidd.property_master_id', '=', 'property_masters.id')
										->where('property_masters.old_propert_id', $searchedId)
										->first();					
									   // dd($propertyMaster);
					    if ($propertyMaster) {
					    	 $propertyMaster->transfer = $propertyMaster->propertyTransferredLesseeDetails;
					        /*
					        |--------------------------------------------------------------------------
					        | CASE 1: Not Joint Property
					        |--------------------------------------------------------------------------
					        */
					        if (is_null($propertyMaster->is_joint_property)) {

					            // check in flats table
					           $flats = Flat::where('old_property_id', $searchedId)->get();
							   $propertyData = [
								    'master' => $propertyMaster,
								    'flats' => $flats,
								    'applications' => $applications = $this->getApplicationsByMultipleOldPropertyIds([$propertyMaster->old_propert_id]),
								     'demands' => $this->getDemandsByMultipleOldPropertyIds([$propertyMaster->old_propert_id]),
								    'has_flats' => $flats->isNotEmpty()
								];
								
					            return $this->returnView($propertyData, $searchedId);
					        }

					        /*
					        |--------------------------------------------------------------------------
					        | CASE 4: Joint Property → Get all children + master
					        |--------------------------------------------------------------------------
					        */
							$splitProperties = SplitedPropertyDetail::select(
										        'splited_property_details.*',
										        DB::raw('(SELECT COUNT(*) 
										                  FROM property_scanned_files 
										                  WHERE property_scanned_files.old_property_id = splited_property_details.old_property_id
										                 ) as scanned_files_count')
										    )
										    ->with('propertyTransferredLesseeDetails','propertyContactDetail','currentLesseeName')
										    ->where('property_master_id', $propertyMaster->id)
										    ->get();
										    $oldPropertyIds = collect($splitProperties)
						                    ->pluck('old_property_id')
						                    ->push($propertyMaster->old_propert_id)
						                    ->unique()
						                    ->toArray();
							$propertyData['demands'] = $this->getDemandsByMultipleOldPropertyIds($oldPropertyIds);
					        $propertyData['master'] = $propertyMaster;
					        $propertyData['children'] = $splitProperties;
                           
					        return $this->returnView($propertyData, $searchedId);
					    }

					    /*
					    |--------------------------------------------------------------------------
					    | STEP 2: If not found in master → check in split table
					    |--------------------------------------------------------------------------
					    */
					    $splitProperties = SplitedPropertyDetail::select(
									        'splited_property_details.*',
									        DB::raw('(SELECT COUNT(*) 
									                  FROM property_scanned_files 
									                  WHERE property_scanned_files.old_property_id = splited_property_details.old_property_id
									                 ) as scanned_files_count')
									    )
									    ->where('old_property_id', $searchedId)
									    ->get();
					    if ($splitProperties->count() > 0) {

					        /*
					        |--------------------------------------------------------------------------
					        | CASE 3: Found in split → fetch master using property_master_id
					        |--------------------------------------------------------------------------
					        */
					        $propertyMasterId = $splitProperties->first()->property_master_id;

					        $propertyMaster = PropertyMaster::with([
									        'propertyTransferredLesseeDetails:property_master_id,process_of_transfer,transferDate,lessee_name,splited_property_detail_id',
									        'propertyLeaseDetail:property_master_id,type_of_lease,lease_no,date_of_expiration,doe,doa,date_of_conveyance_deed'
									    ])
									    ->select(
									        'property_masters.*',
									        'old_colonies.name',
									        'old_colonies.new_name',
									        'cld.lessees_name',
									        'cld.property_known_as',
									        'cld.area',
									        'cld.unit',
									        'cld.area_in_sqm',
									        'pcd.address',
									        'pcd.phone_no',
									        'pcd.email',
									        'pcd.as_on_date',									       
									        'pidd.last_inspection_ir_date',
									        'pidd.last_demand_letter_date',
									        'pidd.last_demand_id',
									        'pidd.last_demand_amount',
									        'pidd.last_amount_received',
									        'pidd.last_amount_received_date',
									        'pidd.total_dues',	
									        DB::raw('(SELECT COUNT(*) 
									                  FROM property_scanned_files 
									                  WHERE property_scanned_files.old_property_id = property_masters.old_propert_id
									                 ) as scanned_files_count')
									    )
									    ->leftJoin('old_colonies', 'old_colonies.id', '=', 'property_masters.new_colony_name')
									    ->leftJoin('current_lessee_details as cld', 'cld.property_master_id', '=', 'property_masters.id')
									    ->leftJoin('property_contact_details as pcd', 'pcd.property_master_id', '=', 'property_masters.id')
									     ->leftJoin('property_inspection_demand_details as pidd', 'pidd.property_master_id', '=', 'property_masters.id')
									    ->where('property_masters.id', $propertyMasterId)
									    ->first();
                    $applications = $this->getApplicationsByMultipleOldPropertyIds([$propertyMaster->old_propert_id, $searchedId ]);
                    $demands = $this->getDemandsByMultipleOldPropertyIds([$propertyMaster->old_propert_id,$searchedId]);
					$propertyMaster->transfer = $propertyMaster->propertyTransferredLesseeDetails;

					       $propertyData['master'] = $propertyMaster;
							$propertyData['children'] = $splitProperties;
							$propertyData['applications'] = $applications;
							$propertyData['demands'] = $demands;

					        return $this->returnView($propertyData, $searchedId);
					    }

					    /*
					    |--------------------------------------------------------------------------
					    | If nothing found
					    |--------------------------------------------------------------------------
					    */
					    return response()->json([
					        'status' => 'error',
					        'message' => 'Property not found'
					    ]);
					}					
					private function returnView($propertyData, $searchedId)
					{
						//dd($propertyData);
					    $html = view('include.partials.filtered_property_data', [
					        'data' => $propertyData,
					        'meta' => [
					            'searched_id' => $searchedId,
					        ]
					    ])->render();

					    return response()->json([
					        'status' => 'success',
					        'html' => $html
					    ]);
					}
    
}

