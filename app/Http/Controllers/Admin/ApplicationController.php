<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\PropertyMaster;
use App\Models\OldColony;
use App\Models\ApplicationStatus;
use App\Models\Document;
use App\Models\DocumentKey;
use App\Models\Application;
use App\Models\User;
use App\Models\Role;
use App\Models\Coapplicant;
// use App\Models\PropertyLeaseDetail;
use App\Models\Section;
use App\Models\OldDemand;
use App\Models\ActionMatrix;
use App\Models\AppLatestAction;
use App\Models\ApplicationMovement;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use App\Helpers\UserActionLogHelper;
use Carbon\Carbon;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Mail;
use App\Mail\CommonMail;
use App\Http\Controllers\ApplicationController as UserApplicationController;
use App\Models\Demand;
use App\Helpers\GeneralFunctions;
use App\Models\DocumentChecklist;
use App\Models\PropertyTransferredLesseeDetail;
use App\Models\DeedOfApartmentApplication;
use App\Models\UserProperty;
use App\Services\CommunicationService;
use App\Models\ConversionApplication;
use App\Models\LandUseChangeApplication;
use App\Models\MutationApplication;
use App\Models\ApplicationAppointmentLink;
use Spatie\Permission\Models\Role as SpatieRole;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Models\ApplicantUserDetail;
use App\Models\DeedOfApartmentApplicationHistory;
use App\Models\LandUseChangeApplicationHistory;
use App\Models\PropertyLeaseDetail;
use App\Models\PropertyMasterHistory;
use Illuminate\Support\Facades\Validator;
use App\Models\MutationApplicationHistory;
use App\Models\ConversionApplicationHistory;
use App\Models\CommunicationTracking;
use App\Models\NocApplication;
use App\Models\NocApplicationHistory;
use App\Events\MailSentSuccess;
use App\Models\Template;
use App\Models\Payment;
use App\Models\UserRegistration;
use App\Models\DemandDetail;
use App\Models\SplitedPropertyDetail;
use App\Models\ApplicantShare;
use App\Models\ApplicationDocumentDetail;
use App\Models\CurrentLesseeDetail;
use Illuminate\Support\Facades\Config;
use DateTime;
use DateTimeZone;
use App\Services\PropertyMasterService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\ApplicationForwardService;
use App\Models\PropertyScannedFile;
use App\Models\SplitedPropertyDetailHistory;
use App\Models\PropertyMiscDetail;
use App\Models\Flat;
use App\Models\ApplicationExtraApplicant;


class ApplicationController extends Controller
{
    protected $communicationService;
    protected $settingsService;

    public function __construct(CommunicationService $communicationService, SettingsService $settingsService)
    {
        $this->communicationService = $communicationService;
        $this->settingsService = $settingsService;
    }

    //get all applications according to the section
    public function index(Request $request)
    {
        $getStatusId = '';
        $applicationType = '';
        $getApplicationTypeId = '';
        $demandType = '';
        if ($request->query('status')) {
            /** code modified by Nitin to add desposed satatus in the list */
            $items = getApplicationStatusList(true, true);
            $getStatusId = $items->where('item_code', trim(Crypt::decrypt($request->query('status'))))->value('id'); // trim added by Nitin to remove extra space and lines from descrypted string - on 04-2024
        }
        if ($request->query('filterByApplication')) {
            /** code modified by Nitin to add desposed satatus in the list */
            $appTypeItems = getApplicationTypeList();
            $getApplicationTypeId = $appTypeItems->where('item_code', trim(Crypt::decrypt($request->query('filterByApplication'))))->value('id'); // trim added by Nitin to remove extra space and lines from descrypted string - on 04-2024
        }
        if ($request->query('applicationType')) {
            $applicationType = $request->query('applicationType');
        }
        if ($request->query('demandType')) {
            $demandType = $request->query('demandType');
        }
        $user = Auth::user();
        $filterPermissionArr = [];
        $items = getApplicationStatusList(true, true);
        $appTypeItems = getApplicationTypeList();
        return view('admin.applications.index', compact('items', 'getStatusId', 'user', 'applicationType', 'appTypeItems', 'getApplicationTypeId', 'demandType'));
    }

    //For showing assigned applications only - SOURAV CHAUHAN (4 May 2025)
    public function myapplications(Request $request)
    {
        $getStatusId = '';
        if ($request->query('status')) {
            $items = getApplicationStatusList(true, true);
            $getStatusId = $items->where('item_code', trim(Crypt::decrypt($request->query('status'))))->value('id'); // trim added by Nitin to remove extra space and lines from descrypted string - on 04-2024
        }
        $user = Auth::user();
        $filterPermissionArr = [];
        $items = getApplicationStatusList(true, true);
        return view('admin.applications.my-applicatons', compact('items', 'getStatusId', 'user'));
    }

     public function forwardedApplications(Request $request)
    {
        $getStatusId = '';
        if ($request->query('status')) {
            $items = getApplicationStatusList(true, true);
            $getStatusId = $items->where('item_code', trim(Crypt::decrypt($request->query('status'))))->value('id'); // trim added by Nitin to remove extra space and lines from descrypted string - on 04-2024
        }
        $user = Auth::user();
        $filterPermissionArr = [];
        $items = getApplicationStatusList(true, true);
        return view('admin.applications.forwarded-applications', compact('items', 'getStatusId', 'user'));
    }

    public function getApplications(Request $request)
    {
        $applicationType = $request->input('applicationType');
        $itemsIdArr = [];
        $items = getApplicationStatusList(true, true);
        if (count($items) > 0) {
            foreach ($items as $key => $item) {
                $itemsIdArr[] = $item->id;
            }
        }

        if ($request->filterByApplication) {
            $applicationItemCode = getServiceCodeById($request->filterByApplication);
            if (!empty($applicationItemCode) && $applicationItemCode == 'SUB_MUT') {
                $applicationType = 'mutation';
            } else if (!empty($applicationItemCode) && $applicationItemCode == 'NOC') {
                $applicationType = 'noc';
            } else if (!empty($applicationItemCode) && $applicationItemCode == 'LUC') {
                $applicationType = 'luc';
            } else if (!empty($applicationItemCode) && $applicationItemCode == 'DOA') {
                $applicationType = 'doa';
            } else if (!empty($applicationItemCode) && $applicationItemCode == 'CONVERSION') {
                $applicationType = 'conversion';
            }
        }

        $user = Auth::user();
        $sections = $user->sections->pluck('id');
        // dd($sections);
        $columns = [
            'id', // index 0
            'application_no', // index 1
            'old_property_id', // index 2
            'new_colony_name', // index 3
            'block_no', // index 4
            'plot_or_property_no', // index 5
            'flat_id', // index 6
            'presently_known_as', // index 7
            'section_code', // index 8
            'model_name', // index 9
            '', // index 10
            '', // index 11
            'created_at', // index 12
            'latest_moved_at', // index 13
        ];

        switch ($applicationType) {
            case 'mutation':
                $serviceType1 = getServiceType('SUB_MUT'); // Ensure this function is defined and works properly.

                $query1 = DB::table('mutation_applications as ma')
                    ->where('ma.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
                    ->leftJoin('property_masters as pm', 'ma.property_master_id', '=', 'pm.id')
                    ->leftJoin('splited_property_details as spd', 'ma.splitted_id', '=', 'spd.id')

                    ->join('property_section_mappings as psm', function ($join) {
                        $join->on('pm.new_colony_name', '=', 'psm.colony_id')
                            ->whereColumn('pm.property_type', 'psm.property_type')
                            ->whereColumn('pm.property_sub_type', 'psm.property_subtype');
                    })
                    ->join('sections', 'psm.section_id', '=', 'sections.id')
                    ->leftJoin('old_colonies as oc', 'pm.new_colony_name', '=', 'oc.id')
                    ->leftJoin('property_lease_details as pld', 'pm.id', '=', 'pld.property_master_id')
                    ->leftJoin('applications as app', 'ma.application_no', '=', 'app.application_no')
                    ->leftJoinSub(
                        DB::table('application_movements')
                            ->select('application_no', DB::raw('MAX(id) as latest_created_at'))
                            ->groupBy('application_no'),
                        'latest_apm',
                        function ($join) {
                            $join->on('ma.application_no', '=', 'latest_apm.application_no');
                        }
                    )
                    ->leftJoin('application_movements as apm', function ($join) {
                        $join->on('ma.application_no', '=', 'apm.application_no')
                            ->on('apm.id', '=', 'latest_apm.latest_created_at');
                    })
                    ->leftJoinSub(
                        DB::table('application_statuses')
                            ->select(
                                'id',
                                'model_id',
                                'reg_app_no',
                                'service_type',
                                'is_mis_checked',
                                'is_scan_file_checked',
                                'is_uploaded_doc_checked',
                                'mis_checked_by',
                                'scan_file_checked_by',
                                'uploaded_doc_checked_by',
                                'created_at',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                            )
                            ->where('service_type', $serviceType1),
                        'latest_statuses',
                        function ($join) {
                            $join->on('ma.id', '=', 'latest_statuses.model_id')
                                ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                        }
                    )
                    ->whereIn('ma.section_id', $sections) // Verify $sections is an array
                    ->select(
                        'ma.id',
                        'ma.created_at',
                        'ma.application_no',
                        'ma.status',
                        'sections.section_code',
                        'latest_statuses.is_mis_checked',
                        'latest_statuses.is_scan_file_checked',
                        'latest_statuses.is_uploaded_doc_checked',
                        'latest_statuses.mis_checked_by',
                        'latest_statuses.scan_file_checked_by',
                        'latest_statuses.uploaded_doc_checked_by',
                        // 'pm.old_propert_id as old_property_id', // Fixed alias
                        DB::raw('case when pm.is_joint_property = 1 then spd.old_property_id else pm.old_propert_id end as old_property_id'),
                        'pm.new_colony_name',
                        'oc.name as colony_name',
                        'pm.block_no',
                        // 'pm.plot_or_property_no',
                        // 'pld.presently_known_as',
                        DB::raw('case when pm.is_joint_property = 1 then spd.plot_flat_no else pm.plot_or_property_no end as plot_or_property_no'),
                        DB::raw('case when pm.is_joint_property = 1 then spd.presently_known_as else pld.presently_known_as end as presently_known_as'),
                        'app.is_objected',
                        'apm.updated_at as latest_moved_at',
                        DB::raw('NULL as flat_id'), // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        DB::raw('NULL as flat_number'), // Add NULL for flat_number on dated 07/01/25 By Lalit Tiwari
                        DB::raw("'MutationApplication' as model_name"), // Add model_name for the first query
                        // DB::raw("$serviceType1 as serviceType") // Added by Nitin 
                    );
                if ($request->status) {
                    $query1 = $query1->where('ma.status', ($request->status));
                } else {
                    $query1 = $query1->whereIn('ma.status', ($itemsIdArr));
                }

                // Add search filter if search.value is present
                if ($request->input('search.value')) {
                    $searchValue = $request->input('search.value');
                    $query1->where(function ($query) use ($searchValue) {
                        $query->where('ma.application_no', 'like', "%$searchValue%")
                            // ->orWhere('pm.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                            ->orWhereRaw("CASE WHEN pm.is_joint_property = 1 THEN spd.old_property_id ELSE pm.old_propert_id END LIKE ?", ["%$searchValue%"]) 
                            ->orWhere(DB::raw('LOWER(oc.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                            ->orWhere('pm.block_no', 'like', "%$searchValue%")  // Correctly reference block
                            // ->orWhere('pm.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhereRaw("CASE WHEN pm.is_joint_property = 1 THEN spd.plot_flat_no ELSE pm.plot_or_property_no END LIKE ?", ["%$searchValue%"])

                            ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                            // ->orWhere('pld.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhereRaw("CASE WHEN pm.is_joint_property = 1 THEN spd.presently_known_as ELSE pld.presently_known_as END LIKE ?", ["%$searchValue%"])
                            ->orWhere(DB::raw("'MutationApplication'"), 'like', "%$searchValue%") // Search by model_name
                            ->orWhere('ma.created_at', 'like', "%$searchValue%");
                    });
                }
                $clonedQuery1 = (clone $query1);
                $combinedQuery = $clonedQuery1;
                break;
            case 'luc':
                // Query for land use changed applications
                $serviceType2 = getServiceType('LUC');
                $query2 = DB::table('land_use_change_applications as lca')
                    ->where('lca.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
                    ->leftJoin('property_masters', 'lca.property_master_id', '=', 'property_masters.id')
                    ->leftJoin('splited_property_details as spd', 'lca.splited_property_detail_id', '=', 'spd.id')

                    ->join('property_section_mappings as psm', function ($join) {
                        $join->on('property_masters.new_colony_name', 'psm.colony_id');
                        $join->whereColumn('property_masters.property_type', 'psm.property_type');
                        $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
                    })
                    ->join('sections', 'psm.section_id', 'sections.id')
                    ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
                    ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
                    ->leftJoin('applications as app', 'lca.application_no', '=', 'app.application_no')
                    ->leftJoinSub(
                        DB::table('application_movements')
                            ->select('application_no', DB::raw('MAX(id) as latest_created_at'))
                            ->groupBy('application_no'),
                        'latest_apm',
                        function ($join) {
                            $join->on('lca.application_no', '=', 'latest_apm.application_no');
                        }
                    )
                    ->leftJoin('application_movements as apm', function ($join) {
                        $join->on('lca.application_no', '=', 'apm.application_no')
                            ->on('apm.id', '=', 'latest_apm.latest_created_at');
                    })
                    ->leftJoinSub(
                        DB::table('application_statuses')
                            ->select(
                                'id',
                                'model_id',
                                'reg_app_no',
                                'service_type',
                                'is_mis_checked',
                                'is_scan_file_checked',
                                'is_uploaded_doc_checked',
                                'mis_checked_by',
                                'scan_file_checked_by',
                                'uploaded_doc_checked_by',
                                'created_at',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                            )
                            ->where('service_type', $serviceType2),
                        'latest_statuses',
                        function ($join) {
                            $join->on('lca.id', '=', 'latest_statuses.model_id')
                                ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                        }
                    )
                    ->whereIn('lca.section_id', $sections) //need to add secton id in all queries
                    ->select(
                        'lca.id',
                        'lca.created_at',
                        DB::raw('coalesce(lca.application_no,"0") as application_no'),
                        'lca.status',
                        'sections.section_code',
                        'latest_statuses.is_mis_checked',
                        'latest_statuses.is_scan_file_checked',
                        'latest_statuses.is_uploaded_doc_checked',
                        'latest_statuses.mis_checked_by',
                        'latest_statuses.scan_file_checked_by',
                        'latest_statuses.uploaded_doc_checked_by',
                        // 'property_masters.old_propert_id as old_property_id',
                        DB::raw('case when property_masters.is_joint_property = 1 then spd.old_property_id else property_masters.old_propert_id end as old_property_id'),

                        'property_masters.new_colony_name',
                        'old_colonies.name as colony_name',
                        'property_masters.block_no',
                        // 'property_masters.plot_or_property_no',
                        // 'property_lease_details.presently_known_as',
                        DB::raw('case when property_masters.is_joint_property = 1 then spd.plot_flat_no else property_masters.plot_or_property_no end as plot_or_property_no'),
                        DB::raw('case when property_masters.is_joint_property = 1 then spd.presently_known_as else property_lease_details.presently_known_as end as presently_known_as'),

                        'app.is_objected',
                        'apm.updated_at as latest_moved_at',
                        DB::raw('NULL as flat_id'), // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        DB::raw('NULL as flat_number'), // Add NULL for flat_number on dated 07/01/25 By Lalit Tiwari
                        DB::raw("'LandUseChangeApplication' as model_name"), // Add model_name for the first query
                        // DB::raw("$serviceType2 as serviceType") // Added by Nitin 
                    );

                if ($request->status) {
                    $query2 = $query2->where('lca.status', ($request->status));
                } else {
                    $query2 = $query2->whereIn('lca.status', ($itemsIdArr));
                }

                // Add search filter if search.value is present
                if ($request->input('search.value')) {
                    $searchValue = $request->input('search.value');
                    $query2->where(function ($query) use ($searchValue) {
                        $query->where('lca.application_no', 'like', "%$searchValue%")
                            // ->orWhere('property_masters.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                            ->orWhereRaw("CASE WHEN property_masters.is_joint_property = 1 THEN spd.old_property_id ELSE property_masters.old_propert_id END LIKE ?", ["%$searchValue%"])
                            ->orWhere(DB::raw('LOWER(old_colonies.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                            ->orWhere('property_masters.block_no', 'like', "%$searchValue%")  // Correctly reference block
                            // ->orWhere('property_masters.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhereRaw("CASE WHEN property_masters.is_joint_property = 1 THEN spd.plot_flat_no ELSE property_masters.plot_or_property_no END LIKE ?", ["%$searchValue%"])

                            ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                            // ->orWhere('property_lease_details.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                            // ->orWhereRaw("CASE WHEN property_masters.is_joint_property = 1 THEN spd.presently_known_as ELSE pld.presently_known_as END LIKE ?", ["%$searchValue%"])
                            ->orWhereRaw(
                            "CASE WHEN property_masters.is_joint_property = 1 THEN spd.presently_known_as ELSE property_lease_details.presently_known_as END LIKE ?",
                            ["%$searchValue%"]
                            )

                            ->orWhere(DB::raw("'LandUseChangeApplication'"), 'like', "%$searchValue%") // Search by model_name
                            ->orWhere('lca.created_at', 'like', "%$searchValue%");
                    });
                }
                $clonedQuery2 = (clone $query2);
                $combinedQuery = $clonedQuery2;
                break;
            case 'doa':
                //Query for Deed Of Apartment applications
                $serviceType3 = getServiceType('DOA');
                $query3 = DB::table('deed_of_apartment_applications as doa')
                    ->where('doa.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
                    ->leftJoin('property_masters', 'doa.property_master_id', '=', 'property_masters.id')
                    ->leftJoin('splited_property_details as spd', 'doa.splited_property_detail_id', '=', 'spd.id')
                    ->join('property_section_mappings as psm', function ($join) {
                        $join->on('property_masters.new_colony_name', 'psm.colony_id');
                        $join->whereColumn('property_masters.property_type', 'psm.property_type');
                        $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
                    })
                    ->join('sections', 'psm.section_id', 'sections.id')
                    ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
                    ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
                    ->leftJoin('flats', 'doa.flat_id', '=', 'flats.id')
                    ->leftJoin('applications as app', 'doa.application_no', '=', 'app.application_no')
                    ->leftJoinSub(
                        DB::table('application_movements')
                            ->select('application_no', DB::raw('MAX(id) as latest_created_at'))
                            ->groupBy('application_no'),
                        'latest_apm',
                        function ($join) {
                            $join->on('doa.application_no', '=', 'latest_apm.application_no');
                        }
                    )
                    ->leftJoin('application_movements as apm', function ($join) {
                        $join->on('doa.application_no', '=', 'apm.application_no')
                            ->on('apm.id', '=', 'latest_apm.latest_created_at');
                    })
                    ->leftJoinSub(
                        DB::table('application_statuses')
                            ->select(
                                'id',
                                'model_id',
                                'reg_app_no',
                                'service_type',
                                'is_mis_checked',
                                'is_scan_file_checked',
                                'is_uploaded_doc_checked',
                                'mis_checked_by',
                                'scan_file_checked_by',
                                'uploaded_doc_checked_by',
                                'created_at',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                            )
                            ->where('service_type', $serviceType3),
                        'latest_statuses',
                        function ($join) {
                            $join->on('doa.id', '=', 'latest_statuses.model_id')
                                ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                        }
                    )
                    ->whereIn('doa.section_id', $sections) //need to add secton id in all queries
                    ->select(
                        'doa.id',
                        'doa.created_at',
                        'doa.application_no',
                        'doa.status',
                        'sections.section_code',
                        'latest_statuses.is_mis_checked',
                        'latest_statuses.is_scan_file_checked',
                        'latest_statuses.is_uploaded_doc_checked',
                        'latest_statuses.mis_checked_by',
                        'latest_statuses.scan_file_checked_by',
                        'latest_statuses.uploaded_doc_checked_by',
                        // 'property_masters.old_propert_id as old_property_id',
                        DB::raw('case when property_masters.is_joint_property = 1 then spd.old_property_id else property_masters.old_propert_id end as old_property_id'),

                        'property_masters.new_colony_name',
                        'old_colonies.name as colony_name',
                        'property_masters.block_no',
                        // 'property_masters.plot_or_property_no',
                        // 'property_lease_details.presently_known_as',
                        DB::raw('case when property_masters.is_joint_property = 1 then spd.plot_flat_no else property_masters.plot_or_property_no end as plot_or_property_no'),
                        DB::raw('case when property_masters.is_joint_property = 1 then spd.presently_known_as else property_lease_details.presently_known_as end as presently_known_as'),

                        'app.is_objected',
                        'apm.updated_at as latest_moved_at',
                        'flats.unique_flat_id as flat_id', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        'flats.flat_number as flat_number', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        DB::raw("'DeedOfApartmentApplication' as model_name"), // Add model_name for the first query
                        // DB::raw("$serviceType3 as serviceType") // Added by Nitin 
                    );
                if ($request->status) {
                    $query3 = $query3->where('doa.status', ($request->status));
                } else {
                    $query3 = $query3->whereIn('doa.status', ($itemsIdArr));
                }

                // Add search filter if search.value is present
                if ($request->input('search.value')) {
                    $searchValue = $request->input('search.value');
                    $query3->where(function ($query) use ($searchValue) {
                        $query->where('doa.application_no', 'like', "%$searchValue%")
                            // ->orWhere('property_masters.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                            ->orWhereRaw("
                            CASE 
                                WHEN property_masters.is_joint_property = 1 
                                THEN spd.old_property_id 
                                ELSE property_masters.old_propert_id 
                            END LIKE ?
                        ", ["%$searchValue%"])  // Use the correct column alias here

                            ->orWhere(DB::raw('LOWER(old_colonies.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                            ->orWhere('property_masters.block_no', 'like', "%$searchValue%")  // Correctly reference block
                            // ->orWhere('property_masters.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhereRaw("
                            CASE 
                                WHEN property_masters.is_joint_property = 1 
                                THEN spd.plot_flat_no 
                                ELSE property_masters.plot_or_property_no 
                            END LIKE ?
                        ", ["%$searchValue%"])  // Correctly reference plot
                            ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere('flats.unique_flat_id', 'like', "%$searchValue%")  // Search by flat_id
                            ->orWhere('flats.flat_number', 'like', "%$searchValue%")    // Search by flat_number
                            // ->orWhere('property_lease_details.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhereRaw("
                            CASE 
                                WHEN property_masters.is_joint_property = 1 
                                THEN spd.presently_known_as 
                                ELSE property_lease_details.presently_known_as 
                            END LIKE ?
                        ", ["%$searchValue%"])
                            ->orWhere(DB::raw("'DeedOfApartmentApplication'"), 'like', "%$searchValue%") // Search by model_name
                            ->orWhere('doa.created_at', 'like', "%$searchValue%");
                    });
                }
                $clonedQuery3 = (clone $query3);
                $combinedQuery = $clonedQuery3;
                break;
            case 'conversion':
                //Query for Conversion applications added by Nitin
                $serviceType4 = getServiceType('CONVERSION');
                $query4 = DB::table('conversion_applications as ca')
                    ->where('ca.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
                    ->leftJoin('property_masters', 'ca.property_master_id', '=', 'property_masters.id')
                    ->leftJoin('splited_property_details as spd', 'ca.splited_property_detail_id', '=', 'spd.id')
                    ->join('property_section_mappings as psm', function ($join) {
                        $join->on('property_masters.new_colony_name', 'psm.colony_id');
                        $join->whereColumn('property_masters.property_type', 'psm.property_type');
                        $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
                    })
                    ->join('sections', 'psm.section_id', 'sections.id')
                    ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
                    ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
                    ->leftJoin('applications as app', 'ca.application_no', '=', 'app.application_no')
                    ->leftJoinSub(
                        DB::table('application_movements')
                            ->select('application_no', DB::raw('MAX(id) as latest_created_at'))
                            ->groupBy('application_no'),
                        'latest_apm',
                        function ($join) {
                            $join->on('ca.application_no', '=', 'latest_apm.application_no');
                        }
                    )
                    ->leftJoin('application_movements as apm', function ($join) {
                        $join->on('ca.application_no', '=', 'apm.application_no')
                            ->on('apm.id', '=', 'latest_apm.latest_created_at');
                    })
                    ->leftJoinSub(
                        DB::table('application_statuses')
                            ->select(
                                'id',
                                'model_id',
                                'reg_app_no',
                                'service_type',
                                'is_mis_checked',
                                'is_scan_file_checked',
                                'is_uploaded_doc_checked',
                                'mis_checked_by',
                                'scan_file_checked_by',
                                'uploaded_doc_checked_by',
                                'created_at',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                            )
                            ->where('service_type', $serviceType4),
                        'latest_statuses',
                        function ($join) {
                            $join->on('ca.id', '=', 'latest_statuses.model_id')
                                ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                        }
                    )
                    ->whereIn('ca.section_id', $sections) //need to add secton id in all queries
                    ->select(
                        'ca.id',
                        'ca.created_at',
                        'ca.application_no',
                        'ca.status',
                        'sections.section_code',
                        'latest_statuses.is_mis_checked',
                        'latest_statuses.is_scan_file_checked',
                        'latest_statuses.is_uploaded_doc_checked',
                        'latest_statuses.mis_checked_by',
                        'latest_statuses.scan_file_checked_by',
                        'latest_statuses.uploaded_doc_checked_by',
                        // 'property_masters.old_propert_id as old_property_id',
                        DB::raw('case when property_masters.is_joint_property = 1 then spd.old_property_id else property_masters.old_propert_id end as old_property_id'),

                        'property_masters.new_colony_name',
                        'old_colonies.name as colony_name',
                        'property_masters.block_no',
                        // 'property_masters.plot_or_property_no',
                        // 'property_lease_details.presently_known_as',
                        DB::raw('case when property_masters.is_joint_property = 1 then spd.plot_flat_no else property_masters.plot_or_property_no end as plot_or_property_no'),
                        DB::raw('case when property_masters.is_joint_property = 1 then spd.presently_known_as else property_lease_details.presently_known_as end as presently_known_as'),

                        'app.is_objected',
                        'apm.updated_at as latest_moved_at',
                        DB::raw('NULL as flat_id'), // Add NULL for flat_id
                        DB::raw('NULL as flat_number'), // Add NULL for flat_number on dated 07/01/25 By Lalit Tiwari
                        DB::raw("'ConversionApplication' as model_name"), // Add model_name for the first query
                        // DB::raw("$serviceType4 as serviceType") // Added by Nitin 
                    );

                if ($request->status) {
                    $query4 = $query4->where('ca.status', ($request->status));
                } else {
                    $query4 = $query4->whereIn('ca.status', ($itemsIdArr));
                }

                // Add search filter if search.value is present
                if ($request->input('search.value')) {
                    $searchValue = $request->input('search.value');
                    $query4->where(function ($query) use ($searchValue) {
                        $query->where('ca.application_no', 'like', "%$searchValue%")
                            // ->orWhere('property_masters.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                            ->orWhereRaw("
                            CASE 
                                WHEN property_masters.is_joint_property = 1 
                                THEN spd.old_property_id 
                                ELSE property_masters.old_propert_id 
                            END LIKE ?
                            ", ["%$searchValue%"])  // Use the correct column alias here

                            ->orWhere(DB::raw('LOWER(old_colonies.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                            ->orWhere('property_masters.block_no', 'like', "%$searchValue%")  // Correctly reference block
                            // ->orWhere('property_masters.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhereRaw("
                            CASE 
                                WHEN property_masters.is_joint_property = 1 
                                THEN spd.plot_flat_no 
                                ELSE property_masters.plot_or_property_no 
                            END LIKE ?
                            ", ["%$searchValue%"])  // Correctly reference plot

                            ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                            // ->orWhere('property_lease_details.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhereRaw("
                            CASE 
                                WHEN property_masters.is_joint_property = 1 
                                THEN spd.presently_known_as 
                                ELSE property_lease_details.presently_known_as 
                            END LIKE ?
                        ", ["%$searchValue%"])
                            ->orWhere(DB::raw("'ConversionApplication'"), 'like', "%$searchValue%") // Search by model_name
                            ->orWhere('ca.created_at', 'like', "%$searchValue%");
                    });
                }
                $clonedQuery4 = (clone $query4);
                $combinedQuery = $clonedQuery4;
                break;
            case 'noc':
                // Query added for NOC application - Lalit Tiwari (20/March/2025)
                $serviceType5 = getServiceType('NOC');
                $query5 = DB::table('noc_applications as noc')
                    ->where('noc.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
                    ->leftJoin('property_masters as pm', 'noc.property_master_id', '=', 'pm.id')
                    ->leftJoin('splited_property_details as spd', 'noc.splitted_id', '=', 'spd.id')
                    ->leftJoin('flats', 'noc.flat_id', '=', 'flats.id')
                    ->join('property_section_mappings as psm', function ($join) {
                        $join->on('pm.new_colony_name', '=', 'psm.colony_id')
                            ->whereColumn('pm.property_type', 'psm.property_type')
                            ->whereColumn('pm.property_sub_type', 'psm.property_subtype');
                    })
                    ->join('sections', 'psm.section_id', '=', 'sections.id')
                    ->leftJoin('old_colonies as oc', 'pm.new_colony_name', '=', 'oc.id')
                    ->leftJoin('property_lease_details as pld', 'pm.id', '=', 'pld.property_master_id')
                    ->leftJoin('applications as app', 'noc.application_no', '=', 'app.application_no')
                    ->leftJoinSub(
                        DB::table('application_movements')
                            ->select('application_no', DB::raw('MAX(id) as latest_created_at'))
                            ->groupBy('application_no'),
                        'latest_apm',
                        function ($join) {
                            $join->on('noc.application_no', '=', 'latest_apm.application_no');
                        }
                    )
                    ->leftJoin('application_movements as apm', function ($join) {
                        $join->on('noc.application_no', '=', 'apm.application_no')
                            ->on('apm.id', '=', 'latest_apm.latest_created_at');
                    })
                    ->leftJoinSub(
                        DB::table('application_statuses')
                            ->select(
                                'id',
                                'model_id',
                                'reg_app_no',
                                'service_type',
                                'is_mis_checked',
                                'is_scan_file_checked',
                                'is_uploaded_doc_checked',
                                'mis_checked_by',
                                'scan_file_checked_by',
                                'uploaded_doc_checked_by',
                                'created_at',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                            )
                            ->where('service_type', $serviceType5),
                        'latest_statuses',
                        function ($join) {
                            $join->on('noc.id', '=', 'latest_statuses.model_id')
                                ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                        }
                    )
                    ->whereIn('noc.section_id', $sections) // Verify $sections is an array
                    ->select(
                        'noc.id',
                        'noc.created_at',
                        'noc.application_no',
                        'noc.status',
                        'sections.section_code',
                        'latest_statuses.is_mis_checked',
                        'latest_statuses.is_scan_file_checked',
                        'latest_statuses.is_uploaded_doc_checked',
                        'latest_statuses.mis_checked_by',
                        'latest_statuses.scan_file_checked_by',
                        'latest_statuses.uploaded_doc_checked_by',
                        // 'pm.old_propert_id as old_property_id', // Fixed alias
                        DB::raw('case when pm.is_joint_property = 1 then spd.old_property_id else pm.old_propert_id end as old_property_id'),

                        'pm.new_colony_name',
                        'oc.name as colony_name',
                        'pm.block_no',
                        // 'pm.plot_or_property_no',
                        // 'pld.presently_known_as',
                        DB::raw('case when pm.is_joint_property = 1 then spd.plot_flat_no else pm.plot_or_property_no end as plot_or_property_no'),
                        DB::raw('case when pm.is_joint_property = 1 then spd.presently_known_as else pld.presently_known_as end as presently_known_as'),

                        'app.is_objected',
                        'apm.updated_at as latest_moved_at',
                        'flats.unique_flat_id as flat_id', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        'flats.flat_number as flat_number', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        DB::raw("'NocApplication' as model_name"), // Add model_name for the first query
                        // DB::raw("$serviceType5 as serviceType") // Added by Nitin 
                    );
                    $dateStart = '2006-02-14';  
           		$dateEnd = '2017-05-01';
					/* $query5->leftJoin(DB::raw("
				    (
				        SELECT ptld1.*
				        FROM property_transferred_lessee_details ptld1
				        INNER JOIN (
				            SELECT MAX(id) as max_id
				            FROM property_transferred_lessee_details
				            WHERE process_of_transfer = 'Conversion'
				            AND transferDate BETWEEN '$dateStart' AND '$dateEnd'
				            GROUP BY property_master_id
				        ) ptld2 ON ptld1.id = ptld2.max_id
				    ) as pld3
				"), 'pm.id', '=', 'pld3.property_master_id'); */
                // $query5->leftJoin(DB::raw("(
                //     SELECT ptld1.*
                //     FROM property_transferred_lessee_details ptld1
                //     INNER JOIN (
                //         SELECT property_master_id, MAX(id) as max_id
                //         FROM property_transferred_lessee_details
                //         WHERE process_of_transfer = 'Conversion'
                //         AND transferDate BETWEEN '$dateStart' AND '$dateEnd'
                //         AND deleted_at IS NULL  -- Only non-deleted records
                //         GROUP BY property_master_id
                //     ) ptld2 ON ptld1.id = ptld2.max_id
                //     WHERE ptld1.deleted_at IS NULL  -- Additional safety filter
                // ) as pld3"), 'pm.id', '=', 'pld3.property_master_id');
                $query5->leftJoin(DB::raw("
								    (
								        SELECT ptld1.*
								        FROM property_transferred_lessee_details ptld1
								        INNER JOIN (
								            SELECT MAX(id) as max_id
								            FROM property_transferred_lessee_details
								            WHERE process_of_transfer = 'Conversion'
								            AND transferDate BETWEEN '$dateStart' AND '$dateEnd'
                                             AND deleted_at IS NULL  -- Only non-deleted records
								            GROUP BY property_master_id, splited_property_detail_id
								        ) ptld2 ON ptld1.id = ptld2.max_id
								    ) as pld3
								"), function ($join) {
								    $join->on(function ($query) {
								        $query->on('pm.id', '=', 'pld3.property_master_id')
								              ->whereNull('noc.splitted_id');
								    })
								    ->orOn(function ($query) {
								        $query->on('spd.id', '=', 'pld3.splited_property_detail_id')
								              ->whereNotNull('noc.splitted_id');
								    });
								});

                if ($request->status) {
                    $query5 = $query5->where('noc.status', ($request->status));
                } else {
                    $query5 = $query5->whereIn('noc.status', ($itemsIdArr));
                }
                if ($request->demandType) {

				    $query5->where(function ($query) use ($request) {

				        if ($request->demandType == 'with_demand') {
				            $query->whereNotNull('pld3.id');
				        }

				        if ($request->demandType == 'without_demand') {
				            $query->whereNull('pld3.id');
				        }

				    });
				}

            //     if ($request->status) {
            //         $query5 = $query5->where('noc.status', ($request->status));
            //     } else {
            //         $query5 = $query5->whereIn('noc.status', ($itemsIdArr));
            //     }
            //    if ($request->demandType) {
            //         $dateStart = '2006-02-14';
            //         $dateEnd   = '2017-05-01';

            //         $query5->where(function ($query) use ($request, $dateStart, $dateEnd) {
            //             if ($request->demandType == 'with_demand') {
            //                 $query->whereBetween('pld.doe', [$dateStart, $dateEnd])
            //                     ->orWhereNull('pld.doe');
            //             }

            //             if ($request->demandType == 'without_demand') {
            //                 $query->whereNotBetween('pld.doe', [$dateStart, $dateEnd])
            //                     ->whereNotNull('pld.doe');
            //             }
            //         });
            //     } 
                // Add search filter if search.value is present
                if ($request->input('search.value')) {
                    $searchValue = $request->input('search.value');
                    $query5->where(function ($query) use ($searchValue) {
                        $query->where('noc.application_no', 'like', "%$searchValue%")
                                                        // ->orWhere('pm.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                            ->orWhereRaw("
                            CASE 
                                WHEN pm.is_joint_property = 1 
                                THEN spd.old_property_id 
                                ELSE pm.old_propert_id 
                            END LIKE ?
                        ", ["%$searchValue%"])  // Use the correct column alias here

                            ->orWhere(DB::raw('LOWER(oc.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                            ->orWhere('pm.block_no', 'like', "%$searchValue%")  // Correctly reference block
                                                        // ->orWhere('pm.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhereRaw("
                            CASE 
                                WHEN pm.is_joint_property = 1 
                                THEN spd.plot_flat_no 
                                ELSE pm.plot_or_property_no 
                            END LIKE ?
                        ", ["%$searchValue%"])  // Correctly reference plot

                            ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                                                        // ->orWhere('pld.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhereRaw("
                            CASE 
                                WHEN pm.is_joint_property = 1 
                                THEN spd.presently_known_as 
                                ELSE pld.presently_known_as 
                            END LIKE ?
                        ", ["%$searchValue%"])

                            ->orWhere(DB::raw("'NocApplication'"), 'like', "%$searchValue%") // Search by model_name
                            ->orWhere('noc.created_at', 'like', "%$searchValue%");
                    });
                }
                $clonedQuery5 = (clone $query5);
                $combinedQuery = $clonedQuery5;
                break;
            default:
                $serviceType1 = getServiceType('SUB_MUT'); // Ensure this function is defined and works properly.

                $query1 = DB::table('mutation_applications as ma')
                    ->where('ma.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
                    ->leftJoin('property_masters as pm', 'ma.property_master_id', '=', 'pm.id')
                    ->leftJoin('splited_property_details as spd', 'ma.splitted_id', '=', 'spd.id')

                    ->join('property_section_mappings as psm', function ($join) {
                        $join->on('pm.new_colony_name', '=', 'psm.colony_id')
                            ->whereColumn('pm.property_type', 'psm.property_type')
                            ->whereColumn('pm.property_sub_type', 'psm.property_subtype');
                    })
                    ->join('sections', 'psm.section_id', '=', 'sections.id')
                    ->leftJoin('old_colonies as oc', 'pm.new_colony_name', '=', 'oc.id')
                    ->leftJoin('property_lease_details as pld', 'pm.id', '=', 'pld.property_master_id')
                    ->leftJoin('applications as app', 'ma.application_no', '=', 'app.application_no')
                    ->leftJoinSub(
                        DB::table('application_movements')
                            ->select('application_no', DB::raw('MAX(id) as latest_created_at'))
                            ->groupBy('application_no'),
                        'latest_apm',
                        function ($join) {
                            $join->on('ma.application_no', '=', 'latest_apm.application_no');
                        }
                    )
                    ->leftJoin('application_movements as apm', function ($join) {
                        $join->on('ma.application_no', '=', 'apm.application_no')
                            ->on('apm.id', '=', 'latest_apm.latest_created_at');
                    })
                    ->leftJoinSub(
                        DB::table('application_statuses')
                            ->select(
                                'id',
                                'model_id',
                                'reg_app_no',
                                'service_type',
                                'is_mis_checked',
                                'is_scan_file_checked',
                                'is_uploaded_doc_checked',
                                'mis_checked_by',
                                'scan_file_checked_by',
                                'uploaded_doc_checked_by',
                                'created_at',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                            )
                            ->where('service_type', $serviceType1),
                        'latest_statuses',
                        function ($join) {
                            $join->on('ma.id', '=', 'latest_statuses.model_id')
                                ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                        }
                    )
                    ->whereIn('ma.section_id', $sections) // Verify $sections is an array
                    ->select(
                        'ma.id',
                        'ma.created_at',
                        'ma.application_no',
                        'ma.status',
                        'sections.section_code',
                        'latest_statuses.is_mis_checked',
                        'latest_statuses.is_scan_file_checked',
                        'latest_statuses.is_uploaded_doc_checked',
                        'latest_statuses.mis_checked_by',
                        'latest_statuses.scan_file_checked_by',
                        'latest_statuses.uploaded_doc_checked_by',
                        // 'pm.old_propert_id as old_property_id', // Fixed alias
                        DB::raw('case when pm.is_joint_property = 1 then spd.old_property_id else pm.old_propert_id end as old_property_id'),

                        'pm.new_colony_name',
                        'oc.name as colony_name',
                        'pm.block_no',
                        // 'pm.plot_or_property_no',
                        // 'pld.presently_known_as',
                        DB::raw('case when pm.is_joint_property = 1 then spd.plot_flat_no else pm.plot_or_property_no end as plot_or_property_no'),
                        DB::raw('case when pm.is_joint_property = 1 then spd.presently_known_as else pld.presently_known_as end as presently_known_as'),

                        'app.is_objected',
                        'apm.updated_at as latest_moved_at',
                        DB::raw('NULL as flat_id'), // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        DB::raw('NULL as flat_number'), // Add NULL for flat_number on dated 07/01/25 By Lalit Tiwari
                        DB::raw("'MutationApplication' as model_name"), // Add model_name for the first query
                        // DB::raw("$serviceType1 as serviceType") // Added by Nitin 
                    );
                if ($request->status) {
                    $query1 = $query1->where('ma.status', ($request->status));
                } else {
                    $query1 = $query1->whereIn('ma.status', ($itemsIdArr));
                }

                // Add search filter if search.value is present
                if ($request->input('search.value')) {
                    $searchValue = $request->input('search.value');
                    $query1->where(function ($query) use ($searchValue) {
                        $query->where('ma.application_no', 'like', "%$searchValue%")
                            // ->orWhere('pm.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                            ->orWhereRaw("CASE WHEN pm.is_joint_property = 1 THEN spd.old_property_id ELSE pm.old_propert_id END LIKE ?", ["%$searchValue%"])

                            ->orWhere(DB::raw('LOWER(oc.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                            ->orWhere('pm.block_no', 'like', "%$searchValue%")  // Correctly reference block
                            // ->orWhere('pm.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhereRaw("CASE WHEN pm.is_joint_property = 1 THEN spd.plot_flat_no ELSE pm.plot_or_property_no END LIKE ?", ["%$searchValue%"])

                            ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                            // ->orWhere('pld.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhereRaw("CASE WHEN pm.is_joint_property = 1 THEN spd.presently_known_as ELSE pld.presently_known_as END LIKE ?", ["%$searchValue%"])

                            ->orWhere(DB::raw("'MutationApplication'"), 'like', "%$searchValue%") // Search by model_name
                            ->orWhere('ma.created_at', 'like', "%$searchValue%");
                    });
                }

                // Query for land use changed applications
                $serviceType2 = getServiceType('LUC');
                $query2 = DB::table('land_use_change_applications as lca')
                    ->where('lca.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
                    ->leftJoin('property_masters', 'lca.property_master_id', '=', 'property_masters.id')
                    ->leftJoin('splited_property_details as spd', 'lca.splited_property_detail_id', '=', 'spd.id')

                    ->join('property_section_mappings as psm', function ($join) {
                        $join->on('property_masters.new_colony_name', 'psm.colony_id');
                        $join->whereColumn('property_masters.property_type', 'psm.property_type');
                        $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
                    })
                    ->join('sections', 'psm.section_id', 'sections.id')
                    ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
                    ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
                    ->leftJoin('applications as app', 'lca.application_no', '=', 'app.application_no')
                    ->leftJoinSub(
                        DB::table('application_movements')
                            ->select('application_no', DB::raw('MAX(id) as latest_created_at'))
                            ->groupBy('application_no'),
                        'latest_apm',
                        function ($join) {
                            $join->on('lca.application_no', '=', 'latest_apm.application_no');
                        }
                    )
                    ->leftJoin('application_movements as apm', function ($join) {
                        $join->on('lca.application_no', '=', 'apm.application_no')
                            ->on('apm.id', '=', 'latest_apm.latest_created_at');
                    })
                    ->leftJoinSub(
                        DB::table('application_statuses')
                            ->select(
                                'id',
                                'model_id',
                                'reg_app_no',
                                'service_type',
                                'is_mis_checked',
                                'is_scan_file_checked',
                                'is_uploaded_doc_checked',
                                'mis_checked_by',
                                'scan_file_checked_by',
                                'uploaded_doc_checked_by',
                                'created_at',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                            )
                            ->where('service_type', $serviceType2),
                        'latest_statuses',
                        function ($join) {
                            $join->on('lca.id', '=', 'latest_statuses.model_id')
                                ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                        }
                    )
                    ->whereIn('lca.section_id', $sections) //need to add secton id in all queries
                    ->select(
                        'lca.id',
                        'lca.created_at',
                        DB::raw('coalesce(lca.application_no,"0") as application_no'),
                        'lca.status',
                        'sections.section_code',
                        'latest_statuses.is_mis_checked',
                        'latest_statuses.is_scan_file_checked',
                        'latest_statuses.is_uploaded_doc_checked',
                        'latest_statuses.mis_checked_by',
                        'latest_statuses.scan_file_checked_by',
                        'latest_statuses.uploaded_doc_checked_by',
                        // 'property_masters.old_propert_id as old_property_id',
                        DB::raw('case when property_masters.is_joint_property = 1 then spd.old_property_id else property_masters.old_propert_id end as old_property_id'),

                        'property_masters.new_colony_name',
                        'old_colonies.name as colony_name',
                        'property_masters.block_no',
                        // 'property_masters.plot_or_property_no',
                        // 'property_lease_details.presently_known_as',
                        DB::raw('case when property_masters.is_joint_property = 1 then spd.plot_flat_no else property_masters.plot_or_property_no end as plot_or_property_no'),
                        DB::raw('case when property_masters.is_joint_property = 1 then spd.presently_known_as else property_lease_details.presently_known_as end as presently_known_as'),

                        'app.is_objected',
                        'apm.updated_at as latest_moved_at',
                        DB::raw('NULL as flat_id'), // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        DB::raw('NULL as flat_number'), // Add NULL for flat_number on dated 07/01/25 By Lalit Tiwari
                        DB::raw("'LandUseChangeApplication' as model_name"), // Add model_name for the first query
                        // DB::raw("$serviceType2 as serviceType") // Added by Nitin 
                    );

                if ($request->status) {
                    $query2 = $query2->where('lca.status', ($request->status));
                } else {
                    $query2 = $query2->whereIn('lca.status', ($itemsIdArr));
                }

                // Add search filter if search.value is present
                if ($request->input('search.value')) {
                    $searchValue = $request->input('search.value');
                    $query2->where(function ($query) use ($searchValue) {
                        $query->where('lca.application_no', 'like', "%$searchValue%")
                            // ->orWhere('property_masters.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                            ->orWhereRaw("CASE WHEN property_masters.is_joint_property = 1 THEN spd.old_property_id ELSE property_masters.old_propert_id END LIKE ?", ["%$searchValue%"])

                            ->orWhere(DB::raw('LOWER(old_colonies.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                            ->orWhere('property_masters.block_no', 'like', "%$searchValue%")  // Correctly reference block
                            // ->orWhere('property_masters.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhereRaw("CASE WHEN property_masters.is_joint_property = 1 THEN spd.plot_flat_no ELSE property_masters.plot_or_property_no END LIKE ?", ["%$searchValue%"])

                            ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                            // ->orWhere('property_lease_details.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhereRaw("CASE WHEN property_masters.is_joint_property = 1 THEN spd.presently_known_as ELSE property_lease_details.presently_known_as END LIKE ?", ["%$searchValue%"])

                            ->orWhere(DB::raw("'LandUseChangeApplication'"), 'like', "%$searchValue%") // Search by model_name
                            ->orWhere('lca.created_at', 'like', "%$searchValue%");
                    });
                }

                //Query for Deed Of Apartment applications
                $serviceType3 = getServiceType('DOA');
                $query3 = DB::table('deed_of_apartment_applications as doa')
                    ->where('doa.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
                    ->leftJoin('property_masters', 'doa.property_master_id', '=', 'property_masters.id')
                    ->leftJoin('splited_property_details as spd', 'doa.splited_property_detail_id', '=', 'spd.id')
                    ->join('property_section_mappings as psm', function ($join) {
                        $join->on('property_masters.new_colony_name', 'psm.colony_id');
                        $join->whereColumn('property_masters.property_type', 'psm.property_type');
                        $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
                    })
                    ->join('sections', 'psm.section_id', 'sections.id')
                    ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
                    ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
                    ->leftJoin('flats', 'doa.flat_id', '=', 'flats.id')
                    ->leftJoin('applications as app', 'doa.application_no', '=', 'app.application_no')
                    ->leftJoinSub(
                        DB::table('application_movements')
                            ->select('application_no', DB::raw('MAX(id) as latest_created_at'))
                            ->groupBy('application_no'),
                        'latest_apm',
                        function ($join) {
                            $join->on('doa.application_no', '=', 'latest_apm.application_no');
                        }
                    )
                    ->leftJoin('application_movements as apm', function ($join) {
                        $join->on('doa.application_no', '=', 'apm.application_no')
                            ->on('apm.id', '=', 'latest_apm.latest_created_at');
                    })
                    ->leftJoinSub(
                        DB::table('application_statuses')
                            ->select(
                                'id',
                                'model_id',
                                'reg_app_no',
                                'service_type',
                                'is_mis_checked',
                                'is_scan_file_checked',
                                'is_uploaded_doc_checked',
                                'mis_checked_by',
                                'scan_file_checked_by',
                                'uploaded_doc_checked_by',
                                'created_at',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                            )
                            ->where('service_type', $serviceType3),
                        'latest_statuses',
                        function ($join) {
                            $join->on('doa.id', '=', 'latest_statuses.model_id')
                                ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                        }
                    )
                    ->whereIn('doa.section_id', $sections) //need to add secton id in all queries
                    ->select(
                        'doa.id',
                        'doa.created_at',
                        'doa.application_no',
                        'doa.status',
                        'sections.section_code',
                        'latest_statuses.is_mis_checked',
                        'latest_statuses.is_scan_file_checked',
                        'latest_statuses.is_uploaded_doc_checked',
                        'latest_statuses.mis_checked_by',
                        'latest_statuses.scan_file_checked_by',
                        'latest_statuses.uploaded_doc_checked_by',
                        // 'property_masters.old_propert_id as old_property_id',
                        DB::raw('case when property_masters.is_joint_property = 1 then spd.old_property_id else property_masters.old_propert_id end as old_property_id'),

                        'property_masters.new_colony_name',
                        'old_colonies.name as colony_name',
                        'property_masters.block_no',
                        // 'property_masters.plot_or_property_no',
                        // 'property_lease_details.presently_known_as',
                        DB::raw('case when property_masters.is_joint_property = 1 then spd.plot_flat_no else property_masters.plot_or_property_no end as plot_or_property_no'),
                        DB::raw('case when property_masters.is_joint_property = 1 then spd.presently_known_as else property_lease_details.presently_known_as end as presently_known_as'),

                        'app.is_objected',
                        'apm.updated_at as latest_moved_at',
                        'flats.unique_flat_id as flat_id', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        'flats.flat_number as flat_number', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        DB::raw("'DeedOfApartmentApplication' as model_name"), // Add model_name for the first query
                        // DB::raw("$serviceType3 as serviceType") // Added by Nitin 
                    );
                if ($request->status) {
                    $query3 = $query3->where('doa.status', ($request->status));
                } else {
                    $query3 = $query3->whereIn('doa.status', ($itemsIdArr));
                }

                // Add search filter if search.value is present
                if ($request->input('search.value')) {
                    $searchValue = $request->input('search.value');
                    $query3->where(function ($query) use ($searchValue) {
                        $query->where('doa.application_no', 'like', "%$searchValue%")
                            // ->orWhere('property_masters.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                                                        // ->orWhere('property_masters.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                            ->orWhereRaw("
                            CASE 
                                WHEN property_masters.is_joint_property = 1 
                                THEN spd.old_property_id 
                                ELSE property_masters.old_propert_id 
                            END LIKE ?
                        ", ["%$searchValue%"])  // Use the correct column alias here
                            ->orWhere(DB::raw('LOWER(old_colonies.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                            ->orWhere('property_masters.block_no', 'like', "%$searchValue%")  // Correctly reference block
                                                        // ->orWhere('property_masters.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhereRaw("
                            CASE 
                                WHEN property_masters.is_joint_property = 1 
                                THEN spd.plot_flat_no 
                                ELSE property_masters.plot_or_property_no 
                            END LIKE ?
                        ", ["%$searchValue%"])  // Correctly reference plot
                            ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere('flats.unique_flat_id', 'like', "%$searchValue%")  // Search by flat_id
                            ->orWhere('flats.flat_number', 'like', "%$searchValue%")    // Search by flat_number
                                                        // ->orWhere('property_lease_details.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhereRaw("
                            CASE 
                                WHEN property_masters.is_joint_property = 1 
                                THEN spd.presently_known_as 
                                ELSE property_lease_details.presently_known_as 
                            END LIKE ?
                        ", ["%$searchValue%"])
                            ->orWhere(DB::raw("'DeedOfApartmentApplication'"), 'like', "%$searchValue%") // Search by model_name
                            ->orWhere('doa.created_at', 'like', "%$searchValue%");
                    });
                }

                //Query for Conversion applications added by Nitin
                $serviceType4 = getServiceType('CONVERSION');
                $query4 = DB::table('conversion_applications as ca')
                    ->where('ca.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
                    ->leftJoin('property_masters', 'ca.property_master_id', '=', 'property_masters.id')
                    ->leftJoin('splited_property_details as spd', 'ca.splited_property_detail_id', '=', 'spd.id')
                    ->join('property_section_mappings as psm', function ($join) {

                        $join->on('property_masters.new_colony_name', 'psm.colony_id');
                        $join->whereColumn('property_masters.property_type', 'psm.property_type');
                        $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
                    })
                    ->join('sections', 'psm.section_id', 'sections.id')
                    ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
                    ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
                    ->leftJoin('applications as app', 'ca.application_no', '=', 'app.application_no')
                    ->leftJoinSub(
                        DB::table('application_movements')
                            ->select('application_no', DB::raw('MAX(id) as latest_created_at'))
                            ->groupBy('application_no'),
                        'latest_apm',
                        function ($join) {
                            $join->on('ca.application_no', '=', 'latest_apm.application_no');
                        }
                    )
                    ->leftJoin('application_movements as apm', function ($join) {
                        $join->on('ca.application_no', '=', 'apm.application_no')
                            ->on('apm.id', '=', 'latest_apm.latest_created_at');
                    })
                    ->leftJoinSub(
                        DB::table('application_statuses')
                            ->select(
                                'id',
                                'model_id',
                                'reg_app_no',
                                'service_type',
                                'is_mis_checked',
                                'is_scan_file_checked',
                                'is_uploaded_doc_checked',
                                'mis_checked_by',
                                'scan_file_checked_by',
                                'uploaded_doc_checked_by',
                                'created_at',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                            )
                            ->where('service_type', $serviceType4),
                        'latest_statuses',
                        function ($join) {
                            $join->on('ca.id', '=', 'latest_statuses.model_id')
                                ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                        }
                    )
                    ->whereIn('ca.section_id', $sections) //need to add secton id in all queries
                    ->select(
                        'ca.id',
                        'ca.created_at',
                        'ca.application_no',
                        'ca.status',
                        'sections.section_code',
                        'latest_statuses.is_mis_checked',
                        'latest_statuses.is_scan_file_checked',
                        'latest_statuses.is_uploaded_doc_checked',
                        'latest_statuses.mis_checked_by',
                        'latest_statuses.scan_file_checked_by',
                        'latest_statuses.uploaded_doc_checked_by',
                        // 'property_masters.old_propert_id as old_property_id',
                        DB::raw('case when property_masters.is_joint_property = 1 then spd.old_property_id else property_masters.old_propert_id end as old_property_id'),

                        'property_masters.new_colony_name',
                        'old_colonies.name as colony_name',
                        'property_masters.block_no',
                        // 'property_masters.plot_or_property_no',
                        // 'property_lease_details.presently_known_as',
                        DB::raw('case when property_masters.is_joint_property = 1 then spd.plot_flat_no else property_masters.plot_or_property_no end as plot_or_property_no'),
                        DB::raw('case when property_masters.is_joint_property = 1 then spd.presently_known_as else property_lease_details.presently_known_as end as presently_known_as'),

                        'app.is_objected',
                        'apm.updated_at as latest_moved_at',
                        DB::raw('NULL as flat_id'), // Add NULL for flat_id
                        DB::raw('NULL as flat_number'), // Add NULL for flat_number on dated 07/01/25 By Lalit Tiwari
                        DB::raw("'ConversionApplication' as model_name"), // Add model_name for the first query
                        // DB::raw("$serviceType4 as serviceType") // Added by Nitin 
                    );

                if ($request->status) {
                    $query4 = $query4->where('ca.status', ($request->status));
                } else {
                    $query4 = $query4->whereIn('ca.status', ($itemsIdArr));
                }

                // Add search filter if search.value is present
                if ($request->input('search.value')) {
                    $searchValue = $request->input('search.value');
                    $query4->where(function ($query) use ($searchValue) {
                        $query->where('ca.application_no', 'like', "%$searchValue%")
                                                        // ->orWhere('property_masters.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                            ->orWhereRaw("
                            CASE 
                                WHEN property_masters.is_joint_property = 1 
                                THEN spd.old_property_id 
                                ELSE property_masters.old_propert_id 
                            END LIKE ?
                        ", ["%$searchValue%"])  // Use the correct column alias here
                            ->orWhere(DB::raw('LOWER(old_colonies.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                            ->orWhere('property_masters.block_no', 'like', "%$searchValue%")  // Correctly reference block
                                                        // ->orWhere('property_masters.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhereRaw("
                            CASE 
                                WHEN property_masters.is_joint_property = 1 
                                THEN spd.plot_flat_no 
                                ELSE property_masters.plot_or_property_no 
                            END LIKE ?
                        ", ["%$searchValue%"])  // Correctly reference plot

                            ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                                                        // ->orWhere('property_lease_details.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhereRaw("
                            CASE 
                                WHEN property_masters.is_joint_property = 1 
                                THEN spd.presently_known_as 
                                ELSE property_lease_details.presently_known_as 
                            END LIKE ?
                        ", ["%$searchValue%"])

                            ->orWhere(DB::raw("'ConversionApplication'"), 'like', "%$searchValue%") // Search by model_name
                            ->orWhere('ca.created_at', 'like', "%$searchValue%");
                    });
                }

                // Query added for NOC application - Lalit Tiwari (20/March/2025)
                $serviceType5 = getServiceType('NOC');
                $query5 = DB::table('noc_applications as noc')
                    ->where('noc.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
                    ->leftJoin('property_masters as pm', 'noc.property_master_id', '=', 'pm.id')
                    ->leftJoin('splited_property_details as spd', 'noc.splitted_id', '=', 'spd.id')
                    ->leftJoin('flats', 'noc.flat_id', '=', 'flats.id')
                    ->join('property_section_mappings as psm', function ($join) {
                        $join->on('pm.new_colony_name', '=', 'psm.colony_id')
                            ->whereColumn('pm.property_type', 'psm.property_type')
                            ->whereColumn('pm.property_sub_type', 'psm.property_subtype');
                    })
                    ->join('sections', 'psm.section_id', '=', 'sections.id')
                    ->leftJoin('old_colonies as oc', 'pm.new_colony_name', '=', 'oc.id')
                    ->leftJoin('property_lease_details as pld', 'pm.id', '=', 'pld.property_master_id')
                    ->leftJoin('applications as app', 'noc.application_no', '=', 'app.application_no')
                    ->leftJoinSub(
                        DB::table('application_movements')
                            ->select('application_no', DB::raw('MAX(id) as latest_created_at'))
                            ->groupBy('application_no'),
                        'latest_apm',
                        function ($join) {
                            $join->on('noc.application_no', '=', 'latest_apm.application_no');
                        }
                    )
                    ->leftJoin('application_movements as apm', function ($join) {
                        $join->on('noc.application_no', '=', 'apm.application_no')
                            ->on('apm.id', '=', 'latest_apm.latest_created_at');
                    })
                    ->leftJoinSub(
                        DB::table('application_statuses')
                            ->select(
                                'id',
                                'model_id',
                                'reg_app_no',
                                'service_type',
                                'is_mis_checked',
                                'is_scan_file_checked',
                                'is_uploaded_doc_checked',
                                'mis_checked_by',
                                'scan_file_checked_by',
                                'uploaded_doc_checked_by',
                                'created_at',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                            )
                            ->where('service_type', $serviceType5),
                        'latest_statuses',
                        function ($join) {
                            $join->on('noc.id', '=', 'latest_statuses.model_id')
                                ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                        }
                    )
                    ->whereIn('noc.section_id', $sections) // Verify $sections is an array
                    ->select(
                        'noc.id',
                        'noc.created_at',
                        'noc.application_no',
                        'noc.status',
                        'sections.section_code',
                        'latest_statuses.is_mis_checked',
                        'latest_statuses.is_scan_file_checked',
                        'latest_statuses.is_uploaded_doc_checked',
                        'latest_statuses.mis_checked_by',
                        'latest_statuses.scan_file_checked_by',
                        'latest_statuses.uploaded_doc_checked_by',
                        // 'pm.old_propert_id as old_property_id', // Fixed alias
                        DB::raw('case when pm.is_joint_property = 1 then spd.old_property_id else pm.old_propert_id end as old_property_id'),

                        'pm.new_colony_name',
                        'oc.name as colony_name',
                        'pm.block_no',
                        // 'pm.plot_or_property_no',
                        // 'pld.presently_known_as',
                        DB::raw('case when pm.is_joint_property = 1 then spd.plot_flat_no else pm.plot_or_property_no end as plot_or_property_no'),
                        DB::raw('case when pm.is_joint_property = 1 then spd.presently_known_as else pld.presently_known_as end as presently_known_as'),

                        'app.is_objected',
                        'apm.updated_at as latest_moved_at',
                        'flats.unique_flat_id as flat_id', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        'flats.flat_number as flat_number', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        DB::raw("'NocApplication' as model_name"), // Add model_name for the first query
                        // DB::raw("$serviceType5 as serviceType") // Added by Nitin 
                    );
                if ($request->status) {
                    $query5 = $query5->where('noc.status', ($request->status));
                } else {
                    $query5 = $query5->whereIn('noc.status', ($itemsIdArr));
                }

                // Add search filter if search.value is present
                if ($request->input('search.value')) {
                    $searchValue = $request->input('search.value');
                    $query5->where(function ($query) use ($searchValue) {
                        $query->where('noc.application_no', 'like', "%$searchValue%")
                            // ->orWhere('pm.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                            ->orWhereRaw("
                            CASE 
                                WHEN pm.is_joint_property = 1 
                                THEN spd.old_property_id 
                                ELSE pm.old_propert_id 
                            END LIKE ?
                        ", ["%$searchValue%"])  // Use the correct column alias here
                            ->orWhere(DB::raw('LOWER(oc.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                            ->orWhere('pm.block_no', 'like', "%$searchValue%")  // Correctly reference block
                                                        // ->orWhere('pm.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhereRaw("
                            CASE 
                                WHEN pm.is_joint_property = 1 
                                THEN spd.plot_flat_no 
                                ELSE pm.plot_or_property_no 
                            END LIKE ?
                        ", ["%$searchValue%"])  // Correctly reference plot
                            ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                                                        // ->orWhere('pld.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhereRaw("
                            CASE 
                                WHEN pm.is_joint_property = 1 
                                THEN spd.presently_known_as 
                                ELSE pld.presently_known_as 
                            END LIKE ?
                        ", ["%$searchValue%"])
                            ->orWhere(DB::raw("'NocApplication'"), 'like', "%$searchValue%") // Search by model_name
                            ->orWhere('noc.created_at', 'like', "%$searchValue%");
                    });
                }

                $clonedQuery1 = (clone $query1);
                $clonedQuery2 = (clone $query2);
                $clonedQuery3 = (clone $query3);
                $clonedQuery4 = (clone $query4);
                $clonedQuery5 = (clone $query5);

                // Combine all three queries using UNION
                $combinedQuery = $clonedQuery1->union($clonedQuery2)->union($clonedQuery3)->union($clonedQuery4)->union($clonedQuery5);
                break;


                // dd($combinedQuery);
        }


        $limit = $request->input('length');
        $start = $request->input('start');
        if ($request->input('order.0.column')) {
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
        } else {
            $order = 'created_at';
            $dir = 'desc';
        }

        $totalData = $combinedQuery->count();
        $totalFiltered = $totalData;
        $aggregatedQuery = DB::table(DB::raw("({$combinedQuery->toSql()}) as combined"))
            ->mergeBindings($combinedQuery);
        $allApplicationsQuery = clone $aggregatedQuery;
        $paginatedQuery = clone $aggregatedQuery;

        $allApplications = $allApplicationsQuery
            // ->whereIn('status', [getServiceType('APP_NEW'), getServiceType('APP_IP')])  //not working
            ->select('application_no', 'created_at', 'status')
            ->get();
        // dd($allApplications);
        $applications = $paginatedQuery->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = [];
        $showSendProofReadingLink = false;
        // dd($applications, $allApplications);

        foreach ($applications as $key => $application) {
            if ($application->status) {
                // Get the service code only once to avoid repetitive calls
                $serviceCode = getServiceCodeById($application->status);

                // Check if the application status is 'objected', 'rejected', or 'approved'
                if (in_array($serviceCode, ['APP_OBJ', 'APP_REJ', 'APP_APR'])) {
                    $showSendProofReadingLink = false;
                } else {
                    // Check if the proof reading link has been sent at least once
                    $isProofReadingLinkSent = ApplicationAppointmentLink::where('application_no', $application->application_no)->exists();

                    // Show the proof reading link if it has been sent at least once
                    $showSendProofReadingLink = $isProofReadingLinkSent;
                }
            }
            $model = base64_encode($application->model_name);
            $mis_checked_by = User::find($application->mis_checked_by);
            $scan_file_checked_by = User::find($application->scan_file_checked_by);
            $uploaded_doc_checked_by = User::find($application->uploaded_doc_checked_by);
            $nestedData['id'] = $key + 1;
            $applicationNumber = $application->application_no;


            // $userCurrentApplication = userCurrentActionableApplication();
            /* if (Auth::user()->roles[0]->name == 'section-officer')
                $userCurrentApplication = $this->userCurrentActionableApplications($allApplications);
            else */
            // $userCurrentApplication = userCurrentActionableApplication();
            // dd($userCurrentApplication);

            $appMovementCount = ApplicationMovement::where('application_no', $application->application_no)->count();
            if (Auth::user()->roles[0]->name == 'section-officer' && getServiceCodeById($application->status) == 'APP_NEW') {
                if ($this->checkOfficalCanViewTheApplication($application->application_no)) {
                    $applicationNumber = '<div class="d-flex gap-2 align-items-center cursor-pointer text-decoration-underline" onclick="handleViewApplication(\'' . $application->application_no . '\', \'' . $model . '\', ' . $application->id . ')">' . $application->application_no . '<div class="alertGreen"></div></div>';
                } else {
                    $applicationNumber = '<div class="d-flex gap-2 align-items-center cursor-pointer text-decoration-underline" onclick="handleViewApplication(\'' . $application->application_no . '\', \'' . $model . '\', ' . $application->id . ')">' . $application->application_no . '<div class="alertDot"></div></div>';
                }
            } else if (Auth::user()->roles[0]->name == 'section-officer' && getServiceCodeById($application->status) == 'APP_IP' &&  $appMovementCount == 1) {
                if ($this->checkOfficalCanViewTheApplication($application->application_no)) {
                    $applicationNumber = '<div class="d-flex gap-2 align-items-center cursor-pointer text-decoration-underline" onclick="handleViewApplication(\'' . $application->application_no . '\', \'' . $model . '\', ' . $application->id . ')">' . $application->application_no . '<div class="alertGreen"></div></div>';
                } else {
                    $applicationNumber = '<div class="d-flex gap-2 align-items-center cursor-pointer text-decoration-underline" onclick="handleViewApplication(\'' . $application->application_no . '\', \'' . $model . '\', ' . $application->id . ')">' . $application->application_no . '<div class="alertDot"></div></div>';
                }
            } else {
                $latestRecord = ApplicationMovement::where('application_no', $application->application_no)
                    ->latest('created_at')
                    ->first();
                if (!is_null($latestRecord) && $latestRecord->assigned_to == Auth::user()->id) {
                    if ($this->checkOfficalCanViewTheApplication($application->application_no)) {
                        $applicationNumber = '<div class="d-flex gap-2 align-items-center cursor-pointor text-decoration-underline" onclick="handleViewApplication(\'' . $application->application_no . '\', \'' . $model . '\', ' . $application->id . ')">' . $application->application_no . '<div class="alertGreen"></div></div>';
                    } else {
                        $applicationNumber = '<div class="d-flex gap-2 align-items-center cursor-pointer text-decoration-underline" onclick="handleViewApplication(\'' . $application->application_no . '\', \'' . $model . '\', ' . $application->id . ')">' . $application->application_no . '<div class="alertDot"></div></div>';
                    }
                } else {
                    $applicationNumber =  '<div class="d-flex gap-2 align-items-center cursor-pointer text-decoration-underline" onclick="handleViewApplication(\'' . $application->application_no . '\', \'' . $model . '\', ' . $application->id . ')">' . $application->application_no . '</div>';
                }
            }
           
            $nestedData['application_no'] = $applicationNumber;
            //Display current file location user name with user role by Lalit (15/09/2025)
            $latest = ApplicationMovement::with(['assignedTo', 'assignedRole'])
                ->where('application_no', $application->application_no)
                ->latest('id')
                ->first(['id', 'assigned_to', 'assigned_to_role']);
            if ($latest && $latest->assignedTo) {
                $userName = $latest->assignedTo->name;
                $roleName = $latest->assignedRole->title
                    ?? $latest->assignedTo->roles->pluck('title')->first()
                    ?? 'NA';

                $nestedData['file_current_location'] = '<div class="d-flex flex-column">
                    <span>' . e($userName) . '</span>
                    <span class="text-secondary">' . ucwords(str_replace('-', ' ', $roleName)) . '</span>
                </div>';
            } else {
                $nestedData['file_current_location'] = 'NA';
            }
            // dd($application);
            // $nestedData['old_property_id'] = $application->old_property_id ?? $application->old_propert_id;
            $nestedData['old_property_id'] = $application->old_property_id ?? "N/A";
            $nestedData['new_colony_name'] = $application->block_no . '/' . $application->plot_or_property_no . '/' . $application->colony_name;
            // $nestedData['block_no'] = $application->block_no;
            // $nestedData['plot_or_property_no'] = $application->plot_or_property_no;

            $nestedData['presently_known_as'] = $application->presently_known_as;
            $flatHTML = '';
            if (!empty($application->flat_id)) {
                $flatHTML .= '<div class="d-flex gap-2 align-items-center">' . $application->flat_number . '</div><span class="text-secondary">(' . $application->flat_id . ')</span>';
            } else {
                $flatHTML .= '<div>NA</div>';
            }
            $nestedData['flat_id'] =   $flatHTML;
            $nestedData['section'] = $application->section_code;

            switch ($application->model_name) {
                case 'MutationApplication':
                    $appliedFor = 'Mutation';
                    break;
                case 'LandUseChangeApplication':
                    $appliedFor = 'LUC';
                    break;
                case 'DeedOfApartmentApplication':
                    $appliedFor = 'DOA';
                    break;
                case 'ConversionApplication':
                    $appliedFor = 'CONVERSION';
                    break;
                case 'NocApplication':
                    $appliedFor = 'NOC';
                    break;
                default:
                    // Default action
                    break;
            }
            //for getting status
            $item = getStatusDetailsById($application->status);
            $itemCode = $item->item_code;
            $itemName = $item->item_name;
            $itemColor = $item->color_code;
            $statusClasses = [
                'APP_REJ' => 'statusRejected',
                'APP_NEW' => 'statusNew',
                'APP_IP' => 'statusSecondary',
                'RS_REW' => 'text-white bg-secondary',
                'RS_PEN' => 'text-info bg-light-info',
                'APP_APR' => 'landtypeFreeH',
                'APP_OBJ' => 'statusObject',
                'APP_HOLD' => 'statusHold',
            ];
            $class = $statusClasses[$itemCode] ?? 'text-secondary bg-light';
            $nestedData['applied_for'] = '<div class="d-flex align-items-center gap-1">
                <label class="badge bg-info mx-1">' . $appliedFor . '</label>';
                
                if ($application->is_objected == 1) {
                    $nestedData['applied_for'] .= '<div class="alertYellow"></div>';
                }
                
            $nestedData['applied_for'] .= '</div>';
            $nestedData['activity'] = [
                'mis' => !empty($application->is_mis_checked) ? $application->is_mis_checked : 'NA',
                'scanned_files' => !empty($application->is_scan_file_checked) ? $application->is_scan_file_checked : 'NA',
                'uploaded_doc' => !empty($application->is_uploaded_doc_checked) ? $application->is_uploaded_doc_checked : 'NA',
                'mis_checked_by' => !empty($application->mis_checked_by) ? $mis_checked_by->name : '',
                'scan_file_checked_by' => !empty($application->scan_file_checked_by) ? $scan_file_checked_by->name : '',
                'uploaded_doc_checked_by' => !empty($application->uploaded_doc_checked_by) ? $uploaded_doc_checked_by->name : '',
                'mis_color_code' => !empty(getServiceTypeColorCode('MIS_CHECK')) ? getServiceTypeColorCode('MIS_CHECK') : '',
                'scan_file_color_code' => !empty(getServiceTypeColorCode('SCAN_CHECK')) ? getServiceTypeColorCode('SCAN_CHECK') : '',
                'uploaded_doc_color_code' => !empty(getServiceTypeColorCode('UP_DOC_CHE')) ? getServiceTypeColorCode('UP_DOC_CHE') : '',
            ];

            $nestedData['status'] = '<span class="highlight_value ' . $class . '">' . ucwords($itemName) . '</span>';
            //$model = base64_encode($application->model_name);

            // Prepare actions
            $action = '<div class="d-flex gap-2">';
            $action .= '<button type="button" class="btn btn-primary px-5" onclick="handleViewApplication(\'' . $application->application_no . '\', \'' . $model . '\', ' . $application->id . ')">View</button>
                        <button type="button" class="btn btn-success" onclick="getFileMovement(\'' . $application->application_no . '\', this)">
                            File Movement
                        </button>';

            // Add meeting link button (commented as per discussion, not required at deputy end, IT Cell will do this) - SOURAV CHAUHAN (27 Feb 2025)
            // if (Auth::user()->roles[0]->name == 'deputy-lndo' && $appliedFor != "LUC" && $showSendProofReadingLink) {
            //     $action .= '<button type="button" class="btn btn-secondary px-5 send-meeting-link" data-application-id="' . $application->id . '" data-application-model_name="' . $application->model_name . '" data-application-no="' . $application->application_no . '">Send Meeting Link</button>';
            // }
            $action .= '</div>';

            $nestedData['action'] = $action;
            $nestedData['created_at'] = Carbon::parse($application->created_at)
                ->setTimezone('Asia/Kolkata')
                ->format('d M Y h:m:s');
            $nestedData['latest_moved_at'] = Carbon::parse($application->latest_moved_at)
                ->setTimezone('Asia/Kolkata')
                ->format('d M Y h:m:s');

            $data[] = $nestedData;
        }

        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        ];


        // dd($json_data);

        return response()->json($json_data);
    }


    //For getting my applications - Sourav Chauhan (4 May 2025)
    public function getMyApplications(Request $request)
    {
        $itemsIdArr = [];
        $items = getApplicationStatusList(true, true);
        if (count($items) > 0) {
            foreach ($items as $key => $item) {
                $itemsIdArr[] = $item->id;
            }
        }

        $user = Auth::user();
        $sections = $user->sections->pluck('id');
        $columns = [
            'id', // index 0
            'application_no', // index 1
            'old_property_id', // index 2
            'new_colony_name', // index 3
            'block_no', // index 4
            'plot_or_property_no', // index 5
            'flat_id', // index 6
            'presently_known_as', // index 7
            'section_code', // index 8
            'model_name', // index 9
            '', // index 10
            '', // index 11
            'created_at', // index 12
        ];

        // $totalApplicationsQuery = ApplicationMovement::where('assigned_to', $user->id);
        // $latestIdByApplication = ApplicationMovement::selectRaw('MAX(id) as id')
        //     ->groupBy('application_no')
        //     ->pluck('id')->toArray();

        $userId = $user->id;
        $allMovements = ApplicationMovement::where('service_type', '!=', 1370)->orderBy('created_at', 'desc')
            ->get();
        $latestMovements = $allMovements->unique('application_no');
        $userAssigned = $latestMovements->where('assigned_to', $userId);
        $allAssignedApplications = $userAssigned->pluck('application_no')->toArray();






        $serviceType1 = getServiceType('SUB_MUT'); // Ensure this function is defined and works properly.

        $query1 = DB::table('mutation_applications as ma')
            ->where('ma.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
            ->leftJoin('property_masters as pm', 'ma.property_master_id', '=', 'pm.id')
            ->leftJoin('splited_property_details as spd', 'ma.splitted_id', '=', 'spd.id')
            ->join('property_section_mappings as psm', function ($join) {
                $join->on('pm.new_colony_name', '=', 'psm.colony_id')
                    ->whereColumn('pm.property_type', 'psm.property_type')
                    ->whereColumn('pm.property_sub_type', 'psm.property_subtype');
            })
            ->join('sections', 'psm.section_id', '=', 'sections.id')
            ->leftJoin('old_colonies as oc', 'pm.new_colony_name', '=', 'oc.id')
            ->leftJoin('property_lease_details as pld', 'pm.id', '=', 'pld.property_master_id')
            ->leftJoin('applications as app', 'ma.application_no', '=', 'app.application_no')
            ->leftJoinSub(
                DB::table('application_statuses')
                    ->select(
                        'id',
                        'model_id',
                        'reg_app_no',
                        'service_type',
                        'is_mis_checked',
                        'is_scan_file_checked',
                        'is_uploaded_doc_checked',
                        'mis_checked_by',
                        'scan_file_checked_by',
                        'uploaded_doc_checked_by',
                        'created_at',
                        DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                    )
                    ->where('service_type', $serviceType1),
                'latest_statuses',
                function ($join) {
                    $join->on('ma.id', '=', 'latest_statuses.model_id')
                        ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                }
            )
            ->whereIn('ma.application_no', $allAssignedApplications)
            ->select(
                'ma.id',
                'ma.created_at',
                'ma.application_no',
                'ma.status',
                'sections.section_code',
                'latest_statuses.is_mis_checked',
                'latest_statuses.is_scan_file_checked',
                'latest_statuses.is_uploaded_doc_checked',
                'latest_statuses.mis_checked_by',
                'latest_statuses.scan_file_checked_by',
                'latest_statuses.uploaded_doc_checked_by',
                // 'pm.old_propert_id as old_property_id', // Fixed alias
                DB::raw('case when pm.is_joint_property = 1 then spd.old_property_id else pm.old_propert_id end as old_property_id'),

                'pm.new_colony_name',
                'oc.name as colony_name',
                'pm.block_no',
                // 'pm.plot_or_property_no',
                // 'pld.presently_known_as',
                DB::raw('case when pm.is_joint_property = 1 then spd.plot_flat_no else pm.plot_or_property_no end as plot_or_property_no'),
                DB::raw('case when pm.is_joint_property = 1 then spd.presently_known_as else pld.presently_known_as end as presently_known_as'),

                'app.is_objected',
                DB::raw('NULL as flat_id'), // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                DB::raw('NULL as flat_number'), // Add NULL for flat_number on dated 07/01/25 By Lalit Tiwari
                DB::raw("'MutationApplication' as model_name") // Add model_name for the first query
            );
        if ($request->status) {
            $query1 = $query1->where('ma.status', ($request->status));
        } else {
            $query1 = $query1->whereIn('ma.status', ($itemsIdArr));
        }

        // Add search filter if search.value is present
        if ($request->input('search.value')) {
            $searchValue = $request->input('search.value');
            $query1->where(function ($query) use ($searchValue) {
                $query->where('ma.application_no', 'like', "%$searchValue%")
                    // ->orWhere('pm.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                    ->orWhereRaw("CASE WHEN pm.is_joint_property = 1 THEN spd.old_property_id ELSE pm.old_propert_id END LIKE ?", ["%$searchValue%"])

                    ->orWhere(DB::raw('LOWER(oc.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                    ->orWhere('pm.block_no', 'like', "%$searchValue%")  // Correctly reference block
                    // ->orWhere('pm.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhereRaw("CASE WHEN pm.is_joint_property = 1 THEN spd.plot_flat_no ELSE pm.plot_or_property_no END LIKE ?", ["%$searchValue%"])

                    ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                    // ->orWhere('pld.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                    ->orWhereRaw("CASE WHEN pm.is_joint_property = 1 THEN spd.presently_known_as ELSE pld.presently_known_as END LIKE ?", ["%$searchValue%"])

                    ->orWhere(DB::raw("'MutationApplication'"), 'like', "%$searchValue%") // Search by model_name
                    ->orWhere('ma.created_at', 'like', "%$searchValue%");
            });
        }

        // Query for land use changed applications
        $serviceType2 = getServiceType('LUC');
        $query2 = DB::table('land_use_change_applications as lca')
            ->where('lca.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
            ->leftJoin('property_masters', 'lca.property_master_id', '=', 'property_masters.id')
            ->leftJoin('splited_property_details as spd', 'lca.splited_property_detail_id', '=', 'spd.id')

            ->join('property_section_mappings as psm', function ($join) {
                $join->on('property_masters.new_colony_name', 'psm.colony_id');
                $join->whereColumn('property_masters.property_type', 'psm.property_type');
                $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
            })
            ->join('sections', 'psm.section_id', 'sections.id')
            ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
            ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
            ->leftJoin('applications as app', 'lca.application_no', '=', 'app.application_no')
            ->leftJoinSub(
                DB::table('application_statuses')
                    ->select(
                        'id',
                        'model_id',
                        'reg_app_no',
                        'service_type',
                        'is_mis_checked',
                        'is_scan_file_checked',
                        'is_uploaded_doc_checked',
                        'mis_checked_by',
                        'scan_file_checked_by',
                        'uploaded_doc_checked_by',
                        'created_at',
                        DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                    )
                    ->where('service_type', $serviceType2),
                'latest_statuses',
                function ($join) {
                    $join->on('lca.id', '=', 'latest_statuses.model_id')
                        ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                }
            )
            ->whereIn('lca.application_no', $allAssignedApplications)
            ->select(
                'lca.id',
                'lca.created_at',
                DB::raw('coalesce(lca.application_no,"0") as application_no'),
                'lca.status',
                'sections.section_code',
                'latest_statuses.is_mis_checked',
                'latest_statuses.is_scan_file_checked',
                'latest_statuses.is_uploaded_doc_checked',
                'latest_statuses.mis_checked_by',
                'latest_statuses.scan_file_checked_by',
                'latest_statuses.uploaded_doc_checked_by',
                // 'property_masters.old_propert_id as old_property_id',
                DB::raw('case when property_masters.is_joint_property = 1 then spd.old_property_id else property_masters.old_propert_id end as old_property_id'),

                'property_masters.new_colony_name',
                'old_colonies.name as colony_name',
                'property_masters.block_no',
                // 'property_masters.plot_or_property_no',
                // 'property_lease_details.presently_known_as',
                DB::raw('case when property_masters.is_joint_property = 1 then spd.plot_flat_no else property_masters.plot_or_property_no end as plot_or_property_no'),
                DB::raw('case when property_masters.is_joint_property = 1 then spd.presently_known_as else property_lease_details.presently_known_as end as presently_known_as'),

                'app.is_objected',
                DB::raw('NULL as flat_id'), // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                DB::raw('NULL as flat_number'), // Add NULL for flat_number on dated 07/01/25 By Lalit Tiwari
                DB::raw("'LandUseChangeApplication' as model_name") // Add model_name for the first query
            );

        if ($request->status) {
            $query2 = $query2->where('lca.status', ($request->status));
        } else {
            $query2 = $query2->whereIn('lca.status', ($itemsIdArr));
        }

        // Add search filter if search.value is present
        if ($request->input('search.value')) {
            $searchValue = $request->input('search.value');
            $query2->where(function ($query) use ($searchValue) {
                $query->where('lca.application_no', 'like', "%$searchValue%")
                    // ->orWhere('property_masters.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                    ->orWhereRaw("CASE WHEN property_masters.is_joint_property = 1 THEN spd.old_property_id ELSE property_masters.old_propert_id END LIKE ?", ["%$searchValue%"])

                    ->orWhere(DB::raw('LOWER(old_colonies.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                    ->orWhere('property_masters.block_no', 'like', "%$searchValue%")  // Correctly reference block
                    // ->orWhere('property_masters.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhereRaw("CASE WHEN property_masters.is_joint_property = 1 THEN spd.plot_flat_no ELSE property_masters.plot_or_property_no END LIKE ?", ["%$searchValue%"])

                    ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                    // ->orWhere('property_lease_details.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                    // ->orWhereRaw("CASE WHEN property_masters.is_joint_property = 1 THEN spd.presently_known_as ELSE pld.presently_known_as END LIKE ?", ["%$searchValue%"])
                    ->orWhereRaw(
                    "CASE WHEN property_masters.is_joint_property = 1 THEN spd.presently_known_as ELSE property_lease_details.presently_known_as END LIKE ?",
                    ["%$searchValue%"]
                    )
                    ->orWhere(DB::raw("'LandUseChangeApplication'"), 'like', "%$searchValue%") // Search by model_name
                    ->orWhere('lca.created_at', 'like', "%$searchValue%");
            });
        }

        //Query for Deed Of Apartment applications
        $serviceType3 = getServiceType('DOA');
        $query3 = DB::table('deed_of_apartment_applications as doa')
            ->where('doa.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
            ->leftJoin('property_masters', 'doa.property_master_id', '=', 'property_masters.id')
            ->leftJoin('splited_property_details as spd', 'doa.splited_property_detail_id', '=', 'spd.id')
            ->join('property_section_mappings as psm', function ($join) {

                $join->on('property_masters.new_colony_name', 'psm.colony_id');
                $join->whereColumn('property_masters.property_type', 'psm.property_type');
                $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
            })
            ->join('sections', 'psm.section_id', 'sections.id')
            ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
            ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
            ->leftJoin('flats', 'doa.flat_id', '=', 'flats.id')
            ->leftJoin('applications as app', 'doa.application_no', '=', 'app.application_no')
            ->leftJoinSub(
                DB::table('application_statuses')
                    ->select(
                        'id',
                        'model_id',
                        'reg_app_no',
                        'service_type',
                        'is_mis_checked',
                        'is_scan_file_checked',
                        'is_uploaded_doc_checked',
                        'mis_checked_by',
                        'scan_file_checked_by',
                        'uploaded_doc_checked_by',
                        'created_at',
                        DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                    )
                    ->where('service_type', $serviceType3),
                'latest_statuses',
                function ($join) {
                    $join->on('doa.id', '=', 'latest_statuses.model_id')
                        ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                }
            )
            ->whereIn('doa.application_no', $allAssignedApplications)
            ->select(
                'doa.id',
                'doa.created_at',
                'doa.application_no',
                'doa.status',
                'sections.section_code',
                'latest_statuses.is_mis_checked',
                'latest_statuses.is_scan_file_checked',
                'latest_statuses.is_uploaded_doc_checked',
                'latest_statuses.mis_checked_by',
                'latest_statuses.scan_file_checked_by',
                'latest_statuses.uploaded_doc_checked_by',
                // 'property_masters.old_propert_id as old_property_id',
                DB::raw('case when property_masters.is_joint_property = 1 then spd.old_property_id else property_masters.old_propert_id end as old_propert_id'),
                'property_masters.new_colony_name',
                'old_colonies.name as colony_name',
                'property_masters.block_no',
                // 'property_masters.plot_or_property_no',
                // 'property_lease_details.presently_known_as',
                DB::raw('case when property_masters.is_joint_property = 1 then spd.plot_flat_no else property_masters.plot_or_property_no end as plot_or_property_no'),
                DB::raw('case when property_masters.is_joint_property = 1 then spd.presently_known_as else property_lease_details.presently_known_as end as presently_known_as'),
                // 'property_lease_details.presently_known_as',
                'app.is_objected',
                'flats.unique_flat_id as flat_id', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                'flats.flat_number as flat_number', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                DB::raw("'DeedOfApartmentApplication' as model_name") // Add model_name for the first query
            );
        if ($request->status) {
            $query3 = $query3->where('doa.status', ($request->status));
        } else {
            $query3 = $query3->whereIn('doa.status', ($itemsIdArr));
        }

        // Add search filter if search.value is present
        if ($request->input('search.value')) {
            $searchValue = $request->input('search.value');
            $query3->where(function ($query) use ($searchValue) {
                $query->where('doa.application_no', 'like', "%$searchValue%")
                    // ->orWhere('property_masters.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                    ->orWhereRaw("
                        CASE 
                            WHEN property_masters.is_joint_property = 1 
                            THEN spd.old_property_id 
                            ELSE property_masters.old_propert_id 
                        END LIKE ?
                    ", ["%$searchValue%"])  // Use the correct column alias here

                    ->orWhere(DB::raw('LOWER(old_colonies.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                    ->orWhere('property_masters.block_no', 'like', "%$searchValue%")  // Correctly reference block
                    // ->orWhere('property_masters.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhereRaw("
                        CASE 
                            WHEN property_masters.is_joint_property = 1 
                            THEN spd.plot_flat_no 
                            ELSE property_masters.plot_or_property_no 
                        END LIKE ?
                    ", ["%$searchValue%"])  // Correctly reference plot
                    ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhere('flats.unique_flat_id', 'like', "%$searchValue%")  // Search by flat_id
                    ->orWhere('flats.flat_number', 'like', "%$searchValue%")    // Search by flat_number
                    ->orWhere('property_lease_details.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                    // ->orWhereRaw("
                    //     CASE 
                    //         WHEN property_masters.is_joint_property = 1 
                    //         THEN spd.presently_known_as 
                    //         ELSE property_lease_details.presently_known_as 
                    //     END LIKE ?
                    // ", ["%$searchValue%"])

                    ->orWhere(DB::raw("'DeedOfApartmentApplication'"), 'like', "%$searchValue%") // Search by model_name
                    ->orWhere('doa.created_at', 'like', "%$searchValue%");
            });
        }




        //Query for Conversion applications added by Nitin
        $serviceType4 = getServiceType('CONVERSION');
        $query4 = DB::table('conversion_applications as ca')
            ->where('ca.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
            ->leftJoin('property_masters', 'ca.property_master_id', '=', 'property_masters.id')
            ->leftJoin('splited_property_details as spd', 'ca.splited_property_detail_id', '=', 'spd.id')
            ->join('property_section_mappings as psm', function ($join) {

                $join->on('property_masters.new_colony_name', 'psm.colony_id');
                $join->whereColumn('property_masters.property_type', 'psm.property_type');
                $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
            })
            ->join('sections', 'psm.section_id', 'sections.id')
            ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
            ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
            ->leftJoin('applications as app', 'ca.application_no', '=', 'app.application_no')
            ->leftJoinSub(
                DB::table('application_statuses')
                    ->select(
                        'id',
                        'model_id',
                        'reg_app_no',
                        'service_type',
                        'is_mis_checked',
                        'is_scan_file_checked',
                        'is_uploaded_doc_checked',
                        'mis_checked_by',
                        'scan_file_checked_by',
                        'uploaded_doc_checked_by',
                        'created_at',
                        DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                    )
                    ->where('service_type', $serviceType4),
                'latest_statuses',
                function ($join) {
                    $join->on('ca.id', '=', 'latest_statuses.model_id')
                        ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                }
            )
            ->whereIn('ca.application_no', $allAssignedApplications)
            ->select(
                'ca.id',
                'ca.created_at',
                'ca.application_no',
                'ca.status',
                'sections.section_code',
                'latest_statuses.is_mis_checked',
                'latest_statuses.is_scan_file_checked',
                'latest_statuses.is_uploaded_doc_checked',
                'latest_statuses.mis_checked_by',
                'latest_statuses.scan_file_checked_by',
                'latest_statuses.uploaded_doc_checked_by',
                // 'property_masters.old_propert_id as old_property_id',
                DB::raw('case when property_masters.is_joint_property = 1 then spd.old_property_id else property_masters.old_propert_id end as old_propert_id'),

                'property_masters.new_colony_name',
                'old_colonies.name as colony_name',
                'property_masters.block_no',
                // 'property_masters.plot_or_property_no',
                // 'property_lease_details.presently_known_as',
                DB::raw('case when property_masters.is_joint_property = 1 then spd.plot_flat_no else property_masters.plot_or_property_no end as plot_or_property_no'),
                DB::raw('case when property_masters.is_joint_property = 1 then spd.presently_known_as else property_lease_details.presently_known_as end as presently_known_as'),

                'app.is_objected',
                DB::raw('NULL as flat_id'), // Add NULL for flat_id
                DB::raw('NULL as flat_number'), // Add NULL for flat_number on dated 07/01/25 By Lalit Tiwari
                DB::raw("'ConversionApplication' as model_name") // Add model_name for the first query
            );

        if ($request->status) {
            $query4 = $query4->where('ca.status', ($request->status));
        } else {
            $query4 = $query4->whereIn('ca.status', ($itemsIdArr));
        }

        // Add search filter if search.value is present
        if ($request->input('search.value')) {
            $searchValue = $request->input('search.value');
            $query4->where(function ($query) use ($searchValue) {
                $query->where('ca.application_no', 'like', "%$searchValue%")
                    // ->orWhere('property_masters.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                    ->orWhereRaw("
                        CASE 
                            WHEN property_masters.is_joint_property = 1 
                            THEN spd.old_property_id 
                            ELSE property_masters.old_propert_id 
                        END LIKE ?
                    ", ["%$searchValue%"])  // Use the correct column alias here

                    ->orWhere(DB::raw('LOWER(old_colonies.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                    ->orWhere('property_masters.block_no', 'like', "%$searchValue%")  // Correctly reference block
                    // ->orWhere('property_masters.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhereRaw("
                        CASE 
                            WHEN property_masters.is_joint_property = 1 
                            THEN spd.plot_flat_no 
                            ELSE property_masters.plot_or_property_no 
                        END LIKE ?
                    ", ["%$searchValue%"])  // Correctly reference plot

                    ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                    // ->orWhere('property_lease_details.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                    ->orWhereRaw("
                        CASE 
                            WHEN property_masters.is_joint_property = 1 
                            THEN spd.presently_known_as 
                            ELSE property_lease_details.presently_known_as 
                        END LIKE ?
                    ", ["%$searchValue%"])

                    ->orWhere(DB::raw("'ConversionApplication'"), 'like', "%$searchValue%") // Search by model_name
                    ->orWhere('ca.created_at', 'like', "%$searchValue%");
            });
        }

        // Query added for NOC application - Lalit Tiwari (20/March/2025)
        $serviceType5 = getServiceType('NOC');
        $query5 = DB::table('noc_applications as noc')
            ->where('noc.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
            ->leftJoin('property_masters as pm', 'noc.property_master_id', '=', 'pm.id')
            ->leftJoin('splited_property_details as spd', 'noc.splitted_id', '=', 'spd.id')
            ->leftJoin('flats', 'noc.flat_id', '=', 'flats.id')
            ->join('property_section_mappings as psm', function ($join) {
                $join->on('pm.new_colony_name', '=', 'psm.colony_id')
                    ->whereColumn('pm.property_type', 'psm.property_type')
                    ->whereColumn('pm.property_sub_type', 'psm.property_subtype');
            })
            ->join('sections', 'psm.section_id', '=', 'sections.id')
            ->leftJoin('old_colonies as oc', 'pm.new_colony_name', '=', 'oc.id')
            ->leftJoin('property_lease_details as pld', 'pm.id', '=', 'pld.property_master_id')
            ->leftJoin('applications as app', 'noc.application_no', '=', 'app.application_no')
            ->leftJoinSub(
                DB::table('application_statuses')
                    ->select(
                        'id',
                        'model_id',
                        'reg_app_no',
                        'service_type',
                        'is_mis_checked',
                        'is_scan_file_checked',
                        'is_uploaded_doc_checked',
                        'mis_checked_by',
                        'scan_file_checked_by',
                        'uploaded_doc_checked_by',
                        'created_at',
                        DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                    )
                    ->where('service_type', $serviceType5),
                'latest_statuses',
                function ($join) {
                    $join->on('noc.id', '=', 'latest_statuses.model_id')
                        ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                }
            )
            ->whereIn('noc.application_no', $allAssignedApplications)
            ->select(
                'noc.id',
                'noc.created_at',
                'noc.application_no',
                'noc.status',
                'sections.section_code',
                'latest_statuses.is_mis_checked',
                'latest_statuses.is_scan_file_checked',
                'latest_statuses.is_uploaded_doc_checked',
                'latest_statuses.mis_checked_by',
                'latest_statuses.scan_file_checked_by',
                'latest_statuses.uploaded_doc_checked_by',
                // 'pm.old_propert_id as old_property_id', // Fixed alias
                DB::raw('case when pm.is_joint_property = 1 then spd.old_property_id else pm.old_propert_id end as old_propert_id'),

                'pm.new_colony_name',
                'oc.name as colony_name',
                'pm.block_no',
                // 'pm.plot_or_property_no',
                // 'pld.presently_known_as',
                DB::raw('case when pm.is_joint_property = 1 then spd.plot_flat_no else pm.plot_or_property_no end as plot_or_property_no'),
                DB::raw('case when pm.is_joint_property = 1 then spd.presently_known_as else pld.presently_known_as end as presently_known_as'),

                'app.is_objected',
                'flats.unique_flat_id as flat_id', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                'flats.flat_number as flat_number', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                DB::raw("'NocApplication' as model_name") // Add model_name for the first query
            );
        if ($request->status) {
            $query5 = $query5->where('noc.status', ($request->status));
        } else {
            $query5 = $query5->whereIn('noc.status', ($itemsIdArr));
        }

        // Add search filter if search.value is present
        if ($request->input('search.value')) {
            $searchValue = $request->input('search.value');
            $query5->where(function ($query) use ($searchValue) {
                $query->where('noc.application_no', 'like', "%$searchValue%")
                    // ->orWhere('pm.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                    ->orWhereRaw("
                        CASE 
                            WHEN pm.is_joint_property = 1 
                            THEN spd.old_property_id 
                            ELSE pm.old_propert_id 
                        END LIKE ?
                    ", ["%$searchValue%"])  // Use the correct column alias here

                    ->orWhere(DB::raw('LOWER(oc.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                    ->orWhere('pm.block_no', 'like', "%$searchValue%")  // Correctly reference block
                    // ->orWhere('pm.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhereRaw("
                        CASE 
                            WHEN pm.is_joint_property = 1 
                            THEN spd.plot_flat_no 
                            ELSE pm.plot_or_property_no 
                        END LIKE ?
                    ", ["%$searchValue%"])  // Correctly reference plot

                    ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                    // ->orWhere('pld.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                    ->orWhereRaw("
                        CASE 
                            WHEN pm.is_joint_property = 1 
                            THEN spd.presently_known_as 
                            ELSE pld.presently_known_as 
                        END LIKE ?
                    ", ["%$searchValue%"])

                    ->orWhere(DB::raw("'NocApplication'"), 'like', "%$searchValue%") // Search by model_name
                    ->orWhere('noc.created_at', 'like', "%$searchValue%");
            });
        }

        $clonedQuery1 = (clone $query1);
        $clonedQuery2 = (clone $query2);
        $clonedQuery3 = (clone $query3);
        $clonedQuery4 = (clone $query4);
        $clonedQuery5 = (clone $query5);

        // Combine all three queries using UNION
        $combinedQuery = $clonedQuery1->union($clonedQuery2)->union($clonedQuery3)->union($clonedQuery4)->union($clonedQuery5);
        // $combinedQuery = $clonedQuery1;
        $limit = $request->input('length');
        $start = $request->input('start');
        if ($request->input('order.0.column')) {
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
            // dd($order, $dir);
        } else {
            $order = 'created_at';
            $dir = 'desc';
        }

        $totalData = $combinedQuery->count();
        $totalFiltered = $totalData;

        $applications = $combinedQuery->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
        $data = [];
        $showSendProofReadingLink = false;
        // dd($applications);
        foreach ($applications as $key => $application) {
            if ($application->status) {
                // Get the service code only once to avoid repetitive calls
                $serviceCode = getServiceCodeById($application->status);

                // Check if the application status is 'objected', 'rejected', or 'approved'
                if (in_array($serviceCode, ['APP_OBJ', 'APP_REJ', 'APP_APR'])) {
                    $showSendProofReadingLink = false;
                } else {
                    // Check if the proof reading link has been sent at least once
                    $isProofReadingLinkSent = ApplicationAppointmentLink::where('application_no', $application->application_no)->exists();

                    // Show the proof reading link if it has been sent at least once
                    $showSendProofReadingLink = $isProofReadingLinkSent;
                }
            }
            $model = base64_encode($application->model_name);
            $mis_checked_by = User::find($application->mis_checked_by);
            $scan_file_checked_by = User::find($application->scan_file_checked_by);
            $uploaded_doc_checked_by = User::find($application->uploaded_doc_checked_by);
            $nestedData['id'] = $key + 1;
            $applicationNumber = $application->application_no;

            $appMovementCount = ApplicationMovement::where('application_no', $application->application_no)->count();
            if ($this->checkOfficalCanViewTheApplication($application->application_no)) {
                $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertGreen"></div></div>';
             }/* else if (Auth::user()->roles[0]->name == 'section-officer' && getServiceCodeById($application->status) == 'APP_IP' &&     $appMovementCount == 1) {
                $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertDot"></div></div>';
            } */ else {
                $latestRecord = ApplicationMovement::where('application_no', $application->application_no)
                    ->latest('created_at')
                    ->first();
                if (!is_null($latestRecord) && $latestRecord->assigned_to == Auth::user()->id) {
                    $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertDot"></div></div>';
                } else {
                    $applicationNumber = $application->application_no;
                }
            }
            
            $applicationNumber = '<a href="'. route('applications.view', ['id' => $application->id, 'type' => $model]).' ">
                            '.$applicationNumber.'
                        </a>';
            $nestedData['application_no'] = $applicationNumber;
            $nestedData['old_property_id'] = $application->old_property_id;
            $nestedData['new_colony_name'] = $application->colony_name;
            $nestedData['block_no'] = $application->block_no;
            $nestedData['plot_or_property_no'] = $application->plot_or_property_no;
            $nestedData['presently_known_as'] = $application->presently_known_as;
            $flatHTML = '';
            if (!empty($application->flat_id)) {
                $flatHTML .= '<div class="d-flex gap-2 align-items-center">' . $application->flat_number . '</div><span class="text-secondary">(' . $application->flat_id . ')</span>';
            } else {
                $flatHTML .= '<div>NA</div>';
            }
            $nestedData['flat_id'] =   $flatHTML;
            $nestedData['section'] = $application->section_code;

            switch ($application->model_name) {
                case 'MutationApplication':
                    $appliedFor = 'Mutation';
                    break;
                case 'LandUseChangeApplication':
                    $appliedFor = 'LUC';
                    break;
                case 'DeedOfApartmentApplication':
                    $appliedFor = 'DOA';
                    break;
                case 'ConversionApplication':
                    $appliedFor = 'CONVERSION';
                    break;
                case 'NocApplication':
                    $appliedFor = 'NOC';
                    break;
                default:
                    // Default action
                    break;
            }
            //for getting status
            $item = getStatusDetailsById($application->status);
            $itemCode = $item->item_code;
            $itemName = $item->item_name;
            $itemColor = $item->color_code;
            $statusClasses = [
                'APP_REJ' => 'statusRejected',
                'APP_NEW' => 'statusNew',
                'APP_IP' => 'statusSecondary',
                'RS_REW' => 'text-white bg-secondary',
                'RS_PEN' => 'text-info bg-light-info',
                'APP_APR' => 'landtypeFreeH',
                'APP_OBJ' => 'statusObject',
                'APP_HOLD' => 'statusHold',
            ];
            $class = $statusClasses[$itemCode] ?? 'text-secondary bg-light';
            $nestedData['applied_for'] = '<div class="d-flex flex-column gap-1">
                <label class="badge bg-info mx-1">' . $appliedFor . '</label>';

            if ($application->is_objected == 1) {
                $nestedData['applied_for'] .= '<label class="badge bg-danger mx-1">Objected</label>';
            }

            $nestedData['applied_for'] .= '</div>';
            $nestedData['activity'] = [
                'mis' => !empty($application->is_mis_checked) ? $application->is_mis_checked : 'NA',
                'scanned_files' => !empty($application->is_scan_file_checked) ? $application->is_scan_file_checked : 'NA',
                'uploaded_doc' => !empty($application->is_uploaded_doc_checked) ? $application->is_uploaded_doc_checked : 'NA',
                'mis_checked_by' => !empty($application->mis_checked_by) ? $mis_checked_by->name : '',
                'scan_file_checked_by' => !empty($application->scan_file_checked_by) ? $scan_file_checked_by->name : '',
                'uploaded_doc_checked_by' => !empty($application->uploaded_doc_checked_by) ? $uploaded_doc_checked_by->name : '',
                'mis_color_code' => !empty(getServiceTypeColorCode('MIS_CHECK')) ? getServiceTypeColorCode('MIS_CHECK') : '',
                'scan_file_color_code' => !empty(getServiceTypeColorCode('SCAN_CHECK')) ? getServiceTypeColorCode('SCAN_CHECK') : '',
                'uploaded_doc_color_code' => !empty(getServiceTypeColorCode('UP_DOC_CHE')) ? getServiceTypeColorCode('UP_DOC_CHE') : '',
            ];

            $nestedData['status'] = '<span class="highlight_value ' . $class . '">' . ucwords($itemName) . '</span>';
            $model = base64_encode($application->model_name);

            // Prepare actions
            $action = '<div class="d-flex gap-2">';
            $action .= '
                        <a href="'. route('applications.view', ['id' => $application->id, 'type' => $model]).' ">
                            <button type="button" class="btn btn-primary px-5">View</button>
                        </a>
                        <button type="button" class="btn btn-success" onclick="getFileMovement(\'' . $application->application_no . '\', this)">
                            File Movement
                        </button>';

            // Add meeting link button (commented as per discussion, not required at deputy end, IT Cell will do this) - SOURAV CHAUHAN (27 Feb 2025)
            // if (Auth::user()->roles[0]->name == 'deputy-lndo' && $appliedFor != "LUC" && $showSendProofReadingLink) {
            //     $action .= '<button type="button" class="btn btn-secondary px-5 send-meeting-link" data-application-id="' . $application->id . '" data-application-model_name="' . $application->model_name . '" data-application-no="' . $application->application_no . '">Send Meeting Link</button>';
            // }
            $action .= '</div>';

            $nestedData['action'] = $action;
            $nestedData['created_at'] = Carbon::parse($application->created_at)
                ->setTimezone('Asia/Kolkata')
                ->format('d M Y h:m:s');

            $data[] = $nestedData;
        }

        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        ];

        return response()->json($json_data);
    }

    public function getForwardedApplications(Request $request)
    {

        $itemsIdArr = [];
        $items = getApplicationStatusList(true, true);
        if (count($items) > 0) {
            foreach ($items as $key => $item) {
                $itemsIdArr[] = $item->id;
            }
        }

        $user = Auth::user();
        $sections = $user->sections->pluck('id');
        $columns = [
            'id', // index 0
            'application_no', // index 1
            'old_property_id', // index 2
            'new_colony_name', // index 3
            'block_no', // index 4
            'plot_or_property_no', // index 5
            'flat_id', // index 6
            'presently_known_as', // index 7
            'section_code', // index 8
            'model_name', // index 9
            '', // index 10
            '', // index 11
            'created_at', // index 12
        ];

        // $totalApplicationsQuery = ApplicationMovement::where('assigned_to', $user->id);
        // $latestIdByApplication = ApplicationMovement::selectRaw('MAX(id) as id')
        //     ->groupBy('application_no')
        //     ->pluck('id')->toArray();

        $userId = $user->id;
        $forwardedApplications = ApplicationMovement::where('is_forwarded', 1)->where('assigned_by', $userId)->distinct('application_no')->pluck('application_no')->toArray();
        $searchValue = $request->input('search.value') ?? null;
        $requestedStatus = $request->status ?? null;

        $combinedQuery = $this->applicationCombinedQuery($forwardedApplications, $requestedStatus, $searchValue);
        // Combine all three queries using UNION
        // $combinedQuery = $clonedQuery1->union($clonedQuery2)->union($clonedQuery3)->union($clonedQuery4)->union($clonedQuery5);
        // $combinedQuery = $clonedQuery1;
        $limit = $request->input('length');
        $start = $request->input('start');
        if ($request->input('order.0.column')) {
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
            // dd($order, $dir);
        } else {
            $order = 'created_at';
            $dir = 'desc';
        }

        $totalData = $combinedQuery->count();
        $totalFiltered = $totalData;

        $applications = $combinedQuery->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
        $data = [];
        $showSendProofReadingLink = false;
        // dd($applications);
        foreach ($applications as $key => $application) {
            if ($application->status) {
                // Get the service code only once to avoid repetitive calls
                $serviceCode = getServiceCodeById($application->status);

                // Check if the application status is 'objected', 'rejected', or 'approved'
                if (in_array($serviceCode, ['APP_OBJ', 'APP_REJ', 'APP_APR'])) {
                    $showSendProofReadingLink = false;
                } else {
                    // Check if the proof reading link has been sent at least once
                    $isProofReadingLinkSent = ApplicationAppointmentLink::where('application_no', $application->application_no)->exists();

                    // Show the proof reading link if it has been sent at least once
                    $showSendProofReadingLink = $isProofReadingLinkSent;
                }
            }
            $mis_checked_by = User::find($application->mis_checked_by);
            $scan_file_checked_by = User::find($application->scan_file_checked_by);
            $uploaded_doc_checked_by = User::find($application->uploaded_doc_checked_by);
            $nestedData['id'] = $key + 1;
            $applicationNumber = $application->application_no;

            $appMovementCount = ApplicationMovement::where('application_no', $application->application_no)->count();
            if (Auth::user()->roles[0]->name == 'section-officer' && getServiceCodeById($application->status) == 'APP_NEW') {
                if ($this->checkOfficalCanViewTheApplication($application->application_no)) {
                    $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertGreen"></div></div>';
                    $nestedData['orderBy'] = 1;
                } else {
                    $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertDot"></div></div>';
                    $nestedData['orderBy'] = 2;
                }
            } else if (Auth::user()->roles[0]->name == 'section-officer' && getServiceCodeById($application->status) == 'APP_IP' && $appMovementCount == 1) {
                if ($this->checkOfficalCanViewTheApplication($application->application_no)) {
                    $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertGreen"></div></div>';
                    $nestedData['orderBy'] = 1;
                } else {
                    $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertDot"></div></div>';
                    $nestedData['orderBy'] = 2;
                }
            } else {
                $latestRecord = ApplicationMovement::where('application_no', $application->application_no)
                    ->latest('created_at')
                    ->first();
                if (!is_null($latestRecord) && $latestRecord->assigned_to == Auth::user()->id) {
                    if ($this->checkOfficalCanViewTheApplication($application->application_no)) {
                        $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertGreen"></div></div>';
                        $nestedData['orderBy'] = 1;
                    } else {
                        $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertDot"></div></div>';
                        $nestedData['orderBy'] = 2;
                    }
                } else {
                    $applicationNumber = $application->application_no;
                    $nestedData['orderBy'] = 2;
                }
            }
            $nestedData['application_no'] = $applicationNumber;
            $nestedData['old_property_id'] = $application->old_property_id;
            $nestedData['new_colony_name'] = $application->block_no . '/' . $application->plot_or_property_no . '/' . $application->colony_name;
            /* $nestedData['new_colony_name'] = $application->colony_name;
            $nestedData['block_no'] = $application->block_no;
            $nestedData['plot_or_property_no'] = $application->plot_or_property_no; */
            $nestedData['presently_known_as'] = $application->presently_known_as;
            $flatHTML = '';
            if (!empty($application->flat_id)) {
                $flatHTML .= '<div class="d-flex gap-2 align-items-center">' . $application->flat_number . '</div><span class="text-secondary">(' . $application->flat_id . ')</span>';
            } else {
                $flatHTML .= '<div>NA</div>';
            }
            $nestedData['flat_id'] =   $flatHTML;
            $nestedData['section'] = $application->section_code;

            switch ($application->model_name) {
                case 'MutationApplication':
                    $appliedFor = 'Mutation';
                    break;
                case 'LandUseChangeApplication':
                    $appliedFor = 'LUC';
                    break;
                case 'DeedOfApartmentApplication':
                    $appliedFor = 'DOA';
                    break;
                case 'ConversionApplication':
                    $appliedFor = 'CONVERSION';
                    break;
                case 'NocApplication':
                    $appliedFor = 'NOC';
                    break;
                default:
                    // Default action
                    break;
            }
            //for getting status
            $item = getStatusDetailsById($application->status);
            $itemCode = $item->item_code;
            $itemName = $item->item_name;
            $itemColor = $item->color_code;
            $statusClasses = [
                'APP_REJ' => 'statusRejected',
                'APP_NEW' => 'statusNew',
                'APP_IP' => 'statusSecondary',
                'RS_REW' => 'text-white bg-secondary',
                'RS_PEN' => 'text-info bg-light-info',
                'APP_APR' => 'landtypeFreeH',
                'APP_OBJ' => 'statusObject',
                'APP_HOLD' => 'statusHold',
            ];
            $class = $statusClasses[$itemCode] ?? 'text-secondary bg-light';
            $nestedData['applied_for'] = '<div class="d-flex flex-column gap-1">
                <label class="badge bg-info mx-1">' . $appliedFor . '</label>';

            if ($application->is_objected == 1) {
                $nestedData['applied_for'] .= '<label class="badge bg-danger mx-1">Objected</label>';
            }

            $nestedData['applied_for'] .= '</div>';
            $nestedData['activity'] = [
                'mis' => !empty($application->is_mis_checked) ? $application->is_mis_checked : 'NA',
                'scanned_files' => !empty($application->is_scan_file_checked) ? $application->is_scan_file_checked : 'NA',
                'uploaded_doc' => !empty($application->is_uploaded_doc_checked) ? $application->is_uploaded_doc_checked : 'NA',
                'mis_checked_by' => !empty($application->mis_checked_by) ? $mis_checked_by->name : '',
                'scan_file_checked_by' => !empty($application->scan_file_checked_by) ? $scan_file_checked_by->name : '',
                'uploaded_doc_checked_by' => !empty($application->uploaded_doc_checked_by) ? $uploaded_doc_checked_by->name : '',
                'mis_color_code' => !empty(getServiceTypeColorCode('MIS_CHECK')) ? getServiceTypeColorCode('MIS_CHECK') : '',
                'scan_file_color_code' => !empty(getServiceTypeColorCode('SCAN_CHECK')) ? getServiceTypeColorCode('SCAN_CHECK') : '',
                'uploaded_doc_color_code' => !empty(getServiceTypeColorCode('UP_DOC_CHE')) ? getServiceTypeColorCode('UP_DOC_CHE') : '',
            ];

            $nestedData['status'] = '<span class="highlight_value ' . $class . '">' . ucwords($itemName) . '</span>';
            $model = base64_encode($application->model_name);

            // Prepare actions
            $action = '<div class="d-flex gap-2">';
            $action .= '<a href="' . url('edharti/applications/' . $application->id) . '?type=' . $model . '">
                        <button type="button" class="btn btn-primary px-5">View</button>
                    </a>

                        <button type="button" class="btn btn-success" onclick="getFileMovement(\'' . $application->application_no . '\', this)">
                            File Movement
                        </button>';

            // Add meeting link button (commented as per discussion, not required at deputy end, IT Cell will do this) - SOURAV CHAUHAN (27 Feb 2025)
            // if (Auth::user()->roles[0]->name == 'deputy-lndo' && $appliedFor != "LUC" && $showSendProofReadingLink) {
            //     $action .= '<button type="button" class="btn btn-secondary px-5 send-meeting-link" data-application-id="' . $application->id . '" data-application-model_name="' . $application->model_name . '" data-application-no="' . $application->application_no . '">Send Meeting Link</button>';
            // }
            $action .= '</div>';
            $applicationForwardedTo = ApplicationMovement::where('application_no', $application->application_no)->where('is_forwarded', 1)->where('assigned_by', $userId)->first()?->assigned_to;
            $nestedData['forwarded_to'] = !is_null($applicationForwardedTo) ? (User::find($applicationForwardedTo)?->name ?? 'N/A') : "N/A";
            $nestedData['action'] = $action;
            $nestedData['created_at'] = Carbon::parse($application->created_at)
                ->setTimezone('Asia/Kolkata')
                ->format('d M Y h:m:s');



            $data[] = $nestedData;
        }

        if ($order == 'created_at') {
            usort($data, fn($a, $b) => $a['orderBy'] <=> $b['orderBy']);
        }
        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        ];

        return response()->json($json_data);
    }

    /*public function getApplications(Request $request)
    {
        $user = Auth::user();
        $sections = $user->sections->pluck('id');
        $columns = ['id', 'old_property_id'];

        

        // Query for land use changed applications
        $serviceType2 = getServiceType('LUC');
        $query2 = DB::table('land_use_change_applications as lca')
            ->leftJoin('property_masters', 'lca.property_master_id', '=', 'property_masters.id')
            ->join('property_section_mappings as psm', function ($join) {
                $join->on('property_masters.new_colony_name', 'psm.colony_id');
                $join->whereColumn('property_masters.property_type', 'psm.property_type');
                $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
            })
            ->join('sections', 'psm.section_id', 'sections.id')
            ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
            ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
            ->leftJoinSub(
                DB::table('application_statuses')
                    ->select(
                        'id',
                        'model_id',
                        'reg_app_no',
                        'service_type',
                        'is_mis_checked',
                        'is_scan_file_checked',
                        'is_uploaded_doc_checked',
                        'mis_checked_by',
                        'scan_file_checked_by',
                        'uploaded_doc_checked_by',
                        'created_at',
                        DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                    )
                    ->where('service_type', $serviceType2),
                'latest_statuses',
                function ($join) {
                    $join->on('lca.id', '=', 'latest_statuses.model_id')
                         ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                }
            )
            // ->leftJoin('application_statuses', function ($join) use ($serviceType2) {
            //     $join->on('lca.id', '=', 'application_statuses.model_id')
            //         ->whereColumn('application_statuses.reg_app_no', 'lca.application_no')
            //         ->where('application_statuses.service_type', $serviceType2)
            //         ->orderBy('application_statuses.created_at', 'desc') // Order by the latest
            //         ->groupBy('application_statuses.model_id');
            // })
            ->whereIn('lca.section_id', $sections) //need to add secton id in all queries
            ->select(
                'lca.id',
                'lca.created_at',
                DB::raw('coalesce(lca.application_no,"0") as application_no'),
                'lca.status',
                'sections.section_code',
                'latest_statuses.is_mis_checked',
                'latest_statuses.is_scan_file_checked',
                'latest_statuses.is_uploaded_doc_checked',
                'latest_statuses.mis_checked_by',
                'latest_statuses.scan_file_checked_by',
                'latest_statuses.uploaded_doc_checked_by',
                'property_masters.old_propert_id',
                'property_masters.new_colony_name',
                'old_colonies.name as colony_name',
                'property_masters.block_no',
                'property_masters.plot_or_property_no',
                'property_lease_details.presently_known_as',
                DB::raw("'LandUseChangeApplication' as model_name") // Add model_name for the first query
            );


            // Query for mutation applications
        $serviceType1 = getServiceType('SUB_MUT');
        $query1 = DB::table('mutation_applications as ma')
            ->leftJoin('property_masters', 'ma.property_master_id', '=', 'property_masters.id')->join('property_section_mappings as psm', function ($join) {
                $join->on('property_masters.new_colony_name', 'psm.colony_id');
                $join->whereColumn('property_masters.property_type', 'psm.property_type');
                $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
            })
            ->join('sections', 'psm.section_id', 'sections.id')
            ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
            ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
            ->leftJoinSub(
                DB::table('application_statuses')
                    ->select(
                        'id',
                        'model_id',
                        'reg_app_no',
                        'service_type',
                        'is_mis_checked',
                        'is_scan_file_checked',
                        'is_uploaded_doc_checked',
                        'mis_checked_by',
                        'scan_file_checked_by',
                        'uploaded_doc_checked_by',
                        'created_at',
                        DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                    )
                    ->where('service_type', $serviceType1),
                'latest_statuses',
                function ($join) {
                    $join->on('ma.id', '=', 'latest_statuses.model_id')
                         ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                }
            )
            // ->leftJoin('application_statuses', function ($join) use ($serviceType1) {
            //     $join->on('ma.id', '=', 'application_statuses.model_id')
            //         ->whereColumn('application_statuses.reg_app_no', 'ma.application_no')
            //         ->where('application_statuses.service_type', $serviceType1)
            //         ->orderBy('application_statuses.created_at', 'desc') // Order by the latest
            //         ->groupBy('application_statuses.model_id');
            // })
            ->whereIn('ma.section_id', $sections) //need to add secton id in all queries
            ->select(
                'ma.id',
                'ma.created_at',
                'ma.application_no',
                'ma.status',
                'sections.section_code',
                'latest_statuses.is_mis_checked',
                'latest_statuses.is_scan_file_checked',
                'latest_statuses.is_uploaded_doc_checked',
                'latest_statuses.mis_checked_by',
                'latest_statuses.scan_file_checked_by',
                'latest_statuses.uploaded_doc_checked_by',
                'property_masters.old_propert_id',
                'property_masters.new_colony_name',
                'old_colonies.name as colony_name',
                'property_masters.block_no',
                'property_masters.plot_or_property_no',
                'property_lease_details.presently_known_as',
                DB::raw("'MutationApplication' as model_name") // Add model_name for the first query
            );
        // $results = $query1->get();
        // dd($results); // Dumps the results and stops further execution

        //Query for Deed Of Apartment applications
        $serviceType3 = getServiceType('DOA');
        $query3 = DB::table('deed_of_apartment_applications as doa')
            ->leftJoin('property_masters', 'doa.property_master_id', '=', 'property_masters.id')->join('property_section_mappings as psm', function ($join) {
                $join->on('property_masters.new_colony_name', 'psm.colony_id');
                $join->whereColumn('property_masters.property_type', 'psm.property_type');
                $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
            })
            ->join('sections', 'psm.section_id', 'sections.id')
            ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
            ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
            
            ->leftJoinSub(
                DB::table('application_statuses')
                    ->select(
                        'id',
                        'model_id',
                        'reg_app_no',
                        'service_type',
                        'is_mis_checked',
                        'is_scan_file_checked',
                        'is_uploaded_doc_checked',
                        'mis_checked_by',
                        'scan_file_checked_by',
                        'uploaded_doc_checked_by',
                        'created_at',
                        DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                    )
                    ->where('service_type', $serviceType3),
                'latest_statuses',
                function ($join) {
                    $join->on('doa.id', '=', 'latest_statuses.model_id')
                         ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                }
            )
            ->whereIn('doa.section_id', $sections) //need to add secton id in all queries
            ->select(
                'doa.id',
                'doa.created_at',
                'doa.application_no',
                'doa.status',
                'sections.section_code',
                'latest_statuses.is_mis_checked',
                'latest_statuses.is_scan_file_checked',
                'latest_statuses.is_uploaded_doc_checked',
                'latest_statuses.mis_checked_by',
                'latest_statuses.scan_file_checked_by',
                'latest_statuses.uploaded_doc_checked_by',
                'property_masters.old_propert_id',
                'property_masters.new_colony_name',
                'old_colonies.name as colony_name',
                'property_masters.block_no',
                'property_masters.plot_or_property_no',
                'property_lease_details.presently_known_as',
                DB::raw("'DeedOfApartmentApplication' as model_name") // Add model_name for the first query
            );
            



        //Query for Conversion applications added by Nitin
        $serviceType4 = getServiceType('CONVERSION');
        $query4 = DB::table('conversion_applications as ca')
            ->leftJoin('property_masters', 'ca.property_master_id', '=', 'property_masters.id')->join('property_section_mappings as psm', function ($join) {
                $join->on('property_masters.new_colony_name', 'psm.colony_id');
                $join->whereColumn('property_masters.property_type', 'psm.property_type');
                $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
            })
            ->join('sections', 'psm.section_id', 'sections.id')
            ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
            ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
            ->leftJoinSub(
                DB::table('application_statuses')
                    ->select(
                        'id',
                        'model_id',
                        'reg_app_no',
                        'service_type',
                        'is_mis_checked',
                        'is_scan_file_checked',
                        'is_uploaded_doc_checked',
                        'mis_checked_by',
                        'scan_file_checked_by',
                        'uploaded_doc_checked_by',
                        'created_at',
                        DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                    )
                    ->where('service_type', $serviceType3),
                'latest_statuses',
                function ($join) {
                    $join->on('ca.id', '=', 'latest_statuses.model_id')
                         ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                }
            )
            // ->leftJoin('application_statuses', function ($join) use ($serviceType4) {
            //     $join->on('ca.id', '=', 'application_statuses.model_id')
            //         ->whereColumn('application_statuses.reg_app_no', 'ca.application_no')
            //         ->where('application_statuses.service_type', $serviceType4)
            //         ->orderBy('application_statuses.created_at', 'desc') // Order by the latest
            //         ->groupBy('application_statuses.model_id');
            // })
            ->whereIn('ca.section_id', $sections) //need to add secton id in all queries
            ->select(
                'ca.id',
                'ca.created_at',
                'ca.application_no',
                'ca.status',
                'sections.section_code',
                'latest_statuses.is_mis_checked',
                'latest_statuses.is_scan_file_checked',
                'latest_statuses.is_uploaded_doc_checked',
                'latest_statuses.mis_checked_by',
                'latest_statuses.scan_file_checked_by',
                'latest_statuses.uploaded_doc_checked_by',
                'property_masters.old_propert_id',
                'property_masters.new_colony_name',
                'old_colonies.name as colony_name',
                'property_masters.block_no',
                'property_masters.plot_or_property_no',
                'property_lease_details.presently_known_as',
                DB::raw("'ConversionApplication' as model_name") // Add model_name for the first query
            );

        $clonedQuery1 = (clone $query1);
        $clonedQuery2 = (clone $query2);
        $clonedQuery3 = (clone $query3);
        $clonedQuery4 = (clone $query4);

        // Combine all three queries using UNION
        $combinedQuery = $clonedQuery1->union($clonedQuery2)->union($clonedQuery3)->union($clonedQuery4);
        // $results = $combinedQuery->get();
        // dd($results); // Dumps the results and stops further execution
        // Condition update due to display application record twice, So now it will display latest application status record  by Lalit Tiwari - (17/dec/2024)
        // Check if 'status' is present in the request
        if ($request->status) {
            // Wrap the combined query in a subquery and apply the where clause
            $combinedQuery = DB::table(DB::raw("({$combinedQuery->toSql()}) as combined"))
            ->mergeBindings($query2) // Merge bindings from the second query
            ->mergeBindings($query1) // Merge bindings from the first query
                ->mergeBindings($query3) // Merge bindings from the third query
                ->mergeBindings($query4) // Merge bindings from the fourth query
                ->where('combined.status', $request->status);
        }
        $combinedQuery =  $combinedQuery->orderBy('created_at', 'desc');
        // else {
        //     // If 'status' is not provided, simply return the combined query without the where clause
        //     $combinedQuery = DB::table(DB::raw("({$combinedQuery->toSql()}) as combined"))
        //         ->mergeBindings($query1)
        //         ->mergeBindings($query2)
        //         ->mergeBindings($query3)
        //         ->mergeBindings($query4)
        //         ->orderBy('created_at', 'desc');
        // }

        // Clone all queries
        // $clonedQueries = [
        //     clone $query1,
        //     clone $query2,
        //     clone $query3,
        //     clone $query4,
        // ];

        // // Combine all queries using UNION
        // $combinedQuery = array_reduce(array_slice($clonedQueries, 1), function ($carry, $query) {
        //     return $carry->union($query);
        // }, $clonedQueries[0]);

        // // Wrap the combined query in a subquery
        // $combinedQuery = DB::table(DB::raw("({$combinedQuery->toSql()}) as combined"))
        //     ->mergeBindings($query1)
        //     ->mergeBindings($query2)
        //     ->mergeBindings($query3)
        //     ->mergeBindings($query4)
        //     ->orderBy('created_at', 'desc');

        // // Apply the 'status' filter if provided
        // if ($request->status) {
        //     $combinedQuery->where('combined.status', $request->status);
        // }


        

        $totalData = $combinedQuery->count();
        $totalFiltered = $totalData;

        // Pagination parameters
        $limit = $request->input('length');
        $start = $request->input('start');

        // Order by requested column
        $orderColumnIndex = $request->input('order.0.column');

        $applications = $combinedQuery->offset($start)
            ->limit($limit)
            ->get();
        $data = [];

        // dd($applications);
        foreach ($applications as $key => $application) {
            $mis_checked_by = User::find($application->mis_checked_by);
            $scan_file_checked_by = User::find($application->scan_file_checked_by);
            $uploaded_doc_checked_by = User::find($application->uploaded_doc_checked_by);
            $nestedData['id'] = $key + 1;
            $applicationNumber = $application->application_no;

            $appMovementCount = ApplicationMovement::where('application_no', $application->application_no)->count();
            if (Auth::user()->roles[0]->name == 'section-officer' && getServiceCodeById($application->status) == 'APP_NEW') {
                $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertDot"></div></div>';
            } else if (Auth::user()->roles[0]->name == 'section-officer' && getServiceCodeById($application->status) == 'APP_IP' &&     $appMovementCount == 1) {
                $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertDot"></div></div>';
            } else {
                $latestRecord = ApplicationMovement::where('application_no', $application->application_no)
                    ->latest('created_at')
                    ->first();
                if (!is_null($latestRecord) && $latestRecord->assigned_to == Auth::user()->id) {
                    $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertDot"></div></div>';
                } else {
                    $applicationNumber = $application->application_no;
                }
            }
            $nestedData['application_no'] = $applicationNumber;
            $nestedData['old_property_id'] = $application->old_propert_id;
            $nestedData['new_colony_name'] = $application->colony_name;
            $nestedData['block_no'] = $application->block_no;
            $nestedData['plot_or_property_no'] = $application->plot_or_property_no;
            $nestedData['presently_known_as'] = $application->presently_known_as;
            $nestedData['section'] = $application->section_code;

            switch ($application->model_name) {
                case 'MutationApplication':
                    $appliedFor = 'Mutation';
                    break;
                case 'LandUseChangeApplication':
                    $appliedFor = 'LUC';
                    break;
                case 'DeedOfApartmentApplication':
                    $appliedFor = 'DOA';
                    break;
                case 'ConversionApplication':
                    $appliedFor = 'CONVERSION';
                    break;
                default:
                    // Default action
                    break;
            }
            //for getting status
            $item = getStatusDetailsById($application->status);
            $itemCode = $item->item_code;
            $itemName = $item->item_name;
            $itemColor = $item->color_code;
            $statusClasses = [
                'APP_REJ' => 'text-danger bg-light-danger',
                'APP_NEW' => 'text-primary bg-light-primary',
                'APP_IP' => 'text-warning bg-light-warning',
                'RS_REW' => 'text-white bg-secondary',
                'RS_PEN' => 'text-info bg-light-info',
                'APP_APR' => 'text-success bg-light-success',
            ];
            $class = $statusClasses[$itemCode] ?? 'text-secondary bg-light';
            $nestedData['applied_for'] = '<label class="badge bg-info mx-1">' . $appliedFor . '</label>';
            $nestedData['activity'] = [
                'mis' => !empty($application->is_mis_checked) ? $application->is_mis_checked : 'NA',
                'scanned_files' => !empty($application->is_scan_file_checked) ? $application->is_scan_file_checked : 'NA',
                'uploaded_doc' => !empty($application->is_uploaded_doc_checked) ? $application->is_uploaded_doc_checked : 'NA',
                'mis_checked_by' => !empty($application->mis_checked_by) ? $mis_checked_by->name : '',
                'scan_file_checked_by' => !empty($application->scan_file_checked_by) ? $scan_file_checked_by->name : '',
                'uploaded_doc_checked_by' => !empty($application->uploaded_doc_checked_by) ? $uploaded_doc_checked_by->name : '',
                'mis_color_code' => !empty(getServiceTypeColorCode('MIS_CHECK')) ? getServiceTypeColorCode('MIS_CHECK') : '',
                'scan_file_color_code' => !empty(getServiceTypeColorCode('SCAN_CHECK')) ? getServiceTypeColorCode('SCAN_CHECK') : '',
                'uploaded_doc_color_code' => !empty(getServiceTypeColorCode('UP_DOC_CHE')) ? getServiceTypeColorCode('UP_DOC_CHE') : '',
            ];

            $nestedData['status'] = '<div class="badge rounded-pill ' . $class . ' p-2 text-uppercase px-3">' . ucwords($itemName) . '</div>';
            $model = base64_encode($application->model_name);

            // Prepare actions
            // $action = '<div class="d-flex gap-2">
            //     <a href="' . url('applications/' . $application->id) . '?type=' . $model . '">
            //         <button type="button" class="btn btn-primary px-5">View</button>
            //     </a>
            //    <button type="button" class="btn btn-success" onclick="getFileMovement(\'' . $application->application_no . '\', this)">
            //         File Movement
            //     </button>
            // </div>';

            // Prepare actions
            $action = '<div class="d-flex gap-2">';
            $action .= '<a href="' . url('applications/' . $application->id) . '?type=' . $model . '">
                            <button type="button" class="btn btn-primary px-5">View</button>
                        </a>
                        <button type="button" class="btn btn-success" onclick="getFileMovement(\'' . $application->application_no . '\', this)">
                            File Movement
                        </button>';
            // Add meeting link button
            if (Auth::user()->roles[0]->name == 'deputy-lndo' && $appliedFor != "LUC") {
                $action .= '<button type="button" class="btn btn-secondary px-5 send-meeting-link" data-application-id="' . $application->id . '" data-application-model_name="' . $application->model_name . '" data-application-no="' . $application->application_no . '">Send Meeting Link</button>';
            }
            $action .= '</div>';

            $nestedData['action'] = $action;
            $nestedData['created_at'] = $application->created_at;

            $data[] = $nestedData;
        }

        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        ];

        return response()->json($json_data);
    }*/

    // commented by lalit on dated (17/december/2024) due to application record repeat twice due to applicaiton status, above is the function after changes
    /*public function getApplications(Request $request)
    {
        $user = Auth::user();
        $sections = $user->sections->pluck('id');
        $columns = ['id', 'old_property_id'];

        // Query for land use changed applications
        $serviceType2 = getServiceType('LUC');
        $query2 = DB::table('land_use_change_applications as lca')
            ->leftJoin('property_masters', 'lca.property_master_id', '=', 'property_masters.id')
            ->join('property_section_mappings as psm', function ($join) {
                $join->on('property_masters.new_colony_name', 'psm.colony_id');
                $join->whereColumn('property_masters.property_type', 'psm.property_type');
                $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
            })
            ->join('sections', 'psm.section_id', 'sections.id')
            ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
            ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
            ->leftJoin('application_statuses', function ($join) use ($serviceType2) {
                $join->on('lca.id', '=', 'application_statuses.model_id')
                    ->whereColumn('application_statuses.reg_app_no', 'lca.application_no')
                    ->where('application_statuses.service_type', $serviceType2);
            })
            ->select(
                'lca.id',
                'lca.created_at',
                DB::raw('coalesce(lca.application_no,"0") as application_no'),
                'lca.status',
                'sections.section_code',
                'application_statuses.is_mis_checked',
                'application_statuses.is_scan_file_checked',
                'application_statuses.is_uploaded_doc_checked',
                'application_statuses.mis_checked_by',
                'application_statuses.scan_file_checked_by',
                'application_statuses.uploaded_doc_checked_by',
                'property_masters.old_propert_id',
                'property_masters.new_colony_name',
                'old_colonies.name as colony_name',
                'property_masters.block_no',
                'property_masters.plot_or_property_no',
                'property_lease_details.presently_known_as',
                DB::raw("'LandUseChangeApplication' as model_name") // Add model_name for the first query
            );

        // dd($query2->toSql(),$query2->getBindings());

        // Query for mutation applications
        $serviceType1 = getServiceType('SUB_MUT');
        $query1 = DB::table('mutation_applications as ma')
            ->leftJoin('property_masters', 'ma.property_master_id', '=', 'property_masters.id')->join('property_section_mappings as psm', function ($join) {
                $join->on('property_masters.new_colony_name', 'psm.colony_id');
                $join->whereColumn('property_masters.property_type', 'psm.property_type');
                $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
            })
            ->join('sections', 'psm.section_id', 'sections.id')
            ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
            ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
            ->leftJoin('application_statuses', function ($join) use ($serviceType1) {
                $join->on('ma.id', '=', 'application_statuses.model_id')
                    ->whereColumn('application_statuses.reg_app_no', 'ma.application_no')
                    ->where('application_statuses.service_type', $serviceType1);
            })
            ->whereIn('ma.section_id', $sections) //need to add secton id in all queries
            ->select(
                'ma.id',
                'ma.created_at',
                'ma.application_no',
                'ma.status',
                'sections.section_code',
                'application_statuses.is_mis_checked',
                'application_statuses.is_scan_file_checked',
                'application_statuses.is_uploaded_doc_checked',
                'application_statuses.mis_checked_by',
                'application_statuses.scan_file_checked_by',
                'application_statuses.uploaded_doc_checked_by',
                'property_masters.old_propert_id',
                'property_masters.new_colony_name',
                'old_colonies.name as colony_name',
                'property_masters.block_no',
                'property_masters.plot_or_property_no',
                'property_lease_details.presently_known_as',
                DB::raw("'MutationApplication' as model_name") // Add model_name for the first query
            );

        //Query for Deed Of Apartment applications
        $serviceType3 = getServiceType('DOA');
        $query3 = DB::table('deed_of_apartment_applications as doa')
            ->leftJoin('property_masters', 'doa.property_master_id', '=', 'property_masters.id')->join('property_section_mappings as psm', function ($join) {
                $join->on('property_masters.new_colony_name', 'psm.colony_id');
                $join->whereColumn('property_masters.property_type', 'psm.property_type');
                $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
            })
            ->join('sections', 'psm.section_id', 'sections.id')
            ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
            ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
            ->leftJoin('application_statuses', function ($join) use ($serviceType3) {
                $join->on('doa.id', '=', 'application_statuses.model_id')
                    ->whereColumn('application_statuses.reg_app_no', 'doa.application_no')
                    ->where('application_statuses.service_type', $serviceType3);
            })
            ->select(
                'doa.id',
                'doa.created_at',
                'doa.application_no',
                'doa.status',
                'sections.section_code',
                'application_statuses.is_mis_checked',
                'application_statuses.is_scan_file_checked',
                'application_statuses.is_uploaded_doc_checked',
                'application_statuses.mis_checked_by',
                'application_statuses.scan_file_checked_by',
                'application_statuses.uploaded_doc_checked_by',
                'property_masters.old_propert_id',
                'property_masters.new_colony_name',
                'old_colonies.name as colony_name',
                'property_masters.block_no',
                'property_masters.plot_or_property_no',
                'property_lease_details.presently_known_as',
                DB::raw("'DeedOfApartmentApplication' as model_name") // Add model_name for the first query
            );


        //Query for Conversion applications added by Nitin
        $serviceType4 = getServiceType('CONVERSION');
        $query4 = DB::table('conversion_applications as ca')
            ->leftJoin('property_masters', 'ca.property_master_id', '=', 'property_masters.id')->join('property_section_mappings as psm', function ($join) {
                $join->on('property_masters.new_colony_name', 'psm.colony_id');
                $join->whereColumn('property_masters.property_type', 'psm.property_type');
                $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
            })
            ->join('sections', 'psm.section_id', 'sections.id')
            ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
            ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
            ->leftJoin('application_statuses', function ($join) use ($serviceType4) {
                $join->on('ca.id', '=', 'application_statuses.model_id')
                    ->whereColumn('application_statuses.reg_app_no', 'ca.application_no')
                    ->where('application_statuses.service_type', $serviceType4);
            })
            ->select(
                'ca.id',
                'ca.created_at',
                'ca.application_no',
                'ca.status',
                'sections.section_code',
                'application_statuses.is_mis_checked',
                'application_statuses.is_scan_file_checked',
                'application_statuses.is_uploaded_doc_checked',
                'application_statuses.mis_checked_by',
                'application_statuses.scan_file_checked_by',
                'application_statuses.uploaded_doc_checked_by',
                'property_masters.old_propert_id',
                'property_masters.new_colony_name',
                'old_colonies.name as colony_name',
                'property_masters.block_no',
                'property_masters.plot_or_property_no',
                'property_lease_details.presently_known_as',
                DB::raw("'ConversionApplication' as model_name") // Add model_name for the first query
            );

        $clonedQuery1 = (clone $query1);
        $clonedQuery2 = (clone $query2);
        $clonedQuery3 = (clone $query3);
        $clonedQuery4 = (clone $query4);

        // Combine all three queries using UNION
        $combinedQuery = $clonedQuery1->union($clonedQuery2)->union($clonedQuery3)->union($clonedQuery4);

        // dd($request->status, getServiceType('APP_REJ'), getServiceType('APP_APR'));
        /** code modified by Nitin to add disposed statsu in the list - 16dec2024 /
        $requestStatus = $request->status;
        if ($requestStatus != "") {
            // Wrap the combined query in a subquery and apply the where clause
            $combinedQuery = DB::table(DB::raw("({$combinedQuery->toSql()}) as combined"))
                ->mergeBindings($query1) // Merge bindings from the mutation query
                ->mergeBindings($query2) // Merge bindings from the LUC query
                ->mergeBindings($query3) // Merge bindings from the deed of appartment query
                ->mergeBindings($query4) // Merge bindings from the conversion query
                ->where(function ($quer) use ($requestStatus) {
                    if ($requestStatus == 0) {
                        return $quer->whereIn('combined.status', [getServiceType('APP_REJ'), getServiceType('APP_APR')]);
                    } else {
                        return $quer->where('combined.status', $requestStatus);
                    }
                })
                ->orderBy('created_at', 'desc');
        }

        //     $rawSql = $combinedQuery->toSql();
        // $bindings = $combinedQuery->getBindings();

        // dd([
        //     'sql' => $rawSql,
        //     'bindings' => $bindings,
        //     'count_placeholders' => substr_count($rawSql, '?'),
        //     'count_bindings' => count($bindings)
        // ]);


        // dd($combinedQuery->toSql(),$combinedQuery->getBindings());
        // $rawSql = $combinedQuery->toSql();
        // $bindings = $combinedQuery->getBindings();

        // $fullSql = $rawSql;
        // foreach ($bindings as $binding) {
        //     $binding = is_numeric($binding) ? $binding : "'" . addslashes($binding) . "'";
        //     $fullSql = preg_replace('/\?/', $binding, $fullSql, 1);
        // }

        // dd($fullSql);

        // dd($combinedQuery->toSql(), $combinedQuery->getBindings());

        // Apply search filter for global search
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $combinedQuery->where(function ($q) use ($search) {
                $q->where('old_property_id', 'like', "%{$search}%");
            });
        }

        $totalData = $combinedQuery->count();
        $totalFiltered = $totalData;

        // Pagination parameters
        $limit = $request->input('length');
        $start = $request->input('start');

        // Order by requested column
        $orderColumnIndex = $request->input('order.0.column');

        $applications = $combinedQuery->offset($start)
            ->limit($limit)
            ->get();
        $data = [];

        // dd($applications);
        foreach ($applications as $key => $application) {
            $mis_checked_by = User::find($application->mis_checked_by);
            $scan_file_checked_by = User::find($application->scan_file_checked_by);
            $uploaded_doc_checked_by = User::find($application->uploaded_doc_checked_by);
            $nestedData['id'] = $key + 1;
            $applicationNumber = $application->application_no;

            $appMovementCount = ApplicationMovement::where('application_no', $application->application_no)->count();
            if (Auth::user()->roles[0]->name == 'section-officer' && getServiceCodeById($application->status) == 'APP_NEW') {
                $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertDot"></div></div>';
            } else if (Auth::user()->roles[0]->name == 'section-officer' && getServiceCodeById($application->status) == 'APP_IP' &&     $appMovementCount == 1) {
                $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertDot"></div></div>';
            } else {
                $latestRecord = ApplicationMovement::where('application_no', $application->application_no)
                    ->latest('created_at')
                    ->first();
                if (!is_null($latestRecord) && $latestRecord->assigned_to == Auth::user()->id) {
                    $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertDot"></div></div>';
                } else {
                    $applicationNumber = $application->application_no;
                }
            }
            $nestedData['application_no'] = $applicationNumber;
            $nestedData['old_property_id'] = $application->old_propert_id;
            $nestedData['new_colony_name'] = $application->colony_name;
            $nestedData['block_no'] = $application->block_no;
            $nestedData['plot_or_property_no'] = $application->plot_or_property_no;
            $nestedData['presently_known_as'] = $application->presently_known_as;
            $nestedData['section'] = $application->section_code;

            switch ($application->model_name) {
                case 'MutationApplication':
                    $appliedFor = 'Mutation';
                    break;
                case 'LandUseChangeApplication':
                    $appliedFor = 'LUC';
                    break;
                case 'DeedOfApartmentApplication':
                    $appliedFor = 'DOA';
                    break;
                case 'ConversionApplication':
                    $appliedFor = 'CONVERSION';
                    break;
                default:
                    // Default action
                    break;
            }
            //for getting status
            $item = getStatusDetailsById($application->status);
            $itemCode = $item->item_code;
            $itemName = $item->item_name;
            $itemColor = $item->color_code;
            // dd()
            $statusClasses = [
                'APP_REJ' => 'text-danger bg-light-danger',
                'APP_NEW' => 'text-primary bg-light-primary',
                'APP_IP' => 'text-warning bg-light-warning',
                'RS_REW' => 'text-white bg-secondary',
                'RS_PEN' => 'text-info bg-light-info',
                'APP_APR' => 'text-success bg-light-success',
            ];
            $class = $statusClasses[$itemCode] ?? 'text-secondary bg-light';
            // dd($statusClasses[$itemCode]);

            $nestedData['applied_for'] = '<label class="badge bg-info mx-1">' . $appliedFor . '</label>';

            $nestedData['activity'] = [
                'mis' => !empty($application->is_mis_checked) ? $application->is_mis_checked : 'NA',
                'scanned_files' => !empty($application->is_scan_file_checked) ? $application->is_scan_file_checked : 'NA',
                'uploaded_doc' => !empty($application->is_uploaded_doc_checked) ? $application->is_uploaded_doc_checked : 'NA',
                'mis_checked_by' => !empty($application->mis_checked_by) ? $mis_checked_by->name : '',
                'scan_file_checked_by' => !empty($application->scan_file_checked_by) ? $scan_file_checked_by->name : '',
                'uploaded_doc_checked_by' => !empty($application->uploaded_doc_checked_by) ? $uploaded_doc_checked_by->name : '',
                'mis_color_code' => !empty(getServiceTypeColorCode('MIS_CHECK')) ? getServiceTypeColorCode('MIS_CHECK') : '',
                'scan_file_color_code' => !empty(getServiceTypeColorCode('SCAN_CHECK')) ? getServiceTypeColorCode('SCAN_CHECK') : '',
                'uploaded_doc_color_code' => !empty(getServiceTypeColorCode('UP_DOC_CHE')) ? getServiceTypeColorCode('UP_DOC_CHE') : '',
            ];

            $nestedData['status'] = '<div class="badge rounded-pill ' . $class . ' p-2 text-uppercase px-3">' . ucwords($itemName) . '</div>';
            $model = base64_encode($application->model_name);

            // Prepare actions
            // $action = '<div class="d-flex gap-2">
            //     <a href="' . url('applications/' . $application->id) . '?type=' . $model . '">
            //         <button type="button" class="btn btn-primary px-5">View</button>
            //     </a>
            //    <button type="button" class="btn btn-success" onclick="getFileMovement(\'' . $application->application_no . '\', this)">
            //         File Movement
            //     </button>
            // </div>';

            // Prepare actions
            $action = '<div class="d-flex gap-2">';
            $action .= '<a href="' . url('applications/' . $application->id) . '?type=' . $model . '">
                            <button type="button" class="btn btn-primary px-5">View</button>
                        </a>
                        <button type="button" class="btn btn-success" onclick="getFileMovement(\'' . $application->application_no . '\', this)">
                            File Movement
                        </button>';
            // Add meeting link button
            if (Auth::user()->roles[0]->name == 'deputy-lndo' && $appliedFor != "LUC") {
                $action .= '<button type="button" class="btn btn-secondary px-5 send-meeting-link" data-application-id="' . $application->id . '" data-application-model_name="' . $application->model_name . '" data-application-no="' . $application->application_no . '">Send Meeting Link</button>';
            }
            $action .= '</div>';

            $nestedData['action'] = $action;
            $nestedData['created_at'] = $application->created_at;

            $data[] = $nestedData;
        }

        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        ];

        return response()->json($json_data);
    }*/


    private function applicationCombinedQuery($applicationNoArray = [], $requestedStatus = null, $searchValue = null)
    {
        $itemsIdArr = [];
        $items = getApplicationStatusList(true, true);
        if (count($items) > 0) {
            foreach ($items as $key => $item) {
                $itemsIdArr[] = $item->id;
            }
        }
        $serviceType1 = getServiceType('SUB_MUT'); // Ensure this function is defined and works properly.

        $query1 = DB::table('mutation_applications as ma')
            ->where('ma.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
            ->leftJoin('property_masters as pm', 'ma.property_master_id', '=', 'pm.id')
            ->join('property_section_mappings as psm', function ($join) {
                $join->on('pm.new_colony_name', '=', 'psm.colony_id')
                    ->whereColumn('pm.property_type', 'psm.property_type')
                    ->whereColumn('pm.property_sub_type', 'psm.property_subtype');
            })
            ->join('sections', 'psm.section_id', '=', 'sections.id')
            ->leftJoin('old_colonies as oc', 'pm.new_colony_name', '=', 'oc.id')
            ->leftJoin('property_lease_details as pld', 'pm.id', '=', 'pld.property_master_id')
            ->leftJoin('applications as app', 'ma.application_no', '=', 'app.application_no')
            ->leftJoinSub(
                DB::table('application_statuses')
                    ->select(
                        'id',
                        'model_id',
                        'reg_app_no',
                        'service_type',
                        'is_mis_checked',
                        'is_scan_file_checked',
                        'is_uploaded_doc_checked',
                        'mis_checked_by',
                        'scan_file_checked_by',
                        'uploaded_doc_checked_by',
                        'created_at',
                        DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                    )
                    ->where('service_type', $serviceType1),
                'latest_statuses',
                function ($join) {
                    $join->on('ma.id', '=', 'latest_statuses.model_id')
                        ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                }
            )
            ->whereIn('ma.application_no', $applicationNoArray)
            ->select(
                'ma.id',
                'ma.created_at',
                'ma.application_no',
                'ma.status',
                'sections.section_code',
                'latest_statuses.is_mis_checked',
                'latest_statuses.is_scan_file_checked',
                'latest_statuses.is_uploaded_doc_checked',
                'latest_statuses.mis_checked_by',
                'latest_statuses.scan_file_checked_by',
                'latest_statuses.uploaded_doc_checked_by',
                'pm.old_propert_id as old_property_id', // Fixed alias
                'pm.new_colony_name',
                'oc.name as colony_name',
                'pm.block_no',
                'pm.plot_or_property_no',
                'pld.presently_known_as',
                'app.is_objected',
                DB::raw('NULL as flat_id'), // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                DB::raw('NULL as flat_number'), // Add NULL for flat_number on dated 07/01/25 By Lalit Tiwari
                DB::raw("'MutationApplication' as model_name") // Add model_name for the first query
            );
        if (!is_null($requestedStatus)) {
            $query1 = $query1->where('ma.status', ($requestedStatus));
        } else {
            $query1 = $query1->whereIn('ma.status', ($itemsIdArr));
        }

        // Add search filter if search.value is present
        if (!is_null($searchValue)) {
            $query1->where(function ($query) use ($searchValue) {
                $query->where('ma.application_no', 'like', "%$searchValue%")
                    ->orWhere('pm.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                    ->orWhere(DB::raw('LOWER(oc.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                    ->orWhere('pm.block_no', 'like', "%$searchValue%")  // Correctly reference block
                    ->orWhere('pm.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                    ->orWhere('pld.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                    ->orWhere(DB::raw("'MutationApplication'"), 'like', "%$searchValue%") // Search by model_name
                    ->orWhere('ma.created_at', 'like', "%$searchValue%");
            });
        }

        // Query for land use changed applications
        $serviceType2 = getServiceType('LUC');
        $query2 = DB::table('land_use_change_applications as lca')
            ->where('lca.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
            ->leftJoin('property_masters', 'lca.property_master_id', '=', 'property_masters.id')
            ->join('property_section_mappings as psm', function ($join) {
                $join->on('property_masters.new_colony_name', 'psm.colony_id');
                $join->whereColumn('property_masters.property_type', 'psm.property_type');
                $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
            })
            ->join('sections', 'psm.section_id', 'sections.id')
            ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
            ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
            ->leftJoin('applications as app', 'lca.application_no', '=', 'app.application_no')
            ->leftJoinSub(
                DB::table('application_statuses')
                    ->select(
                        'id',
                        'model_id',
                        'reg_app_no',
                        'service_type',
                        'is_mis_checked',
                        'is_scan_file_checked',
                        'is_uploaded_doc_checked',
                        'mis_checked_by',
                        'scan_file_checked_by',
                        'uploaded_doc_checked_by',
                        'created_at',
                        DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                    )
                    ->where('service_type', $serviceType2),
                'latest_statuses',
                function ($join) {
                    $join->on('lca.id', '=', 'latest_statuses.model_id')
                        ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                }
            )
            ->whereIn('lca.application_no', $applicationNoArray)
            ->select(
                'lca.id',
                'lca.created_at',
                DB::raw('coalesce(lca.application_no,"0") as application_no'),
                'lca.status',
                'sections.section_code',
                'latest_statuses.is_mis_checked',
                'latest_statuses.is_scan_file_checked',
                'latest_statuses.is_uploaded_doc_checked',
                'latest_statuses.mis_checked_by',
                'latest_statuses.scan_file_checked_by',
                'latest_statuses.uploaded_doc_checked_by',
                'property_masters.old_propert_id as old_property_id',
                'property_masters.new_colony_name',
                'old_colonies.name as colony_name',
                'property_masters.block_no',
                'property_masters.plot_or_property_no',
                'property_lease_details.presently_known_as',
                'app.is_objected',
                DB::raw('NULL as flat_id'), // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                DB::raw('NULL as flat_number'), // Add NULL for flat_number on dated 07/01/25 By Lalit Tiwari
                DB::raw("'LandUseChangeApplication' as model_name") // Add model_name for the first query
            );

        if ($requestedStatus) {
            $query2 = $query2->where('lca.status', ($requestedStatus));
        } else {
            $query2 = $query2->whereIn('lca.status', ($itemsIdArr));
        }

        // Add search filter if search.value is present
        if (!is_null($searchValue)) {
            $query2->where(function ($query) use ($searchValue) {
                $query->where('lca.application_no', 'like', "%$searchValue%")
                    ->orWhere('property_masters.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                    ->orWhere(DB::raw('LOWER(old_colonies.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                    ->orWhere('property_masters.block_no', 'like', "%$searchValue%")  // Correctly reference block
                    ->orWhere('property_masters.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                    ->orWhere('property_lease_details.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                    ->orWhere(DB::raw("'LandUseChangeApplication'"), 'like', "%$searchValue%") // Search by model_name
                    ->orWhere('lca.created_at', 'like', "%$searchValue%");
            });
        }

        //Query for Deed Of Apartment applications
        $serviceType3 = getServiceType('DOA');
        $query3 = DB::table('deed_of_apartment_applications as doa')
            ->where('doa.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
            ->leftJoin('property_masters', 'doa.property_master_id', '=', 'property_masters.id')->join('property_section_mappings as psm', function ($join) {
                $join->on('property_masters.new_colony_name', 'psm.colony_id');
                $join->whereColumn('property_masters.property_type', 'psm.property_type');
                $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
            })
            ->join('sections', 'psm.section_id', 'sections.id')
            ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
            ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
            ->leftJoin('flats', 'doa.flat_id', '=', 'flats.id')
            ->leftJoin('applications as app', 'doa.application_no', '=', 'app.application_no')
            ->leftJoinSub(
                DB::table('application_statuses')
                    ->select(
                        'id',
                        'model_id',
                        'reg_app_no',
                        'service_type',
                        'is_mis_checked',
                        'is_scan_file_checked',
                        'is_uploaded_doc_checked',
                        'mis_checked_by',
                        'scan_file_checked_by',
                        'uploaded_doc_checked_by',
                        'created_at',
                        DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                    )
                    ->where('service_type', $serviceType3),
                'latest_statuses',
                function ($join) {
                    $join->on('doa.id', '=', 'latest_statuses.model_id')
                        ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                }
            )
            ->whereIn('doa.application_no', $applicationNoArray)
            ->select(
                'doa.id',
                'doa.created_at',
                'doa.application_no',
                'doa.status',
                'sections.section_code',
                'latest_statuses.is_mis_checked',
                'latest_statuses.is_scan_file_checked',
                'latest_statuses.is_uploaded_doc_checked',
                'latest_statuses.mis_checked_by',
                'latest_statuses.scan_file_checked_by',
                'latest_statuses.uploaded_doc_checked_by',
                'property_masters.old_propert_id as old_property_id',
                'property_masters.new_colony_name',
                'old_colonies.name as colony_name',
                'property_masters.block_no',
                'property_masters.plot_or_property_no',
                'property_lease_details.presently_known_as',
                'app.is_objected',
                'flats.unique_flat_id as flat_id', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                'flats.flat_number as flat_number', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                DB::raw("'DeedOfApartmentApplication' as model_name") // Add model_name for the first query
            );
        if ($requestedStatus) {
            $query3 = $query3->where('doa.status', ($requestedStatus));
        } else {
            $query3 = $query3->whereIn('doa.status', ($itemsIdArr));
        }

        // Add search filter if search.value is present
        if (!is_null($searchValue)) {
            $query3->where(function ($query) use ($searchValue) {
                $query->where('doa.application_no', 'like', "%$searchValue%")
                    ->orWhere('property_masters.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                    ->orWhere(DB::raw('LOWER(old_colonies.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                    ->orWhere('property_masters.block_no', 'like', "%$searchValue%")  // Correctly reference block
                    ->orWhere('property_masters.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhere('flats.unique_flat_id', 'like', "%$searchValue%")  // Search by flat_id
                    ->orWhere('flats.flat_number', 'like', "%$searchValue%")    // Search by flat_number
                    ->orWhere('property_lease_details.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                    ->orWhere(DB::raw("'DeedOfApartmentApplication'"), 'like', "%$searchValue%") // Search by model_name
                    ->orWhere('doa.created_at', 'like', "%$searchValue%");
            });
        }




        //Query for Conversion applications added by Nitin
        $serviceType4 = getServiceType('CONVERSION');
        $query4 = DB::table('conversion_applications as ca')
            ->where('ca.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
            ->leftJoin('property_masters', 'ca.property_master_id', '=', 'property_masters.id')->join('property_section_mappings as psm', function ($join) {
                $join->on('property_masters.new_colony_name', 'psm.colony_id');
                $join->whereColumn('property_masters.property_type', 'psm.property_type');
                $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
            })
            ->join('sections', 'psm.section_id', 'sections.id')
            ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
            ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
            ->leftJoin('applications as app', 'ca.application_no', '=', 'app.application_no')
            ->leftJoinSub(
                DB::table('application_statuses')
                    ->select(
                        'id',
                        'model_id',
                        'reg_app_no',
                        'service_type',
                        'is_mis_checked',
                        'is_scan_file_checked',
                        'is_uploaded_doc_checked',
                        'mis_checked_by',
                        'scan_file_checked_by',
                        'uploaded_doc_checked_by',
                        'created_at',
                        DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                    )
                    ->where('service_type', $serviceType4),
                'latest_statuses',
                function ($join) {
                    $join->on('ca.id', '=', 'latest_statuses.model_id')
                        ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                }
            )
            ->whereIn('ca.application_no', $applicationNoArray)
            ->select(
                'ca.id',
                'ca.created_at',
                'ca.application_no',
                'ca.status',
                'sections.section_code',
                'latest_statuses.is_mis_checked',
                'latest_statuses.is_scan_file_checked',
                'latest_statuses.is_uploaded_doc_checked',
                'latest_statuses.mis_checked_by',
                'latest_statuses.scan_file_checked_by',
                'latest_statuses.uploaded_doc_checked_by',
                'property_masters.old_propert_id as old_property_id',
                'property_masters.new_colony_name',
                'old_colonies.name as colony_name',
                'property_masters.block_no',
                'property_masters.plot_or_property_no',
                'property_lease_details.presently_known_as',
                'app.is_objected',
                DB::raw('NULL as flat_id'), // Add NULL for flat_id
                DB::raw('NULL as flat_number'), // Add NULL for flat_number on dated 07/01/25 By Lalit Tiwari
                DB::raw("'ConversionApplication' as model_name") // Add model_name for the first query
            );

        if ($requestedStatus) {
            $query4 = $query4->where('ca.status', ($requestedStatus));
        } else {
            $query4 = $query4->whereIn('ca.status', ($itemsIdArr));
        }

        // Add search filter if search.value is present
        if (!is_null($searchValue)) {
            $query4->where(function ($query) use ($searchValue) {
                $query->where('ca.application_no', 'like', "%$searchValue%")
                    ->orWhere('property_masters.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                    ->orWhere(DB::raw('LOWER(old_colonies.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                    ->orWhere('property_masters.block_no', 'like', "%$searchValue%")  // Correctly reference block
                    ->orWhere('property_masters.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                    ->orWhere('property_lease_details.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                    ->orWhere(DB::raw("'ConversionApplication'"), 'like', "%$searchValue%") // Search by model_name
                    ->orWhere('ca.created_at', 'like', "%$searchValue%");
            });
        }

        // Query added for NOC application - Lalit Tiwari (20/March/2025)
        $serviceType5 = getServiceType('NOC');
        $query5 = DB::table('noc_applications as noc')
            ->where('noc.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
            ->leftJoin('property_masters as pm', 'noc.property_master_id', '=', 'pm.id')
            ->leftJoin('flats', 'noc.flat_id', '=', 'flats.id')
            ->join('property_section_mappings as psm', function ($join) {
                $join->on('pm.new_colony_name', '=', 'psm.colony_id')
                    ->whereColumn('pm.property_type', 'psm.property_type')
                    ->whereColumn('pm.property_sub_type', 'psm.property_subtype');
            })
            ->join('sections', 'psm.section_id', '=', 'sections.id')
            ->leftJoin('old_colonies as oc', 'pm.new_colony_name', '=', 'oc.id')
            ->leftJoin('property_lease_details as pld', 'pm.id', '=', 'pld.property_master_id')
            ->leftJoin('applications as app', 'noc.application_no', '=', 'app.application_no')
            ->leftJoinSub(
                DB::table('application_statuses')
                    ->select(
                        'id',
                        'model_id',
                        'reg_app_no',
                        'service_type',
                        'is_mis_checked',
                        'is_scan_file_checked',
                        'is_uploaded_doc_checked',
                        'mis_checked_by',
                        'scan_file_checked_by',
                        'uploaded_doc_checked_by',
                        'created_at',
                        DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                    )
                    ->where('service_type', $serviceType5),
                'latest_statuses',
                function ($join) {
                    $join->on('noc.id', '=', 'latest_statuses.model_id')
                        ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                }
            )
            ->whereIn('noc.application_no', $applicationNoArray)
            ->select(
                'noc.id',
                'noc.created_at',
                'noc.application_no',
                'noc.status',
                'sections.section_code',
                'latest_statuses.is_mis_checked',
                'latest_statuses.is_scan_file_checked',
                'latest_statuses.is_uploaded_doc_checked',
                'latest_statuses.mis_checked_by',
                'latest_statuses.scan_file_checked_by',
                'latest_statuses.uploaded_doc_checked_by',
                'pm.old_propert_id as old_property_id', // Fixed alias
                'pm.new_colony_name',
                'oc.name as colony_name',
                'pm.block_no',
                'pm.plot_or_property_no',
                'pld.presently_known_as',
                'app.is_objected',
                'flats.unique_flat_id as flat_id', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                'flats.flat_number as flat_number', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                DB::raw("'NocApplication' as model_name") // Add model_name for the first query
            );
        if ($requestedStatus) {
            $query5 = $query5->where('noc.status', ($requestedStatus));
        } else {
            $query5 = $query5->whereIn('noc.status', ($itemsIdArr));
        }

        // Add search filter if search.value is present
        if (!is_null($searchValue)) {
            $query5->where(function ($query) use ($searchValue) {
                $query->where('noc.application_no', 'like', "%$searchValue%")
                    ->orWhere('pm.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                    ->orWhere(DB::raw('LOWER(oc.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                    ->orWhere('pm.block_no', 'like', "%$searchValue%")  // Correctly reference block
                    ->orWhere('pm.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhere('flats.unique_flat_id', 'like', "%$searchValue%")  // Search by flat_id
                    ->orWhere('flats.flat_number', 'like', "%$searchValue%")    // Search by flat_number
                    ->orWhere('pld.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                    ->orWhere(DB::raw("'NocApplication'"), 'like', "%$searchValue%") // Search by model_name
                    ->orWhere('noc.created_at', 'like', "%$searchValue%");
            });
        }

        $clonedQuery1 = (clone $query1);
        $clonedQuery2 = (clone $query2);
        $clonedQuery3 = (clone $query3);
        $clonedQuery4 = (clone $query4);
        $clonedQuery5 = (clone $query5);

        // Combine all three queries using UNION
        $combinedQuery = $clonedQuery1->union($clonedQuery2)->union($clonedQuery3)->union($clonedQuery4)->union($clonedQuery5);
        return $combinedQuery;
    }

    public function applicationCanView(Request $request)
    {
        $applicationNo = $request->applicationNo;
        $isOfficeViewTheApplication = /*Auth::user()->hasRole('section-officer') || Auth::user()->hasRole('deputy-lndo') ?*/ $this->checkOfficalCanViewTheApplication($applicationNo)/* : isOfficeViewTheApplication($applicationNo)*/;
        // dd($isOfficeViewTheApplication);
        // $isOfficeViewTheApplication = isOfficeViewTheApplication($applicationNo);
        if ($isOfficeViewTheApplication === true || (is_array($isOfficeViewTheApplication) && $isOfficeViewTheApplication['canView'])) {
            return response()->json(['canView' => true, 'messaege' => 'You can view the application.']);
        } else {
            // return response()->json(['canView' => false, 'message' => 'An older application is there to be processed. Please process that first.']);
            return response()->json([
                'canView' => false,
                'message' => 'Older application ' . (is_array($isOfficeViewTheApplication) ? $isOfficeViewTheApplication['currentActionableApplicationNo'] : '') . ' is pending, please process it first.'
            ]);
        }
    }



    //for view single application details - SOURAV CHAUHAN (14/Oct/2024)
    public function view(Request $request, $id)
    {
        // dd('Hello');
       
        // dd(base64_decode($request->type));
        // try {
            // dd($request->all(), $id);
            $isSplited = 0;
            $splitedProperty = 0;
            $splited_primary_id = 0;
            $uniquePropertyId = 0;
            $requestModel = base64_decode($request->type);
            $model = '\\App\\Models\\' . $requestModel;
            $applicationDetails = $model::find($id);

            $user = Auth::user();
            $filterUserSections = $user->hasAnyRole('super-admin', 'minister', 'lndo');
            if(!$filterUserSections){
                $isApplicant = $user->hasAnyRole('applicant');
                if($isApplicant){
                    //is application applied by this applicant
                    if($applicationDetails->created_by != $user->id){
                        return redirect()->route('dashboard')->with('failure', "You don't have permission to view this application.");
                    }
                } else {
                    $assignedSections = getUserAssignedSections();
                    //  dd($applicationDetails->section_id,$assignedSections[1]);
                    if(!in_array($applicationDetails->section_id,$assignedSections[1])){
                        return redirect()->route('dashboard')->with('failure', "You don't have permission to view this application.");
                    }
                }
            }




            $downloading = isset($request->downloading) && $request->downloading == 1;
            $splittedColumn = in_array($requestModel, ['ConversionApplication', 'LandUseChangeApplication', 'DeedOfApartmentApplication']) ? 'splited_property_detail_id' : 'splitted_id';
            $itemsArrId = Item::where('group_id',17011)                        
                                ->whereIn('item_code', ['PAY_DEMAND', 'PAY_APP_CHG'])
                                ->pluck('id')
                                ->toArray();
                    
            $paymentDetails = Payment::where("model",$requestModel)->where("model_id",$applicationDetails->id)->whereIn("type",$itemsArrId)->where("property_master_id",$applicationDetails->property_master_id)->where('status',getServiceType('PAY_SUCCESS'))->latest()->first();
            // $paymentDetails = Payment::where("model",$requestModel)->where("model_id",$applicationDetails->id)->where("type",1550)->where("property_master_id",$applicationDetails->property_master_id)->first();
        //    dd($paymentDetails);
            if($paymentDetails){
                $uniquePaymentId = $paymentDetails->unique_payment_id;
            } else{
                $uniquePaymentId = 0;
            }
            // dd($uniquePaymentId);
            if ($applicationDetails) {
                 $isOfficeViewTheApplication = /* Auth::user()->hasRole('section-officer') || Auth::user()->hasRole('deputy-lndo') ? */ $this->checkOfficalCanViewTheApplication($applicationDetails['application_no']) /* : isOfficeViewTheApplication($applicationNo) */;
                
                 if(!$isOfficeViewTheApplication)
                 {
                    return redirect()->back()->with('failure', 'An older application is there to be processed. Please process that first.');
                 }

                 if (!empty($applicationDetails[$splittedColumn]) && $applicationDetails[$splittedColumn] != null && !empty($applicationDetails['old_property_id']) && $applicationDetails['old_property_id'] != null) {
                        $splittedDetails = SplitedPropertyDetail::find($applicationDetails[$splittedColumn]);
                        if (!empty($splittedDetails)) {
                            // dd($splittedDetails->child_prop_id);
                            // dd('inside if');
                            $splited_primary_id = !empty($splittedDetails->id) ? $splittedDetails->id : null;
                            $propertyMasterId = !empty($splittedDetails->property_master_id) ? $splittedDetails->property_master_id : $applicationDetails['property_master_id'];
                            $oldPropertyId = !empty($splittedDetails->old_property_id) ? $splittedDetails->old_property_id : $applicationDetails['old_property_id'];
                            $newPropertyId = !empty($splittedDetails->id) ? $splittedDetails->id : $applicationDetails['new_property_id'];
                            $isSplited = 1;
                            $splitedProperty = 1;
                            $uniquePropertyId = $splittedDetails->child_prop_id ?? $splittedDetails->child_prop_id;
                        } else {
                            $propertyMasterId = $applicationDetails['property_master_id'];
                            $oldPropertyId = $applicationDetails['old_property_id'];
                            $newPropertyId = $applicationDetails['new_property_id'];
                            $isSplited = 0;
                            $splitedProperty = 0;
                            $splited_primary_id = 0;
                            $uniquePropertyId = $applicationDetails['new_property_id'];
                        }
                    } else {
                        $propertyMasterId = $applicationDetails['property_master_id'];
                        $oldPropertyId = $applicationDetails['old_property_id'];
                        $newPropertyId = $applicationDetails['new_property_id'];
                        $isSplited = 0;
                        $splitedProperty = 0;
                        $splited_primary_id = 0;
                        $uniquePropertyId = $applicationDetails['new_property_id'];
                    }

                    // dd($uniquePropertyId);
                $application = Application::where('application_no', $applicationDetails['application_no'])->first();
                if(!empty($application->created_by)){
                    $user = User::withTrashed()->find($application->created_by);
                }
                // dd($application,$user, $user->applicantUserDetails);
                //for updating application status to in progress when application viewd
                if (Auth::user()->hasRole('section-officer') && $application->status == getServiceType('APP_NEW')) {
                    $status = getServiceType('APP_IP');
                    $application->status = $status;
                    $application->save();

                    $applicationDetails['status'] = $status;
                    $applicationDetails->save();

                    if ($requestModel == 'MutationApplication') {
                        $mailServiceType = 'Mutation';
                    } else if ($requestModel == 'ConversionApplication') {
                        $mailServiceType = 'Conversion';
                    } else if ($requestModel == 'DeedOfApartmentApplication') {
                        $mailServiceType = 'Deed Of Apartment';
                    } else if ($requestModel == 'LandUseChangeApplication') {
                        $mailServiceType = 'Land Use Change';
                    } else if ($requestModel == 'NocApplication') {
                        $mailServiceType = 'No Objection Certificate';
                    } else {
                        $mailServiceType = 'Service Not Defined';
                    }

                    //for send notification - SOURAV CHAUHAN (21/Nov/2024)
                    $user = User::find($application->created_by);
                    // $propertyMasterId = $applicationDetails['property_master_id'];
                    // $oldPropertyId = $applicationDetails['old_property_id'];
                    // $newPropertyId = $applicationDetails['new_property_id'];
                    
                    
                    // dd($applicationDetails[$splittedColumn]);
                    $propertyKnownAs = PropertyLeaseDetail::where('property_master_id', $propertyMasterId)
                        ->pluck('presently_known_as')
                        ->first();

                    $data = [
                        'application_type' => $mailServiceType,
                        'application_no' => $applicationDetails['application_no'],
                        'property_details' => $propertyKnownAs . " [" . $oldPropertyId . " (" . $newPropertyId . ") ]"
                    ];

                    $action = 'APP_INP';
                   
                    try {
                        $mailSettings = app(SettingsService::class)->getMailSettings($action);
                        $mailer = new \App\Mail\CommonPHPMail($data, $action, $communicationTrackingId ?? null);
                        $mailResponse = $mailer->send($user['email'], $mailSettings);

                        Log::info("Email sent successfully.", [
                            'action' => $action,
                            'email'  => $user['email'],
                            'data'   => $data,
                        ]);
                    } catch (\Exception $e) {
                        Log::error("Email sending failed.", [
                            'action' => $action,
                            'email'  => $user['email'],
                            'error'  => $e->getMessage(),
                        ]);
                    }
                    $mobileNo = $user['mobile_no'];
                    $checkSmsTemplateExists = checkTemplateExists('sms', $action);
                    $communicationService = new CommunicationService;
                    if (!empty($checkSmsTemplateExists)) {
                        $communicationService->sendSmsMessage($data, $mobileNo, $action);
                    }
                    $checkWhatsappTemplateExists = checkTemplateExists('whatsapp', $action);
                    if (!empty($checkWhatsappTemplateExists)) {
                        $communicationService->sendWhatsAppMessage($data, $mobileNo, $action);
                    }
                }
                //for logs - SOURAV CHAHAN (18/Nov/2024) 
                $actionLink = url('edharti/applications/' . $id) . '?type=' . $request->type;
                UserActionLogHelper::UserActionLog(
                    'Application View',
                    $actionLink,
                    'adminApplication',
                    "Application <a target='_blank' href='" . $actionLink . "'>" . $applicationDetails['application_no'] . "</a> has been viewed by user " . Auth::user()->name . "."
                );
                // }


                // if ($applicationDetails) {
                $data = [];
                $data['application'] = $application;
                // dd($data);

                $flatDetails = Flat::where('id', $applicationDetails['flat_id'])->first();
                if ($flatDetails) {
                    $applicationDetails['flatDetails'] = $flatDetails;
                }

                /** get pending demands amount for property added by Nitin 19Nov2024*/
                // $pendingAmount = 0;
                // $demands = Demand::where('property_master_id', $applicationDetails->property_master_id)->where(function ($query) use ($applicationDetails) {
                //     if (isset($applicationDetails->splited_property_detail_id) && !is_null($applicationDetails->splited_property_detail_id)) {
                //         return $query->where('splited_property_detail_id', $applicationDetails->splited_property_detail_id);
                //     } else {
                //         return $query->whereNull('splited_property_detail_id');
                //     }
                // })
                //     ->whereIn('status', [getServiceType('DEM_PENDING'), getServiceType('DEM_PART_PAID'), getServiceType('DEM_DRAFT'),getServiceType('DEM_PAID')])
                //     ->where('app_no',$applicationDetails['application_no'])
                //     ->get();




// dd($applicationDetails);
                /** get pending demands amount with flat condition - SOURAV CHAUHAN (02 June 2026) */
                $pendingAmount = 0;

                $demands = Demand::where(function ($query) use ($applicationDetails,$requestModel) {

                    // If flat_id exists then search demand by flat_id
                    if (!empty($applicationDetails->flat_id)) {
                        $query->where('flat_id', $applicationDetails->flat_id);

                    } else {
                            if ($requestModel == 'MutationApplication' || $requestModel == 'NocApplication') {
                                $splitPropertyId = 'splitted_id';
                            } else {
                                $splitPropertyId = 'splited_property_detail_id';
                            }

                        // Otherwise search by property
                        $query->where('property_master_id', $applicationDetails->property_master_id)

                            ->where(function ($subQuery) use ($applicationDetails,$splitPropertyId) {

                                if (
                                    isset($applicationDetails->$splitPropertyId) &&
                                    !is_null($applicationDetails->$splitPropertyId)
                                ) {

                                    $subQuery->where(
                                        'splited_property_detail_id',
                                        $applicationDetails->$splitPropertyId
                                    );

                                } else {

                                    $subQuery->whereNull('splited_property_detail_id');
                                }
                            });
                    }
                })
                ->whereIn('status', [
                    getServiceType('DEM_PENDING'),
                    getServiceType('DEM_PART_PAID'),
                    getServiceType('DEM_DRAFT'),
                    getServiceType('DEM_PAID')
                ])
                ->where('app_no', $applicationDetails['application_no'])
                ->get();


// dd($demands);






                 $isNewDemand = true;
                if($demands->isEmpty() && empty($applicationDetails->flat_id)){
                    $isNewDemand = false;
                    $oldDemand = OldDemand::where('property_id', $applicationDetails['old_property_id'])->where('outstanding','!=',0)->first();
                    $pendingAmount = $oldDemand['outstanding'] ?? 0;
                    $demandAmount = $oldDemand['amount'] ?? 0;
                    $paidAmount = $oldDemand['paid_amount'] ?? 0;
                } else {
                    $pendingAmount = $demands->sum('balance_amount');
                    $demandAmount = $demands->sum('net_total');
                    $paidAmount = $demands->sum('paid_amount');
                }


                // Store in response array
                $data['pendingAmount'] = $pendingAmount;
                $data['demandAmount'] = $demandAmount;
                $data['paidAmount'] = $paidAmount;
                $data['isNewDemand'] = $isNewDemand;
                $data['pendingDemands'] = $demands;

                
                /** ---------------------------------------- */

                // $documents = Document::where('model_name', $requestModel)
                //     ->where('model_id', $id)
                //     ->get();
                // // dd($documents, $decodedModel, $id);
                // $finalDocs = [];

                // foreach ($documents as $key => $document) {
                //     // foreach ($document as $doc) {
                //     $values = DocumentKey::where('document_id', $document->id)->get();
                //     $document->values = $values;
                //     // }
                //     $finalDocs[] = $document;
                // }

              
                
                //for show hide action buttons
                $applicationLatestMov = ApplicationMovement::where('application_no', $applicationDetails['application_no'])->latest()->first();
                $showActionButtons = false;
                $showRevertButton = false; //show revert button in vew file added by Nitin 09Dec2024
                if ($application->status == getServiceType('APP_REJ') || $application->status == getServiceType('APP_APR')) {
                    $showActionButtons = false;
                } else if ($applicationLatestMov) {
                    $assignedTo = $applicationLatestMov->assigned_to;
                    // dd($assignedTo,Auth::user()->id);
                    if ($assignedTo == Auth::user()->id) {
                        $showActionButtons = true;


                        //For showing revert button to login user when forward done on multiple levels - 06 August 2026 START *********
                        $isApplicationRevertible = ApplicationMovement::where('application_no', $applicationDetails['application_no'])
                                                ->where('assigned_by','!=',Auth::id())
                                                ->where('assigned_to',Auth::id())
                                                ->where('is_forwarded', 1)
                                                ->latest()
                                                ->first();
                        if(!empty($isApplicationRevertible)){
                            $showRevertButton =  true;
                        }
                        //For showing revert button to login user when forward done on multiple levels - 06 August 2026 END *********




                        if ($applicationLatestMov->is_forwarded >= 1) {
                            $showActionButtons = false;
                            $showRevertButton =  true;
                            $firstForwardEntry = ApplicationMovement::where('application_no', $applicationDetails['application_no'])->where('is_forwarded', 1)->first();
                            if (!empty($firstForwardEntry) && $firstForwardEntry->assigned_by == Auth::id()) {
                                $showActionButtons = true;
                                $showRevertButton =  false;
                            }
                        }
                        // dd($showActionButtons);
                    } else if ($assignedTo == null && Auth::user()->hasRole('section-officer')) {
                        $showActionButtons = true;
                    }
                }
                
                // for show hide the create letter button  SOURAV CHAUHAN
                $showCreateLetterButtons = false;
                $applicationMovAll = ApplicationMovement::where('application_no', $applicationDetails['application_no'])->get();
                if ($applicationMovAll->count() == 1 || $applicationMovAll->last()->assigned_to_role == 7 || $applicationMovAll->last()->assigned_to_role == 4) {
                    $showCreateLetterButtons = true;
                }


                // dd($applicationLatestMov);


                // for show hide the send appointment link button for CDV  SOURAV CHAUHAN
                /*$showAppointmentLinkButton = true;
                $applicationAppointmentLink = ApplicationAppointmentLink::where('application_no', $applicationDetails['application_no'])->latest()->first();
                if ($applicationAppointmentLink) {
                    $showAppointmentLinkButton = false;
                }*/

                $applicationAppointmentLink = ApplicationAppointmentLink::where('application_no', $applicationDetails['application_no'])->latest()->first();


                //for show hide warning mail sent button
                $shoWarningMailButton = false;
                if ($application->is_warning_sent == null) {
                    if (isset($applicationAppointmentLink) && $applicationAppointmentLink->is_attended == 0 && $applicationAppointmentLink->is_active == 0) {
                        $shoWarningMailButton = true;
                    }
                }

                // for checking, is proof reading attended by applicant- SOURAV CHAUHAN (17/Dec/2024)
                $isAppointmentAttended = false;
                if (isset($applicationAppointmentLink) && $applicationAppointmentLink->is_attended == 1) {
                    $isAppointmentAttended = true;
                }
                $showApproveButton = false; // Nitin 13Dec2024
                $showCdvActionInDocuments = false;
                $showRecommandForAppoval = false;
                $showUploadSignedLetter = false;
                $showActionButtonSection = false;
                $showRecommandAndObjectButtonSection = true;
                $isSignedLetterAvailable = false;
                $applicationRecommendeByCdv = false;
                switch ($requestModel) {
                    case 'MutationApplication':
                         $extraApplicants = ApplicationExtraApplicant::where('application_no', $applicationDetails['application_no'])->get();
                        $showRecommandForAppoval = true;
                        $applicationType = 'Mutation';
                        $serviceType = getServiceType('SUB_MUT');
                        $documents = Document::where('service_type', $serviceType)->where('model_name', $requestModel)->where('model_id', $id)->get();
                        // dd($documents);
                        if (!empty($documents)) {
                            //show or not CDV action in document listing - SOURAV CHAUHAN (23/Dec/2024)
                            $isApplicationMoveToCdv = ApplicationMovement::where('application_no', $applicationDetails['application_no'])->where('assigned_to_role', 4)->where('action', '!=', null)->count();
                            if ($isApplicationMoveToCdv > 0) {
                                $showCdvActionInDocuments = true;
                            }
                        }

                        //For showing the upload singned letter option
                       // $isApplicationRecommendeByCdv = ApplicationMovement::where('application_no', $applicationDetails['application_no'])->where('assigned_by_role', 4)->where('action', 'RECOMMENDED')->count();
                       if($applicationDetails->service_type->item_code == "CONVERSION" || $applicationDetails->service_type->item_code == "SUB_MUT"){
        	                $isApplicationRecommendeByCdv = ApplicationMovement::where('application_no', $applicationDetails['application_no'])->where('assigned_by_role', 4)->where('action', 'RECOMMENDED')->count();
                        }
                        else {
                            $isApplicationRecommendeByCdv = ApplicationMovement::where('application_no', $applicationDetails['application_no'])->where('assigned_by_role', 7)->where('action', 'RECOMMENDED')->get();
                        }
        // dd($isApplicationRecommendeByCdv);
                        if ($isApplicationRecommendeByCdv > 0) {
                            $showUploadSignedLetter = true;
                            $applicationRecommendeByCdv = true;
                        }
        //                  if ($isApplicationRecommendeByCdv > 0) {
        //                     $showUploadSignedLetter = 1;
		// }
		// else {
		// 	 $showUploadSignedLetter = 0;
		// }

                        $isSignedLetterAvailable = Application::where('application_no', $applicationDetails['application_no'])
                            ->whereNotNull('Signed_letter')
                            ->exists();


                        //for showing Approve button to deputy - SOURAV CHAUHAN (31/Dec/2024)
                        if (!empty($application->Signed_letter)) {
                            $showApproveButton = true;
                        }



                        //coapplicants
                        $data['coapplicants'] = Coapplicant::where('service_type', $serviceType)->where('model_name', $requestModel)->where('model_id', $id)->get();
                        $showAppointmentLinkButton = self::showAppointmentLinkButtonFun($requestModel, $applicationDetails['application_no']);

                        //for showing Recommend for approval button at CDV end - SOURAV CHAUHAN (24/Dec/2024)
                        $documentsUploadedByApplicant = Document::where('service_type', $serviceType)
                            ->where('model_name', $requestModel)
                            ->where('model_id', $id)
                            ->whereNotNull('file_path')
                            ->get();
                        foreach ($documentsUploadedByApplicant as $documentApp) {
                            if (!is_null($documentApp->file_path)) {
                                if (is_null($documentApp->office_file_path)) {
                                    $showRecommandForAppoval = false;
                                    break;
                                } else {
                                    continue;
                                }
                            }
                        }

                        $applicantShare = ApplicantShare::where('application_no', $applicationDetails['application_no'])->value('share');
                        // Fetch documents from application_document_details table
                        // In your controller method where you prepare the view data
                        $applicationDocumentDetails = ApplicationDocumentDetail::where('application_no', $applicationDetails['application_no'])
                            ->orderBy('id', 'asc')
                            ->get();

                        // Prepare document data for 4 rows
                        $mutationApplicationDocumentData = [
                            0 => ['name' => '', 'date' => ''],
                            1 => ['name' => '', 'date' => ''],
                            2 => ['name' => '', 'date' => ''],
                            3 => ['name' => '', 'date' => '']
                        ];

                        // Populate with existing data
                        foreach ($applicationDocumentDetails as $index => $documentData) {
                            if ($index < 4) { // Only fill up to 4 rows
                                $mutationApplicationDocumentData[$index] = [
                                    'name' => $documentData->document_name ?? '',
                                    'date' => $documentData->document_date ? Carbon::parse($documentData->document_date)->format('Y-m-d') : ''
                                ];
                            }
                        }


                        break;
                    case 'LandUseChangeApplication':
                        $applicationType = 'Land Use Change';
                        $serviceType = getServiceType('LUC');
                        $latestAppAction = AppLatestAction::where('application_no', $applicationDetails['application_no'])->first();
                        /** code adde by NItin 13 Dec 2024 */
                        if (Auth::user()->hasRole('deputy-lndo') && $latestAppAction && $latestAppAction->latest_action == "RECOMMENDED" && Self::getUserIdBySectionCodeAndRole(11) == $latestAppAction->latest_action_by) {
                            $showApproveButton = true;
                        }

                        /** --- code adde by NItin 13 Dec 2024 */
                        $documentList = config('applicationDocumentType.LUC.documents');
                        $requiredDocuments = collect($documentList)->where('required', 1)->all();
                        $requiredDocumentTypes = array_map(function ($element) {
                            return $element['label'];
                        }, $requiredDocuments);
                        $uploadedDocuments = Document::where('service_type', $serviceType)->where('model_name', $requestModel)->where('model_id', $id)->get();
                        // dd($requiredDocumentTypes, $uploadedDocuments);
                        $showAppointmentLinkButton = false;
                        $documents = [
                            'required' => [],
                            'optional' => [],
                            'additional' => [] // added by Nitin on 24-01-2025
                        ];

                        // Required documents
                        foreach ($requiredDocumentTypes as $requiredDocument) {
                            foreach ($uploadedDocuments as $uploadedDocument) {
                                if ($requiredDocument == $uploadedDocument->title) {
                                    $documents['required'][] = [
                                        'title' => $uploadedDocument->title,
                                        'file_path' => $uploadedDocument->file_path
                                    ];
                                    break;
                                }
                            }
                        }

                        //optional documents
                        $optionalDocuments = collect($documentList)->where('required', 0)->all();
                        $optionalDocumentTypes = array_map(function ($element) {
                            return $element['label'];
                        }, $optionalDocuments);

                        foreach ($optionalDocumentTypes as $optionalDocument) {
                            $found = false;
                            foreach ($uploadedDocuments as $uploadedDocument) {
                                if ($optionalDocument == $uploadedDocument->title) {
                                    $documents['optional'][] = [
                                        'title' => $uploadedDocument->title,
                                        'file_path' => $uploadedDocument->file_path
                                    ];
                                    $found = true;
                                    break;
                                }
                            }
                            if (!$found) {
                                $documents['optional'][] = [
                                    'title' => $optionalDocument,
                                    'file_path' => null
                                ];
                            }
                        }
                        /** get additional documents */

                        $additionalDocuments = $uploadedDocuments->where('document_type', 'AdditionalDocument')->values()->toArray();
                        $documents['additional'] = $additionalDocuments;
                        // dd($documents);
                        $data['documents'] = $documents;
                        break;
                    case 'DeedOfApartmentApplication':
                        $showRecommandForAppoval = true;
                        $showActionButtonSection = true;
                        $showRecommandAndObjectButtonSection = false;
                        $applicationType = 'Deed Of Apartment';
                        $serviceType = getServiceType('DOA');
                        $requiredDocuments = config('applicationDocumentType.DOA.documents');
                        $uploadedDocuments = Document::where('service_type', $serviceType)->where('model_name', $requestModel)->where('model_id', $id)->get();
                        $documents = [
                            'required' => [],
                        ];
                        foreach ($requiredDocuments as $key => $requiredDocument) {
                            foreach ($requiredDocument as $key => $value) {
                                foreach ($uploadedDocuments as $uploadedDocument) {
                                    if ($requiredDocument[$key] == $uploadedDocument->document_type) {
                                        $documents['required'][] = [
                                            'title' => $uploadedDocument->title,
                                            'file_path' => $uploadedDocument->file_path
                                        ];
                                        break;
                                    }
                                }
                            }
                        }

                        $data['documents'] = $documents;
                        //For showing the upload singned letter option
                        // $isApplicationRecommendeBySection = ApplicationMovement::where('application_no', $applicationDetails['application_no'])->where('assigned_by_role', 7)->where('action', 'RECOMMENDED')->count();
                        $isApplicationRecommendeBySection = ApplicationMovement::where('application_no', $applicationDetails['application_no'])->where('assigned_by_role', 7)->where('action', 'RECOMMENDED_FOR_APPROVAL')->count();
                        if ($isApplicationRecommendeBySection > 0) {
                            $showUploadSignedLetter = true;
                        }


                        //for showing Approve button to deputy - SOURAV CHAUHAN (31/Dec/2024)
                        if (!empty($application->Signed_letter)) {
                            $showApproveButton = true;
                        }
                        $showAppointmentLinkButton = self::showAppointmentLinkButtonFun($requestModel, $applicationDetails['application_no']);
                        //for showing Recommend for approval button at Deputy end - LALIT TIWAR (01/Jan/2025)
                        $documentsUploadedByApplicant = Document::where('service_type', $serviceType)
                            ->where('model_name', $requestModel)
                            ->where('model_id', $id)
                            ->whereNotNull('file_path')
                            ->get();

                            // dd($documentsUploadedByApplicant);
                        foreach ($documentsUploadedByApplicant as $documentApp) {
                            if (!is_null($documentApp->file_path)) {
                                if (is_null($documentApp->office_file_path)) {
                                    $showRecommandForAppoval = false;
                                    $recordExistsAppLink = ApplicationAppointmentLink::where('application_no', $applicationDetails['application_no'])->where('is_active', 1)->exists();
                                    if ($recordExistsAppLink) {
                                        $showRecommandAndObjectButtonSection = true;
                                    }
                                    break;
                                } else {
                                    continue;
                                }
                            }
                        }

                        //For showing Recommonded & object button to section
                        $recordExists = AppLatestAction::where('application_no', $applicationDetails['application_no'])->exists();
                        if (!$recordExists) {
                            $showActionButtonSection = true;
                        }




                        break;
                    case 'ConversionApplication':
                        $showRecommandForAppoval = true;
                        $applicationType = 'Conversion';
                        $serviceType = getServiceType('CONVERSION');
                        $requiredDocuments = config('applicationDocumentType.CONVERSION.Required');
                        $uploadedDocuments = Document::where('service_type', $serviceType)->where('model_name', $requestModel)->where('model_id', $id)->get();


                        if (!empty($uploadedDocuments)) {
                            //show or not CDV action in document listing - SOURAV CHAUHAN (23/Dec/2024)
                            $isApplicationMoveToCdv = ApplicationMovement::where('application_no', $applicationDetails['application_no'])->where('assigned_to_role', 4)->where('action', '!=', null)->count();
                            if ($isApplicationMoveToCdv > 0) {
                                $showCdvActionInDocuments = true;
                            }
                        }

                        //For showing the upload singned letter option
                        $isApplicationRecommendeByCdv = ApplicationMovement::where('application_no', $applicationDetails['application_no'])->where('assigned_by_role', 4)->where('action', 'RECOMMENDED')->count();
                        if ($isApplicationRecommendeByCdv > 0) {
                            $showUploadSignedLetter = true;
                             $applicationRecommendeByCdv = true;
                        }


                        //for showing Approve button to deputy - SOURAV CHAUHAN (31/Dec/2024)
                         $isSignedLetterAvailable = Application::where('application_no', $applicationDetails['application_no'])
                            ->whereNotNull('Signed_letter')
                            ->exists();

                        if (!empty($application->Signed_letter)) {
                            $showApproveButton = true;
                        }



                        $data['coapplicants'] = Coapplicant::where('service_type', $serviceType)->where('model_name', $requestModel)->where('model_id', $id)->get();
                        $documents = [
                            'required' => [],
                        ];
                        foreach ($requiredDocuments as $key => $requiredDocument) {
                            foreach ($uploadedDocuments as $uploadedDocument) {
                                if ($key == $uploadedDocument->title) {
                                    $documents['required'][] = [
                                        'title' => $uploadedDocument->title,
                                        'file_path' => $uploadedDocument->file_path
                                    ];
                                    break;
                                }
                            }
                        }
                        $data['documents'] = $documents;
                        $showAppointmentLinkButton = self::showAppointmentLinkButtonFun($requestModel, $applicationDetails['application_no']);

                        //for showing Recommend for approval button at CDV end - SOURAV CHAUHAN (24/Dec/2024)
                        $documentsUploadedByApplicant = Document::where('service_type', $serviceType)
                            ->where('model_name', $requestModel)
                            ->where('model_id', $id)
                            ->whereNotNull('file_path')
                            ->get();
                        foreach ($documentsUploadedByApplicant as $documentApp) {
                            if ($documentApp->document_type != 'mortgageNoCFile' && $documentApp->document_type != 'convCourtOrderFile') {
                                if (!is_null($documentApp->file_path)) {
                                    if (is_null($documentApp->office_file_path)) {
                                        $showRecommandForAppoval = false;
                                        break;
                                    } else {
                                        continue;
                                    }
                                }
                            }
                        }



                        break;

                    case 'NocApplication':
                        $showRecommandForAppoval = true;
                        $applicationType = 'Noc';
                        $serviceType = getServiceType('NOC');
                        $requiredDocuments = config('applicationDocumentType.NOC.Required');
                        $uploadedDocuments = Document::where('service_type', $serviceType)->where('model_name', $requestModel)->where('model_id', $id)->get();

                        //For showing the upload singned letter option
                        $isApplicationRecommendeByCdv = ApplicationMovement::where('application_no', $applicationDetails['application_no'])->where('assigned_by_role', 7)->where('action', 'RECOMMENDED')->count();
                        if ($isApplicationRecommendeByCdv > 0) {
                            $showUploadSignedLetter = true;
                        }


                        //for showing Approve button to deputy - SOURAV CHAUHAN (31/Dec/2024)
                        if (!empty($application->Signed_letter)) {
                            $showApproveButton = true;
                        }



                        $data['coapplicants'] = Coapplicant::where('service_type', $serviceType)->where('model_name', $requestModel)->where('model_id', $id)->get();

                        $documents = [
                            'required' => [],
                        ];
                        foreach ($requiredDocuments as $key => $requiredDocument) {
                            foreach ($uploadedDocuments as $uploadedDocument) {
                                if ($key == $uploadedDocument->title) {
                                    $documents['required'][] = [
                                        'title' => $uploadedDocument->title,
                                        'file_path' => $uploadedDocument->file_path
                                    ];
                                    break;
                                }
                            }
                        }
                        $data['documents'] = $documents;
                        $showAppointmentLinkButton = self::showAppointmentLinkButtonFun($requestModel, $applicationDetails['application_no']);

                        //for showing Recommend for approval button at CDV end - SOURAV CHAUHAN (24/Dec/2024)
                        $documentsUploadedByApplicant = Document::where('service_type', $serviceType)
                            ->where('model_name', $requestModel)
                            ->where('model_id', $id)
                            ->whereNotNull('file_path')
                            ->get();
                        foreach ($documentsUploadedByApplicant as $documentApp) {
                            if ($documentApp->document_type != 'mortgageNoCFile' && $documentApp->document_type != 'convCourtOrderFile') {
                                if (!is_null($documentApp->file_path)) {
                                    if (is_null($documentApp->office_file_path)) {
                                        $showRecommandForAppoval = false;
                                        break;
                                    } else {
                                        continue;
                                    }
                                }
                            }
                        }
                        break;
                    default:
                        $applicationType = '';
                        break;
                }

                $data['applicationType'] = $applicationType;
                $data['roles'] = Auth::user()->roles[0]->name;
                $property = PropertyMaster::find($applicationDetails['property_master_id']);
                $data['showApproveButton'] = $showApproveButton;
                $data['uniquePaymentId'] = $uniquePaymentId;
                $scannedFiles['files'] = [];
                if (!empty($property['id'])) {

                    $splitedColumnName = in_array($requestModel, ['LandUseChangeApplication', 'ConversionApplication']) ? 'splitted_property_detail_id' : 'splitted_id';
                    // dd($applicationDetails[$splitedColumnName],$applicationDetails['property_master_id']);
                    // dd($splitedColumnName);
                    // dd($applicationDetails);
                    $scannedFiles = GeneralFunctions::getLocalScannedFiles($applicationDetails['property_master_id'], $applicationDetails[$splitedColumnName]);
                    // $scanningLink=Config:: get('constants.propertyDocList');
                    // // dd($scanningLink);
                    // $response = Http::timeout(10)->get( $scanningLink . $property['old_propert_id']);
                    // // $response = Http::get('https://ldo.gov.in/edhartiapi/Api/PropDocs/bypropertyID?PropertyID=' . $property['old_propert_id']);
                    // // dd($response);
                    // if ($response->successful()) {
                    //     $jsonData = $response->json();
                        
                    //     // Proceed if jsonData is not empty
                    //     if (!empty($jsonData)) {
                    //         $scannedFiles['baseUrl'] = $jsonData[0]['Path'];
                    //         $vol=1;
                    //        foreach ($jsonData[0]['ListFileName'] as $value) {
                    //         	$decoded = $this->decryptDotNet($value['PropertyFileNameDcrpt'], '1234567890123456');
					// 			// if (!$decoded) { 
					// 			//     // Decode fail ho gaya → original naam use karo
					// 			//     $decoded = $value['PropertyFileName'];
					// 			// }
					// 			//$scannedFiles['files'][] = $decoded;
                    //          //$scannedFiles['files'][] = $this->decryptDotNet($value['PropertyFileName'],'1234567890123456');
                    //             $scannedFiles['files'][] = [
                    //             'actual' => $value['PropertyFileNameDcrpt'],
                    //            'display' => 'Volume-'. $vol++
                    //             ];

                    //            // $vol++;
                    //         }
                    //     } else {
                    //         // Handle case where the response is empty or not as expected
                    //         Log::warning('API response returned empty or invalid data.');
                    //         $scannedFiles['files'] = [];
                    //     }
                    // } elseif ($response->clientError()) {
                    //     \Log::error("Client error: " . $response->status() . ' - ' . $response->body());
                    //     $scannedFiles['files'] = [];
                    // } elseif ($response->serverError()) {
                    //     \Log::error("Server error: " . $response->status() . ' - ' . $response->body());
                    //     $scannedFiles['files'] = [];
                    // } else {
                    //     Log::error('API request failed with status: ' . $response->status());
                    //     $scannedFiles['files'] = [];
                    // }

                    $data['scannedFiles'] = $scannedFiles;
                    $data['propertyMasterId'] = $property['id'];
                    $data['suggestedPropertyId'] = $oldPropertyId;
                    $data['oldPropertyId'] = $oldPropertyId;
                    $data['uniquePropertyId'] = $uniquePropertyId;
                    $data['sectionCode'] = $property['section_code'];
                    $data['isSplited'] = $isSplited;
                    $data['splitedProperty'] = $splitedProperty;
                    $data['splited_primary_id'] = $splited_primary_id;

                    // dd($data);

                } else {
                    $data['propertyMasterId'] = '';
                    $data['suggestedPropertyId'] = '';
                    $data['oldPropertyId'] = '';
                    $data['uniquePropertyId'] = '';
                    $data['sectionCode'] = '';
                    $data['isSplited'] = '';
                    $data['splitedProperty'] = '';
                    $data['splited_primary_id'] = '';

                }

                // dd($data);

                $applicationDetails['serviceType'] = $serviceType;
                if ($applicationDetails->section_id) {
                    $section = Section::find($applicationDetails->section_id);
                    $applicationDetails['sectionCode'] = $section['section_code'];
                } else {
                    $applicationDetails['sectionCode'] = $applicationDetails->sectionCode;
                }

                $data['details'] = $applicationDetails;
                if($applicationType == 'Noc'){
                    $doe = PropertyLeaseDetail::where('property_master_id',$applicationDetails->property_master_id)->value('doe');
                    $data['details']['executed_on'] = $doe;
                }
                $data['applicationMovementId'] = $id;

                // $data['checkList'] = ApplicationStatus::where('service_type', $serviceType)->where('model_id', $id)->first();
                // Specify the column to sort by
                $data['checkList'] = ApplicationStatus::where('service_type', $serviceType)->where('model_id', $id)->latest('created_at')->first();
                $oldPropertyId = (string) $applicationDetails['old_property_id'];
                $UserApplicationController = new UserApplicationController();
                $data['propertyCommonDetails'] = $UserApplicationController->getPropertyCommonDetails($oldPropertyId);

                // $data['user'] = User::find($applicationDetails['created_by']);
                $data['user'] = User::withTrashed()->find($applicationDetails['created_by']);
                $data['latestAppAction'] = AppLatestAction::where('application_no', $applicationDetails['application_no'])->first();
                // dd($showActionButtons);
                $data['showActionButtons'] = $showActionButtons;
                $data['showRevertButton'] = $showRevertButton;
                $data['showCreateLetterButtons'] = $showCreateLetterButtons;
                $data['showAppointmentLinkButton'] = $showAppointmentLinkButton;
                $data['applicationAppointmentLink'] = $applicationAppointmentLink;
                $data['actionServiceType'] = $serviceType;
                $data['latestMovement'] = $applicationLatestMov;
                $data['isAppointmentAttended'] = $isAppointmentAttended;
                $data['showCdvActionInDocuments'] = $showCdvActionInDocuments;
                $data['showRecommandForAppoval'] = $showRecommandForAppoval;
                $data['showUploadSignedLetter'] = $showUploadSignedLetter;
                $data['showActionButtonSection'] = $showActionButtonSection;
                $data['showRecommandAndObjectButtonSection'] = $showRecommandAndObjectButtonSection;
                $data['shoWarningMailButton'] = $shoWarningMailButton;
                $data['isSignedLetterAvailable'] = $isSignedLetterAvailable;
                $data['applicationRecommendeByCdv'] = $applicationRecommendeByCdv;

                $data['splittedColumn'] = $splittedColumn;

                $data['applicationDocumentDetails'] = !empty($mutationApplicationDocumentData) ? $mutationApplicationDocumentData : [];
                $data['applicantShare'] = !empty($applicantShare) ? $applicantShare : '';
                 $data['extraApplicants'] = !empty($extraApplicants) ? $extraApplicants : '';



                // Get all roles for Move Forward Application To Department - Lalit on 25/Nov/2024
                /* if (Auth::user()->hasRole('deputy-lndo')) {
                    $data['departmentRoles'] = Role::whereNotIn('name', ['deputy-lndo','CDN', 'user', 'super-admin', 'admin', 'applicant'])->pluck('name', 'name')->all();
                } elseif (Auth::user()->hasRole('lndo')) {
                    $data['departmentRoles'] = Role::whereNotIn('name', ['lndo','CDN', 'user', 'super-admin', 'admin', 'applicant'])->pluck('name', 'name')->all();
                } elseif (Auth::user()->hasRole('section-officer')) {
                    $data['departmentRoles'] = Role::whereIn('name', ['deputy-lndo'])->pluck('name', 'name')->all();
                } elseif (Auth::user()->hasRole('engineer-officer')) {
                    $data['departmentRoles'] = Role::whereIn('name', ['AE', 'JE'])->pluck('name', 'name')->all();
                } else {
                    $data['departmentRoles'] = [];
                } */

                // Update assigned department roles to forward application role wise - Lalit Tiwari (16/04/2025)
                $user = Auth::user();
                $role = collect([
                    'deputy-lndo' => ['section-officer', 'AO', 'engineer-officer', 'lndo', 'audit-cell', 'vegillence'],
                    'lndo' => ['section-officer', 'deputy-lndo'],
                    'CDV' => ['section-officer', 'deputy-lndo'],
                    'section-officer' => ['deputy-lndo'],
                    'engineer-officer' => ['deputy-lndo', 'AE', 'JE'],
                    'AE' => ['engineer-officer', 'JE'],
                    'AO' => ['deputy-lndo'],
                    'audit-cell' => ['deputy-lndo'],
                    'it-cell' => ['section-officer'],
                    'vegillence' => ['deputy-lndo'],
                ]);

                $latestForwardedBy = ApplicationMovement::where('application_no', $applicationDetails['application_no'])->where('is_forwarded', 1)->first();
                // if(isset($latestForwardedBy)){
                //     $forwardedByRoleId = $latestForwardedBy->assigned_by_role;
                //     $forwardedByRole = Role::find($forwardedByRoleId)->name;
                //     $role = $role->map(function ($roles) {
                //         return array_values(array_filter($roles, function ($item) {
                //             return $item !== $forwardedByRole;
                //         }));
                //     });
                // }


                if (isset($latestForwardedBy)) {
                    $forwardedByRoleId = $latestForwardedBy->assigned_by_role;
                    $forwardedByRole = optional(Role::find($forwardedByRoleId))->name;

                    if ($forwardedByRole) {
                        $role = $role->map(function ($roles) use ($forwardedByRole) {
                            return array_values(array_filter($roles, function ($item) use ($forwardedByRole) {
                                return $item !== $forwardedByRole;
                            }));
                        });
                    }
                }

                $matchedRole = $role->first(function ($_roles, $key) use ($user) {
                    return $user->hasRole($key);
                });

                // Commented the code below to display the title instead of the name in the "Forward to Department Role" dropdown - Lalit (2 April 2025)
                // $data['departmentRoles'] = $matchedRole
                //     ? Role::whereIn('name', $matchedRole)->pluck('name', 'name')->all()
                //     : [];
                // Added the code below to display the title in the "Forward to Department Role" dropdown - Lalit (2 April 2025)
                $data['departmentRoles'] = $matchedRole
                    ? Role::whereIn('name', $matchedRole)->pluck('title', 'name')->all()
                    : [];

                    // dd($downloading);
                    if (!$downloading) {
                        return view('application.view', $data);
                    } else {
                    // dd($data['details']->executed_on);
                    // dd($data['details']);
                        
                    $data['downloading'] = $downloading;
                    $doe = PropertyLeaseDetail::where('property_master_id', $applicationDetails['property_master_id'])
                        ->pluck('doe')
                        ->first();
                        $data['doe'] = $doe;
                    // return view('application.pdf', $data);
                    $pdf = PDF::loadView('application.pdf', $data);
                    return $pdf->download($applicationDetails['application_no'] . '.pdf', $data);
                }
            } else {
                return redirect()->back()->with('failure', 'Application not found!');
            }
        // } catch (RequestException $e) {
        //     \Log::error('Request exception: ' . $e->getMessage());
        //     return redirect()->back()->with('failure', 'Could not connect to the external API. Please try again later');
        // } catch (\Exception $e) {
        //     \Log::error('Unexpected error: ' . $e->getMessage());
        //     return redirect()->back()->with('failure', 'An unexpected error occurred!');
        // }
    }

    public function decryptDotNet($token, $key)
    {
        if (!is_string($token) || $token === '') {
            return false;
        }

        // .NET UrlTokenEncode appends a single-digit pad count (0,1,2) at the end.
        $padCount = 0;
        if (ctype_digit(substr($token, -1))) {
            $padCount = (int)substr($token, -1);
            $token = substr($token, 0, -1);
        }

        // Convert URL-safe Base64 -> standard Base64
        $b64 = strtr($token, '-_', '+/');

        // Restore padding based on padCount (UrlTokenEncode uses 0/1/2)
        if ($padCount > 0) {
            $b64 .= str_repeat('=', $padCount);
        } else {
            // also ensure length multiple of 4
            $mod4 = strlen($b64) % 4;
            if ($mod4 > 0) $b64 .= str_repeat('=', 4 - $mod4);
        }

        $cipherData = base64_decode($b64, true);
        if ($cipherData === false) return false;

        $iv = str_repeat("\0", 16);
        $decrypted = openssl_decrypt($cipherData, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        if ($decrypted === false) return false;

        // PKCS7 unpad
        $len = strlen($decrypted);
        if ($len === 0) return '';
        $pad = ord($decrypted[$len - 1]);
        if ($pad > 0 && $pad <= 16) {
            // basic check that padding bytes are all same
            $paddingOk = substr($decrypted, -$pad) === str_repeat(chr($pad), $pad);
            if ($paddingOk) {
                $decrypted = substr($decrypted, 0, $len - $pad);
            }
        }

        return $decrypted;
    }

    private function storeApplicantShare($request)
    {
        if (isset($request->applicationNo) && isset($request->modelName)) {
            $application = Application::where('application_no', $request->applicationNo)->first();
            $model = '\\App\\Models\\' . $request->modelName;
            $applicationDetails = $model::where('id', $application->model_id)->first();
            if ($applicationDetails->splitted_id) {
                $oldPropertyId = SplitedPropertyDetail::where('id', $applicationDetails->splitted_id)->value('old_property_id');
            } else {
                $oldPropertyId = $applicationDetails->old_property_id;
            }
            ApplicantShare::updateOrCreate(
                [
                    'application_no' => (string)$request->applicationNo,
                    'old_property_id' => (int)$oldPropertyId,
                ],
                [
                    'share' => $request->applicantShare,
                ]
            );
        }
    }

    private function storeCoapplicantShares($request)
    {
        if (isset($request->coapplicantShareIds)) {
            foreach ($request->coapplicantShareIds as $key => $coapplicantShareId) {

                Coapplicant::where('id', $coapplicantShareId)
                    ->update([
                        'share' => $request->coapplicantShares[$key]
                    ]);
            }
        }
    }

     // Add this new method to your controller
    private function storeMutationApplicationDocumentDetails($request)
    {
        try {
            $applicationNo = $request->applicationNo;
            $documents = $request->documentNameAndDate;

            // Validate application exists
            $application = Application::where('application_no', $applicationNo)->first();
            if (!$application) {
                Log::warning('Application not found when saving documents: ' . $applicationNo);
                return false;
            }

            // Process only if we have documents array
            if (empty($documents) || !is_array($documents)) {
                // If no documents in request, delete all existing documents (clean slate)
                ApplicationDocumentDetail::where('application_no', $applicationNo)->delete();
                Log::info('All documents deleted for application (no new documents): ' . $applicationNo);
                return true;
            }

            // Filter valid documents (both name and date must be present)
            $validDocuments = [];
            foreach ($documents as $index => $doc) {
                $name = isset($doc['name']) ? trim($doc['name']) : '';
                $date = isset($doc['date']) ? trim($doc['date']) : '';

                // Skip if both are empty
                if (empty($name) && empty($date)) {
                    continue;
                }

                // Require both fields to be filled
                if (!empty($name) && !empty($date)) {
                    $validDocuments[] = [
                        'application_no' => $applicationNo,
                        'model_name' => $application->model_name,
                        'document_name' => $name,
                        'document_date' => $date,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                } else {
                    Log::warning('Skipping incomplete document for application ' . $applicationNo .
                        ' at index ' . $index . ': ' . json_encode($doc));
                }
            }

            // Use database transaction for data consistency
            DB::beginTransaction();

            try {
                // 1. Delete all existing documents for this application
                $deletedCount = ApplicationDocumentDetail::where('application_no', $applicationNo)->delete();
                Log::info('Deleted ' . $deletedCount . ' existing documents for application: ' . $applicationNo);

                // 2. Insert all new valid documents (if any)
                if (!empty($validDocuments)) {
                    ApplicationDocumentDetail::insert($validDocuments);
                    Log::info('Inserted ' . count($validDocuments) . ' new documents for application: ' . $applicationNo);
                }

                DB::commit();

                return true;
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Transaction failed for application documents: ' . $applicationNo . ' - Error: ' . $e->getMessage());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Error in storeMutationApplicationDocumentDetails: ' . $e->getMessage());
            return false;
        }
    }



    private function storeMutationApplicationExtraApplicants($request)
{
    try {
        $applicationNo = $request->applicationNo;
        $applicants = $request->applicants ?? [];


        // Validate application exists
        $application = Application::where('application_no', $applicationNo)->first();

        if (!$application) {
            Log::warning("Application not found while saving extra applicants: {$applicationNo}");
            return false;
        }

        // If no applicants are received, remove existing ones
        if (empty($applicants) || !is_array($applicants)) {
            ApplicationExtraApplicant::where('application_no', $applicationNo)->delete();

            Log::info("Deleted all extra applicants for application: {$applicationNo}");

            return true;
        }

        $validExtraApplicants = [];

        foreach ($applicants as $index => $applicant) {

            $name     = trim($applicant['name'] ?? '');
            $relation = trim($applicant['relation'] ?? '');
            $age      = trim($applicant['age'] ?? '');
            $gender   = trim($applicant['gender'] ?? '');
            $share    = trim($applicant['share'] ?? '');

            // Skip completely empty row
            if (
                $name === '' &&
                $relation === '' &&
                $age === '' &&
                $gender === '' &&
                $share === ''
            ) {
                continue;
            }

            // Validate mandatory fields
            if ($name === '' || $share === '') {
                Log::warning(
                    "Skipping incomplete extra applicant at index {$index} for application {$applicationNo}",
                    $applicant
                );

                continue;
            }
            $gender = $gender === '' ? null : $gender;

            $validExtraApplicants[] = [
                'application_no' => $applicationNo,
                'model_name'     => $application->model_name,
                'name'           => $name,
                'relation'       => $relation,
                'age'            => $age,
                'gender'         => $gender,
                'share'          => $share,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        DB::transaction(function () use ($applicationNo, $validExtraApplicants) {

            ApplicationExtraApplicant::where('application_no', $applicationNo)->delete();

            if (!empty($validExtraApplicants)) {
                ApplicationExtraApplicant::insert($validExtraApplicants);
            }
        });

        Log::info(
            'Stored ' . count($validExtraApplicants) .
            " extra applicants for application {$applicationNo}"
        );

        return true;

    } catch (\Throwable $e) {

        Log::error(
            "Error in storeMutationApplicationExtraApplicants: {$e->getMessage()}",
            [
                'application_no' => $request->applicationNo ?? null,
                'trace' => $e->getTraceAsString(),
            ]
        );

        return false;
    }
}
    //for tracking actions of application and assign to apllications to official users - SOURAV CHAUHAN (12/Nov/2024)
    public function applicationAction(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $action = $request->action;

            $actionName = getServiceNameByCode($action);
            $applicationNo = $request->applicationNo;
            $role = Auth::user()->roles[0]->name;
            $roleId = Auth::user()->roles[0]->id;
            $application = Application::where('application_no', $applicationNo)->first();
            $serviceType = getServiceCodeById($application->service_type);
            $latestAppAction = AppLatestAction::where('application_no', $applicationNo)->first();
            // dd($latestAppAction);
            $section_id = $application->section_id;
            $user = User::find($application->created_by);

            if ($action == 'PROOFREADINGLINK') {
                $requestData = [
                    'applicationId' => $application->id,
                    'applicationNo' => $application->application_no,
                    'applicationModelName' => $request->modelName, // or use the correct model name here
                    // 'applicationModelName' => 'MutationApplication', // or use the correct model name here
                ];
                $request = Request::create('/applications/appointment/link', 'POST', $requestData);
                $appointmentLink = Self::sendAppointmentLinkToApplicant($request);
                $responseData = json_decode($appointmentLink->getContent(), true);
                if ($responseData['status'] === 'success') {
                    //Move applicaiton to section after proof reading link send by Dy. L&DO - Lalit (16/dec/2024)
                    if ($request->applicationModelName === 'DeedOfApartmentApplication') {
                        $status = getServiceType('APP_IP');
                        if ($latestAppAction) {
                            $latestAction = $latestAppAction->latest_action;
                            $latestActionBy = $latestAppAction->latest_action_by;
                            $latestActionByRoleId = User::find($latestActionBy)->roles[0]->id;
                            // dd($serviceType, $latestAction, $latestActionByRoleId, $action, $roleId);
                            $actionMatrix = ActionMatrix::where('service_type', $serviceType)->where('action_one', $latestAction)->where('action_one_by_role', $latestActionByRoleId)->where('action_two', $action)->where('action_two_by_role', $roleId)->first();
                            $sendToRole = $actionMatrix->sent_to_role;
                            $assignedToUser = Self::getUserIdBySectionCodeAndRole($sendToRole, $section_id);
                            if (is_null($assignedToUser)) {
                                // to handle the null value
                                $response = ['status' => false, 'message' => 'User Not Available'];
                                return json_encode($response);
                            }
                            //store to apllication movement for trackin
                            $applicationMovement = ApplicationMovement::create([
                                'assigned_by' => Auth::user()->id,
                                'assigned_by_role' => Auth::user()->roles[0]->id,
                                'assigned_to' => $assignedToUser,
                                'assigned_to_role' => $sendToRole,
                                'service_type' => $application->service_type, //for mutation,LUC,DOA etc
                                'model_id' => $application->model_id,
                                'status' => $status, //for new application, objected application, rejected, approved etc
                                'action' => $action, //for new application, objected application, rejected, approved etc
                                'application_no' => $applicationNo,
                                'remarks' => $request->remark,
                            ]);

                            //update latest action
                            $latestAppAction->update([
                                'prev_action' => $latestAppAction->latest_action,
                                'prev_action_by' => $latestAppAction->latest_action_by,
                                'latest_action' => $action,
                                'latest_action_by' => Auth::user()->id
                            ]);
                        }
                    }
                    $response = ['status' => true, 'message' => 'Proof reading link send successfully'];
                    return response()->json($response);
                } else {
                    Log::info($e->getMessage());
                    return response()->json(['status' => false, 'message' => 'Their is some issue in sending proof reading link']);
                }
            } else if ($action == 'SENT_WARNING_MAIL') {
                $requestData = [
                    'applicationId' => $application->id,
                    'applicationNo' => $application->application_no,
                    'applicationModelName' => $request->modelName, // or use the correct model name here
                ];

                $application->is_warning_sent = true;
                $application->warning_sent_on = date('Y-m-d');
                if ($application->save()) {
                    //send warning mail
                    $data = [
                        'appNo' => $application->application_no,
                        'email' => $user['email'],
                    ];
                    $action = 'SENT_WARNING_MAIL';
                    // Apply mail settings and send email
                    $this->settingsService->applyMailSettings($action);
                    try {
                        Mail::to($user['email'])->send(new CommonMail($data, $action));
                    } catch (\Exception $e) {
                        // Log the error for debugging
                        Log::error("Failed to send email to {$user['email']}: " . $e->getMessage());
                    }
                    $request = Request::create('/applications/appointment/link', 'POST', $requestData);
                    $appointmentLink = Self::sendAppointmentLinkToApplicant($request);
                    $responseContent = $appointmentLink->getContent();
                    $responseArray = json_decode($responseContent, true);
                    if ($responseArray['status'] == 'success') {
                        return response()->json(['status' => true, 'message' => 'Warning Sent Successfully.']);
                    }
                }
            } else if ($action == 'HOLD') {
                Self::applicationFinalStatusChange($application, $latestAppAction, $action, 'APP_HOLD', $request);
                $requestData = [
                    'applicationId' => $application->id,
                    'applicationNo' => $application->application_no,
                    'applicationModelName' => $request->modelName, // or use the correct model name here
                    // 'applicationModelName' => 'MutationApplication', // or use the correct model name here
                ];
                $request = Request::create('/applications/appointment/link', 'POST', $requestData);
                $appointmentLink = Self::sendAppointmentLinkToApplicant($request, $action);
                $responseContent = $appointmentLink->getContent();
                $responseArray = json_decode($responseContent, true);
                if ($responseArray['status'] == 'success') {
                    return response()->json(['status' => true, 'message' => 'Application Hold Successfully.']);
                }
            } else if ($action == 'REJECT_APP') {
                Self::applicationFinalStatusChange($application, $latestAppAction, $action, 'APP_REJ', $request);
            } else if ($action == 'LETTER_GEN') {
                $splittedColumn = in_array($application->model_name, ['ConversionApplication', 'LandUseChangeApplication', 'DeedOfApartmentApplication'])
                    ? 'splited_property_detail_id'
                    : 'splitted_id';
                
                    $model = '\\App\\Models\\' . $application->model_name;
                    $applicationDetails = $model::where('id', $application->model_id)->first();
                    $oldPropertyId = $applicationDetails->old_property_id;
                    $propertyMasterId = $applicationDetails->property_master_id;
                    //Demand check For Conversion head START ---------
                    // dd($serviceType);
                    if($serviceType == 'CONVERSION'){
                      
                        $demand = Demand::where('old_property_id', $oldPropertyId)->whereIn('status', [getServiceType('DEM_DRAFT'), getServiceType('DEM_PENDING'), getServiceType('DEM_PARTIAL_PAID'),getServiceType('DEM_PAID')])->where('app_no',$applicationNo)->latest()->first();
                        $conversionSubheadAvailable = false;
                        if($demand){
                            $demandDetails = DemandDetail::where('demand_id', $demand->id)->get();
                            foreach($demandDetails as $demandDetail)
                            {
                                $subhead = getServiceCodeById($demandDetail->subhead_id);
                                if($subhead == "DEM_CONV_CHG"){
                                    $conversionSubheadAvailable = true;
                                    break; 
                                }
                            }
                        }

                        if(!$conversionSubheadAvailable){
                            $response = ['status' => false, 'message' => 'Please create a demand for this application first.'];
                            return response()->json($response);
                        }
                    }
                    //Demand check For Conversion head END ---------

                    
                    if ($serviceType == 'SUB_MUT') {

                        // Handle applicant share if present in the request
                        if ($request->has('applicantShare')) {
                            $this->storeApplicantShare($request);
                        }
                        // Handle coapplicantShares if present in the request
                        if ($request->has('coapplicantShares') && is_array($request->coapplicantShares)) {
                            $this->storeCoapplicantShares($request);
                        }
                        // Handle documentNameAndDate if present in the request
                        if ($request->has('documentNameAndDate') && is_array($request->documentNameAndDate)) {
                            $this->storeMutationApplicationDocumentDetails($request);
                        }

                        if($request->has('applicantShare') && $request->applicantShare > 0){
                            $this->storeMutationApplicationExtraApplicants($request);
                        }
                    }


                    //for checking demand available or not before generating draft letter
                    $pendingAmount = 0;
                    // $demands = Demand::where('property_master_id', $applicationDetails->property_master_id)->where(function ($query) use ($applicationDetails) {
                    //     if (isset($applicationDetails->splited_property_detail_id) && !is_null($applicationDetails->splited_property_detail_id)) {
                    //         return $query->where('splited_property_detail_id', $applicationDetails->splited_property_detail_id);
                    //     } else {
                    //         return $query->whereNull('splited_property_detail_id');
                    //     }
                    // })
                    //     ->whereIn('status', [getServiceType('DEM_PENDING'), getServiceType('DEM_PART_PAID'), getServiceType('DEM_DRAFT'),getServiceType('DEM_PAID')])
                    //     ->get();



//for checking the available demand according to flat if available- SOURAV CHAUHAN (16 June 2026)
                $demands = Demand::where('property_master_id', $applicationDetails->property_master_id)
                                    ->where(function ($query) use ($applicationDetails) {

                                        if (!is_null($applicationDetails->flat_id)) {
                                            $query->where('flat_id', $applicationDetails->flat_id);

                                        } elseif (!is_null($applicationDetails->splited_property_detail_id)) {
                                            $query->where(
                                                'splited_property_detail_id',
                                                $applicationDetails->splited_property_detail_id
                                            );

                                        } else {
                                            $query->whereNull('splited_property_detail_id');
                                        }
                                    })
                                    ->whereIn('status', [
                                        getServiceType('DEM_PENDING'),
                                        getServiceType('DEM_PART_PAID'),
                                        getServiceType('DEM_DRAFT'),
                                        getServiceType('DEM_PAID')
                                    ])
                                    ->get();





                 $isNewDemand = true;
                if($demands->isEmpty()){
                    $isNewDemand = false;
                    $oldDemand = OldDemand::where('property_id', $applicationDetails['old_property_id'])->where('outstanding','!=',0)->first();
                    $pendingAmount = !empty($oldDemand['outstanding']) ? $oldDemand['outstanding'] : 0;
                } else {
                    $pendingAmount = $demands->sum('balance_amount');
                }
                if(!$isNewDemand && ($serviceType != 'NOC' && $serviceType != 'SUB_MUT' && $serviceType != 'DOA')){
                    $response = ["status" => false, "message" => "First create a new demand using the Create Demand button."];
                    return response()->json($response); 
                }
                if($pendingAmount > 0){
                    $response = ["status" => false, "message" => "Cannot proceed as the full demand is not paid."];
                    return response()->json($response);               
                }


                $letterResponse = Self::generateLetter($application, $latestAppAction, $action, $request);
                if ($letterResponse) {
                    $response = ['status' => true, 'message' => 'Letter Generated Successfully'];
                    return response()->json($response);
                } else {
                    Log::info($e->getMessage());
                    return response()->json(['status' => false, 'message' => 'Their is some issue in letter generation']);
                }
                // Self::applicationFinalStatusChange($application,$latestAppAction,$action,'APP_APR',$request);
            }
            /** action for object  by Nitin 13dec2024*/
            else if ($action == 'OBJECT') {
                // Check if Application Status record does not exist then create it. Because As per requirement we Open Object button to get action by section & deputy without checking Go To Property Details By Lalit (23/Feb/2026)
                $isApplicationStatusRecordExists = ApplicationStatus::where('reg_app_no', $applicationNo)->exists();
                if (!$isApplicationStatusRecordExists) {
                    ApplicationStatus::create([
                        'service_type' => $application->service_type,
                        'model_id' => $application->model_id,
                        'reg_app_no' => $applicationNo,
                        'is_mis_checked' => false,
                        'mis_checked_by' => Auth::user()->id,
                        'is_scan_file_checked' => false,
                        'scan_file_checked_by' => Auth::user()->id,
                        'is_uploaded_doc_checked' => false,
                        'uploaded_doc_checked_by' => Auth::user()->id,
                        'created_by' => Auth::user()->id,
                    ]);
                }

                $status = getServiceType('APP_OBJ');
                if ($latestAppAction) { //
                    $latestAction = $latestAppAction->latest_action;
                    $latestActionBy = $latestAppAction->latest_action_by;
                    $latestActionByRoleId = User::find($latestActionBy)->roles[0]->id;
                    // dd($serviceType, $latestAction, $latestActionByRoleId, $action, $roleId);
                    $actionMatrix = ActionMatrix::where('service_type', $serviceType)->where('action_one', $latestAction)->where('action_one_by_role', $latestActionByRoleId)->where('action_two', $action)->where('action_two_by_role', $roleId)->first();
                    // dd($serviceType,$latestAction,$latestActionByRoleId,$action,$roleId);
                    $sendToRole = $actionMatrix->sent_to_role;
                    if ($sendToRole == 6) { //if applicant
                        $assignedToUser = $application->created_by;
                        $model = '\\App\\Models\\' . $application->model_name;
                        $model::where('id', $application->model_id)->update(['status' => getServiceType('APP_OBJ')]);
                        $application->update([
                            'status' => getServiceType('APP_OBJ'),
                            'is_objected' => 1
                        ]);

                        if ($application->model_name == 'MutationApplication') {
                            $mailServiceType = 'Mutation';
                        } else if ($application->model_name == 'ConversionApplication') {
                            $mailServiceType = 'Conversion';
                        } else if ($application->model_name == 'DeedOfApartmentApplication') {
                            $mailServiceType = 'Deed Of Apartment';
                        } else if ($application->model_name == 'LandUseChangeApplication') {
                            $mailServiceType = 'Land Use Change';
                        } else {
                            $mailServiceType = '';
                        }

                        //for send notification - SOURAV CHAUHAN (21/Nov/2024)
                        $user = User::find($application->created_by);
                        $data = [
                            'application_no' => $applicationNo,
                            'remarks' => $request->remark,
                        ];

                        $action = 'APP_OBJ';
                        $checkEmailTemplateExists = checkTemplateExists('email', $action);
                        if (!empty($checkEmailTemplateExists)) {
                             try {
                                    $mailSettings = app(SettingsService::class)->getMailSettings($action);
                                    $mailer = new \App\Mail\CommonPHPMail($data, $action, $communicationTrackingId ?? null);
                                    $mailResponse = $mailer->send($user['email'], $mailSettings);

                                    Log::info("Email sent successfully.", [
                                        'action' => $action,
                                        'email'  => $user['email'],
                                        'data'   => $data,
                                    ]);
                                } catch (\Exception $e) {
                                    Log::error("Email sending failed.", [
                                        'action' => $action,
                                        'email'  => $user['email'],
                                        'error'  => $e->getMessage(),
                                    ]);
                                }
                        }

                        $mobileNo = $user['mobile_no'];
                        $checkSmsTemplateExists = checkTemplateExists('sms', $action);
                        if (!empty($checkSmsTemplateExists)) {
                            $this->communicationService->sendSmsMessage($data, $mobileNo, $action);
                        }
                        $checkWhatsappTemplateExists = checkTemplateExists('whatsapp', $action);
                        if (!empty($checkWhatsappTemplateExists)) {
                            $this->communicationService->sendWhatsAppMessage($data, $mobileNo, $action);
                        }
                    } else {
                        $assignedToUser = Self::getUserIdBySectionCodeAndRole($sendToRole, $section_id);
                    }
                    if (is_null($assignedToUser)) {
                        // to handle the null value
                        $response = ['status' => false, 'message' => 'User Not Available'];
                        return json_encode($response);
                    }
                    //store to apllication movement for trackin
                    $applicationMovement = ApplicationMovement::create([
                        'assigned_by' => Auth::user()->id,
                        'assigned_by_role' => Auth::user()->roles[0]->id,
                        'assigned_to' => $assignedToUser,
                        'assigned_to_role' => $sendToRole,
                        'service_type' => $application->service_type, //for mutation,LUC,DOA etc
                        'model_id' => $application->model_id,
                        'status' => $status, //for new application, objected application, rejected, approved etc
                        'action' => $action, //for new application, objected application, rejected, approved etc
                        'application_no' => $applicationNo,
                        'remarks' => $request->remark,
                    ]);

                    //update latest action
                    $latestAppAction->update([
                        'prev_action' => $latestAppAction->latest_action,
                        'prev_action_by' => $latestAppAction->latest_action_by,
                        'latest_action' => $action,
                        'latest_action_by' => Auth::user()->id
                    ]);
                    // $response = ['status' => true, 'message' => 'Application ' .$actionName. ' Successfully'];
                    // return response()->json($response);

                    // dd($latestAction,$latestActionBy,$action);

                } else { //its first action on application by section
                    $actionMatrix = ActionMatrix::where('service_type', $serviceType)->where('action_one', $action)->where('action_one_by_role', $roleId)->whereNull('action_two')->first();
                    // dd($actionMatrix->toSql(), $actionMatrix->getBindings());
                    $assigned_to_role = $actionMatrix->sent_to_role;
                    // dd($application);
                    // dd($assigned_to_role,$section_id);
                    $assignedToUser = Self::getUserIdBySectionCodeAndRole($assigned_to_role, $section_id);

                    if (is_null($assignedToUser)) {
                        // to handle the null value
                        $response = ['status' => false, 'message' => 'User Not Available'];
                        return json_encode($response);
                    }
                    //store to apllication movement for trackin
                    $applicationMovement = ApplicationMovement::create([
                        'assigned_by' => Auth::user()->id,
                        'assigned_by_role' => Auth::user()->roles[0]->id,
                        'assigned_to' => $assignedToUser,
                        'assigned_to_role' => $assigned_to_role,
                        'service_type' => $application->service_type, //for mutation,LUC,DOA etc
                        'model_id' => $application->model_id,
                        'status' => $status, //for new application, objected application, rejected, approved etc
                        'action' => $action, //for new application, objected application, rejected, approved etc
                        'application_no' => $applicationNo,
                        'remarks' => $request->remark,
                    ]);
                    //store latest action
                    AppLatestAction::create([
                        'application_no' => $applicationNo,
                        'prev_action' => null,
                        'prev_action_by' => null,
                        'latest_action' => $action,
                        'latest_action_by' => Auth::user()->id
                    ]);
                }
            } else if ($action == 'APPROVE') {
                /** code to check pending demand 
                 * added by Nitin on 15-05-2025
                 */
                $model = '\\App\\Models\\' . $application->model_name;
                $applicationDetails = $model::find($application->model_id);
                $oldPropertyId = $applicationDetails->old_property_id;
                $demandFindQuery = Demand::where('old_property_id', $oldPropertyId)
                            ->whereIn('status', [
                                getServiceType('DEM_DRAFT'),
                                getServiceType('DEM_PENDING'),
                                getServiceType('DEM_PART_PAID')
                            ])
                            ->when(!empty($applicationDetails->flat_id), function ($query) use ($applicationDetails) {
                                $query->where('flat_id', $applicationDetails->flat_id);
                            });
                if ($demandFindQuery->exists()) {
                    $pendingDemand = $demandFindQuery->first();
                    if ($pendingDemand->status == getServiceType('DEM_DRAFT')) {
                        return response()->json(['status' => false, 'message' => "Can not proceed. Demand with id:{$pendingDemand->unique_id} is saved in demand drafts."]);
                    }
                    $pendingAmount = '₹ ' . customNumFormat($pendingDemand->balance_amount);
                    return response()->json(['status' => false, 'message' => "Can not proceed. Paymend of demand id:{$pendingDemand->unique_id} having payable amount $pendingAmount is pending."]);
                }
                /** code to check pending demand end*/
                $status = getServiceType('APP_APR');
                if ($latestAppAction) { //
                    $latestAction = $latestAppAction->latest_action;
                    $latestActionBy = $latestAppAction->latest_action_by;
                    $latestActionByRoleId = User::find($latestActionBy)->roles[0]->id;
                    $model = '\\App\\Models\\' . $application->model_name;
                    

                        $model::where('id', $application->model_id)->update(['status' => $status]);
                        $application->update([
                            'status' => $status,
                            'is_objected' => 0,
                            'disposed_at' => date('Y-m-d H:i:s')
                        ]);
                    
                    
                        //store to apllication movement for trackin
                        $applicationMovement = ApplicationMovement::create([
                            'assigned_by' => Auth::user()->id,
                            'assigned_by_role' => Auth::user()->roles[0]->id,
                            'assigned_to' => null,
                            'assigned_to_role' => null,
                            'service_type' => $application->service_type, //for mutation,LUC,DOA etc
                            'model_id' => $application->model_id,
                            'status' => $status, //for new application, objected application, rejected, approved etc
                            'action' => $action, //for new application, objected application, rejected, approved etc
                            'application_no' => $applicationNo,
                            'remarks' => $request->remark,
                        ]);

                        //update latest action
                            $latestAppAction->update([
                                'prev_action' => $latestAppAction->latest_action,
                                'prev_action_by' => $latestAppAction->latest_action_by,
                                'latest_action' => $action,
                                'latest_action_by' => Auth::user()->id
                            ]);
                   
                    // if($applicationNo != "APP0000015"){
                    /** Finalize the application --  added by NItin on 22Dec2024 */

                    if ($application->service_type == getServiceType("LUC")) {
                        $lucApplication = $model::where('id', $application->model_id)->first();
                        $finalized = $this->finalizeLUCApplication($lucApplication);
                        if (!$finalized['status']) {
                            return response()->json($finalized);
                        }
                        // return response()->json($finalized);
                    }

                    if ($application->service_type == getServiceType("CONVERSION")) {
                        $conversionApplication = $model::where('id', $application->model_id)->first();
                        $finalized = $this->finalizeConversionApplication($conversionApplication);
                        if (!$finalized['status']) {
                            return response()->json($finalized);
                        }
                        // return response()->json($finalized);
                    }

                    if ($application->service_type == getServiceType("SUB_MUT")) {
                        $mutationApplication = $model::where('id', $application->model_id)->first();
                        $finalized = $this->finalizeMutationApplication($mutationApplication);
                        if (!$finalized['status']) {
                            return response()->json($finalized);
                        }
                        // return response()->json($finalized);
                    }
                    // }


                    $getApplicationDetails = $model::where('id', $application->model_id)->first();
                    if ($application->model_name == 'MutationApplication') {
                        $mailServiceType = 'Mutation';
                    } else if ($application->model_name == 'ConversionApplication') {
                        $mailServiceType = 'Conversion';
                    } else if ($application->model_name == 'DeedOfApartmentApplication') {
                        $mailServiceType = 'Deed Of Apartment';
                    } else if ($application->model_name == 'LandUseChangeApplication') {
                        $mailServiceType = 'Land Use Change';
                    } else if ($application->model_name == 'NocApplication') {
                        $mailServiceType = 'No Objection Certificate';
                    } else {
                        $mailServiceType = '';
                    }

                    $oldPropertyId = $getApplicationDetails->old_property_id;
                    $propertyMasterId = $getApplicationDetails->property_master_id;
                    $newPropertyId = $getApplicationDetails->new_property_id;
                    $propertyKnownAs = PropertyLeaseDetail::where('property_master_id', $propertyMasterId)
                        ->pluck('presently_known_as')
                        ->first();

                    //for send notification - SOURAV CHAUHAN (21/Nov/2024)
                    $userDetails = User::where('id', $application->created_by)->first();
                    $data = [
                        'application_no' =>  $applicationNo,
                        'application_type' => $mailServiceType,
                        'property_details' => $propertyKnownAs . " [" . $oldPropertyId . " (" . $newPropertyId . ") ]",
                    ];

                    $action = "APP_APR";
                    $checkEmailTemplateExists = checkTemplateExists('email', $action);
                    $signedLetter = 'storage/' . $application->Signed_letter;
                    // dd($signedLetter);
                    if (!empty($checkEmailTemplateExists)) {

                         // --- EMAIL ---
                        try {
                            $mailSettings = app(SettingsService::class)->getMailSettings($action);
                            $mailer = new \App\Mail\CommonPHPMail($data, $action, $communicationTrackingId ?? null,$signedLetter ?? null);
                            $mailResponse = $mailer->send($userDetails->email, $mailSettings);

                            Log::info("Email sent successfully.", [
                                'action' => $action,
                                'email'  => $userDetails->email,
                                'data'   => $data,
                            ]);
                        } catch (\Exception $e) {
                            Log::error("Email sending failed.", [
                                'action' => $action,
                                'email'  => $userDetails->email,
                                'error'  => $e->getMessage(),
                            ]);
                        }
                    }

                    // dd('email sent');

                    $mobileNo = $userDetails->mobile_no;
                    $checkSmsTemplateExists = checkTemplateExists('sms', $action);
                    if (!empty($checkSmsTemplateExists)) {
                        $this->communicationService->sendSmsMessage($data, $mobileNo, $action);
                    }
                    $checkWhatsappTemplateExists = checkTemplateExists('whatsapp', $action);
                    if (!empty($checkWhatsappTemplateExists)) {
                        $this->communicationService->sendWhatsAppMessage($data, $mobileNo, $action);
                    }

                    // $response = ['status' => true, 'message' => 'Application ' .$actionName. ' Successfully'];
                    // return response()->json($response);

                    // dd($latestAction,$latestActionBy,$action);

                }
            } else {
                if($serviceType == "CONVERSION"){
                    self::checkConversionDemand($application);
                }
                $status = getServiceType('APP_IP');
                if ($latestAppAction) {
                    $latestAction = $latestAppAction->latest_action;
                    $latestActionBy = $latestAppAction->latest_action_by;
                    $latestActionByRoleId = User::find($latestActionBy)->roles[0]->id;

                    // dd($serviceType,$latestAction,$latestActionByRoleId,$action,$roleId);
                    //  dd($serviceType, $latestAction, $latestActionByRoleId, $action, $roleId);
                    $actionMatrix = ActionMatrix::where('service_type', $serviceType)->where('action_one', $latestAction)->where('action_one_by_role', $latestActionByRoleId)->where('action_two', $action)->where('action_two_by_role', $roleId)->first();
                    //   dd($actionMatrix);
                    $sendToRole = $actionMatrix->sent_to_role;
                    // dd($sendToRole,$section_id);
                    if ($sendToRole == 6) { //if applicant
                        $assignedToUser = $application->created_by;
                    } else if ($sendToRole == 4) { //if cdv
                        $role = Role::find(4);
                        $usersWithRole = User::role($role->name)->get();
                        $assignedToUser = Self::getUserIdBySectionCodeAndRole($sendToRole, $section_id);
                        $requestData = [
                            'applicationId' => $application->id,
                            'applicationNo' => $application->application_no,
                            'applicationModelName' => $request->modelName, // or use the correct model name here
                            // 'applicationModelName' => 'MutationApplication', // or use the correct model name here
                        ];
                        $request = Request::create('/applications/appointment/link', 'POST', $requestData);
                        $appointmentLink = Self::sendAppointmentLinkToApplicant($request);
                        
                        
                        $responseData = json_decode($appointmentLink->getContent(), true);
                        // if($application->application_no == "APP0000001"){
                            // dd($responseData);
                        // }
                        if ($responseData['status'] === 'success') { 
                            //$response = ['status' => true, 'message' => 'Proof reading link send successfully'];
                            //return response()->json($response);
                        } else {
                        /// return response()->json(['status' => false, 'message' => 'Their is some issue in sending proof reading link']);
                        }
                        // dd($usersWithRole);
                        // $assignedToUser = $usersWithRole[0]->id;
                    } else {
                        $assignedToUser = Self::getUserIdBySectionCodeAndRole($sendToRole, $section_id);
                    }
                    if (is_null($assignedToUser)) {
                        // to handle the null value
                        $response = ['status' => false, 'message' => 'User Not Available'];
                        return json_encode($response);
                    }
                    //store to apllication movement for trackin
                    $applicationMovement = ApplicationMovement::create([
                        'assigned_by' => Auth::user()->id,
                        'assigned_by_role' => Auth::user()->roles[0]->id,
                        'assigned_to' => $assignedToUser,
                        'assigned_to_role' => $sendToRole,
                        'service_type' => $application->service_type, //for mutation,LUC,DOA etc
                        'model_id' => $application->model_id,
                        'status' => $status, //for new application, objected application, rejected, approved etc
                        'action' => $action, //for new application, objected application, rejected, approved etc
                        'application_no' => $applicationNo,
                        'remarks' => $request->remark,
                    ]);

                    //update latest action
                    $latestAppAction->update([
                        'prev_action' => $latestAppAction->latest_action,
                        'prev_action_by' => $latestAppAction->latest_action_by,
                        'latest_action' => $action,
                        'latest_action_by' => Auth::user()->id
                    ]);
                    // $response = ['status' => true, 'message' => 'Application ' .$actionName. ' Successfully'];
                    // return response()->json($response);

                    // dd($latestAction,$latestActionBy,$action);

                } else { //its first action on application by section
                    $actionMatrix = ActionMatrix::where('service_type', $serviceType)->where('action_one', $action)->where('action_one_by_role', $roleId)->whereNull('action_two')->first();
                    // dd($actionMatrix->toSql(), $actionMatrix->getBindings());
                    $assigned_to_role = $actionMatrix->sent_to_role;
                    // dd($application);
                    // dd($assigned_to_role,$section_id);
                    $assignedToUser = Self::getUserIdBySectionCodeAndRole($assigned_to_role, $section_id);

                    if (is_null($assignedToUser)) {
                        // to handle the null value
                        $response = ['status' => false, 'message' => 'User Not Available'];
                        return json_encode($response);
                    }
                    //store to apllication movement for trackin
                    $applicationMovement = ApplicationMovement::create([
                        'assigned_by' => Auth::user()->id,
                        'assigned_by_role' => Auth::user()->roles[0]->id,
                        'assigned_to' => $assignedToUser,
                        'assigned_to_role' => $assigned_to_role,
                        'service_type' => $application->service_type, //for mutation,LUC,DOA etc
                        'model_id' => $application->model_id,
                        'status' => $status, //for new application, objected application, rejected, approved etc
                        'action' => $action, //for new application, objected application, rejected, approved etc
                        'application_no' => $applicationNo,
                        'remarks' => $request->remark,
                    ]);
                    //store latest action
                    AppLatestAction::create([
                        'application_no' => $applicationNo,
                        'prev_action' => null,
                        'prev_action_by' => null,
                        'latest_action' => $action,
                        'latest_action_by' => Auth::user()->id
                    ]);
                }
            }
            /** reset forward status of all movemt entries for application
             * code added by Nitin o 09 dec 2024
             */
            ApplicationMovement::where('application_no', $applicationNo)->update(['is_forwarded' => 0]);

            // reset forward status of all movemt entries for application

            //for logs - SOURAV CHAHAN (18/Nov/2024) 
            $decodedModel = base64_encode($application->model_name);
            $actionLink = url('edharti/applications/' . $application->model_id) . '?type=' . $decodedModel;
            UserActionLogHelper::UserActionLog(
                'Application ' . $action,
                $actionLink,
                'adminApplication',
                "Application <a target='_blank' href='" . $actionLink . "'>" . $applicationNo . "</a> has been " . $action . " by user " . Auth::user()->name . "."
            );

            $response = ['status' => true, 'message' => 'Application ' . $actionName . ' Successfully'];
            return response()->json($response);
        });
    }


   function checkConversionDemand($application){
        $model = '\\App\\Models\\' . $application->model_name;
        $data = $model::where('id', $application->model_id)->first();
        $propertyMasterId = $data->property_master_id;
        $splitedPropertyId = $data->splited_property_detail_id; 
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

    public function getFileMovements(Request $request, $appNo = null)
    {
        // dd($applicationNo);
        try {
            $applicationNo = $appNo ?? $request->applicationNo;  // Use the null coalescing operator to assign value
            $data['applicationNo']  = $applicationNo; // need to show application no in alde file. Added by Nitin 29112024
            $takeRecords = $appNo === null ? 3 : null;  // Take 3 records only if $appNo is null

            if ($appNo == null) {
                $applicationTrackings = ApplicationMovement::where('application_no', $applicationNo)
                    ->latest()
                    ->take(3)
                    ->get();
            } else {
                $applicationTrackings = ApplicationMovement::where('application_no', $applicationNo)
                    ->orderBy('created_at')
                    ->get();
            }

            $applicationDetails = Application::where('application_no', $applicationNo)->first();
            $model = '\\App\\Models\\' . $applicationDetails->model_name;
            $data = $model::where('id', $applicationDetails->model_id)->first();
            $propertyLeaseDetail = PropertyLeaseDetail::where('property_master_id', $data->property_master_id)->first();
            $presentlyKnownAs = $propertyLeaseDetail->presently_known_as;

            if ($applicationDetails->model_name == 'MutationApplication') {
                $applicationType = 'Mutation';
            } else if ($applicationDetails->model_name == 'ConversionApplication') {
                $applicationType = 'Conversion';
            } else if ($applicationDetails->model_name == 'DeedOfApartmentApplication') {
                $applicationType = 'Deed Of Apartment';
            } else if ($applicationDetails->model_name == 'LandUseChangeApplication') {
                $applicationType = 'Land Use Change';
            } else if ($applicationDetails->model_name == 'NocApplication') {
                $applicationType = 'No Objection Certificate';
            } else {
                $applicationType = 'Service Not Defined';
            }

            $fileMovement = [];
            $userModel = new User;
            $roleModel = new Role;
            foreach ($applicationTrackings as $tracking) {
                $user = [];
                $user['assigned_by'] = $tracking->assigned_by ? $userModel->userNameById($tracking->assigned_by) : null;
                $user['assigned_by_role'] = $tracking->assigned_by_role ? ucwords(str_replace('-', ' ', $roleModel->roleNameById($tracking->assigned_by_role))) : null;
                $user['assigned_to'] = $tracking->assigned_to ? $userModel->userNameById($tracking->assigned_to) : null;
                $user['assigned_to_role'] = $tracking->assigned_to_role ? $roleModel->roleNameById($tracking->assigned_to_role) : null;
                $user['status'] = getServiceNameById($tracking->status);
                // Commented by Lalit Tiwari - (24/dec/2024), Because we need to show Action Instead of Status
                // $user['action'] = getServiceNameByCode($tracking->action);
                $user['action'] = $tracking->action;
                $user['remark'] = $tracking->remarks ? $tracking->remarks : null;
                $simpleDateTime = Carbon::parse($tracking->created_at)->format('d-m-Y H:i:s');
                $user['created_at'] = $simpleDateTime;
                $fileMovement[] = $user;
            }
            // $fileMovement['applicationType'] = $applicationType;
            $data['fileMovement'] = $fileMovement;
            if ($appNo !== null) {
                // dd($data);
                return view('admin.applications.movement')->with(compact('data'));
            } else {
                $response = ['status' => true, 'message' => 'File movement fetched', 'data' => $fileMovement, 'applicationType' => $applicationType, 'presentlyKnownAs' => $presentlyKnownAs];
                return response()->json($response);
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['status' => false, 'message' => 'An error occurred while getting application movement details'], 500);
        }
    }

    public function applicationFinalStatusChange($application, $latestAppAction, $action, $status, $request)
    {

        if ($action == "HOLD") {
            $assignedTo = Auth::user()->id;
            $assignedToRole = Auth::user()->roles[0]->id;
        } else {
            $assignedTo = null;
            $assignedToRole = null;
        }
        $applicationMovement = ApplicationMovement::create([
            'assigned_by' => Auth::user()->id,
            'assigned_by_role' => Auth::user()->roles[0]->id,
            'assigned_to' => $assignedTo,
            'assigned_to_role' => $assignedToRole,
            'service_type' => $application->service_type, //for mutation,LUC,DOA etc
            'model_id' => $application->model_id,
            'status' => getServiceType($status), //for new application, objected application, rejected, approved etc
            'action' => $action, //for new application, objected application, rejected, approved etc
            'application_no' => $request->applicationNo,
            'remarks' => $request->remark,
        ]);
        $latestAppAction->update([
            'prev_action' => $latestAppAction->latest_action,
            'prev_action_by' => $latestAppAction->latest_action_by,
            'latest_action' => $action,
            'latest_action_by' => Auth::user()->id
        ]);

        //change application status
        $application->status = getServiceType($status);

        /** This code added by Nitin to capture disposed date then the aapplication is approved or rejected. added on 20-08-2025  */
        if ($status != "HOLD") {
            $application->disposed_at = date('Y-m-d H:i:s');
        }
        /** end code added on 20-08-2025 */

        $application->save();

        $model = '\\App\\Models\\' . $application->model_name;
        $applicationDetails = $model::find($application->model_id);

        $applicationDetails['status'] = getServiceType($status);
        $applicationDetails->save();

        if ($application->model_name == 'MutationApplication') {
            $mailServiceType = 'Mutation';
        } else if ($application->model_name == 'ConversionApplication') {
            $mailServiceType = 'Conversion';
        } else if ($application->model_name == 'DeedOfApartmentApplication') {
            $mailServiceType = 'Deed Of Apartment';
        } else if ($application->model_name == 'LandUseChangeApplication') {
            $mailServiceType = 'Land Use Change';
        } else if ($application->model_name == 'NocApplication') {
            $mailServiceType = 'No Objection Certificate';
        } else {
            $mailServiceType = '';
        }

        //for send notification - SOURAV CHAUHAN (21/Nov/2024)
        $user = User::find($application->created_by);
        $data = [
            'application_type' => $mailServiceType,
            'application_no' => $applicationDetails['application_no'],
            'date' => Carbon::now()->format('d-m-Y'),
            'time' => Carbon::now('Asia/Kolkata')->format('H:i:s'),
            'reason' => $request->remark,
        ];

        if ($action != "HOLD" || $action == "REJECT_APP") {
            $action = $status;
            $checkEmailTemplateExists = checkTemplateExists('email', $action);
            if (!empty($checkEmailTemplateExists)) {
                try {
                        
                        $mailSettings = app(SettingsService::class)->getMailSettings($action);
                        $mailer = new \App\Mail\CommonPHPMail($data, $action, $communicationTrackingId ?? null);
                        $mailResponse = $mailer->send($user['email'], $mailSettings);

                        Log::info("Email sent successfully.", [
                            'action' => $action,
                            'email'  => $user['email'],
                            'data'   => $data,
                        ]);
                    } catch (\Exception $e) {
                        Log::error("Email sending failed.", [
                            'action' => $action,
                            'email'  => $user['email'],
                            'error'  => $e->getMessage(),
                        ]);
                    }
            }

            $mobileNo = $user['mobile_no'];
            $checkSmsTemplateExists = checkTemplateExists('sms', $action);
            if (!empty($checkSmsTemplateExists)) {
                $this->communicationService->sendSmsMessage($data, $mobileNo, $action);
            }
            $checkWhatsappTemplateExists = checkTemplateExists('whatsapp', $action);
            if (!empty($checkWhatsappTemplateExists)) {
                $this->communicationService->sendWhatsAppMessage($data, $mobileNo, $action);
            }
        }

        //Commented as discussed Lalit as same code in both rejected and not hold - 12 Feb 2026 ******************
        // dd($action,$status);
        // if ($action == "REJECT_APP") {
        //     $action = $status;
        //     $checkEmailTemplateExists = checkTemplateExists('email', $action);
        //     if (!empty($checkEmailTemplateExists)) {
                
        //          try {
                        
        //                 $mailSettings = app(SettingsService::class)->getMailSettings($action);
        //                 $mailer = new \App\Mail\CommonPHPMail($data, $action, $communicationTrackingId ?? null);
        //                 $mailResponse = $mailer->send($user['email'], $mailSettings);

        //                 Log::info("Email sent successfully.", [
        //                     'action' => $action,
        //                     'email'  => $user['email'],
        //                     'data'   => $data,
        //                 ]);
        //             } catch (\Exception $e) {
        //                 Log::error("Email sending failed.", [
        //                     'action' => $action,
        //                     'email'  => $user['email'],
        //                     'error'  => $e->getMessage(),
        //                 ]);
        //             }
        //     }

        //     $mobileNo = $user['mobile_no'];
        //     $checkSmsTemplateExists = checkTemplateExists('sms', $action);
        //     if (!empty($checkSmsTemplateExists)) {
        //         $this->communicationService->sendSmsMessage($data, $mobileNo, $action);
        //     }
        //     $checkWhatsappTemplateExists = checkTemplateExists('whatsapp', $action);
        //     if (!empty($checkWhatsappTemplateExists)) {
        //         $this->communicationService->sendWhatsAppMessage($data, $mobileNo, $action);
        //     }
        // }
    }

    public function generateLetter($application, $latestAppAction, $action, $request)
    {
        // dd($application, $latestAppAction, $action, $request);
        $model = '\\App\\Models\\' . $application->model_name;
        $applicationDetails = $model::find($application->model_id);
        // dd($applicationDetails);
        // $splitedPropertyDetails = SplitedPropertyDetail::where('old_property_id', $applicationDetails->old_property_id)->first();
        // if($splitedPropertyDetails){
        //     $newPropertyId = $splitedPropertyDetails['child_prop_id'];
        //     $propertyDetails = PropertyMaster::where('old_propert_id', $splitedPropertyDetails['property_master_id'])->first();
        //     $plotNo = $splitedPropertyDetails['plot_flat_no'];
        //     $propertyStatus = getServiceNameById($splitedPropertyDetails['property_status']);
        //     $leaseDetails = PropertyLeaseDetail::where('property_master_id', $splitedPropertyDetails['property_master_id'])->first();
        //     $plotArea = $splitedPropertyDetails['current_area'];
        //     $unit = getServiceNameById($splitedPropertyDetails['unit']);
        // } else {
        //     $propertyDetails = PropertyMaster::where('old_propert_id', $applicationDetails->old_property_id)->first();
        //     $newPropertyId = $propertyDetails['unique_propert_id'];
        //     $plotNo = $propertyDetails->plot_or_property_no;
        //     $propertyStatus = getServiceNameById($propertyDetails->status);
        //     $leaseDetails = PropertyLeaseDetail::where('property_master_id', $applicationDetails->property_master_id)->first();
        //     $plotArea = $leaseDetails->plot_area;
        //     $unit = getServiceNameById($leaseDetails->unit);
        // }

        $propertyMasertService = new PropertyMasterService();
        $findProperty = $propertyMasertService->propertyFromSelected($applicationDetails->old_property_id);
        // dd($findProperty);
        if ($findProperty['status'] == 'error') {
            // return $countOnly ? 0 : collect(); //incase of error return empty collection
        } else {
            $propertyDetails = $findProperty['masterProperty'];
            $propertyMasterId = $propertyDetails->id;
            $childProperty = $findProperty['childProperty'] ?? null;
            $childId = $childProperty?->id;
        }

        $propertyId = $childProperty ? $childProperty?->old_property_id : $propertyDetails->old_propert_id;
        $uniquePropertyId = $childProperty ? $childProperty?->child_prop_id : $propertyDetails->unique_propert_id;
        $plotNo = $childProperty ? $childProperty?->plot_flat_no : $propertyDetails->plot_or_property_no;
        $property_status = $childProperty ? $childProperty?->property_status : $propertyDetails->status;

        $colonyId = $propertyDetails['new_colony_name'];
        $colony = OldColony::find($colonyId);
        $colonyCode = $colony->code;
        $applicantUserDetail = ApplicantUserDetail::where('user_id', $application->created_by)->first();

        $section = Section::find($application->section_id);
        $role = Role::where('name', 'deputy-lndo')->first();
        $deputyUserId = Self::getUserIdBySectionCodeAndRole($role->id, $application->section_id);
        $sectionName = $section->section_code;

        $deputyUserDetails = User::find($deputyUserId);
        $deputyUserName = $deputyUserDetails->name;
        
        // $propertyId = $applicationDetails->old_property_id;

        $applicationNo = $application->application_no;

       

        $blockNo = $propertyDetails->block_no;
        $newColonyName = $propertyDetails->new_colony_name;
        $oldColony = OldColony::where('id', $newColonyName)->first();
        $colonyName = $colony->name;

        $propertyStatus = getServiceNameById($property_status);
        $leaseDetails = PropertyLeaseDetail::where('property_master_id', $applicationDetails->property_master_id)->first();
        $plotArea = $childProperty ? $childProperty?->area_in_sqm : $leaseDetails->plot_area_in_sqm;
        //area from supplementary lease
        if($childProperty){

            // PropertyMiscDetail::where
        } else {
            $propertyMisDetails = PropertyMiscDetail::where('old_property_id',$applicationDetails->old_property_id)->first();
            if($propertyMisDetails && $propertyMisDetails->supplementary_area_in_sqm > 0){
                $plotArea = $plotArea + $propertyMisDetails->supplementary_area_in_sqm;
            }
        }
        $knownAs = $childProperty ? $childProperty?->presently_known_as : $leaseDetails->presently_known_as;
        $unit = getServiceNameById($leaseDetails->unit);

       
       
        $lesseeNames = [];
        $transferDate = [];

        // $propertyTransferredLesseeDetail = PropertyTransferredLesseeDetail::where('property_master_id', $applicationDetails->property_master_id)->where('process_of_transfer', 'Conversion')->get();
        // dd($propertyTransferredLesseeDetail);
        $hasChildRecords = false;
        // dd($propertyDetails);
        if(!empty($childId)){
            $hasChildRecords = PropertyTransferredLesseeDetail::where(
                        'property_master_id', $propertyDetails->id
                    )
                    ->where('splited_property_detail_id', $childId)
                    ->exists();
        } 
        // dd($childId, $hasChildRecords);

        $propertyTransferredLesseeDetail = PropertyTransferredLesseeDetail::where(
                    'property_master_id', $propertyDetails->id
                )
                ->where(function ($query) use ($childId, $hasChildRecords) {
                    if ($childId && $hasChildRecords) {
                        $query->where('splited_property_detail_id', $childId);
                    } else {
                        $query->whereNull('splited_property_detail_id');
                    }
                })
                ->where('batch_transfer_id', function ($q) use ($propertyDetails, $childId, $hasChildRecords) {
                    $q->selectRaw('MAX(batch_transfer_id)')
                        ->from('property_transferred_lessee_details')
                        ->where('deleted_at', NULL)
                        ->where('property_master_id', $propertyDetails->id)
                        ->where('process_of_transfer','Conversion')//added for adding transfer date of conversion only - SOURAV Chauhan (6 May 2026)
                        ->where(function ($sub) use ($childId, $hasChildRecords) {
                            if ($childId && $hasChildRecords) {
                                $sub->where('splited_property_detail_id', $childId);
                            } else {
                                $sub->whereNull('splited_property_detail_id');
                            }
                        });
                })
                
                
                ->get();

        // dd($propertyTransferredLesseeDetail);
        $lesseeNames = $transferDate = array();

        foreach ($propertyTransferredLesseeDetail as $lesseeDetail) {
            $lesseeNames[] = $lesseeDetail->lessee_name;
            $transferDate[] = $lesseeDetail->transferDate;
        }
// dd($lesseeNames);
        // Convert to strings/arrays safely
        $lesseeNames = !empty($lesseeNames) ? implode(' , ', $lesseeNames) : 'N/A';
        
        $transferDate = !empty($transferDate) ? array_values(array_unique($transferDate)) : ['N/A'];
        $isPropertyFlat = false;
        $flatDetails = '';
        if (!empty($applicationDetails->flat_id)) {
            $isPropertyFlat = true;
            $flatDetails = Flat::find($applicationDetails->flat_id);
        }
        if($applicationDetails->service_type->item_code == "CONVERSION"){
        	$isApplicationRecommendeByCdv = ApplicationMovement::where('application_no', $applicationNo)->where('assigned_by_role', 4)->where('action', 'RECOMMENDED')->count();
        }
        else {
        $isApplicationRecommendeByCdv = ApplicationMovement::where('application_no', $applicationDetails['application_no'])->where('assigned_by_role', 7)->where('action', 'RECOMMENDED')->count();
		}
    	
                        if ($isApplicationRecommendeByCdv > 0) {
                            $showUploadSignedLetter = 1;
		}
		else {
			 $showUploadSignedLetter = 0;
		}

        $noticeData = [
            'sectionName'    => $sectionName,
            'propertyId'     => $propertyId,
            'uniquePropertyId' => $uniquePropertyId,

            'applicationNo'  => $applicationNo,
            // 'plotNo'         => $plotNo,
            // 'blockNo'        => $blockNo,
            // 'propertyStatus' => $propertyStatus,
            // 'plotArea' => $plotArea,
            // 'unit' => $unit,
            // 'knownAs' => $knownAs,
            'plotNo'         => $flatDetails->plot ?? $plotNo,
            'blockNo'        => $flatDetails->block ?? $blockNo,
            'colonyName'     => $colonyName,
            'propertyStatus' => !empty($flatDetails->property_flat_status) ? getServiceNameById($flatDetails->property_flat_status) : $propertyStatus,
            'plotArea'       => $flatDetails->area_in_sqm ?? $plotArea,
            'unit'           => !empty($flatDetails->unit) ? getServiceNameById($flatDetails->unit) : $unit,
            'knownAs' => $flatDetails->known_as ?? $knownAs,
            'lesseeNames' => $lesseeNames,
            'transferDate' => ($transferDate[0] !='N/A')?Carbon::parse($transferDate[0])
                ->setTimezone('Asia/Kolkata')
                ->format('d-m-Y') :'NA',
            'deputyUserName' => $deputyUserName,
            'date'           => Carbon::now()->format('d-m-Y'),
            'showUploadSignedLetter' => $showUploadSignedLetter,
            'isPropertyFlat' => $isPropertyFlat,
            'flatNo'    => $flatDetails->flat_number ?? null,
            'flatId'    => $flatDetails->unique_flat_id ?? null,
            'floor'    => $flatDetails->floor ?? null,
        ];
        // dd($noticeData);
        // dd(getServiceNameById($application->service_type));
        if ($application->service_type == getServiceType('SUB_MUT')) {
            $process = 'mutation';
            $applicantShare = ApplicantShare::where('application_no', $applicationNo)->value('share');

            $baseUrl = url('/');

            $fullUrl = $baseUrl . '/storage/public/' 
                . $applicantUserDetail->applicant_number 
                . '/' . $colonyCode 
                . '/' . $process 
                . '/' . $request->applicationNo 
                . '/official/' .$applicationNo.'_'. $process . '_letter.pdf';
            $filename = time().'.svg';
            $path = public_path('qrcode/'.$filename);
            $qrcode =  QrCode::size(300)
                     ->generate($fullUrl, $path);

            $coapplicants = Coapplicant::where('model_name', $application->model_name)->where('model_id', $application->model_id)->get();
            $applicantDetail = UserRegistration::where('applicant_number', $applicantUserDetail->applicant_number)->first();
            $latestApplicationExtraApplicant = ApplicationExtraApplicant::where('application_no', $applicationDetails->application_no)->get();
          
            
            

            $hasChildRecords = PropertyTransferredLesseeDetail::where(
                    'property_master_id', $propertyDetails->id
                )
                ->whereNotNull('splited_property_detail_id')
                ->exists();
                // dd($hasChildRecords);
            $latestLesseeDetails = PropertyTransferredLesseeDetail::where(
                    'property_master_id', $propertyDetails->id
                )
                ->where(function ($query) use ($childId, $hasChildRecords) {
                    if ($childId && $hasChildRecords) {
                        $query->where('splited_property_detail_id', $childId);
                    } else {
                        $query->whereNull('splited_property_detail_id');
                    }
                })
                ->where('batch_transfer_id', function ($q) use ($propertyDetails, $childId, $hasChildRecords) {
                    $q->selectRaw('MAX(batch_transfer_id)')
                        ->from('property_transferred_lessee_details')
                        ->where('property_master_id', $propertyDetails->id)
                        ->where('deleted_at', NULL)
                        ->where(function ($sub) use ($childId, $hasChildRecords) {
                            if ($childId && $hasChildRecords) {
                                $sub->where('splited_property_detail_id', $childId);
                            } else {
                                $sub->whereNull('splited_property_detail_id');
                            }
                        });
                })
                ->get();
                // dd($latestLesseeDetails);
            $applicationDocumentDetails = ApplicationDocumentDetail::where('application_no', $application->application_no)
                ->orderBy('id', 'asc')
                ->get();

            //Generate QR Code Code
            $baseUrl = url('/');

            $fullUrl = $baseUrl . '/storage/public/'
                . $applicantUserDetail->applicant_number
                . '/' . $colonyCode
                . '/' . $process
                . '/' . $applicationNo
                . '/official/' . $applicationNo . '_' . $process . '_letter.pdf';

            // $qrcode = QrCode::size(200)->generate($fullUrl);
                        $filename = time() . '.svg';
            $path = public_path('qrcode/' . $filename);
            $qrcode =  QrCode::size(300)
                ->generate($fullUrl, $path);
            


            $mutationData = array_merge($noticeData, [
                'applicationData' => $applicationDetails,
                'applicantDetail' => $applicantDetail,
                'applicantUserDetail' => $applicantUserDetail,
                'coapplicants' => $coapplicants,
                'leaseDetails' => $leaseDetails,
                'childProperty' => $childProperty,
                'latestLesseeDetails' => $latestLesseeDetails,
                'deputyUserName' => $deputyUserName,
                'applicationDocumentDetails' => $applicationDocumentDetails,
                'filename' => $filename,
                'applicantShare' => $applicantShare,
                'latestApplicationExtraApplicant' => $latestApplicationExtraApplicant
            ]);

            // dd($mutationData);
            $pdf = Pdf::loadView('application/mutation/mutation-letter', $mutationData);

        } else if ($application->service_type == getServiceType('DOA')) {
            $process = 'deed_of_apartment';
            $pdf = Pdf::loadView('application/deed_of_apartment/deed_of_apartment-letter');
        } else if ($application->service_type == getServiceType('CONVERSION')) {
            $process = 'conversion';

            $baseUrl = url('/');

            $fullUrl = $baseUrl . '/storage/public/' 
                . $applicantUserDetail->applicant_number 
                . '/' . $colonyCode 
                . '/' . $process 
                . '/' . $request->applicationNo 
                . '/official/' .$applicationNo.'_'. $process . '_letter.pdf';
            $filename = time().'.svg';
            $path = public_path('qrcode/'.$filename);
            $qrcode =  QrCode::size(300)
                     ->generate($fullUrl, $path);

            $coapplicants = Coapplicant::where('model_name', $application->model_name)->where('model_id', $application->model_id)->get();
            $applicantDetail = UserRegistration::where('applicant_number', $applicantUserDetail->applicant_number)->first();
            $originalLessseeDetail =  PropertyTransferredLesseeDetail::where('property_master_id', $applicationDetails->property_master_id)->where('process_of_transfer', 'Original')->get();
            $pms = new PropertyMasterService();
            $cc = $pms->conversionCharges($propertyId);
            if (is_array($cc)) {
                $conversionCharges = '₹' . customNumFormat(round($cc['amount']));
                $conversionChargesInWords = convertNumberToWords(round($cc['amount']));
            } else {
                return false;
            }

            $conversionData = array_merge($noticeData, [
                'applicationData' => $applicationDetails,
                'applicantDetail' => $applicantDetail,
                'applicantUserDetail' => $applicantUserDetail,
                'coapplicants' => $coapplicants,
                'leaseDetails' => $leaseDetails,
                'originalLessseeDetail' => $originalLessseeDetail,
                'conversionCharges' => $conversionCharges,
                'conversionChargesInWords' => $conversionChargesInWords,
                'deputyUserName' => $deputyUserName,
                'filename' => $filename
               
            ]);

            // dd($conversionData);
            

            $pdf = Pdf::loadView('application/conversion/conversion-letter', $conversionData);
        } else if ($application->service_type == getServiceType('LUC')) {
            $process = 'luc';
            $pdf = Pdf::loadView('application/luc/luc-letter');
        } else if ($application->service_type == getServiceType('NOC')) {
            $process = 'NOC';

            $baseUrl = url('/');

            $fullUrl = $baseUrl . '/storage/public/' 
                . $applicantUserDetail->applicant_number 
                . '/' . $colonyCode 
                . '/' . $process 
                . '/' . $request->applicationNo 
                . '/official/' .$applicationNo.'_'. $process . '_letter.pdf';
            $filename = time().'.svg';
            $path = public_path('qrcode/'.$filename);
            $qrcode =  QrCode::size(300)
                     ->generate($fullUrl, $path);
            $pdf = Pdf::loadView('application/noc/noc-letter', [
                'noticeData' => $noticeData,
                'filename' => $filename,
            ]);
        }

        // $pdf = Pdf::loadView('application/mutation/mutation-letter');
        //generate the letter
        if ($pdf) {
            $pathToUpload = 'public/' . $applicantUserDetail->applicant_number . '/' . $colonyCode . '/' . $process . '/' . $request->applicationNo . '/official/' .$applicationNo.'_'. $process . '_letter.pdf';
            $pdfContent = $pdf->output();

            // Save the PDF to the specified location
            $saved = Storage::disk('public')->put($pathToUpload, $pdfContent);
            if ($saved) {
                $application->letter = $pathToUpload;
                if ($application->save()) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    public function sendAppointmentLinkToApplicant(Request $request, $action = null)
    {
        // dd($request->applicationModelName);
        try {
        $transactionSuccess = false;
        return DB::transaction(function () use ($request, &$transactionSuccess, $action) {
            if (empty($request->applicationId) || empty($request->applicationNo) || empty($request->applicationModelName)) {
                return response()->json(['message' => 'Missing required parameters'], 400);
            }
            // Determine application details based on the application model name
            $applicationModel = match ($request->applicationModelName) {
                'MutationApplication' => MutationApplication::class,
                'LandUseChangeApplication' => LandUseChangeApplication::class,
                'DeedOfApartmentApplication' => DeedOfApartmentApplication::class,
                'ConversionApplication' => ConversionApplication::class,
                default => null,
            };

            if (!$applicationModel) {
                return response()->json(['message' => 'Invalid application model name'], 400);
            }

            $getApplicationDetails = $applicationModel::where('application_no', $request->applicationNo)->first();
            // dd($getApplicationDetails);

            if (!$getApplicationDetails) {
                return response()->json(['message' => 'Application details not found'], 404);
            }

            $userPropertyQuery = UserProperty::where([
                ['old_property_id', $getApplicationDetails->old_property_id],
                ['new_property_id', $getApplicationDetails->property_master_id],
            ]);
            if (!empty($getApplicationDetails->flat_id)) {
                $userPropertyQuery->where('flat_id', $getApplicationDetails->flat_id);
            }
            $userId = $userPropertyQuery->pluck('user_id')->first();
            // dd($userId);
            if (!$userId) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $userDetails = User::find($userId);

            if (!$userDetails) {
                return response()->json(['message' => 'User details not found'], 404);
            }
            // if($request->applicationNo == "APP0000353")
            //     {
                    // dd($userDetails);
                // }
                

            // Generate Encoded Meeting Link
            $meetingLink = url('edharti/applicant/appointment/' . base64_encode($request->applicationNo) . '/' . base64_encode(strtotime(now())));
            $clickableMeetingLink = '<a href="' . $meetingLink . '" target="_blank">Click Here</a>';
            $isApplicationAppointmentLink = ApplicationAppointmentLink::where('application_no', $request->applicationNo)
                ->orderBy('created_at', 'desc')->first();
            if ($isApplicationAppointmentLink) {
                // Update only the first record found
                $isApplicationAppointmentLink->update(['is_active' => 0]);
            }
            //insert meeting record into application_appointment_links table
            $ifRecordInserted = ApplicationAppointmentLink::create([
                'application_no' => $request->applicationNo,
                'link' => $meetingLink,
                'schedule_date' => null,
                'valid_till' => Carbon::now()->addMonth()->format('Y-m-d'),
                'is_attended' => null,
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            if ($ifRecordInserted) {

                if ($request->applicationModelName == 'MutationApplication') {
                    $mailServiceType = 'Mutation';
                } else if ($request->applicationModelName == 'ConversionApplication') {
                    $mailServiceType = 'Conversion';
                } else if ($request->applicationModelName == 'DeedOfApartmentApplication') {
                    $mailServiceType = 'Deed Of Apartment';
                } else if ($request->applicationModelName == 'LandUseChangeApplication') {
                    $mailServiceType = 'Land Use Change';
                } else {
                    $mailServiceType = '';
                }


                $oldPropertyId = $getApplicationDetails->old_property_id;
                $propertyMasterId = $getApplicationDetails->property_master_id;
                $newPropertyId = $getApplicationDetails->new_property_id;
                $propertyKnownAs = PropertyLeaseDetail::where('property_master_id', $propertyMasterId)
                    ->pluck('presently_known_as')
                    ->first();

                //for send notification - SOURAV CHAUHAN (21/Nov/2024)
                $data = [
                    'application_no' =>  $request->applicationNo,
                    'application_type' => $mailServiceType,
                    'property_details' => $propertyKnownAs . " [" . $oldPropertyId . " (" . $newPropertyId . ") ]",
                    'link' => $clickableMeetingLink,
                ];

                if ($action == "HOLD") {
                    $action = "APP_HOLD";
                } else {
                    $action = "APP_MEETING_LINK";
                }
                $attachment = '';
                // Only attach letters for Mutation and Conversion added by Swati Mishra on 26082025
                    if (in_array($request->applicationModelName, ['MutationApplication', 'ConversionApplication', 'DeedOfApartmentApplication', 'LandUseChangeApplication'])) {
                        $baseApplication = Application::where('application_no', $request->applicationNo)->first();
                        // dd($baseApplication);
                        if ($baseApplication && !empty($baseApplication->letter)) {
                            // resolve absolute file path
                            // $absolutePath = Storage::disk('public')->path(
                            //     str_replace('public/', '', $baseApplication->letter)
                            // );

                             $absolutePath = "storage/".$baseApplication->letter;



                            // dd($absolutePath);
                            if (file_exists($absolutePath)) {
                                $attachment = $absolutePath;
                            }
                        }
                    }
                    // dd($attachment);
                $checkEmailTemplateExists = checkTemplateExists('email', $action);
                if (!empty($checkEmailTemplateExists)) {
                     // --- EMAIL ---
                    try {
                        $mailSettings = app(SettingsService::class)->getMailSettings($action);
                        $mailer = new \App\Mail\CommonPHPMail($data, $action, $communicationTrackingId ?? null, $attachment);
                        $mailResponse = $mailer->send($userDetails->email, $mailSettings);

                        Log::info("Email sent successfully.", [
                            'action' => $action,
                            'email'  => $userDetails->email,
                            'data'   => $data,
                        ]);
                    } catch (\Exception $e) {
                        Log::error("Email sending failed.", [
                            'action' => $action,
                            'email'  => $userDetails->email,
                            'error'  => $e->getMessage(),
                        ]);
                    }
                }

                $mobileNo = $userDetails->mobile_no;
                $checkSmsTemplateExists = checkTemplateExists('sms', $action);
                if (!empty($checkSmsTemplateExists)) {
                    $this->communicationService->sendSmsMessage($data, $mobileNo, $action);
                }
                $checkWhatsappTemplateExists = checkTemplateExists('whatsapp', $action);
                if (!empty($checkWhatsappTemplateExists)) {
                    $this->communicationService->sendWhatsAppMessage($data, $mobileNo, $action);
                }

                $transactionSuccess = true;
                return response()->json(['status' => 'success', 'message' => 'Meeting link sent successfully']);
            }
        });
        if ($transactionSuccess) {
            return response()->json(['status' => 'success', 'message' => 'Meeting link sent successfully']);
        } else {
            Log::info("Meeting link faliled to send");
            return response()->json(['status' => 'failure', 'message' => 'Meeting link not sent successfully']);
        }
        } catch (\Exception $e) {
            Log::error('Error sending appointment link: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'An error occurred while processing your request'], 500);
        }
    }

    public function uploadSignedLetter(Request $request)
    {
        $applicationNo = $request->application_no;
        /** check there is pending demand of the property. code added by Nitin */
        $application = Application::where('application_no', $applicationNo)->first();
        $model = '\\App\\Models\\' . $application->model_name;
        $applicationDetails = $model::find($application->model_id);
        $oldPropertyId = $applicationDetails->old_property_id;
         $demandFindQuery = Demand::where('old_property_id', $oldPropertyId)
                            ->whereIn('status', [
                                getServiceType('DEM_DRAFT'),
                                getServiceType('DEM_PENDING'),
                                getServiceType('DEM_PART_PAID')
                            ])
                            ->when(!empty($applicationDetails->flat_id), function ($query) use ($applicationDetails) {
                                $query->where('flat_id', $applicationDetails->flat_id);
                            });
        if ($demandFindQuery->exists()) {
            $pendingDemand = $demandFindQuery->first();
            if ($pendingDemand->status == getServiceType('DEM_DRAFT')) {
                return back()->with('failure', "Can not proceed. Demand with id:{$pendingDemand->unique_id} is saved in demand drafts.");
            }
            $pendingAmount = '₹ ' . customNumFormat($pendingDemand->balance_amount);
            return back()->with('failure', "Can not proceed. Paymend of demand id:{$pendingDemand->unique_id} having payable amount $pendingAmount is pending.");
        }
        $signedLetter = $request->signedLetter;


        $model = '\\App\\Models\\' . $application->model_name;
        $applicationDetails = $model::find($application->model_id);
        // $propertyDetails = PropertyMaster::where('old_propert_id', $applicationDetails->old_property_id)->first();
                $isSplittedPropertyObj = SplitedPropertyDetail::where('old_property_id', $oldPropertyId)->first();

        if ($isSplittedPropertyObj) {
            $propertyDetails = PropertyMaster::find($isSplittedPropertyObj->property_master_id);
        } else {
            $propertyDetails = PropertyMaster::where('old_propert_id', $oldPropertyId)->first();
        }
        $colonyId = $propertyDetails['new_colony_name'];
        $colony = OldColony::find($colonyId);
        $colonyCode = $colony->code;
        $applicantUserDetail = ApplicantUserDetail::where('user_id', $application->created_by)->first();
        if ($application->service_type == getServiceType('SUB_MUT')) {
            $process = 'mutation';
        } else if ($application->service_type == getServiceType('DOA')) {
            $process = 'deed_of_apartment';
        } else if ($application->service_type == getServiceType('CONVERSION')) {
            $process = 'conversion';
        } else if ($application->service_type == getServiceType('LUC')) {
            $process = 'luc';
        } else if ($application->service_type == getServiceType('NOC')) {
            $process = 'NOC';
        }

        //generate the letter

        // $pathToUpload = 'public/' . $applicantUserDetail->applicant_number . '/' . $colonyCode . '/' . $process . '/' . $applicationNo . '/official';
        // if ($request->hasFile('signedLetter')) {
        //     $signedLetter = GeneralFunctions::uploadFile($request->signedLetter, $pathToUpload, 'signedLetter');
        // } else {
        //     $signedLetter = null;
        // }


        $pathToUpload = 'public/' . $applicantUserDetail->applicant_number . '/' . $colonyCode . '/' . $process . '/' . $applicationNo . '/official';
        if ($request->hasFile('signedLetter')) {
            // $signedLetter = GeneralFunctions::uploadFile($request->signedLetter, $pathToUpload, 'signedLetter');
            $file = $request->signedLetter;
            $fileName = $applicationNo.'_'.$process . '_letter.' . $file->extension();
            $signedLetter = $file->storeAs($pathToUpload, $fileName, 'public');
        } else {
            $signedLetter = null;
        }

        // Save the PDF to the specified location
        if ($signedLetter) {
            $application->signed_letter = $signedLetter;
            $application->letter = $signedLetter;
            if ($application->save()) {
                return redirect()->back()->with('success', 'Letter uploaded successfully');
            } else {
                return redirect()->back()->with('failure', 'An unexpected error occurred!');
            }
        }
    }

    public function newgetFileMovements()
    {
        return view('admin.applications.officeactivity');
    }

    //forward application to other department - Lalit tiwari - 25/nov/2024
    /*public function forwardApplicationToDepartment(Request $request)
    {

        // Validate the incoming data
        $validated = $request->validate([
            'forwardTo' => 'required|string',
            'forwardRemark' => 'required|string',
            'serviceType' => 'required|string',
            'modalId' => 'required|string',
            'applicantNo' => 'required|string',
        ]);

        // Get the role instance
        $role = SpatieRole::where('name', $request->forwardTo)->first();
        // Check if role exists
        if (!$role) {
            return response()->json(['status' => 'failure', 'message' => 'Role does not exist.'], 404);
        }

        // Get user from model_has_roles table
        $assignedToUser = DB::table('model_has_roles')->where('role_id', $role->id)->value('model_id');
        // Check if user exists for given role
        if (!$assignedToUser) {
            return response()->json(['status' => 'failure', 'message' => 'User does not exist for given role.'], 404);
        }

        
        
        $application = Application::where('application_no', $request->applicantNo)->first();
        $applicationCurrentStatus = $application->status;
        // Store application movement record for tracking
        $applicationMovement = ApplicationMovement::create([
            'assigned_by' => Auth::user()->id,
            'assigned_by_role' => Auth::user()->roles[0]->id,
            'assigned_to' => $assignedToUser,
            'assigned_to_role' => $role->id,
            'service_type' => getServiceType($request->serviceType), // Custom function for service type
            'model_id' => $request->modalId,
            'status' => $applicationCurrentStatus, //modified by Nitin - 12 dec 2024
            'action' => null, //$getLastAppMovementRec->action, //modified by Nitin - 12 dec 2024
            'application_no' => $request->applicantNo,
            'remarks' => $request->forwardRemark,
            'is_forwarded' => 1, // added by Nitin on 09-dec-2024 to to add forwarded status
        ]);

        // Return success response if application movement created successfully
        if ($applicationMovement) {
            return response()->json(['status' => 'success', 'message' => 'Application forwarded successfully!'], 200);
        } else {
            // If role or user assignment fails, return failure response
            return response()->json(['status' => 'failure', 'message' => 'Error forwarding the application.'], 500);
        }
    }*/

    //forward application to other department - Lalit tiwari - 25/nov/2024
    public function forwardApplicationToDepartment(Request $request, ApplicationForwardService $service) //applicationForwardService Modification by NItin 17-12-2025
    {

        // Validate the incoming data
        $validated = $request->validate([
            'forwardTo' => 'required|string',
            'forwardRemark' => 'required|string',
            'serviceType' => 'required|string',
            'modalId' => 'required|string',
            'applicantNo' => 'required|string',
        ]);

        $result = $service->forward($request->all());

        return response()->json(
            ['status' => $result['status'], 'message' => $result['message']],
            $result['code']
        );
    }

    //forward application to revert assignee - Lalit tiwari - 26/nov/2024
    public function revertApplicationToAssignee(Request $request, ApplicationForwardService $service) //applicationForwardService Modification by NItin 17-12-2025
    {
        // Validate the incoming data
        $validated = $request->validate([
            'revertRemark' => 'required|string',
            'serviceType' => 'required|string',
            'modalId' => 'required|string',
            'applicantNo' => 'required|string',
        ]);
        $result = $service->revert($request->all());

        return response()->json(
            ['status' => $result['status'], 'message' => $result['message']],
            $result['code']
        );
    }

    //for storing the checklist documents response entered by CDV
    public function checklist(Request $request)
    {
        $applicationNo = $request->applicationNo;
        $checklistRemark = $request->checklistRemark;
        $documentId = $request->documentId;
        $type = $request->type;
        $documentChecklist = DocumentChecklist::updateOrCreate(
            [
                'application_no' => $applicationNo,
                'document_id' => $documentId
            ],
            [
                'is_correct' => $type,
                'remark' => $checklistRemark,
                'created_by' => Auth::id(),
            ]
        );
        if ($documentChecklist) {
            return redirect()->back()->with('success', 'Document checked successfully');
        } else {
            return redirect()->back()->with('failure', 'An unexpected error occurred!');
        }
    }

    /** function added by Nitin to get applications assigned to user */
    // public function applicationsAssignedToUser($onlyCurrentApplicatinos = null)
    // {
    //     $userId = Auth::id();
    //     $totalApplicationsQuery = ApplicationMovement::where('assigned_to', $userId);
    //     $latestIdByApplication = ApplicationMovement::selectRaw('MAX(id) as id')
    //         ->groupBy('application_no')
    //         ->pluck('id')->toArray();
    //     if (!$onlyCurrentApplicatinos) { // give all applicatin assigned to user
    //         $allAssignedApplications = $totalApplicationsQuery->pluck('application_no')->toArray();
    //         $data = Application::whereIn('application_no', $allAssignedApplications)->get();
    //     } else {
    //         $totalApplicationId = ($totalApplicationsQuery->get()->keyBy('id'))->keys()->all();
    //         // check application is currently assigned to user -  check that current entry is latest for the application
    //         if ($onlyCurrentApplicatinos == 1) {
    //             $latestMovementForUser = array_intersect($totalApplicationId, $latestIdByApplication);
    //             if (!empty($latestMovementForUser)) {
    //                 $currentApplications = $totalApplicationsQuery->whereIn('id', $latestMovementForUser)->pluck('application_no')->toArray();
    //                 $data = Application::whereIn('application_no', $currentApplications)->get();
    //             } else {
    //                 $data =  [];
    //             }
    //             $showAssigned = true;
    //         }
    //         if ($onlyCurrentApplicatinos == 2) {
    //             $latestMovementNotForUser = array_diff($totalApplicationId, $latestIdByApplication);
    //             if (!empty($latestMovementNotForUser)) {
    //                 $currentApplications = $totalApplicationsQuery->whereIn('id', $latestMovementNotForUser)->pluck('application_no')->toArray();
    //                 $data = Application::whereIn('application_no', $currentApplications)->get();
    //             } else {
    //                 $data =  [];
    //             }
    //             $showAssigned = false;
    //         }
    //     }
    //     return view('admin.applications.other-official-index', ['data' => $data, 'showAssigned' => $showAssigned]);
    // }

    /** function for getting applications assigned to user - SOURAV CHAUHAN (2 Feb 2025)*/
    public function applicationsAssignedToUser(Request $request, $onlyCurrentApplicatinos = null)
    {
        $getStatusId = '';
        if ($request->query('status')) {
            $items = getApplicationStatusList(true);
            $getStatusId = $items->where('item_code', trim(Crypt::decrypt($request->query('status'))))->value('id'); // trim added by Nitin to remove extra space and lines from descrypted string - on 04-2024
        }
        $user = Auth::user();
        $filterPermissionArr = [];
        $items = getApplicationStatusList(true);
        return view('admin.applications.other-official-index', compact('items', 'getStatusId', 'user'));
    }


    /** function for getting applications assigned to user - SOURAV CHAUHAN (2 Feb 2025)*/
    public function getApplicationsAssignedToUser(Request $request)
    {
        $user = Auth::user();
        $columns = [
            'id', // index 0
            'application_no', // index 1
            'old_property_id', // index 2
            'new_colony_name', // index 3
            'block_no', // index 4
            'plot_or_property_no', // index 5
            'presently_known_as', // index 6
            'section_code', // index 7
            'applied_for', // index 8
            'created_at', // index 9
        ];


        $totalApplicationsQuery = ApplicationMovement::where('assigned_to', $user->id);
        $latestIdByApplication = ApplicationMovement::selectRaw('MAX(id) as id')
            ->groupBy('application_no')
            ->pluck('id')->toArray();
        $allAssignedApplications = $totalApplicationsQuery->pluck('application_no')->toArray();

        $serviceType1 = getServiceType('SUB_MUT'); // Ensure this function is defined and works properly.

        $query1 = DB::table('mutation_applications as ma')
            ->leftJoin('property_masters as pm', 'ma.property_master_id', '=', 'pm.id')
            ->join('property_section_mappings as psm', function ($join) {
                $join->on('pm.new_colony_name', '=', 'psm.colony_id')
                    ->whereColumn('pm.property_type', 'psm.property_type')
                    ->whereColumn('pm.property_sub_type', 'psm.property_subtype');
            })
            ->join('sections', 'psm.section_id', '=', 'sections.id')
            ->leftJoin('old_colonies as oc', 'pm.new_colony_name', '=', 'oc.id')
            ->leftJoin('property_lease_details as pld', 'pm.id', '=', 'pld.property_master_id')
            ->leftJoin('applications as app', 'ma.application_no', '=', 'app.application_no')
            ->leftJoinSub(
                DB::table('application_statuses')
                    ->select(
                        'id',
                        'model_id',
                        'reg_app_no',
                        'service_type',
                        'is_mis_checked',
                        'is_scan_file_checked',
                        'is_uploaded_doc_checked',
                        'mis_checked_by',
                        'scan_file_checked_by',
                        'uploaded_doc_checked_by',
                        'created_at',
                        DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                    )
                    ->where('service_type', $serviceType1),
                'latest_statuses',
                function ($join) {
                    $join->on('ma.id', '=', 'latest_statuses.model_id')
                        ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                }
            )
            ->whereIn('ma.application_no', $allAssignedApplications) // Verify $sections is an array
            ->select(
                'ma.id',
                'ma.created_at',
                'ma.application_no',
                'ma.status',
                'sections.section_code',
                'latest_statuses.is_mis_checked',
                'latest_statuses.is_scan_file_checked',
                'latest_statuses.is_uploaded_doc_checked',
                'latest_statuses.mis_checked_by',
                'latest_statuses.scan_file_checked_by',
                'latest_statuses.uploaded_doc_checked_by',
                'pm.old_propert_id as old_property_id', // Fixed alias
                'pm.new_colony_name',
                'oc.name as colony_name',
                'pm.block_no',
                'pm.plot_or_property_no',
                'pld.presently_known_as',
                'app.is_objected',
                DB::raw('NULL as flat_id'), // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                DB::raw('NULL as flat_number'), // Add NULL for flat_number on dated 07/01/25 By Lalit Tiwari
                DB::raw("'MutationApplication' as model_name") // Add model_name for the first query
            );
        if ($request->status) {
            $query1 = $query1->where('ma.status', ($request->status));
        }

        // Add search filter if search.value is present
        if ($request->input('search.value')) {
            $searchValue = $request->input('search.value');
            $query1->where(function ($query) use ($searchValue) {
                $query->where('ma.application_no', 'like', "%$searchValue%")
                    ->orWhere('pm.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                    ->orWhere('pm.new_colony_name', 'like', "%$searchValue%")  // Correctly reference new_colony_name
                    ->orWhere('pm.block_no', 'like', "%$searchValue%")  // Correctly reference block
                    ->orWhere('pm.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhere('ma.created_at', 'like', "%$searchValue%");
            });
        }

        //Query for Conversion applications added by Nitin
        $serviceType4 = getServiceType('CONVERSION');
        $query4 = DB::table('conversion_applications as ca')
            ->leftJoin('property_masters', 'ca.property_master_id', '=', 'property_masters.id')->join('property_section_mappings as psm', function ($join) {
                $join->on('property_masters.new_colony_name', 'psm.colony_id');
                $join->whereColumn('property_masters.property_type', 'psm.property_type');
                $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
            })
            ->join('sections', 'psm.section_id', 'sections.id')
            ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
            ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
            ->leftJoin('applications as app', 'ca.application_no', '=', 'app.application_no')
            ->leftJoinSub(
                DB::table('application_statuses')
                    ->select(
                        'id',
                        'model_id',
                        'reg_app_no',
                        'service_type',
                        'is_mis_checked',
                        'is_scan_file_checked',
                        'is_uploaded_doc_checked',
                        'mis_checked_by',
                        'scan_file_checked_by',
                        'uploaded_doc_checked_by',
                        'created_at',
                        DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                    )
                    ->where('service_type', $serviceType4),
                'latest_statuses',
                function ($join) {
                    $join->on('ca.id', '=', 'latest_statuses.model_id')
                        ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                }
            )
            ->whereIn('ca.application_no', $allAssignedApplications)
            ->select(
                'ca.id',
                'ca.created_at',
                'ca.application_no',
                'ca.status',
                'sections.section_code',
                'latest_statuses.is_mis_checked',
                'latest_statuses.is_scan_file_checked',
                'latest_statuses.is_uploaded_doc_checked',
                'latest_statuses.mis_checked_by',
                'latest_statuses.scan_file_checked_by',
                'latest_statuses.uploaded_doc_checked_by',
                'property_masters.old_propert_id as old_property_id',
                'property_masters.new_colony_name',
                'old_colonies.name as colony_name',
                'property_masters.block_no',
                'property_masters.plot_or_property_no',
                'property_lease_details.presently_known_as',
                'app.is_objected',
                DB::raw('NULL as flat_id'), // Add NULL for flat_id
                DB::raw('NULL as flat_number'), // Add NULL for flat_number on dated 07/01/25 By Lalit Tiwari
                DB::raw("'ConversionApplication' as model_name") // Add model_name for the first query
            );

        if ($request->status) {
            $query4 = $query4->where('ca.status', ($request->status));
        }

        // Add search filter if search.value is present
        if ($request->input('search.value')) {
            $searchValue = $request->input('search.value');
            $query4->where(function ($query) use ($searchValue) {
                $query->where('ca.application_no', 'like', "%$searchValue%")
                    ->orWhere('property_masters.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                    ->orWhere('property_masters.new_colony_name', 'like', "%$searchValue%")  // Correctly reference new_colony_name
                    ->orWhere('property_masters.block_no', 'like', "%$searchValue%")  // Correctly reference block
                    ->orWhere('property_masters.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                    ->orWhere('ca.created_at', 'like', "%$searchValue%");
            });
        }

        $clonedQuery1 = (clone $query1);
        $clonedQuery4 = (clone $query4);

        // Combine all three queries using UNION
        $combinedQuery = $clonedQuery1->union($clonedQuery4);
        // $combinedQuery = $clonedQuery1;

        $limit = $request->input('length');
        $start = $request->input('start');
        if ($request->input('order.0.column')) {
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
        } else {
            $order = 'created_at';
            $dir = 'desc';
        }

        $totalData = $combinedQuery->count();
        $totalFiltered = $totalData;

        $applications = $combinedQuery->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
        $data = [];
        $showSendProofReadingLink = false;
        // dd($applications);
        foreach ($applications as $key => $application) {
            if ($application->status) {
                // Get the service code only once to avoid repetitive calls
                $serviceCode = getServiceCodeById($application->status);

                // Check if the application status is 'objected', 'rejected', or 'approved'
                if (in_array($serviceCode, ['APP_OBJ', 'APP_REJ', 'APP_APR'])) {
                    $showSendProofReadingLink = false;
                } else {
                    // Check if the proof reading link has been sent at least once
                    $isProofReadingLinkSent = ApplicationAppointmentLink::where('application_no', $application->application_no)->exists();

                    // Show the proof reading link if it has been sent at least once
                    $showSendProofReadingLink = $isProofReadingLinkSent;
                }
            }
            $mis_checked_by = User::find($application->mis_checked_by);
            $scan_file_checked_by = User::find($application->scan_file_checked_by);
            $uploaded_doc_checked_by = User::find($application->uploaded_doc_checked_by);
            $nestedData['id'] = $key + 1;
            $applicationNumber = $application->application_no;
            $userCurrentApplication = userCurrentActionableApplication();

            //  $appMovementCount = ApplicationMovement::where('application_no', $application->application_no)->count();
            //  if (Auth::user()->roles[0]->name == 'section-officer' && getServiceCodeById($application->status) == 'APP_NEW') {
            //      $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertDot"></div></div>';
            //  } else if (Auth::user()->roles[0]->name == 'section-officer' && getServiceCodeById($application->status) == 'APP_IP' &&     $appMovementCount == 1) {
            //      $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertDot"></div></div>';
            //  } else {
            //      $latestRecord = ApplicationMovement::where('application_no', $application->application_no)
            //          ->latest('created_at')
            //          ->first();
            //      if (!is_null($latestRecord) && $latestRecord->assigned_to == Auth::user()->id) {
            //          $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertDot"></div></div>';
            //      } else {
            //          $applicationNumber = $application->application_no;
            //      }
            //  }




            $appMovementCount = ApplicationMovement::where('application_no', $application->application_no)->count();
            if (Auth::user()->roles[0]->name == 'section-officer' && getServiceCodeById($application->status) == 'APP_NEW') {
                if ($userCurrentApplication == $application->application_no && $userCurrentApplication != null) {
                    $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertGreen"></div></div>';
                } else {
                    $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertDot"></div></div>';
                }
            } else if (Auth::user()->roles[0]->name == 'section-officer' && getServiceCodeById($application->status) == 'APP_IP' &&     $appMovementCount == 1) {
                if ($userCurrentApplication == $application->application_no && $userCurrentApplication != null) {
                    $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertGreen"></div></div>';
                } else {
                    $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertDot"></div></div>';
                }
            } else {
                $latestRecord = ApplicationMovement::where('application_no', $application->application_no)
                    ->latest('created_at')
                    ->first();
                if (!is_null($latestRecord) && $latestRecord->assigned_to == Auth::user()->id) {
                    if ($userCurrentApplication == $application->application_no && $userCurrentApplication != null) {
                        $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertGreen"></div></div>';
                    } else {
                        $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertDot"></div></div>';
                    }
                } else {
                    $applicationNumber = $application->application_no;
                }
            }




            $nestedData['application_no'] = $applicationNumber;
            $nestedData['old_property_id'] = $application->old_property_id;
            $nestedData['new_colony_name'] = $application->colony_name;
            $nestedData['block_no'] = $application->block_no;
            $nestedData['plot_or_property_no'] = $application->plot_or_property_no;
            $nestedData['presently_known_as'] = $application->presently_known_as;
            $flatHTML = '';
            if (!empty($application->flat_id)) {
                $flatHTML .= '<div class="d-flex gap-2 align-items-center">' . $application->flat_number . '</div><span class="text-secondary">(' . $application->flat_id . ')</span>';
            } else {
                $flatHTML .= '<div>NA</div>';
            }
            $nestedData['flat_id'] =   $flatHTML;
            $nestedData['section'] = $application->section_code;

            switch ($application->model_name) {
                case 'MutationApplication':
                    $appliedFor = 'Mutation';
                    break;
                case 'LandUseChangeApplication':
                    $appliedFor = 'LUC';
                    break;
                case 'DeedOfApartmentApplication':
                    $appliedFor = 'DOA';
                    break;
                case 'ConversionApplication':
                    $appliedFor = 'CONVERSION';
                    break;
                default:
                    // Default action
                    break;
            }
            //for getting status
            $item = getStatusDetailsById($application->status);
            $itemCode = $item->item_code;
            $itemName = $item->item_name;
            $itemColor = $item->color_code;
            $statusClasses = [
                'APP_REJ' => 'statusRejected',
                'APP_NEW' => 'statusNew',
                'APP_IP' => 'statusSecondary',
                'RS_REW' => 'text-white bg-secondary',
                'RS_PEN' => 'text-info bg-light-info',
                'APP_APR' => 'landtypeFreeH',
                'APP_OBJ' => 'statusObject',
                'APP_HOLD' => 'statusHold',
            ];
            $class = $statusClasses[$itemCode] ?? 'text-secondary bg-light';
            $nestedData['applied_for'] = '<div class="d-flex flex-column gap-1">
                 <label class="badge bg-info mx-1">' . $appliedFor . '</label>';

            if ($application->is_objected == 1) {
                $nestedData['applied_for'] .= '<label class="badge bg-danger mx-1">Objected</label>';
            }

            $nestedData['applied_for'] .= '</div>';
            $nestedData['activity'] = [
                'mis' => !empty($application->is_mis_checked) ? $application->is_mis_checked : 'NA',
                'scanned_files' => !empty($application->is_scan_file_checked) ? $application->is_scan_file_checked : 'NA',
                'uploaded_doc' => !empty($application->is_uploaded_doc_checked) ? $application->is_uploaded_doc_checked : 'NA',
                'mis_checked_by' => !empty($application->mis_checked_by) ? $mis_checked_by->name : '',
                'scan_file_checked_by' => !empty($application->scan_file_checked_by) ? $scan_file_checked_by->name : '',
                'uploaded_doc_checked_by' => !empty($application->uploaded_doc_checked_by) ? $uploaded_doc_checked_by->name : '',
                'mis_color_code' => !empty(getServiceTypeColorCode('MIS_CHECK')) ? getServiceTypeColorCode('MIS_CHECK') : '',
                'scan_file_color_code' => !empty(getServiceTypeColorCode('SCAN_CHECK')) ? getServiceTypeColorCode('SCAN_CHECK') : '',
                'uploaded_doc_color_code' => !empty(getServiceTypeColorCode('UP_DOC_CHE')) ? getServiceTypeColorCode('UP_DOC_CHE') : '',
            ];

            $nestedData['status'] = '<span class="highlight_value ' . $class . '">' . ucwords($itemName) . '</span>';
            $model = base64_encode($application->model_name);

            // Prepare actions


            $action = '<div class="d-flex gap-2">';
            $action .= '<button type="button" class="btn btn-primary px-5" onclick="handleViewApplication(\'' . $application->application_no . '\', \'' . $model . '\', ' . $application->id . ')">View</button>
                        <button type="button" class="btn btn-success" onclick="getFileMovement(\'' . $application->application_no . '\', this)">
                            File Movement
                        </button>';
            // Add meeting link button
            if (Auth::user()->roles[0]->name == 'deputy-lndo' && $appliedFor != "LUC" && $showSendProofReadingLink) {
                $action .= '<button type="button" class="btn btn-secondary px-5 send-meeting-link" data-application-id="' . $application->id . '" data-application-model_name="' . $application->model_name . '" data-application-no="' . $application->application_no . '">Send Meeting Link</button>';
            }
            $action .= '</div>';




            $nestedData['action'] = $action;
            $nestedData['created_at'] = Carbon::parse($application->created_at)
                ->setTimezone('Asia/Kolkata')
                ->format('d M Y H:i:s');

            $data[] = $nestedData;
        }

        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        ];

        return response()->json($json_data);
    }

    //Update application - Lalit tiwari (02/dec/2024)
    public function updateApplication(Request $request)
    {
        try {
        $transactionSuccess = false;
        if (!empty($request->applicationModelType)) {
            return DB::transaction(function () use ($request, &$transactionSuccess) {
                //Checking Model Type 
                if ($request->applicationModelType == 'DeedOfApartmentApplication') {
                    //Checking Applicaiton Number & Model Id
                    if (!empty($request->applicationNumber) && !empty($request->updateId)) {
                        //Fetching application Record
                        $applicationObj = DeedOfApartmentApplication::find($request->updateId);
                        if (!empty($applicationObj)) {
                            //Get Application details record
                            $applicationDetails = Application::where('application_no', $applicationObj->application_no)->first();
                            $oldApplicationObj = $applicationObj->getOriginal();
                            $applicationObj->building_name = !empty($request->buildingName) ? $request->buildingName : $applicationObj->building_name;
                            $applicationObj->original_buyer_name = !empty($request->originalBuyerName) ? $request->originalBuyerName : $applicationObj->original_buyer_name;
                            $applicationObj->present_occupant_name = !empty($request->presentOccupantName) ? $request->presentOccupantName : $applicationObj->present_occupant_name;
                            $applicationObj->purchased_from = !empty($request->purchasedFrom) ? $request->purchasedFrom : $applicationObj->purchased_from;
                            $applicationObj->purchased_date = !empty($request->purchaseDate) ? $request->purchaseDate : $applicationObj->purchased_date;
                            $applicationObj->flat_area = !empty($request->apartmentArea) ? $request->apartmentArea : $applicationObj->flat_area;
                            $applicationObj->plot_area = !empty($request->plotArea) ? $request->plotArea : $applicationObj->plot_area;
                            $applicationObj->status = getServiceType('APP_IP');
                            if ($applicationObj->isDirty()) {
                                $applicationObj->save();
                                $changes = $applicationObj->getChanges();
                                //Update record into history table
                                $deedOfApartmentHistory = new DeedOfApartmentApplicationHistory();
                                $deedOfApartmentHistory->application_no = $request->applicationNumber;
                                foreach ($changes as $key => $change) {
                                    if ($key != 'updated_at' && $key != 'updated_by' && $key != 'status') {
                                        $deedOfApartmentHistory->$key = $oldApplicationObj[$key];
                                        $newKey = 'new_' . $key;
                                        $deedOfApartmentHistory->$newKey = $change;
                                    }
                                }
                                $deedOfApartmentHistory->updated_by = Auth::id();
                                $deedOfApartmentHistory->save();
                            }
                            //Commen Function for Update Additonal Documents
                            self::uploadAdditionalDocuments($request, $applicationDetails, $applicationObj);
                            //Update Status In Progress in Main Applicaition Table
                            self::updateMainApplicationStatus($request);
                            //Update Status in progress in Own Application Status Like DOA,Subtitution mutation, luc, convertion table
                            self::updateApplicationStatus($request);
                            //Insert record into application movement table
                            self::insertRecordApplicationMovement($applicationDetails, $request);
                            //Insert Record into application status table
                            self::insertRecordApplicationStatus($applicationDetails);
                            // Delete record into App Latest Action table
                            self::deleteAppLatestActionRecord($applicationDetails);
                            $transactionSuccess = true;
                        }
                    }
                } elseif ($request->applicationModelType == 'MutationApplication') {
                    // Code for MutationApplication
                    // dd($request->all());

                    $messages = [
                        'mutNameAsConLease.required' => 'Executed in favour of is required.',
                        'mutExecutedOnAsConLease.required' => 'Executed on is required.',
                        'mutRegnoAsConLease.required' => 'Registration no. is required.',
                        'mutBooknoAsConLease.required' => 'Book no. is required.',
                        'mutVolumenoAsConLease.required' => 'Volume no. is required.',
                        'mutPagenoFrom.required' => 'Page no. from is required.',
                        'mutPagenoTo.required' => 'Page no. to is required.',
                        'mutRegdateAsConLease.required' => 'Registration date is required.',
                    ];

                    $validator = Validator::make($request->all(), [
                        'mutNameAsConLease' => 'required',
                        'mutExecutedOnAsConLease' => 'required',
                        'mutRegnoAsConLease' => 'required',
                        'mutBooknoAsConLease' => 'required',
                        'mutVolumenoAsConLease' => 'required',
                        'mutPagenoFrom' => 'required',
                        'mutPagenoTo' => 'required',
                        'mutRegdateAsConLease' => 'required',
                    ], $messages);

                    if ($validator->fails()) {
                        // Log the error message if validation fails
                        Log::info("| " . Auth::user()->email . " | Mutation step first all values not entered: " . json_encode($validator->errors()));
                        return redirect()->back()->with('failure', $validator->errors()->first());
                    }


                    if (!empty($request->applicationNumber) && !empty($request->updateId)) {
                        //Fetching application Record
                        $applicationObj = MutationApplication::find($request->updateId);
                        if (!empty($applicationObj)) {
                            //Get Application details record
                            $applicationDetails = Application::where('application_no', $applicationObj->application_no)->first();
                            $oldApplicationObj = $applicationObj->getOriginal();


                            $documentArray = [
                                $request->deathCertificate_check,
                                $request->saleDeed_check,
                                $request->regdWillDeed_check,
                                $request->unregdWillCodocil_check,
                                $request->relinquishmentDeed_check,
                                $request->giftDeed_check,
                                $request->survivingMemberCertificate_check,
                                $request->sanctionBuildingPlan_check,
                                $request->anyOtherDocument_check,
                            ];
                            // dd($documentArray);
                            $array = array_filter($documentArray);
                            $array = array_values($array);
                            $soughtByApplicantDocuments = json_encode($array);
                            // dd($soughtByApplicantDocuments);

                            $applicationObj->name_as_per_lease_conv_deed = $request->mutNameAsConLease;
                            $applicationObj->executed_on = $request->mutExecutedOnAsConLease;
                            $applicationObj->reg_no_as_per_lease_conv_deed = $request->mutRegnoAsConLease;
                            $applicationObj->book_no_as_per_lease_conv_deed = $request->mutBooknoAsConLease;
                            $applicationObj->volume_no_as_per_lease_conv_deed = $request->mutVolumenoAsConLease;
                            $applicationObj->page_no_as_per_deed = $request->mutPagenoFrom . '-' . $request->mutPagenoTo;
                            $applicationObj->reg_date_as_per_lease_conv_deed = $request->mutRegdateAsConLease;
                            $applicationObj->sought_on_basis_of_documents = $soughtByApplicantDocuments;
                            $applicationObj->property_stands_mortgaged = $request->mutPropertyMortgaged;
                            $applicationObj->mortgaged_remark = ($request->mutPropertyMortgaged == 1) ? $request->mutMortgagedRemarks : NULL;
                            $applicationObj->is_basis_of_court_order = $request->courtorderMutation;
                            if (isset($request->courtorderMutation)) {
                                $applicationObj->court_case_no = $request->mutCaseNo;
                                $applicationObj->court_case_details = $request->mutCaseDetail;
                            } else {
                                $applicationObj->court_case_no = null;
                                $applicationObj->court_case_details = null;
                            }
                            $applicationObj->status = getServiceType('APP_IP');
                            if ($applicationObj->isDirty()) {
                                $applicationObj->save();
                                $changes = $applicationObj->getChanges();
                                //Update record into history table
                                $mutationApplicationHistory = new MutationApplicationHistory();
                                $mutationApplicationHistory->application_no = $request->applicationNumber;
                                foreach ($changes as $key => $change) {
                                    if ($key != 'updated_at' && $key != 'updated_by') {
                                        $mutationApplicationHistory->$key = $oldApplicationObj[$key];
                                        $newKey = 'new_' . $key;
                                        $mutationApplicationHistory->$newKey = $change;
                                    }
                                }
                                $mutationApplicationHistory->created_by = Auth::id();
                                $mutationApplicationHistory->updated_by = Auth::id();
                                $mutationApplicationHistory->save();
                            }

                            //store mutation step three data
                            $documentsSaved = self::updateMutationStepThree($request, $applicationDetails, $applicationObj, $soughtByApplicantDocuments);
                            if ($documentsSaved == false) {
                                $transactionSuccess = false;
                                DB::rollback();
                                return redirect()->back()->with('failure', 'Please upload all selected documents.');
                            }

                            //For update Coapplicants
                            self::updateCoApplicants($request->coapplicant, $request->updateId, $applicationObj->old_property_id, 'mutation', 'MutationApplication', 'SUB_MUT');

                            //Commen Function for Update Additonal Documents
                            self::uploadAdditionalDocuments($request, $applicationDetails, $applicationObj);
                            //Update Status In Progress in Main Applicaition Table
                            self::updateMainApplicationStatus($request);
                            //Insert record into application movement table
                            self::insertRecordApplicationMovement($applicationDetails, $request);
                            //Insert Record into application status table
                            self::insertRecordApplicationStatus($applicationDetails);
                            // Delete record into App Latest Action table
                            self::deleteAppLatestActionRecord($applicationDetails);
                            $transactionSuccess = true;
                        }
                    }
                } elseif ($request->applicationModelType == 'LandUseChangeApplication') {
                    if (!empty($request->applicationNumber) && !empty($request->updateId)) {
                        //Fetching application Record
                        $applicationObj = LandUseChangeApplication::find($request->updateId);
                        if (!empty($applicationObj)) {
                            //Get Application details record
                            $applicationDetails = Application::where('application_no', $applicationObj->application_no)->first();
                            $oldApplicationObj = $applicationObj->getOriginal();
                            $applicationObj->property_type_change_to = !empty($request->lucpropertytypeto) ? $request->lucpropertytypeto : $applicationObj->property_type_change_to;
                            $applicationObj->property_subtype_change_to = !empty($request->lucpropertysubtypeto) ? $request->lucpropertysubtypeto : $applicationObj->property_subtype_change_to;
                            $applicationObj->status = getServiceType('APP_IP');
                            if ($applicationObj->isDirty()) {
                                $applicationObj->save();
                                $changes = $applicationObj->getChanges();
                                //Update record into history table
                                $lucHistory = new LandUseChangeApplicationHistory();
                                $lucHistory->application_no = $request->applicationNumber;
                                unset($changes['updated_at'], $changes['status'], $changes['updated_by']);
                                $keys = array_keys($changes);
                                foreach ($keys as $i) {
                                    $lucHistory->{$i} = $oldApplicationObj[$i];
                                    $newKey = 'new_' . $i;
                                    $lucHistory->{$newKey} = $changes[$i];
                                }
                                $lucHistory->save();
                            }
                            //Commen Function for Update Additonal Documents
                            self::uploadAdditionalDocuments($request, $applicationDetails, $applicationObj);
                            //Update Status In Progress in Main Applicaition Table
                            self::updateMainApplicationStatus($request);
                            //Update Status in progress in Own Application Status Like DOA,Subtitution mutation, luc, convertion table
                            self::updateApplicationStatus($request);
                            //Insert record into application movement table
                            self::insertRecordApplicationMovement($applicationDetails, $request);
                            //Insert Record into application status table
                            self::insertRecordApplicationStatus($applicationDetails);
                            // Delete record into App Latest Action table
                            self::deleteAppLatestActionRecord($applicationDetails);
                            $transactionSuccess = true;
                        }
                    }
                } elseif ($request->applicationModelType == 'ConversionApplication') {
                    // dd($request->convcoapplicant);
                    // Code for ConversionApplication
                    if (!empty($request->applicationNumber) && !empty($request->updateId)) {
                        //Fetching application Record
                        $applicationObj = ConversionApplication::find($request->updateId);
                        if (!empty($applicationObj)) {
                            //Get Application details record
                            $applicationDetails = Application::where('application_no', $applicationObj->application_no)->first();
                            $oldApplicationObj = $applicationObj->getOriginal();
                            $applicationObj->applicant_name = $request->convNameAsOnLease;
                            $applicationObj->relation_prefix = $request->convRelationPrefix;
                            $applicationObj->relation_name = $request->convRelationName;
                            $applicationObj->executed_on = $request->convExecutedOnAsOnLease;
                            $applicationObj->reg_no = $request->convRegnoAsOnLease;
                            $applicationObj->book_no = $request->convBooknoAsOnLease;
                            $applicationObj->volume_no = $request->convVolumenoAsOnLease;
                            $applicationObj->page_no = $request->convPagenoFrom . '-' . $request->convPagenoTo;
                            $applicationObj->reg_date = $request->convRegdateAsOnLease;

                            $applicationObj->status = getServiceType('APP_IP');
                            if ($applicationObj->isDirty()) {
                                $applicationObj->save();
                                $changes = $applicationObj->getChanges();
                                //Update record into history table
                                $conversionApplicationHistory = new ConversionApplicationHistory();
                                $conversionApplicationHistory->application_no = $request->applicationNumber;
                                foreach ($changes as $key => $change) {
                                    if ($key != 'updated_at' && $key != 'updated_by') {
                                        $conversionApplicationHistory->$key = $oldApplicationObj[$key];
                                        $newKey = 'new_' . $key;
                                        $conversionApplicationHistory->$newKey = $change;
                                    }
                                }
                                $conversionApplicationHistory->created_by = Auth::id();
                                $conversionApplicationHistory->updated_by = Auth::id();
                                $conversionApplicationHistory->save();
                            }


                            //to update cooapplicants
                            self::updateCoApplicants($request->convcoapplicant, $request->updateId, $applicationObj->old_property_id, 'CONVERSION', 'ConversionApplication', 'CONVERSION');

                            //Commen Function for Update Additonal Documents
                            self::uploadAdditionalDocuments($request, $applicationDetails, $applicationObj);
                            //Update Status In Progress in Main Applicaition Table
                            self::updateMainApplicationStatus($request);
                            //Insert record into application movement table
                            self::insertRecordApplicationMovement($applicationDetails, $request);
                            //Insert Record into application status table
                            self::insertRecordApplicationStatus($applicationDetails);
                            // Delete record into App Latest Action table
                            self::deleteAppLatestActionRecord($applicationDetails);
                            $transactionSuccess = true;
                        }
                    }
                } elseif ($request->applicationModelType == 'NocApplication') {
                    if (!empty($request->applicationNumber) && !empty($request->updateId)) {
                        //Fetching application Record
                        $applicationObj = NocApplication::find($request->updateId);
                        if (!empty($applicationObj)) {
                            //Get Application details record
                            $applicationDetails = Application::where('application_no', $applicationObj->application_no)->first();
                            $oldApplicationObj = $applicationObj->getOriginal();
                            $applicationObj->name_as_per_noc_conv_deed = $request->conveyanceDeedName;
                            $applicationObj->executed_on_as_per_noc_conv_deed = $request->conveyanceExecutedOn;
                            $applicationObj->reg_no_as_per_noc_conv_deed = $request->conveyanceRegnoDeed;
                            $applicationObj->book_no_as_per_noc_conv_deed = $request->conveyanceBookNoDeed;
                            $applicationObj->volume_no_as_per_noc_conv_deed = $request->conveyanceVolumeNo;
                            $applicationObj->page_no_as_per_noc_conv_deed = $request->conveyancePagenoFrom . '-' . $request->conveyancePagenoTo;
                            $applicationObj->reg_date_as_per_noc_conv_deed = $request->conveyanceRegDate;
                            $applicationObj->con_app_date_as_per_noc_conv_deed = $request->conveyanceConAppDate;

                            $applicationObj->status = getServiceType('APP_IP');
                            if ($applicationObj->isDirty()) {
                                $applicationObj->save();
                                $changes = $applicationObj->getChanges();
                                //Update record into history table
                                $nocApplicationHistory = new NocApplicationHistory();
                                $nocApplicationHistory->application_no = $request->applicationNumber;
                                foreach ($changes as $key => $change) {
                                    if ($key != 'updated_at' && $key != 'updated_by') {
                                        $nocApplicationHistory->$key = $oldApplicationObj[$key];
                                        $newKey = 'new_' . $key;
                                        $nocApplicationHistory->$newKey = $change;
                                    }
                                }
                                $nocApplicationHistory->created_by = Auth::id();
                                $nocApplicationHistory->updated_by = Auth::id();
                                $nocApplicationHistory->save();
                            }


                            //to update cooapplicants
                            self::updateNocCoApplicants($request->noccoapplicant, $request->updateId, $applicationObj->old_property_id, 'NOC', 'NocApplication', 'NOC');

                            //Commen Function for Update Additonal Documents
                            self::uploadAdditionalDocuments($request, $applicationDetails, $applicationObj);
                            //Update Status In Progress in Main Applicaition Table
                            self::updateMainApplicationStatus($request);
                            //Insert record into application movement table
                            self::insertRecordApplicationMovement($applicationDetails, $request);
                            //Insert Record into application status table
                            self::insertRecordApplicationStatus($applicationDetails);
                            // Delete record into App Latest Action table
                            self::deleteAppLatestActionRecord($applicationDetails);
                            $transactionSuccess = true;
                        }
                    }
                }

                if ($transactionSuccess) {
                    //for send notification - SOURAV CHAUHAN (19/Feb/2025)
                    $data = [
                        'application_no' =>  $request->applicationNumber,
                    ];

                    $action = "APP_OBJ_RES";
                    $checkEmailTemplateExists = checkTemplateExists('email', $action);
                    if (!empty($checkEmailTemplateExists)) {
                        try {
                        $mailSettings = app(SettingsService::class)->getMailSettings($action);
                        $mailer = new \App\Mail\CommonPHPMail($data, $action, $communicationTrackingId ?? null);
                        $mailResponse = $mailer->send(Auth::user()->email, $mailSettings);

                        Log::info("Email sent successfully.", [
                            'action' => $action,
                            'email'  => Auth::user()->email,
                            'data'   => $data,
                        ]);
                    } catch (\Exception $e) {
                        Log::error("Email sending failed.", [
                            'action' => $action,
                            'email'  => Auth::user()->email,
                            'error'  => $e->getMessage(),
                        ]);
                    }
                    }

                    $mobileNo = Auth::user()->mobile_no;
                    $checkSmsTemplateExists = checkTemplateExists('sms', $action);
                    if (!empty($checkSmsTemplateExists)) {
                        $this->communicationService->sendSmsMessage($data, $mobileNo, $action);
                    }
                    $checkWhatsappTemplateExists = checkTemplateExists('whatsapp', $action);
                    if (!empty($checkWhatsappTemplateExists)) {
                        $this->communicationService->sendWhatsAppMessage($data, $mobileNo, $action);
                    }
                    return redirect()->route('applications.history.details')->with('success', 'Application successfully updated');
                } else {
                    return redirect()->route('applications.history.details')->with('failure', 'An unexpected error occurred!');
                }
            });
        }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            $response = ['status' => false, 'message' => $e->getMessage(), 'data' => 0];
            return json_encode($response);
        }
    }




    //for storing temp co Applicants - Sourav Chauhan (17/sep/2024)
    protected function updateCoApplicants($coapplicants, $modelId, $propertyId, $type, $model, $serviceCode)
    {
        // dd($coapplicants, $modelId,$propertyId,$type,$model,$serviceCode);
        try {
            $allSaved = true;
            foreach ($coapplicants as $key => $coapplicant) {
                if (!empty($coapplicant['name'])) {

                    $user = Auth::user();
                    $name = $coapplicant['name'] . '_CO_' . $key + 1;

                    $applicantNo = $user->applicantUserDetails->applicant_number;

                    $propertyDetails = PropertyMaster::where('old_propert_id', $propertyId)->first();
                    $colonyId = $propertyDetails['new_colony_name'];
                    $colony = OldColony::find($colonyId);
                    $colonyCode = $colony->code;

                    $updateId = $modelId;
                    if ($serviceCode == 'SUB_MUT') {
                        $coapplicantId = $coapplicant['id'];
                    } else {
                        $coapplicantId = $coapplicant['coapplicantId'];
                    }
                    if (isset($coapplicantId)) {
                        // $coapplicantId = $coapplicant['coapplicantId'];
                        if (!is_null($coapplicantId)) { //for edit coapplicant
                            $mainCoapplicant = Coapplicant::find($coapplicantId);

                            //coapplicant image
                            // if(isset($coapplicant['photo']) && $coapplicant['photo']){
                            //     $photo = $coapplicant['photo'];
                            //     $date = now()->format('YmdHis');
                            //     $fileName =  $name . '_' . $date . '.' . $photo->extension();
                            //     $pathToUpload = $applicantNo . '/' . $colonyCode . '/' . $type . '/' . $updateId.'/co-applicants/co-'.$key+1;
                            //     $photo = $photo->storeAs($pathToUpload, $fileName, 'public');
                            // } else {
                            //     $photo = $mainCoapplicant->image_path;
                            // }

                            //coapplicant aadhaarFile
                            // $aadharnumber = $coapplicant['aadharnumber'];
                            // if(isset($coapplicant['aadhaarFile']) && $coapplicant['aadhaarFile']){
                            //     $aadhaarFile = $coapplicant['aadhaarFile'];
                            //     $date = now()->format('YmdHis');
                            //     $fileName =  $aadharnumber . '_' . $date . '.' . $aadhaarFile->extension();
                            //     $pathToUpload = $applicantNo . '/' . $colonyCode . '/' . $type . '/' . $updateId.'/co-applicants/co-'.$key+1;
                            //     $aadharFile = $aadhaarFile->storeAs($pathToUpload, $fileName, 'public');
                            // } else {
                            //     $aadharFile = $mainCoapplicant->aadhaar_file_path;
                            // }

                            //coapplicant panFile
                            // $pannumber = $coapplicant['pannumber'];
                            // if(isset($coapplicant['panFile']) && $coapplicant['panFile']){
                            //     $panFile = $coapplicant['panFile'];
                            //     $date = now()->format('YmdHis');
                            //     $fileName =  $pannumber . '_' . $date . '.' . $panFile->extension();
                            //     $pathToUpload = $applicantNo . '/' . $colonyCode . '/' . $type . '/' . $updateId.'/co-applicants/co-'.$key+1;
                            //     $panFile = $panFile->storeAs($pathToUpload, $fileName, 'public');
                            // } else {
                            //     $panFile = $mainCoapplicant->pan_file_path;
                            // }

                            $mainCoapplicant->co_applicant_name = $coapplicant['name'];
                            $mainCoapplicant->co_applicant_gender = $coapplicant['gender'];

                            $mainCoapplicant->co_applicant_age = $coapplicant['dateOfBirth'];
                            $mainCoapplicant->prefix = $coapplicant['prefixInv'];
                            $mainCoapplicant->co_applicant_father_name = $coapplicant['secondnameInv'];
                            $mainCoapplicant->co_applicant_aadhar = $coapplicant['aadharnumber'];
                            $mainCoapplicant->co_applicant_pan = $coapplicant['pannumber'];
                            $mainCoapplicant->co_applicant_mobile = $coapplicant['mobilenumber'];
                            // $mainCoapplicant->image_path = $photo;
                            // $mainCoapplicant->aadhaar_file_path = $aadharFile;
                            // $mainCoapplicant->pan_file_path = $panFile;
                            $mainCoapplicant->save();
                        }
                    } else { //for create coapplicant
                        //coapplicant image
                        if ($coapplicant['photo']) {
                            $photo = $coapplicant['photo'];
                            $date = now()->format('YmdHis');
                            $fileName =  $name . '_' . $date . '.' . $photo->extension();
                            $pathToUpload = $applicantNo . '/' . $colonyCode . '/' . $type . '/' . $updateId . '/co-applicants/co-' . $key + 1;
                            $photo = $photo->storeAs($pathToUpload, $fileName, 'public');
                        }

                        //coapplicant aadhaarFile
                        $aadharnumber = $coapplicant['aadharnumber'];
                        if ($coapplicant['aadhaarFile']) {
                            $aadhaarFile = $coapplicant['aadhaarFile'];
                            $date = now()->format('YmdHis');
                            $fileName =  $aadharnumber . '_' . $date . '.' . $aadhaarFile->extension();
                            $pathToUpload = $applicantNo . '/' . $colonyCode . '/' . $type . '/' . $updateId . '/co-applicants/co-' . $key + 1;
                            $aadharFile = $aadhaarFile->storeAs($pathToUpload, $fileName, 'public');
                        }

                        //coapplicant panFile
                        $pannumber = $coapplicant['pannumber'];
                        if ($coapplicant['panFile']) {
                            $panFile = $coapplicant['panFile'];
                            $date = now()->format('YmdHis');
                            $fileName =  $pannumber . '_' . $date . '.' . $panFile->extension();
                            $pathToUpload = $applicantNo . '/' . $colonyCode . '/' . $type . '/' . $updateId . '/co-applicants/co-' . $key + 1;
                            $panFile = $panFile->storeAs($pathToUpload, $fileName, 'public');
                        }

                        // dd('doc uploaded');
                        $mainCoapplicant = Coapplicant::create([
                            'service_type' => getServiceType($serviceCode),
                            'model_name' => $model,
                            'model_id' => $modelId,
                            'co_applicant_name' => $coapplicant['name'],
                            'co_applicant_gender' => $coapplicant['gender'],
                            'co_applicant_age' => $coapplicant['dateOfBirth'],
                            'prefix' => $coapplicant['prefixInv'],
                            'co_applicant_father_name' => $coapplicant['secondnameInv'],
                            'co_applicant_aadhar' => $coapplicant['aadharnumber'],
                            'co_applicant_pan' => $coapplicant['pannumber'],
                            'co_applicant_mobile' => $coapplicant['mobilenumber'],
                            'image_path' => $photo,
                            'aadhaar_file_path' => $aadharFile,
                            'pan_file_path' => $panFile,
                            'created_by' => Auth::user()->id,
                        ]);
                    }

                    if (!$mainCoapplicant) {
                        $allSaved = false;
                    }
                }
            }
            return $allSaved;
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['status' => false, 'message' => 'An error occurred while storing Coapplicants', 'error' => $e->getMessage()], 500);
        }
    }


    //For Update Existing Co applicant & Create New Applicant - Lalit Tiwar (21/march/2025)
    protected function updateNocCoApplicants($coapplicants, $modelId, $propertyId, $type, $model, $serviceCode)
    {
        // dd($coapplicants, $modelId,$propertyId,$type,$model,$serviceCode);
        try {
            $allSaved = true;
            foreach ($coapplicants as $key => $coapplicant) {
                if (!empty($coapplicant['name'])) {

                    $user = Auth::user();
                    $name = $coapplicant['name'] . '_CO_' . $key + 1;

                    $applicantNo = $user->applicantUserDetails->applicant_number;

                    $propertyDetails = PropertyMaster::where('old_propert_id', $propertyId)->first();
                    $colonyId = $propertyDetails['new_colony_name'];
                    $colony = OldColony::find($colonyId);
                    $colonyCode = $colony->code;
                    $updateId = $modelId;
                    $coapplicantId = $coapplicant['coapplicantId'];
                    if (isset($coapplicantId)) {
                        if (!is_null($coapplicantId)) { //for edit coapplicant
                            $mainCoapplicant = Coapplicant::find($coapplicantId);
                            $mainCoapplicant->co_applicant_name = $coapplicant['name'];
                            $mainCoapplicant->co_applicant_gender = $coapplicant['gender'];
                            $mainCoapplicant->co_applicant_age = $coapplicant['dateOfBirth'];
                            $mainCoapplicant->prefix = $coapplicant['conPrefixInv'];
                            $mainCoapplicant->co_applicant_father_name = $coapplicant['fathername'];
                            $mainCoapplicant->co_applicant_aadhar = $coapplicant['aadharnumber'];
                            $mainCoapplicant->co_applicant_pan = $coapplicant['pannumber'];
                            $mainCoapplicant->co_applicant_mobile = $coapplicant['mobilenumber'];
                            $mainCoapplicant->save();
                        }
                    } else { //for create coapplicant
                        //coapplicant image
                        if ($coapplicant['photo']) {
                            $photo = $coapplicant['photo'];
                            $date = now()->format('YmdHis');
                            $fileName =  $name . '_' . $date . '.' . $photo->extension();
                            $pathToUpload = $applicantNo . '/' . $colonyCode . '/' . $type . '/' . $updateId . '/co-applicants/co-' . $key + 1;
                            $photo = $photo->storeAs($pathToUpload, $fileName, 'public');
                        }

                        //coapplicant aadhaarFile
                        $aadharnumber = $coapplicant['aadharnumber'];
                        if ($coapplicant['aadhaarFile']) {
                            $aadhaarFile = $coapplicant['aadhaarFile'];
                            $date = now()->format('YmdHis');
                            $fileName =  $aadharnumber . '_' . $date . '.' . $aadhaarFile->extension();
                            $pathToUpload = $applicantNo . '/' . $colonyCode . '/' . $type . '/' . $updateId . '/co-applicants/co-' . $key + 1;
                            $aadharFile = $aadhaarFile->storeAs($pathToUpload, $fileName, 'public');
                        }

                        //coapplicant panFile
                        $pannumber = $coapplicant['pannumber'];
                        if ($coapplicant['panFile']) {
                            $panFile = $coapplicant['panFile'];
                            $date = now()->format('YmdHis');
                            $fileName =  $pannumber . '_' . $date . '.' . $panFile->extension();
                            $pathToUpload = $applicantNo . '/' . $colonyCode . '/' . $type . '/' . $updateId . '/co-applicants/co-' . $key + 1;
                            $panFile = $panFile->storeAs($pathToUpload, $fileName, 'public');
                        }

                        $mainCoapplicant = Coapplicant::create([
                            'service_type' => getServiceType($serviceCode),
                            'model_name' => $model,
                            'model_id' => $modelId,
                            'co_applicant_name' => $coapplicant['name'],
                            'co_applicant_gender' => $coapplicant['gender'],
                            'co_applicant_age' => $coapplicant['dateOfBirth'],
                            'prefix' => $coapplicant['conPrefixInv'],
                            'co_applicant_father_name' => $coapplicant['fathername'],
                            'co_applicant_aadhar' => $coapplicant['aadharnumber'],
                            'co_applicant_pan' => $coapplicant['pannumber'],
                            'co_applicant_mobile' => $coapplicant['mobilenumber'],
                            'image_path' => $photo,
                            'aadhaar_file_path' => $aadharFile,
                            'pan_file_path' => $panFile,
                            'created_by' => Auth::user()->id,
                        ]);
                    }

                    if (!$mainCoapplicant) {
                        $allSaved = false;
                    }
                }
            }
            return $allSaved;
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['status' => false, 'message' => 'An error occurred while storing Coapplicants', 'error' => $e->getMessage()], 500);
        }
    }







    //store mutation step three data - SOURAV CHAUHAN (6/Jan/2025)
    public function updateMutationStepThree($request, $applicationDetails, $applicationObj, $soughtByApplicantDocuments)
    {
        if (isset($applicationObj)) {
            $serviceType = getServiceType('SUB_MUT');
            // dd($applicationObj['sought_on_basis_of_documents']);
            // $checkedDocuments = json_decode($applicationObj['sought_on_basis_of_documents']);
            $checkedDocuments = json_decode($soughtByApplicantDocuments);
            // dd($soughtByApplicantDocuments);
            foreach ($checkedDocuments as $checkedDocument) {
                $itemName = getServiceTypeColorCode($checkedDocument);
                $isDocUploaded = Document::where('service_type', $serviceType)->where('model_id', $applicationDetails->model_id)->where('document_type', $itemName)->first();
                if (empty($isDocUploaded)) {
                    // dd($itemName);
                    Log::info("| " . Auth::user()->email . " | Mutation step three " . $checkedDocument . " documents not uploaded during re-submission");
                    // $transactionSuccess = false;
                    // DB::rollback();
                    return false;
                    // return redirect()->back()->with('failure', 'Please provide all selected documents.');
                }
            }
        }

        $serviceType = getServiceType('SUB_MUT');
        $applicationDocumentType = config('applicationDocumentType.MUTATION.Optional');
        $doumentDataStored = $this->storeDataForDocuments($request, $serviceType, $applicationDocumentType, $applicationDetails);
        return $doumentDataStored;
    }



    //for storing documents data - SOURAV CHAUHAN (06/Jan/2025)
    protected function storeDataForDocuments($request, $serviceType, $applicationDocumentType, $applicationDetails)
    {
        $doumentDataStored = false;
        foreach ($applicationDocumentType as $index => $documentType) {
            $modelName = 'MutationApplication';
            $serviceType = getServiceType('SUB_MUT');
            $updateId = $applicationDetails->model_id;
            // dd($documentType);
            $mainId = $documentType['id'];
            $mainLabel = $documentType['label'];
            $inputs = $documentType['inputs'];
            foreach ($inputs as $input) {
                $id = $input['id'];
                $label = $input['label'];
                // dd($request->$id);
                if ($request->$id) {
                    $savedDocumentDetails = Document::where('service_type', $serviceType)->where('model_id', $updateId)->where('document_type', $mainId)->first();
                    if ($savedDocumentDetails) {
                        $isDocumentValueAvailable = DocumentKey::where('document_id', $savedDocumentDetails->id)->where('key', $id)->first();
                        if (!empty($isDocumentValueAvailable)) {
                            // dd('inside if');
                            $isDocumentValueAvailable->value = $request->$id;
                            $isDocumentValueAvailable->updated_by = Auth::user()->id;
                            $isDocumentValueAvailable->save();
                        } else {
                            // dd('inside else');
                            $tempDocumentKey = DocumentKey::create([
                                'document_id' => $savedDocumentDetails->id,
                                'key' => $id,
                                'value' => $request->$id,
                                'created_by' => Auth::user()->id
                            ]);
                        }
                    }
                }
            }
        }
        $doumentDataStored = true;
        return $doumentDataStored;
    }


    // Function for upload additional documents for Edit Applications - Lalit Tiwari (03/dec/2024)
    public function uploadAdditionalDocuments($request, $applicationDetails, $applicationObj)
    {
        if (empty($request->applicationModelType)) {
            return redirect()->back()->with('failure', 'Application Model Not Found');
        }

        // Check type of documents to upload directories - Lalit (16/dec/2024)
        switch ($request->applicationModelType) {
            case 'MutationApplication':
                $type = 'mutation';
                break;
            case 'LandUseChangeApplication':
                $type = 'LUC';
                break;
            case 'DeedOfApartmentApplication':
                $type = 'deed_of_apartment';
                break;
            case 'ConversionApplication':
                $type = 'conversion';
                break;
            case 'NocApplication':
                $type = 'noc';
                break;
        }
        $applicantNumber = ApplicantUserDetail::where('user_id', Auth::id())->value('applicant_number');
        $colonyCode = $applicationObj->propertyMaster->newColony->code;
        // Upload Additional Documents
        /*$additionalTitles = $request->input('additional_document_titles');
        $additionalDocuments = $request->file('additional_documents');

        // Check if both inputs are provided and have the same count
        if (!empty($additionalTitles) && !empty($additionalDocuments) && is_array($additionalTitles) && is_array($additionalDocuments) && count($additionalTitles) === count($additionalDocuments)) {
            foreach ($additionalDocuments as $index => $file) {
                // Ensure the title exists and is not empty
                if (!empty($additionalTitles[$index])) {
                    $additionDocPath = GeneralFunctions::uploadFile($file, $applicantNumber . '/' . $colonyCode . '/' . $type . '/' . $request->applicationNumber . '/additional_document', $additionalTitles[$index]);
                    Document::create([
                        'title' => $additionalTitles[$index],
                        'file_path' => $additionDocPath,
                        'user_id' => Auth::user()->id,
                        'property_master_id' => $applicationObj->property_master_id,
                        'old_property_id' => $applicationObj->old_property_id,
                        'flat_id' => !empty($applicationObj->flat_id) ? $applicationObj->flat_id : null,
                        'service_type' => $applicationDetails->service_type,
                        'model_name' => $applicationDetails->model_name,
                        'model_id' => $applicationDetails->model_id,
                        'document_type' => 'AdditionalDocument'
                    ]);
                }
            }
        }*/

        // Adding additional check like index should not be empty for uploaded additional document - Lalit Tiwari (11/02/2025)
        $additionalTitles = $request->input('additional_document_titles');
        $additionalDocuments = $request->file('additional_documents');

        // Check if both inputs are arrays and have values
        if (
            !empty($additionalTitles) && is_array($additionalTitles) &&
            !empty($additionalDocuments) && is_array($additionalDocuments)
        ) {

            foreach ($additionalDocuments as $index => $file) {
                // Ensure the title exists and is not empty, and prevent undefined index errors
                if (isset($additionalTitles[$index]) && !empty($additionalTitles[$index])) {
                    /* $additionDocPath = GeneralFunctions::uploadFile(
                        $file,
                        $applicantNumber . '/' . $colonyCode . '/' . $type . '/' .
                            $request->applicationNumber . '/additional_document',
                        $additionalTitles[$index]
                    ); */
                    $additionDocPath = GeneralFunctions::uploadFileExceptSpecialCharacter(
                        $file,
                        $applicantNumber . '/' . $colonyCode . '/' . $type . '/' .
                            $request->applicationNumber . '/additional_document',
                        $additionalTitles[$index]
                    );
                    Document::create([
                        'title' => $additionalTitles[$index],
                        'file_path' => $additionDocPath,
                        'user_id' => Auth::user()->id,
                        'property_master_id' => $applicationObj->property_master_id,
                        'old_property_id' => $applicationObj->old_property_id,
                        'flat_id' => !empty($applicationObj->flat_id) ? $applicationObj->flat_id : null,
                        'service_type' => $applicationDetails->service_type,
                        'model_name' => $applicationDetails->model_name,
                        'model_id' => $applicationDetails->model_id,
                        'document_type' => 'AdditionalDocument'
                    ]);
                } else {
                    // Redirect back if a file is uploaded without a title
                    return redirect()->back()->with('failure', 'Each uploaded document must have a corresponding title.');
                }
            }
        }
    }

    // Function for update application status in progress for Edit Applications - Lalit Tiwari (05/dec/2024)
    public function updateApplicationStatus($request)
    {
        $modelClass = 'App\\Models\\' . $request->applicationModelType;
        $modelClass::where('id', $request->updateId)
            ->where('application_no', $request->applicationNumber)
            ->update(['status' => getServiceType('APP_IP')]);
    }

    // Function for update main applicaiton status in progress for Edit Applications - Lalit Tiwari (05/dec/2024)
    public function updateMainApplicationStatus($request)
    {
        Application::where('application_no', $request->applicationNumber)
            ->where('model_name', $request->applicationModelType)
            ->update(['status' => getServiceType('APP_IP')]);
    }

    // Function for insert record into applicaiton movements table for Edit Applications - Lalit Tiwari (05/dec/2024)
    public function insertRecordApplicationMovement($applicationDetails, $request)
    {
        $applicationNo = $applicationDetails->application_no;
        $applicationMovement = ApplicationMovement::where('application_no', $applicationNo)->where('assigned_by_role', 7)->first();
        // dd($applicationMovement);
        ApplicationMovement::create([
            'assigned_by' => Auth::user()->id,
            'assigned_by_role' => Auth::user()->roles[0]->id,
            'assigned_to' => $applicationMovement['assigned_by'],
            'assigned_to_role' => 7,
            'service_type' => $applicationDetails->service_type, //for mutation,LUC,DOA etc
            'model_id' => $applicationDetails->model_id,
            'status' => $applicationDetails->status, //for new application, objected application, rejected, approved etc
            'action' => 'moveForward', //for new application, objected application, rejected, approved etc
            'application_no' => $applicationDetails->application_no,
            'remarks' => $request->additionalRemark,
        ]);
    }

    // Function for insert new record for application into applicaiton status table for Edit Applications - Lalit Tiwari (05/dec/2024)
    public function insertRecordApplicationStatus($applicationDetails)
    {
        $applicationStatus = ApplicationStatus::where('reg_app_no', $applicationDetails->application_no)->latest()->first();

        $misCheckedByUser = $applicationStatus->mis_checked_by;
        ApplicationStatus::create([
            'service_type' => $applicationDetails->service_type,
            'model_id' => $applicationDetails->model_id,
            'reg_app_no' => $applicationDetails->application_no,
            'is_mis_checked' => true,
            'mis_checked_by' => $misCheckedByUser,
            'is_scan_file_checked' => false,
            'is_uploaded_doc_checked' => false,
            'created_by' => Auth::user()->id,
        ]);
    }

    // Function for delete app latest action record for edit applications - Lalit Tiwari (05/dec/2024)
    public function deleteAppLatestActionRecord($applicationDetails)
    {
        AppLatestAction::where('application_no', $applicationDetails->application_no)->delete();
    }

    //Send True / False to show send proof reading button application wise - Lalit (12/dec/2024)
    public function showAppointmentLinkButtonFun($modelName, $applicationNumber)
    {
        if (empty($modelName)) {
            return false; // Default case for invalid model name
        }

        switch ($modelName) {
            case 'MutationApplication':
                // for show hide the send appointment link button for CDV  SOURAV CHAUHAN
                $showAppointmentLinkButton = true;
                $applicationAppointmentLink = ApplicationAppointmentLink::where('application_no', $applicationNumber)->latest()->first();
                if ($applicationAppointmentLink) {
                    $showAppointmentLinkButton = false;
                }
                return $showAppointmentLinkButton;
                break;
            case 'LandUseChangeApplication':
                $showAppointmentLinkButton = false;
                return $showAppointmentLinkButton;
                break;
            case 'DeedOfApartmentApplication':
                $role = SpatieRole::where('name', 'lndo')->first();
                $userId = $role->users()->value('id');
                $latestAction = AppLatestAction::where('application_no', $applicationNumber)->first();
                $showAppointmentLinkButton = false;
                $applicationAppointmentLink = ApplicationAppointmentLink::where('application_no', $applicationNumber)->latest()->first();
                if (!empty($applicationAppointmentLink)) {
                    if (Carbon::parse($applicationAppointmentLink->valid_till)->isBefore(Carbon::today())) {
                        $showAppointmentLinkButton = true;
                    }
                } else {
                    if (!empty($latestAction)) {
                        if ($latestAction->latest_action == 'RECOMMENDED' && $latestAction->latest_action_by == $userId) {
                            $showAppointmentLinkButton = true;
                        } else {
                            $showAppointmentLinkButton = false;
                        }
                    }
                }
                return $showAppointmentLinkButton;
                break;
            case 'ConversionApplication':
                // for show hide the send appointment link button for CDV  SOURAV CHAUHAN
                $showAppointmentLinkButton = true;
                $applicationAppointmentLink = ApplicationAppointmentLink::where('application_no', $applicationNumber)->latest()->first();
                if ($applicationAppointmentLink) {
                    $showAppointmentLinkButton = false;
                }
                return $showAppointmentLinkButton;
                break;
            default:
                # code...
                break;
        }
    }

    // for uploading files by CDV - SOURAV CHAUHAN (12/DEC/2024)
    public function uploadFileforCdv(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:5120',
        ]);

        $file = $request->file;
        $id = $request->id;
        if ($file) {
            $documentUploded = Document::where('id', $id)->first();
            $date = now()->format('YmdHis');
            $documentType = $documentUploded->document_type;
            $fileName = $documentType . '_' . $date . '.' . $file->extension();
            $filePath = $documentUploded->file_path;
            $lastSlashPosition = strrpos($filePath, '/');
            $modifiedPath = substr($filePath, 0, $lastSlashPosition);
            if ($documentUploded->office_file_path != null) {
                $deletedFile = $documentUploded->office_file_path;
                if ($deletedFile) {
                    if (Storage::disk('public')->exists($deletedFile)) {
                        Storage::disk('public')->delete($deletedFile);
                    }
                }
            }

            $path = $file->storeAs($modifiedPath . '/official', $fileName, 'public');
            if ($path) {
                $documentUploded->office_file_path = $path;
                if ($documentUploded->save()) {
                    return response()->json(['status' => true, 'path' => $path]);
                } else {
                    return response()->json(['status' => false, 'message' => 'File update failed.']);
                }
            }
        }

        return response()->json(['status' => false, 'message' => 'File upload failed.']);
    }

    // for uploading files by CDV - SOURAV CHAUHAN (12/DEC/2024)
    public function deleteFileforCdv(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required',
        ]);

        $id = $request->id;
        $documentUploded = Document::where('id', $id)->first();

        if ($documentUploded && $documentUploded->office_file_path != null) {
            $deletedFile = $documentUploded->office_file_path;
            $documentUploded->office_file_path = null;

            if ($documentUploded->save()) {
                if ($deletedFile) {
                    if (Storage::disk('public')->exists($deletedFile)) {
                        Storage::disk('public')->delete($deletedFile);
                        return response()->json(['status' => true, 'path' => '']);
                    }
                }
                return response()->json(['status' => false, 'message' => 'File not deleted.']);
            } else {
                return response()->json(['status' => false, 'message' => 'Document update failed.']);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'File not found or already deleted.']);
        }
    }


    //For start proof reading page - SOURAV CHAUHAN (12/Dec/2024)
    public function startProofReading(Request $request, $id)
    {
        $encryptedModel = $request->type;
        $requestModel = base64_decode($request->type);
        $model = '\\App\\Models\\' . $requestModel;
        $applicationDetails = $model::find($id);
        if ($applicationDetails) {
            $applicationAppointmentLink = ApplicationAppointmentLink::where('application_no', $applicationDetails['application_no'])->latest()->first();
                $scheduleDate = Carbon::parse($applicationAppointmentLink->schedule_date);
                $endDate = $scheduleDate->copy()->addDays(10);
                // if (Carbon::today()->between($scheduleDate, $endDate)) {// commented by SOURAV CHAUHAN (07/May/2026) as per discussion with dhiraj sir
                if (Carbon::today()->gte($scheduleDate)) {

                    $applicationAppointmentLink->is_attended = 1;
                    if ($applicationAppointmentLink->save()) {

                        $application = Application::where('application_no', $applicationDetails['application_no'])->first();
                        if ($application) {
                            // dd($application->status,getServiceType('APP_HOLD'));
                            if ($application->status == getServiceType('APP_HOLD')) {
                                $application->status = getServiceType('APP_IP');
                                if ($application->save()) {
                                    $applicationDetails->status = getServiceType('APP_IP');
                                    if ($applicationDetails->save()) {

                                        $latestApplicationovement = ApplicationMovement::where('application_no', $applicationDetails['application_no'])->latest()->first();
                                        $applicationMovement = ApplicationMovement::create([
                                            'assigned_by' => Auth::user()->id,
                                            'assigned_by_role' => Auth::user()->roles[0]->id,
                                            'assigned_to' => $latestApplicationovement->assigned_to,
                                            'assigned_to_role' => $latestApplicationovement->assigned_to_role,
                                            'service_type' => $application->service_type, //for mutation,LUC,DOA etc
                                            'model_id' => $application->model_id,
                                            'status' => getServiceType('APP_IP'), //In progress status
                                            'action' => 'UNHOLD', //for nUn Hold Action
                                            'application_no' => $applicationDetails['application_no']
                                        ]);


                                        $appLatestAction = AppLatestAction::where('application_no', $applicationDetails['application_no'])->first();
                                        $latestAction = $appLatestAction->latest_action;
                                        $latestActionBy = $appLatestAction->latest_action_by;
                                        $appLatestAction->prev_action  = $latestAction;
                                        $appLatestAction->prev_action_by  = $latestActionBy;
                                        $appLatestAction->latest_action = 'UNHOLD';
                                        $appLatestAction->latest_action_by = Auth::user()->id;
                                        $appLatestAction->latest_role_id = Auth::user()->roles[0]->id;
                                        $appLatestAction->save();
                                    }
                                }
                            }
                        }

                        //For logs - SOURAV CHAUHAN (13/Dec/2024) 
                        $actionLink = url('edharti/applications/' . $id . '/start-proof-reading') . '?type=' . $request->type;
                        UserActionLogHelper::UserActionLog(
                            'Start Application Proof Reading',
                            $actionLink,
                            'adminApplication',
                            "Proof reading for application <a target='_blank' href='" . $actionLink . "'>" . $applicationDetails['application_no'] . "</a> has been started by user " . Auth::user()->name . "."
                        );

                        $data = [];
                        $data['application'] = $application;
                        switch ($requestModel) {
                            case 'MutationApplication':
                                $applicationType = 'Mutation';
                                $serviceType = getServiceType('SUB_MUT');
                                $documents = Document::where('service_type', $serviceType)->where('model_name', $requestModel)->where('model_id', $id)->get();
                                break;
                            case 'LandUseChangeApplication':
                                $applicationType = 'Land Use Change';
                                $serviceType = getServiceType('LUC');

                                $documentList = config('applicationDocumentType.LUC.documents');
                                $requiredDocuments = collect($documentList)->where('required', 1)->all();
                                $requiredDocumentTypes = array_map(function ($element) {
                                    return $element['label'];
                                }, $requiredDocuments);
                                $uploadedDocuments = Document::where('service_type', $serviceType)->where('model_name', $requestModel)->where('model_id', $id)->get();

                                $documents = [
                                    'required' => [],
                                    'optional' => [],
                                ];

                                // Required documents
                                foreach ($requiredDocumentTypes as $requiredDocument) {
                                    foreach ($uploadedDocuments as $uploadedDocument) {
                                        if ($requiredDocument == $uploadedDocument->title) {
                                            $documents['required'][] = [
                                                'title' => $uploadedDocument->title,
                                                'file_path' => $uploadedDocument->file_path
                                            ];
                                            break;
                                        }
                                    }
                                }

                                //optional documents
                                $optionalDocuments = collect($documentList)->where('required', 0)->all();
                                $optionalDocumentTypes = array_map(function ($element) {
                                    return $element['label'];
                                }, $optionalDocuments);

                                foreach ($optionalDocumentTypes as $optionalDocument) {
                                    $found = false;
                                    foreach ($uploadedDocuments as $uploadedDocument) {
                                        if ($optionalDocument == $uploadedDocument->title) {
                                            $documents['optional'][] = [
                                                'title' => $uploadedDocument->title,
                                                'file_path' => $uploadedDocument->file_path
                                            ];
                                            $found = true;
                                            break;
                                        }
                                    }
                                    if (!$found) {
                                        $documents['optional'][] = [
                                            'title' => $optionalDocument,
                                            'file_path' => null
                                        ];
                                    }
                                }
                                $data['documents'] = $documents;
                                break;
                            case 'DeedOfApartmentApplication':
                                $applicationType = 'Deed Of Apartment';
                                $serviceType = getServiceType('DOA');
                                $requiredDocuments = config('applicationDocumentType.DOA.documents');
                                $uploadedDocuments = Document::where('service_type', $serviceType)->where('model_name', $requestModel)->where('model_id', $id)->get();
                                $documents = [
                                    'required' => [],
                                ];
                                foreach ($requiredDocuments as $key => $requiredDocument) {
                                    foreach ($uploadedDocuments as $uploadedDocument) {
                                        // if ($key == $uploadedDocument->title) {
                                        if ($key == $uploadedDocument->document_type) {
                                            $documents['required'][] = [
                                                'id' => $uploadedDocument->id,
                                                'title' => $uploadedDocument->title,
                                                'file_path' => $uploadedDocument->file_path,
                                                'office_file_path' => $uploadedDocument->office_file_path,
                                            ];
                                            break;
                                        }
                                    }
                                }
                                $data['documents'] = $documents;
                                break;
                            case 'ConversionApplication':
                                $applicationType = 'Conversion';
                                $serviceType = getServiceType('CONVERSION');
                                $requiredDocuments = config('applicationDocumentType.CONVERSION.Required');
                                $uploadedDocuments = Document::where('service_type', $serviceType)->where('model_name', $requestModel)->where('model_id', $id)->get();
                                $documents = [
                                    'required' => [],
                                ];
                                foreach ($requiredDocuments as $key => $requiredDocument) {
                                    foreach ($uploadedDocuments as $uploadedDocument) {
                                        if ($key == $uploadedDocument->title) {
                                            $documents['required'][] = [
                                                'title' => $uploadedDocument->title,
                                                'file_path' => $uploadedDocument->file_path
                                            ];
                                            break;
                                        }
                                    }
                                }
                                $data['documents'] = $documents;
                                break;
                            default:
                                $applicationType = '';
                                break;
                        }

                        $data['applicationType'] = $applicationType;
                        $data['roles'] = Auth::user()->roles[0]->name;
                        $property = PropertyMaster::find($applicationDetails['property_master_id']);
                        $applicationDetails['serviceType'] = $serviceType;
                        $data['details'] = $applicationDetails;
                        $data['applicationMovementId'] = $id;
                        // Specify the column to sort by
                        $data['checkList'] = ApplicationStatus::where('service_type', $serviceType)->where('model_id', $id)->latest('created_at')->first();
                        $oldPropertyId = (string) $applicationDetails['old_property_id'];
                        $UserApplicationController = new UserApplicationController();
                        $data['propertyCommonDetails'] = $UserApplicationController->getPropertyCommonDetails($oldPropertyId);

                        $data['user'] = User::find($applicationDetails['created_by']);
                        $data['latestAppAction'] = AppLatestAction::where('application_no', $applicationDetails['application_no'])->first();
                        $data['encryptedModel'] = $encryptedModel;
                        return view('application.admin.proof_reading.index', $data);
                    } else {
                        return redirect()->back()->with('failure', 'Something went wrong during Proof reading.');
                    }
                } else {
                    return redirect()->back()->with('failure', 'Proof reading not scheduled for the application');
                }
        } else {
            return redirect()->back()->with('failure', 'Application Not Available');
        }
    }


    // For storing the details and documents uploadd by CDV - SOURAV CHAUHAN (16/Dec/2024)
    public function saveOfficialDocs(Request $request)
    {

        $additionalDocumentTitles = $request->additional_document_titles;
        $additionalDocuments = $request->additional_documents;

        $encryptedModel = $request->encryptedModel;
        $applicationId = $request->applicationId;
        $requestModel = base64_decode($encryptedModel);
        $model = '\\App\\Models\\' . $requestModel;
        $applicationDetails = $model::find($applicationId);
        $user_id = $applicationDetails->created_by;

        $application = Application::where('application_no', $applicationDetails->application_no)->first();

        $applicantNumber = ApplicantUserDetail::where('user_id', $user_id)->value('applicant_number');
        $colonyCode = $applicationDetails->propertyMaster->newColony->code;
        if ($additionalDocumentTitles[0]) {
            switch ($requestModel) {
                case 'MutationApplication':
                    $folder = 'mutation';
                    break;
                case 'ConversionApplication':
                    $folder = 'coversion';
                    break;
                case 'DeedOfApartmentApplication':
                    $folder = 'deed_of_apartment';
                    break;
            }
            // if($additionalDocumentTitles[0] && $additionalDocuments[0]){ // Getting error "Trying to access array offset on value of type null"
            //Update if condition by Lalit - 24/12/2024
            /*if (!empty($additionalDocumentTitles) && is_array($additionalDocumentTitles) && isset($additionalDocumentTitles[0]) && !empty($additionalDocuments) && is_array($additionalDocuments) && isset($additionalDocuments[0])) {
                foreach ($additionalDocumentTitles as $key => $additionalDocumentTitle) {
                    $additionDocPath = GeneralFunctions::uploadFile($additionalDocuments[$key], $applicantNumber . '/' . $colonyCode . '/' . $folder . '/' . $request->applicationNumber . '/additional_document', $additionalDocumentTitle);
                    Document::create([
                        'title' => $additionalDocumentTitle,
                        'office_file_path' => $additionDocPath,
                        'user_id' => $user_id,
                        'service_type' => $application->service_type,
                        'model_name' => $application->model_name,
                        'model_id' => $application->model_id,
                        'document_type' => 'AdditionalDocument'
                    ]);
                }
                return redirect()->to('applications/' . $applicationId . '?type=' . $encryptedModel)->with('success', 'Documents Uploaded Successfully');
            } else {
                return redirect()->back()->with('failure', 'Some file or title missing in additional documents.');
            }*/
            //Update Upload Additional Document Validation code - 11/02/2025
            if (!empty($additionalDocumentTitles) && is_array($additionalDocumentTitles) && !empty($additionalDocuments) && is_array($additionalDocuments)) {
                foreach ($additionalDocumentTitles as $key => $additionalDocumentTitle) {
                    // Ensure corresponding file exists at the same index
                    if (isset($additionalDocuments[$key])) {
                        $additionDocPath = GeneralFunctions::uploadFile(
                            $additionalDocuments[$key],
                            $applicantNumber . '/' . $colonyCode . '/' . $folder . '/' . $request->applicationNumber . '/additional_document',
                            $additionalDocumentTitle
                        );
                        Document::create([
                            'title' => $additionalDocumentTitle,
                            'office_file_path' => $additionDocPath,
                            'user_id' => $user_id,
                            'service_type' => $application->service_type,
                            'model_name' => $application->model_name,
                            'model_id' => $application->model_id,
                            'document_type' => 'AdditionalDocument'
                        ]);
                    }
                }

                return redirect()
                    ->route('applications.view', ['id' => $applicationId, 'type' => $encryptedModel])
                    ->with('success', 'Documents Uploaded Successfully');
            } else {
                return redirect()->back()->with('failure', 'Some file or title missing in additional documents.');
            }
        } else {
            // return redirect()->to('applications/' . $applicationId . '?type=' . $encryptedModel)->with('success', 'Documents Uploaded Successfully');
            return redirect()
                ->route('applications.view', ['id' => $applicationId, 'type' => $encryptedModel])
                ->with('success', 'Documents Uploaded Successfully');
        }
    }

    //for finaliaze LUC application 
    public function finalizeLUCApplication($lucApplication)
    {
        $propertyMasterId = $lucApplication->property_master_id;
        if ($propertyMasterId) {
            $masterProperty = PropertyMaster::find($propertyMasterId);
            if (!empty($masterProperty)) {
                PropertyMasterHistory::create([
                    'property_master_id' => $masterProperty->id,
                    'property_type' => $masterProperty->property_type,
                    'new_property_type' => $lucApplication->property_type_change_to,
                    'property_sub_type' => $masterProperty->property_sub_type,
                    'new_property_sub_type' => $lucApplication->property_subtype_change_to,
                    'updated_by' => Auth::id()
                ]);
                $masterProperty->update([
                    'property_type' => $lucApplication->property_type_change_to,
                    'property_sub_type' => $lucApplication->property_subtype_change_to,
                ]);
                PropertyLeaseDetail::where('property_master_id', $propertyMasterId)->update([
                    'is_land_use_changed' => 1,
                    'date_of_land_change' => date('Y-m-d'),
                    'property_type_at_present' => $lucApplication->property_type_change_to,
                    'property_sub_type_at_present' => $lucApplication->property_subtype_change_to,
                ]);
                return ['status' => true, 'message' => 'Application finalized successfully'];
            } else {
                return ['status' => false, 'message' => 'Application property not Found'];
            }
        } else {
            return ['status' => false, 'message' => 'Property Id not found'];
        }
    }

    /* public function finalizeConversionApplication($conversionApplication)
    {
        $user = User::find($conversionApplication['created_by']);
        $propertyMasterId = $conversionApplication->property_master_id;
        $splittedPropertyId = $conversionApplication->splited_property_detail_id;
        $oldPropertyId = $conversionApplication->old_property_id;

        if ($user && $propertyMasterId) {
            // Determine if we're working with Property Master or Splitted Property
            $isSplittedProperty = !empty($splittedPropertyId);

            if ($isSplittedProperty) {
                // Handle Splitted Property
                $splittedProperty = SplitedPropertyDetail::find($splittedPropertyId);

                if (empty($splittedProperty)) {
                    return ['status' => false, 'message' => 'Splitted property not Found'];
                }

                // Create Splitted Property History and update status
                SplitedPropertyDetailHistory::create([
                    'splited_property_detail_id' => $splittedProperty->id,
                    'property_status' => $splittedProperty->property_status,
                    'new_property_status' => getServiceType('FH'),
                    'updated_by' => Auth::id()
                ]);

                // Update property_status in splited_property_details
                $splittedProperty->update(['property_status' => getServiceType('FH')]);
            } else {
                // Handle Property Master
                $masterProperty = PropertyMaster::find($propertyMasterId);

                if (empty($masterProperty)) {
                    return ['status' => false, 'message' => 'Application property not Found'];
                }

                // Create Property Master History and update status to Free Hold
                PropertyMasterHistory::create([
                    'property_master_id' => $masterProperty->id,
                    'status' => $masterProperty->status,
                    'new_status' => getServiceType('FH'),
                    'updated_by' => Auth::id()
                ]);
                $masterProperty->update(['status' => getServiceType('FH')]);
            }

            // Determine which ID to use for fetching max batch_transfer_id
            if ($isSplittedProperty) {
                $isPrevBatchId = PropertyTransferredLesseeDetail::where('splited_property_detail_id', $splittedPropertyId)->max('batch_transfer_id');
            } else {
                $isPrevBatchId = PropertyTransferredLesseeDetail::where('property_master_id', $propertyMasterId)->max('batch_transfer_id');
            }

            if ($isPrevBatchId) {
                $batch_transfer_id = $isPrevBatchId + 1;
                $previous_batch_transfer_id = $isPrevBatchId;
            } else {
                $batch_transfer_id = 1;
                $previous_batch_transfer_id = null;
            }

            // Insert Applicant Details in PropertyTransferredLesseeDetail Table
            $transferData = [
                'property_master_id' => $propertyMasterId,
                'old_property_id' => $oldPropertyId,
                'lessee_name' => $user->name,
                'lessee_age' => $user->applicantUserDetails->age ?? null,
                'lessee_pan_no' => $user->applicantUserDetails->pan_card ?? null,
                'lessee_aadhar_no' => $user->applicantUserDetails->aadhar_card ?? null,
                'process_of_transfer' => 'Conversion',
                'transferDate' => date('Y-m-d'),
                'batch_transfer_id' => $batch_transfer_id,
                'previous_batch_transfer_id' => $previous_batch_transfer_id,
                'created_by' => Auth::id()
            ];

            // Add splited_property_detail_id if it exists
            if ($isSplittedProperty) {
                $transferData['splited_property_detail_id'] = $splittedPropertyId;
            }

            PropertyTransferredLesseeDetail::create($transferData);

            // Insert CoApplicant Details in PropertyTransferredLesseeDetail Table
            $coApplicants = CoApplicant::where('model_name', 'ConversionApplication')
                ->where('model_id', $conversionApplication->id)
                ->get();

            if ($coApplicants->isNotEmpty()) {
                foreach ($coApplicants as $coApplicant) {
                    $coTransferData = [
                        'property_master_id' => $propertyMasterId,
                        'old_property_id' => $oldPropertyId,
                        'lessee_name' => $coApplicant->co_applicant_name,
                        'lessee_age' => Carbon::parse($coApplicant->co_applicant_age)->diffInYears(Carbon::now()),
                        'lessee_pan_no' => $coApplicant->co_applicant_pan,
                        'lessee_aadhar_no' => $coApplicant->co_applicant_aadhar,
                        'process_of_transfer' => 'Conversion',
                        'transferDate' => date('Y-m-d'),
                        'batch_transfer_id' => $batch_transfer_id,
                        'previous_batch_transfer_id' => $previous_batch_transfer_id,
                        'created_by' => Auth::id()
                    ];

                    // Add splited_property_detail_id if it exists
                    if ($isSplittedProperty) {
                        $coTransferData['splited_property_detail_id'] = $splittedPropertyId;
                    }

                    PropertyTransferredLesseeDetail::create($coTransferData);
                }
            }

            // Fetch Details from Property Transferred Lessee Detail Table for Co-Applicant 
            // and insert in Current Lessee Details Table
            if ($isSplittedProperty) {
                $propertyTransferDetails = PropertyTransferredLesseeDetail::where('splited_property_detail_id', $splittedPropertyId)
                    ->where('batch_transfer_id', $batch_transfer_id)
                    ->get();
            } else {
                $propertyTransferDetails = PropertyTransferredLesseeDetail::where('property_master_id', $propertyMasterId)
                    ->where('batch_transfer_id', $batch_transfer_id)
                    ->get();
            }

            $leaseNames = [];

            if ($propertyTransferDetails->isNotEmpty()) {
                foreach ($propertyTransferDetails as $propertyTransferDetail) {
                    $leaseNames[] = $propertyTransferDetail->lessee_name;
                }
                $leaseNameString = implode(', ', $leaseNames);
            } else {
                $leaseNameString = '';
            }

            // Update Current Lessee Details Table
            if ($isSplittedProperty) {
                // Check if record exists for splitted property
                $existingRecord = CurrentLesseeDetail::where('splited_property_detail_id', $splittedPropertyId)->first();

                if ($existingRecord) {
                    $existingRecord->update([
                        'lessees_name' => $leaseNameString,
                        'property_status' => getServiceType('FH'),
                        'updated_by' => Auth::id()
                    ]);
                } else {
                    // Create new record for splitted property
                    CurrentLesseeDetail::create([
                        'property_master_id' => $propertyMasterId,
                        'splited_property_detail_id' => $splittedPropertyId,
                        'old_property_id' => $oldPropertyId,
                        'lessees_name' => $leaseNameString,
                        'property_status' => getServiceType('FH'),
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id()
                    ]);
                }
            } else {
                // Update for property master
                CurrentLesseeDetail::where('property_master_id', $propertyMasterId)->update([
                    'lessees_name' => $leaseNameString,
                    'property_status' => getServiceType('FH'),
                    'updated_by' => Auth::id()
                ]);
            }

            return ['status' => true, 'message' => 'Application finalized successfully'];
        } else {
            return ['status' => false, 'message' => 'Property Id not found'];
        }
    } */

        public function finalizeConversionApplication($conversionApplication)
    {
        $user = User::find($conversionApplication['created_by']);
        $propertyMasterId = $conversionApplication->property_master_id;
        $splittedPropertyId = $conversionApplication->splited_property_detail_id;
        $oldPropertyId = $conversionApplication->old_property_id;

        if ($user && $propertyMasterId) {
            // Determine if we're working with Property Master or Splitted Property
            $isSplittedProperty = !empty($splittedPropertyId);

            if ($isSplittedProperty) {
                // Handle Splitted Property
                $splittedProperty = SplitedPropertyDetail::find($splittedPropertyId);

                if (empty($splittedProperty)) {
                    return ['status' => false, 'message' => 'Splitted property not Found'];
                }

                // Create Splitted Property History and update status
                SplitedPropertyDetailHistory::create([
                    'splited_property_detail_id' => $splittedProperty->id,
                    'property_status' => $splittedProperty->property_status,
                    'new_property_status' => getServiceType('FH'),
                    'updated_by' => Auth::id()
                ]);

                // Update property_status in splited_property_details
                $splittedProperty->update(['property_status' => getServiceType('FH')]);
            } else {
                // Handle Property Master
                $masterProperty = PropertyMaster::find($propertyMasterId);

                if (empty($masterProperty)) {
                    return ['status' => false, 'message' => 'Application property not Found'];
                }

                // Create Property Master History and update status to Free Hold
                PropertyMasterHistory::create([
                    'property_master_id' => $masterProperty->id,
                    'status' => $masterProperty->status,
                    'new_status' => getServiceType('FH'),
                    'updated_by' => Auth::id()
                ]);
                $masterProperty->update(['status' => getServiceType('FH')]);
            }

            // Determine which ID to use for fetching max batch_transfer_id
            if ($isSplittedProperty) {
                $propertyDetailsFinal = SplitedPropertyDetail::where('id', $splittedPropertyId)->first();
                $presentlyKnownAs = $propertyDetailsFinal->presently_known_as;
                $area = $propertyDetailsFinal->current_area;
                $unit = $propertyDetailsFinal->unit;
                $areaInSqm = $propertyDetailsFinal->area_in_sqm;
                $propertyStatus = $propertyDetailsFinal->property_status;
                $isPrevBatchId = PropertyTransferredLesseeDetail::where('splited_property_detail_id', $splittedPropertyId)->max('batch_transfer_id');
            } else {
                $propertyDetailsFinal = PropertyMaster::where('id', $propertyMasterId)->first();
                $leaseDetails = PropertyLeaseDetail::where('property_master_id', $propertyMasterId)->first();
                $presentlyKnownAs = $leaseDetails->presently_known_as;
                $area = $leaseDetails->plot_area;
                $unit = $leaseDetails->unit;
                $areaInSqm = $leaseDetails->plot_area_in_sqm;
                $propertyStatus = $propertyDetailsFinal->status;
                $isPrevBatchId = PropertyTransferredLesseeDetail::where('property_master_id', $propertyMasterId)->max('batch_transfer_id');
                $isPrevBatchId = PropertyTransferredLesseeDetail::where('property_master_id', $propertyMasterId)->max('batch_transfer_id');
            }

            if ($isPrevBatchId) {
                $batch_transfer_id = $isPrevBatchId + 1;
                $previous_batch_transfer_id = $isPrevBatchId;
            } else {
                $batch_transfer_id = 1;
                $previous_batch_transfer_id = null;
            }

            // Insert Applicant Details in PropertyTransferredLesseeDetail Table
            $transferData = [
                'property_master_id' => $propertyMasterId,
                'old_property_id' => $oldPropertyId,
                'lessee_name' => $user->name,
                'lessee_age' => $user->applicantUserDetails->age ?? null,
                'property_share' => $applicantShare ?? null,
                'lessee_pan_no' => $user->applicantUserDetails->pan_card ?? null,
                'lessee_aadhar_no' => $user->applicantUserDetails->aadhar_card ?? null,
                'process_of_transfer' => 'Conversion',
                'transferDate' => date('Y-m-d'),
                'batch_transfer_id' => $batch_transfer_id,
                'previous_batch_transfer_id' => $previous_batch_transfer_id,
                'created_by' => Auth::id()
            ];

            // Add splited_property_detail_id if it exists
            if ($isSplittedProperty) {
                $transferData['splited_property_detail_id'] = $splittedPropertyId;
            }

            PropertyTransferredLesseeDetail::create($transferData);

            // Insert CoApplicant Details in PropertyTransferredLesseeDetail Table
            $coApplicants = CoApplicant::where('model_name', 'ConversionApplication')
                ->where('model_id', $conversionApplication->id)
                ->get();

            if ($coApplicants->isNotEmpty()) {
                foreach ($coApplicants as $coApplicant) {
                    $coTransferData = [
                        'property_master_id' => $propertyMasterId,
                        'old_property_id' => $oldPropertyId,
                        'lessee_name' => $coApplicant->co_applicant_name,
                        'lessee_age' => Carbon::parse($coApplicant->co_applicant_age)->diffInYears(Carbon::now()),
                        'property_share' => $coApplicant->share,
                        'lessee_pan_no' => $coApplicant->co_applicant_pan,
                        'lessee_aadhar_no' => $coApplicant->co_applicant_aadhar,
                        'process_of_transfer' => 'Conversion',
                        'transferDate' => date('Y-m-d'),
                        'batch_transfer_id' => $batch_transfer_id,
                        'previous_batch_transfer_id' => $previous_batch_transfer_id,
                        'created_by' => Auth::id()
                    ];

                    // Add splited_property_detail_id if it exists
                    if ($isSplittedProperty) {
                        $coTransferData['splited_property_detail_id'] = $splittedPropertyId;
                    }

                    PropertyTransferredLesseeDetail::create($coTransferData);
                }
            }

            // Fetch Details from Property Transferred Lessee Detail Table for Co-Applicant 
            // and insert in Current Lessee Details Table
            if ($isSplittedProperty) {
                $propertyTransferDetails = PropertyTransferredLesseeDetail::where('splited_property_detail_id', $splittedPropertyId)
                    ->where('batch_transfer_id', $batch_transfer_id)
                    ->get();
            } else {
                $propertyTransferDetails = PropertyTransferredLesseeDetail::where('property_master_id', $propertyMasterId)
                    ->where('batch_transfer_id', $batch_transfer_id)
                    ->get();
            }

            $leaseNames = [];

            if ($propertyTransferDetails->isNotEmpty()) {
                foreach ($propertyTransferDetails as $propertyTransferDetail) {
                    $leaseNames[] = $propertyTransferDetail->lessee_name;
                }
                $leaseNameString = implode(', ', $leaseNames);
            } else {
                $leaseNameString = '';
            }

            // Update Current Lessee Details Table
            if ($isSplittedProperty) {
                // Check if record exists for splitted property
                $existingRecord = CurrentLesseeDetail::where('splited_property_detail_id', $splittedPropertyId)->first();

                if ($existingRecord) {
                    $existingRecord->update([
                        'lessees_name' => $leaseNameString,
                        'property_status' => getServiceType('FH'),
                        'updated_by' => Auth::id()
                    ]);
                } else {
                    // Create new record for splitted property
                    CurrentLesseeDetail::create([
                        'property_master_id' => $propertyMasterId,
                        'splited_property_detail_id' => $splittedPropertyId,
                        'old_property_id' => $oldPropertyId,
                        'lessees_name' => $leaseNameString,
                        'property_known_as' => $presentlyKnownAs,
                        'area' => $area,
                        'unit' => $unit,
                        'area_in_sqm' => $areaInSqm,
                        'property_status' => getServiceType('FH'),
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id()
                    ]);
                }
            } else {
                // Update for property master
                CurrentLesseeDetail::where('property_master_id', $propertyMasterId)->update([
                    'lessees_name' => $leaseNameString,
                    'property_status' => getServiceType('FH'),
                    'updated_by' => Auth::id()
                ]);
            }

            return ['status' => true, 'message' => 'Application finalized successfully'];
        } else {
            return ['status' => false, 'message' => 'Property Id not found'];
        }
    }

    public function finalizeMutationApplication($mutationApplication)
    {
        $user = User::find($mutationApplication['created_by']);
        $propertyMasterId = $mutationApplication->property_master_id;
        $splittedPropertyId = $mutationApplication->splitted_id;
        $oldPropertyId = $mutationApplication->old_property_id;

        if ($user && $propertyMasterId) {
            // Determine if we're working with Property Master or Splitted Property
            $isSplittedProperty = !empty($splittedPropertyId);
            // Determine which ID to use for fetching max batch_transfer_id
            if ($isSplittedProperty) {
                $propertyDetailsFinal = SplitedPropertyDetail::where('id', $splittedPropertyId)->first();
                $presentlyKnownAs = $propertyDetailsFinal->presently_known_as;
                $area = $propertyDetailsFinal->current_area;
                $unit = $propertyDetailsFinal->unit;
                $areaInSqm = $propertyDetailsFinal->area_in_sqm;
                $propertyStatus = $propertyDetailsFinal->property_status;
                $isPrevBatchId = PropertyTransferredLesseeDetail::where('splited_property_detail_id', $splittedPropertyId)->max('batch_transfer_id');
            } else {
                $propertyDetailsFinal = PropertyMaster::where('id', $propertyMasterId)->first();
                $leaseDetails = PropertyLeaseDetail::where('property_master_id', $propertyMasterId)->first();
                $presentlyKnownAs = $leaseDetails->presently_known_as;
                $area = $leaseDetails->plot_area;
                $unit = $leaseDetails->unit;
                $areaInSqm = $leaseDetails->plot_area_in_sqm;
                $propertyStatus = $propertyDetailsFinal->status;
                $isPrevBatchId = PropertyTransferredLesseeDetail::where('property_master_id', $propertyMasterId)->max('batch_transfer_id');
            }



            if ($isPrevBatchId) {
                $batch_transfer_id = $isPrevBatchId + 1;
                $previous_batch_transfer_id = $isPrevBatchId;
            } else {
                $batch_transfer_id = 1;
                $previous_batch_transfer_id = null;
            }

            // Insert Applicant Details in PropertyTransferredLesseeDetail Table
            $applicantShare = ApplicantShare::where('application_no', $mutationApplication->application_no)->value('share');
            $transferData = [
                'property_master_id' => $propertyMasterId,
                'old_property_id' => $oldPropertyId,
                'lessee_name' => $user->name,
                'lessee_age' => $user->applicantUserDetails->age ?? null,
                'property_share' => $applicantShare ?? null,
                'lessee_pan_no' => $user->applicantUserDetails->pan_card ?? null,
                'lessee_aadhar_no' => $user->applicantUserDetails->aadhar_card ?? null,
                'process_of_transfer' => 'Mutation',
                'transferDate' => date('Y-m-d'),
                'batch_transfer_id' => $batch_transfer_id,
                'previous_batch_transfer_id' => $previous_batch_transfer_id,
                'created_by' => Auth::id()
            ];

            // Add splited_property_detail_id if it exists
            if ($isSplittedProperty) {
                $transferData['splited_property_detail_id'] = $splittedPropertyId;
            }

            PropertyTransferredLesseeDetail::create($transferData);

            // Insert CoApplicant Details in PropertyTransferredLesseeDetail Table
            $coApplicants = CoApplicant::where('model_name', 'MutationApplication')
                ->where('model_id', $mutationApplication->id)
                ->get();

            if ($coApplicants->isNotEmpty()) {
                foreach ($coApplicants as $coApplicant) {
                    $coTransferData = [
                        'property_master_id' => $propertyMasterId,
                        'old_property_id' => $oldPropertyId,
                        'lessee_name' => $coApplicant->co_applicant_name,
                        'lessee_age' => Carbon::parse($coApplicant->co_applicant_age)->diffInYears(Carbon::now()),
                        'property_share' => $coApplicant->share,
                        'lessee_pan_no' => $coApplicant->co_applicant_pan,
                        'lessee_aadhar_no' => $coApplicant->co_applicant_aadhar,
                        'process_of_transfer' => 'Mutation',
                        'transferDate' => date('Y-m-d'),
                        'batch_transfer_id' => $batch_transfer_id,
                        'previous_batch_transfer_id' => $previous_batch_transfer_id,
                        'created_by' => Auth::id()
                    ];

                    // Add splited_property_detail_id if it exists
                    if ($isSplittedProperty) {
                        $coTransferData['splited_property_detail_id'] = $splittedPropertyId;
                    }

                    PropertyTransferredLesseeDetail::create($coTransferData);
                }
            }


            // Extra Applicants START (SOURAV CHAUHAN - 16 july 2026) *******************************
            $extraApplicants = ApplicationExtraApplicant::where(
                'application_no',
                $mutationApplication->application_no
            )->get();

            foreach ($extraApplicants as $extraApplicant) {

                $extraTransferData = [
                    'property_master_id' => $propertyMasterId,
                    'old_property_id' => $oldPropertyId,
                    'lessee_name' => $extraApplicant->name,
                    'lessee_age' => $extraApplicant->age,
                    'property_share' => $extraApplicant->share,
                    'process_of_transfer' => 'Mutation',
                    'transferDate' => date('Y-m-d'),
                    'batch_transfer_id' => $batch_transfer_id,
                    'previous_batch_transfer_id' => $previous_batch_transfer_id,
                    'created_by' => Auth::id(),
                ];

                if ($isSplittedProperty) {
                    $extraTransferData['splited_property_detail_id'] = $splittedPropertyId;
                }

                PropertyTransferredLesseeDetail::create($extraTransferData);
            }
            // Extra Applicants END *******************************



            // Fetch Details from Property Transferred Lessee Detail Table for Co-Applicant 
            // and insert in Current Lessee Details Table
            if ($isSplittedProperty) {
                $propertyTransferDetails = PropertyTransferredLesseeDetail::where('splited_property_detail_id', $splittedPropertyId)
                    ->where('batch_transfer_id', $batch_transfer_id)
                    ->get();
            } else {
                $propertyTransferDetails = PropertyTransferredLesseeDetail::where('property_master_id', $propertyMasterId)
                    ->where('batch_transfer_id', $batch_transfer_id)
                    ->get();
            }

            $leaseNames = [];

            if ($propertyTransferDetails->isNotEmpty()) {
                foreach ($propertyTransferDetails as $propertyTransferDetail) {
                    $leaseNames[] = $propertyTransferDetail->lessee_name;
                }
                $leaseNameString = implode(', ', $leaseNames);
            } else {
                $leaseNameString = '';
            }

            // Update Current Lessee Details Table
            if ($isSplittedProperty) {
                // Check if record exists for splitted property
                $existingRecord = CurrentLesseeDetail::where('splited_property_detail_id', $splittedPropertyId)->first();

                if ($existingRecord) {
                    $existingRecord->update([
                        'lessees_name' => $leaseNameString,
                        'updated_by' => Auth::id()
                    ]);
                } else {
                    // Create new record for splitted property
                    CurrentLesseeDetail::create([
                        'property_master_id' => $propertyMasterId,
                        'splited_property_detail_id' => $splittedPropertyId,
                        'old_property_id' => $oldPropertyId,
                        'property_status' => $propertyStatus,
                        'lessees_name' => $leaseNameString,
                        'property_known_as' => $presentlyKnownAs,
                        'area' => $area,
                        'unit' => $unit,
                        'area_in_sqm' => $areaInSqm,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id()
                    ]);
                }
            } else {
                // Update for property master
                CurrentLesseeDetail::where('property_master_id', $propertyMasterId)->update([
                    'lessees_name' => $leaseNameString,
                    'updated_by' => Auth::id()
                ]);
            }

            return ['status' => true, 'message' => 'Application finalized successfully'];
        } else {
            return ['status' => false, 'message' => 'Property Id not found'];
        }
    }


    // Get application object latest remark - SOURAV CHAUHAN (30 Jan 2024)
    public function applicationsObjectRemark(Request $request)
    {
        // dd($request->all());
        $applicationNo = $request->applicationNo;
        $lastObjectedMovement = ApplicationMovement::where('application_no', $applicationNo)->where('action', 'OBJECT')->latest()->first();
        $data = [];
        if ($lastObjectedMovement) {
            if ($lastObjectedMovement->remarks) {
                $data['remark'] = $lastObjectedMovement->remarks;
            } else {
                $data['remark'] = '';
            }
            $response = ["status" => true, "data" => $data];
        } else {
            $response = ["status" => false, "data" => $data];
        }
        // dd($response);
        // $response = ['status' => true, 'message' => 'File movement fetched', 'data' => $fileMovement,'applicationType' => $applicationType,'presentlyKnownAs'=> $presentlyKnownAs];
        return response()->json($response);
    }

    //Get all disposed (Approved/Reject) applications - Lalit Tiwari (27/Feb/2025)
    public function applicationsDisposed(Request $request)
    {
       $demandType = '';
        $getStatusId = '';
        $applicationType = '';
        $getApplicationTypeId = '';
        $items = getApplicationStatusList(true, false);
        if ($request->query('filterByApplication')) {
            /** code modified by Nitin to add desposed satatus in the list */
            $appTypeItems = getApplicationTypeList();
            $getApplicationTypeId = $appTypeItems->where('item_code', trim(Crypt::decrypt($request->query('filterByApplication'))))->value('id'); // trim added by Nitin to remove extra space and lines from descrypted string - on 04-2024
        }
        // dd(trim(Crypt::decrypt($request->query('status'))));
        if ($request->query('status')) {
            // dd($items);
             $items = getApplicationStatusList(true, false);
            $getStatusId = $items->where('item_code', trim(Crypt::decrypt($request->query('status'))))->value('id'); // trim added by Nitin to remove extra space and lines from descrypted string - on 04-2024
        }
        if ($request->query('applicationType')) {
            $applicationType = $request->query('applicationType');
        }
       if ($request->query('demandType')) {
            $demandType = $request->query('demandType');
        }
        $user = Auth::user();
        $appTypeItems = getApplicationTypeList();
        return view('admin.applications.disposed', compact('items', 'getStatusId', 'user', 'applicationType', 'appTypeItems', 'getApplicationTypeId', 'demandType'));
    }

    public function getApplicationsDisposed(Request $request)
    {
        $applicationType = $request->input('applicationType');
        $itemsIdArr = [];
        $items = getApplicationStatusList(true, false);
        if (count($items) > 0) {
            foreach ($items as $key => $item) {
                $itemsIdArr[] = $item->id;
            }
        }
        if ($request->filterByApplication) {
            $applicationItemCode = getServiceCodeById($request->filterByApplication);
            if (!empty($applicationItemCode) && $applicationItemCode == 'SUB_MUT') {
                $applicationType = 'mutation';
            } else if (!empty($applicationItemCode) && $applicationItemCode == 'NOC') {
                $applicationType = 'noc';
            } else if (!empty($applicationItemCode) && $applicationItemCode == 'LUC') {
                $applicationType = 'luc';
            } else if (!empty($applicationItemCode) && $applicationItemCode == 'DOA') {
                $applicationType = 'doa';
            } else if (!empty($applicationItemCode) && $applicationItemCode == 'CONVERSION') {
                $applicationType = 'conversion';
            }
        }
        $user = Auth::user();
        $sections = $user->sections->pluck('id');
        $columns = [
            'id', // index 0
            'application_no', // index 1
            'old_property_id', // index 2
            'new_colony_name', // index 3
            'block_no', // index 4
            'plot_or_property_no', // index 5
            'flat_id', // index 6
            'presently_known_as', // index 7
            'section_code', // index 8
            'model_name', // index 9
            '', // index 10
            '', // index 11
            'created_at', // index 12
        ];
        switch ($applicationType) {
            case 'mutation':
                $serviceType1 = getServiceType('SUB_MUT'); // Ensure this function is defined and works properly.

                $query1 = DB::table('mutation_applications as ma')
                    ->where('ma.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
                    ->leftJoin('property_masters as pm', 'ma.property_master_id', '=', 'pm.id')
                    ->join('property_section_mappings as psm', function ($join) {
                        $join->on('pm.new_colony_name', '=', 'psm.colony_id')
                            ->whereColumn('pm.property_type', 'psm.property_type')
                            ->whereColumn('pm.property_sub_type', 'psm.property_subtype');
                    })
                    ->join('sections', 'psm.section_id', '=', 'sections.id')
                    ->leftJoin('old_colonies as oc', 'pm.new_colony_name', '=', 'oc.id')
                    ->leftJoin('property_lease_details as pld', 'pm.id', '=', 'pld.property_master_id')
                    ->leftJoin('applications as app', 'ma.application_no', '=', 'app.application_no')
                    ->leftJoinSub(
                        DB::table('application_statuses')
                            ->select(
                                'id',
                                'model_id',
                                'reg_app_no',
                                'service_type',
                                'is_mis_checked',
                                'is_scan_file_checked',
                                'is_uploaded_doc_checked',
                                'mis_checked_by',
                                'scan_file_checked_by',
                                'uploaded_doc_checked_by',
                                'created_at',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                            )
                            ->where('service_type', $serviceType1),
                        'latest_statuses',
                        function ($join) {
                            $join->on('ma.id', '=', 'latest_statuses.model_id')
                                ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                        }
                    )
                    ->whereIn('ma.section_id', $sections) // Verify $sections is an array
                    ->select(
                        'ma.id',
                        'ma.created_at',
                        'ma.application_no',
                        'ma.status',
                        'sections.section_code',
                        'latest_statuses.is_mis_checked',
                        'latest_statuses.is_scan_file_checked',
                        'latest_statuses.is_uploaded_doc_checked',
                        'latest_statuses.mis_checked_by',
                        'latest_statuses.scan_file_checked_by',
                        'latest_statuses.uploaded_doc_checked_by',
                        'pm.old_propert_id as old_property_id', // Fixed alias
                        'pm.new_colony_name',
                        'oc.name as colony_name',
                        'pm.block_no',
                        'pm.plot_or_property_no',
                        'pld.presently_known_as',
                        'app.is_objected',
                        DB::raw('NULL as flat_id'), // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        DB::raw('NULL as flat_number'), // Add NULL for flat_number on dated 07/01/25 By Lalit Tiwari
                        DB::raw("'MutationApplication' as model_name") // Add model_name for the first query
                    );
                if ($request->status) {
                    $query1 = $query1->where('ma.status', ($request->status));
                } else {
                    $query1 = $query1->whereIn('ma.status', ($itemsIdArr));
                }

                // Add search filter if search.value is present
                if ($request->input('search.value')) {
                    $searchValue = $request->input('search.value');
                    $query1->where(function ($query) use ($searchValue) {
                        $query->where('ma.application_no', 'like', "%$searchValue%")
                            ->orWhere('pm.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                            ->orWhere(DB::raw('LOWER(oc.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                            ->orWhere('pm.block_no', 'like', "%$searchValue%")  // Correctly reference block
                            ->orWhere('pm.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhere('pld.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhere(DB::raw("'MutationApplication'"), 'like', "%$searchValue%") // Search by model_name
                            ->orWhere('ma.created_at', 'like', "%$searchValue%");
                    });
                }

                $clonedQuery1 = (clone $query1);
                $combinedQuery = $clonedQuery1;
                break;
            case 'luc':


                // Query for land use changed applications
                $serviceType2 = getServiceType('LUC');
                $query2 = DB::table('land_use_change_applications as lca')
                    ->where('lca.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
                    ->leftJoin('property_masters', 'lca.property_master_id', '=', 'property_masters.id')
                    ->join('property_section_mappings as psm', function ($join) {
                        $join->on('property_masters.new_colony_name', 'psm.colony_id');
                        $join->whereColumn('property_masters.property_type', 'psm.property_type');
                        $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
                    })
                    ->join('sections', 'psm.section_id', 'sections.id')
                    ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
                    ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
                    ->leftJoin('applications as app', 'lca.application_no', '=', 'app.application_no')
                    ->leftJoinSub(
                        DB::table('application_statuses')
                            ->select(
                                'id',
                                'model_id',
                                'reg_app_no',
                                'service_type',
                                'is_mis_checked',
                                'is_scan_file_checked',
                                'is_uploaded_doc_checked',
                                'mis_checked_by',
                                'scan_file_checked_by',
                                'uploaded_doc_checked_by',
                                'created_at',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                            )
                            ->where('service_type', $serviceType2),
                        'latest_statuses',
                        function ($join) {
                            $join->on('lca.id', '=', 'latest_statuses.model_id')
                                ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                        }
                    )
                    ->whereIn('lca.section_id', $sections) //need to add secton id in all queries
                    ->select(
                        'lca.id',
                        'lca.created_at',
                        DB::raw('coalesce(lca.application_no,"0") as application_no'),
                        'lca.status',
                        'sections.section_code',
                        'latest_statuses.is_mis_checked',
                        'latest_statuses.is_scan_file_checked',
                        'latest_statuses.is_uploaded_doc_checked',
                        'latest_statuses.mis_checked_by',
                        'latest_statuses.scan_file_checked_by',
                        'latest_statuses.uploaded_doc_checked_by',
                        'property_masters.old_propert_id as old_property_id',
                        'property_masters.new_colony_name',
                        'old_colonies.name as colony_name',
                        'property_masters.block_no',
                        'property_masters.plot_or_property_no',
                        'property_lease_details.presently_known_as',
                        'app.is_objected',
                        DB::raw('NULL as flat_id'), // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        DB::raw('NULL as flat_number'), // Add NULL for flat_number on dated 07/01/25 By Lalit Tiwari
                        DB::raw("'LandUseChangeApplication' as model_name") // Add model_name for the first query
                    );

                if ($request->status) {
                    $query2 = $query2->where('lca.status', ($request->status));
                } else {
                    $query2 = $query2->whereIn('lca.status', ($itemsIdArr));
                }

                // Add search filter if search.value is present
                if ($request->input('search.value')) {
                    $searchValue = $request->input('search.value');
                    $query2->where(function ($query) use ($searchValue) {
                        $query->where('lca.application_no', 'like', "%$searchValue%")
                            ->orWhere('property_masters.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                            ->orWhere(DB::raw('LOWER(old_colonies.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                            ->orWhere('property_masters.block_no', 'like', "%$searchValue%")  // Correctly reference block
                            ->orWhere('property_masters.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhere('property_lease_details.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhere(DB::raw("'LandUseChangeApplication'"), 'like', "%$searchValue%") // Search by model_name
                            ->orWhere('lca.created_at', 'like', "%$searchValue%");
                    });
                }
                $clonedQuery2 = (clone $query2);
                $combinedQuery = $clonedQuery2;
                break;
            case 'doa':
                //Query for Deed Of Apartment applications
                $serviceType3 = getServiceType('DOA');
                $query3 = DB::table('deed_of_apartment_applications as doa')
                    ->where('doa.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
                    ->leftJoin('property_masters', 'doa.property_master_id', '=', 'property_masters.id')->join('property_section_mappings as psm', function ($join) {
                        $join->on('property_masters.new_colony_name', 'psm.colony_id');
                        $join->whereColumn('property_masters.property_type', 'psm.property_type');
                        $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
                    })
                    ->join('sections', 'psm.section_id', 'sections.id')
                    ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
                    ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
                    ->leftJoin('flats', 'doa.flat_id', '=', 'flats.id')
                    ->leftJoin('applications as app', 'doa.application_no', '=', 'app.application_no')
                    ->leftJoinSub(
                        DB::table('application_statuses')
                            ->select(
                                'id',
                                'model_id',
                                'reg_app_no',
                                'service_type',
                                'is_mis_checked',
                                'is_scan_file_checked',
                                'is_uploaded_doc_checked',
                                'mis_checked_by',
                                'scan_file_checked_by',
                                'uploaded_doc_checked_by',
                                'created_at',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                            )
                            ->where('service_type', $serviceType3),
                        'latest_statuses',
                        function ($join) {
                            $join->on('doa.id', '=', 'latest_statuses.model_id')
                                ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                        }
                    )
                    ->whereIn('doa.section_id', $sections) //need to add secton id in all queries
                    ->select(
                        'doa.id',
                        'doa.created_at',
                        'doa.application_no',
                        'doa.status',
                        'sections.section_code',
                        'latest_statuses.is_mis_checked',
                        'latest_statuses.is_scan_file_checked',
                        'latest_statuses.is_uploaded_doc_checked',
                        'latest_statuses.mis_checked_by',
                        'latest_statuses.scan_file_checked_by',
                        'latest_statuses.uploaded_doc_checked_by',
                        'property_masters.old_propert_id as old_property_id',
                        'property_masters.new_colony_name',
                        'old_colonies.name as colony_name',
                        'property_masters.block_no',
                        'property_masters.plot_or_property_no',
                        'property_lease_details.presently_known_as',
                        'flats.unique_flat_id as flat_id', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        'flats.flat_number as flat_number', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        'app.is_objected',
                        DB::raw("'DeedOfApartmentApplication' as model_name") // Add model_name for the first query
                    );
                if ($request->status) {
                    $query3 = $query3->where('doa.status', ($request->status));
                } else {
                    $query3 = $query3->whereIn('doa.status', ($itemsIdArr));
                }

                // Add search filter if search.value is present
                if ($request->input('search.value')) {
                    $searchValue = $request->input('search.value');
                    $query3->where(function ($query) use ($searchValue) {
                        $query->where('doa.application_no', 'like', "%$searchValue%")
                            ->orWhere('property_masters.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                            ->orWhere(DB::raw('LOWER(old_colonies.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                            ->orWhere('property_masters.block_no', 'like', "%$searchValue%")  // Correctly reference block
                            ->orWhere('property_masters.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere('flats.unique_flat_id', 'like', "%$searchValue%")  // Search by flat_id
                            ->orWhere('flats.flat_number', 'like', "%$searchValue%")    // Search by flat_number
                            ->orWhere('property_lease_details.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhere(DB::raw("'DeedOfApartmentApplication'"), 'like', "%$searchValue%") // Search by model_name
                            ->orWhere('doa.created_at', 'like', "%$searchValue%");
                    });
                }



                $clonedQuery3 = (clone $query3);
                $combinedQuery = $clonedQuery3;
                break;
            case 'conversion':
                //Query for Conversion applications added by Nitin
                $serviceType4 = getServiceType('CONVERSION');
                $query4 = DB::table('conversion_applications as ca')
                    ->where('ca.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
                    ->leftJoin('property_masters', 'ca.property_master_id', '=', 'property_masters.id')->join('property_section_mappings as psm', function ($join) {
                        $join->on('property_masters.new_colony_name', 'psm.colony_id');
                        $join->whereColumn('property_masters.property_type', 'psm.property_type');
                        $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
                    })
                    ->join('sections', 'psm.section_id', 'sections.id')
                    ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
                    ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
                    ->leftJoin('applications as app', 'ca.application_no', '=', 'app.application_no')
                    ->leftJoinSub(
                        DB::table('application_statuses')
                            ->select(
                                'id',
                                'model_id',
                                'reg_app_no',
                                'service_type',
                                'is_mis_checked',
                                'is_scan_file_checked',
                                'is_uploaded_doc_checked',
                                'mis_checked_by',
                                'scan_file_checked_by',
                                'uploaded_doc_checked_by',
                                'created_at',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                            )
                            ->where('service_type', $serviceType4),
                        'latest_statuses',
                        function ($join) {
                            $join->on('ca.id', '=', 'latest_statuses.model_id')
                                ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                        }
                    )
                    ->whereIn('ca.section_id', $sections) //need to add secton id in all queries
                    ->select(
                        'ca.id',
                        'ca.created_at',
                        'ca.application_no',
                        'ca.status',
                        'sections.section_code',
                        'latest_statuses.is_mis_checked',
                        'latest_statuses.is_scan_file_checked',
                        'latest_statuses.is_uploaded_doc_checked',
                        'latest_statuses.mis_checked_by',
                        'latest_statuses.scan_file_checked_by',
                        'latest_statuses.uploaded_doc_checked_by',
                        'property_masters.old_propert_id as old_property_id',
                        'property_masters.new_colony_name',
                        'old_colonies.name as colony_name',
                        'property_masters.block_no',
                        'property_masters.plot_or_property_no',
                        'property_lease_details.presently_known_as',
                        'app.is_objected',
                        DB::raw('NULL as flat_id'), // Add NULL for flat_id
                        DB::raw('NULL as flat_number'), // Add NULL for flat_number on dated 07/01/25 By Lalit Tiwari
                        DB::raw("'ConversionApplication' as model_name") // Add model_name for the first query
                    );

                if ($request->status) {
                    $query4 = $query4->where('ca.status', ($request->status));
                } else {
                    $query4 = $query4->whereIn('ca.status', ($itemsIdArr));
                }

                // Add search filter if search.value is present
                if ($request->input('search.value')) {
                    $searchValue = $request->input('search.value');
                    $query4->where(function ($query) use ($searchValue) {
                        $query->where('ca.application_no', 'like', "%$searchValue%")
                            ->orWhere('property_masters.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                            ->orWhere(DB::raw('LOWER(old_colonies.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                            ->orWhere('property_masters.block_no', 'like', "%$searchValue%")  // Correctly reference block
                            ->orWhere('property_masters.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhere('property_lease_details.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhere(DB::raw("'ConversionApplication'"), 'like', "%$searchValue%") // Search by model_name
                            ->orWhere('ca.created_at', 'like', "%$searchValue%");
                    });
                }
                $clonedQuery4 = (clone $query4);
                $combinedQuery = $clonedQuery4;
                break;
            case 'noc':
                //Add Noc Query to Display Noc Applicaiton in disposed listing - Lalit Tiwari (02/04/2025)
                $serviceType5 = getServiceType('NOC'); // Ensure this function is defined and works properly.
                $query5 = DB::table('noc_applications as noc')
                    ->where('noc.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
                    ->leftJoin('property_masters as pm', 'noc.property_master_id', '=', 'pm.id')
                    ->join('property_section_mappings as psm', function ($join) {
                        $join->on('pm.new_colony_name', '=', 'psm.colony_id')
                            ->whereColumn('pm.property_type', 'psm.property_type')
                            ->whereColumn('pm.property_sub_type', 'psm.property_subtype');
                    })
                    ->join('sections', 'psm.section_id', '=', 'sections.id')
                    ->leftJoin('flats', 'noc.flat_id', '=', 'flats.id')
                    ->leftJoin('old_colonies as oc', 'pm.new_colony_name', '=', 'oc.id')
                    ->leftJoin('property_lease_details as pld', 'pm.id', '=', 'pld.property_master_id')
                    ->leftJoin('applications as app', 'noc.application_no', '=', 'app.application_no')
                    ->leftJoinSub(
                        DB::table('application_statuses')
                            ->select(
                                'id',
                                'model_id',
                                'reg_app_no',
                                'service_type',
                                'is_mis_checked',
                                'is_scan_file_checked',
                                'is_uploaded_doc_checked',
                                'mis_checked_by',
                                'scan_file_checked_by',
                                'uploaded_doc_checked_by',
                                'created_at',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                            )
                            ->where('service_type', $serviceType5),
                        'latest_statuses',
                        function ($join) {
                            $join->on('noc.id', '=', 'latest_statuses.model_id')
                                ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                        }
                    )
                    ->whereIn('noc.section_id', $sections) // Verify $sections is an array
                    ->select(
                        'noc.id',
                        'noc.created_at',
                        'noc.application_no',
                        'noc.status',
                        'sections.section_code',
                        'latest_statuses.is_mis_checked',
                        'latest_statuses.is_scan_file_checked',
                        'latest_statuses.is_uploaded_doc_checked',
                        'latest_statuses.mis_checked_by',
                        'latest_statuses.scan_file_checked_by',
                        'latest_statuses.uploaded_doc_checked_by',
                        'pm.old_propert_id as old_property_id', // Fixed alias
                        'pm.new_colony_name',
                        'oc.name as colony_name',
                        'pm.block_no',
                        'pm.plot_or_property_no',
                        'pld.presently_known_as',
                        'app.is_objected',
                        'flats.unique_flat_id as flat_id', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        'flats.flat_number as flat_number', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        DB::raw("'NocApplication' as model_name") // Add model_name for the first query
                    );
                    $dateStart = '2006-02-14';  
           		$dateEnd = '2017-05-01';
					$query5->leftJoin(DB::raw("
				    (
				        SELECT ptld1.*
				        FROM property_transferred_lessee_details ptld1
				        INNER JOIN (
				            SELECT MAX(id) as max_id
				            FROM property_transferred_lessee_details
				            WHERE process_of_transfer = 'Conversion'
				            AND transferDate BETWEEN '$dateStart' AND '$dateEnd'
				            GROUP BY property_master_id
				        ) ptld2 ON ptld1.id = ptld2.max_id
				    ) as pld3
				"), 'pm.id', '=', 'pld3.property_master_id');
                if ($request->status) {
                    $query5 = $query5->where('noc.status', ($request->status));
                } else {
                    $query5 = $query5->whereIn('noc.status', ($itemsIdArr));
                }
                if ($request->demandType) {

				    $query5->where(function ($query) use ($request) {

				        if ($request->demandType == 'with_demand') {
				            $query->whereNotNull('pld3.id');
				        }

				        if ($request->demandType == 'without_demand') {
				            $query->whereNull('pld3.id');
				        }

				    });
				}

                // if ($request->status) {
                //     $query5 = $query5->where('noc.status', ($request->status));
                // } else {
                //     $query5 = $query5->whereIn('noc.status', ($itemsIdArr));
                // }
            //    if ($request->demandType) {
            //             $dateStart = '2006-02-14';
            //             $dateEnd   = '2017-05-01';

            //             $query5->where(function ($query) use ($request, $dateStart, $dateEnd) {
            //                 if ($request->demandType == 'with_demand') {
            //                     $query->whereBetween('pld.doe', [$dateStart, $dateEnd])
            //                         ->orWhereNull('pld.doe');
            //                 }

            //                 if ($request->demandType == 'without_demand') {
            //                     $query->whereNotBetween('pld.doe', [$dateStart, $dateEnd])
            //                         ->whereNotNull('pld.doe');
            //                 }
            //             });
            //         } 

                // Add search filter if search.value is present
                if ($request->input('search.value')) {
                    $searchValue = $request->input('search.value');
                    $query5->where(function ($query) use ($searchValue) {
                        $query->where('noc.application_no', 'like', "%$searchValue%")
                            ->orWhere('pm.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                            ->orWhere(DB::raw('LOWER(oc.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                            ->orWhere('pm.block_no', 'like', "%$searchValue%")  // Correctly reference block
                            ->orWhere('pm.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere('flats.unique_flat_id', 'like', "%$searchValue%")  // Search by flat_id
                            ->orWhere('flats.flat_number', 'like', "%$searchValue%")    // Search by flat_number
                            ->orWhere('pld.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhere(DB::raw("'MutationApplication'"), 'like', "%$searchValue%") // Search by model_name
                            ->orWhere('noc.created_at', 'like', "%$searchValue%");
                    });
                }

                $clonedQuery5 = (clone $query5);
                $combinedQuery = $clonedQuery5;
                break;
            default:
                $serviceType1 = getServiceType('SUB_MUT'); // Ensure this function is defined and works properly.

                $query1 = DB::table('mutation_applications as ma')
                    ->where('ma.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
                    ->leftJoin('property_masters as pm', 'ma.property_master_id', '=', 'pm.id')
                    ->join('property_section_mappings as psm', function ($join) {
                        $join->on('pm.new_colony_name', '=', 'psm.colony_id')
                            ->whereColumn('pm.property_type', 'psm.property_type')
                            ->whereColumn('pm.property_sub_type', 'psm.property_subtype');
                    })
                    ->join('sections', 'psm.section_id', '=', 'sections.id')
                    ->leftJoin('old_colonies as oc', 'pm.new_colony_name', '=', 'oc.id')
                    ->leftJoin('property_lease_details as pld', 'pm.id', '=', 'pld.property_master_id')
                    ->leftJoin('applications as app', 'ma.application_no', '=', 'app.application_no')
                    ->leftJoinSub(
                        DB::table('application_statuses')
                            ->select(
                                'id',
                                'model_id',
                                'reg_app_no',
                                'service_type',
                                'is_mis_checked',
                                'is_scan_file_checked',
                                'is_uploaded_doc_checked',
                                'mis_checked_by',
                                'scan_file_checked_by',
                                'uploaded_doc_checked_by',
                                'created_at',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                            )
                            ->where('service_type', $serviceType1),
                        'latest_statuses',
                        function ($join) {
                            $join->on('ma.id', '=', 'latest_statuses.model_id')
                                ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                        }
                    )
                    ->whereIn('ma.section_id', $sections) // Verify $sections is an array
                    ->select(
                        'ma.id',
                        'ma.created_at',
                        'ma.application_no',
                        'ma.status',
                        'sections.section_code',
                        'latest_statuses.is_mis_checked',
                        'latest_statuses.is_scan_file_checked',
                        'latest_statuses.is_uploaded_doc_checked',
                        'latest_statuses.mis_checked_by',
                        'latest_statuses.scan_file_checked_by',
                        'latest_statuses.uploaded_doc_checked_by',
                        'pm.old_propert_id as old_property_id', // Fixed alias
                        'pm.new_colony_name',
                        'oc.name as colony_name',
                        'pm.block_no',
                        'pm.plot_or_property_no',
                        'pld.presently_known_as',
                        'app.is_objected',
                        DB::raw('NULL as flat_id'), // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        DB::raw('NULL as flat_number'), // Add NULL for flat_number on dated 07/01/25 By Lalit Tiwari
                        DB::raw("'MutationApplication' as model_name") // Add model_name for the first query
                    );
                if ($request->status) {
                    $query1 = $query1->where('ma.status', ($request->status));
                } else {
                    $query1 = $query1->whereIn('ma.status', ($itemsIdArr));
                }

                // Add search filter if search.value is present
                if ($request->input('search.value')) {
                    $searchValue = $request->input('search.value');
                    $query1->where(function ($query) use ($searchValue) {
                        $query->where('ma.application_no', 'like', "%$searchValue%")
                            ->orWhere('pm.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                            ->orWhere(DB::raw('LOWER(oc.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                            ->orWhere('pm.block_no', 'like', "%$searchValue%")  // Correctly reference block
                            ->orWhere('pm.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhere('pld.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhere(DB::raw("'MutationApplication'"), 'like', "%$searchValue%") // Search by model_name
                            ->orWhere('ma.created_at', 'like', "%$searchValue%");
                    });
                }

                // Query for land use changed applications
                $serviceType2 = getServiceType('LUC');
                $query2 = DB::table('land_use_change_applications as lca')
                    ->where('lca.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
                    ->leftJoin('property_masters', 'lca.property_master_id', '=', 'property_masters.id')
                    ->join('property_section_mappings as psm', function ($join) {
                        $join->on('property_masters.new_colony_name', 'psm.colony_id');
                        $join->whereColumn('property_masters.property_type', 'psm.property_type');
                        $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
                    })
                    ->join('sections', 'psm.section_id', 'sections.id')
                    ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
                    ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
                    ->leftJoin('applications as app', 'lca.application_no', '=', 'app.application_no')
                    ->leftJoinSub(
                        DB::table('application_statuses')
                            ->select(
                                'id',
                                'model_id',
                                'reg_app_no',
                                'service_type',
                                'is_mis_checked',
                                'is_scan_file_checked',
                                'is_uploaded_doc_checked',
                                'mis_checked_by',
                                'scan_file_checked_by',
                                'uploaded_doc_checked_by',
                                'created_at',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                            )
                            ->where('service_type', $serviceType2),
                        'latest_statuses',
                        function ($join) {
                            $join->on('lca.id', '=', 'latest_statuses.model_id')
                                ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                        }
                    )
                    ->whereIn('lca.section_id', $sections) //need to add secton id in all queries
                    ->select(
                        'lca.id',
                        'lca.created_at',
                        DB::raw('coalesce(lca.application_no,"0") as application_no'),
                        'lca.status',
                        'sections.section_code',
                        'latest_statuses.is_mis_checked',
                        'latest_statuses.is_scan_file_checked',
                        'latest_statuses.is_uploaded_doc_checked',
                        'latest_statuses.mis_checked_by',
                        'latest_statuses.scan_file_checked_by',
                        'latest_statuses.uploaded_doc_checked_by',
                        'property_masters.old_propert_id as old_property_id',
                        'property_masters.new_colony_name',
                        'old_colonies.name as colony_name',
                        'property_masters.block_no',
                        'property_masters.plot_or_property_no',
                        'property_lease_details.presently_known_as',
                        'app.is_objected',
                        DB::raw('NULL as flat_id'), // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        DB::raw('NULL as flat_number'), // Add NULL for flat_number on dated 07/01/25 By Lalit Tiwari
                        DB::raw("'LandUseChangeApplication' as model_name") // Add model_name for the first query
                    );

                if ($request->status) {
                    $query2 = $query2->where('lca.status', ($request->status));
                } else {
                    $query2 = $query2->whereIn('lca.status', ($itemsIdArr));
                }

                // Add search filter if search.value is present
                if ($request->input('search.value')) {
                    $searchValue = $request->input('search.value');
                    $query2->where(function ($query) use ($searchValue) {
                        $query->where('lca.application_no', 'like', "%$searchValue%")
                            ->orWhere('property_masters.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                            ->orWhere(DB::raw('LOWER(old_colonies.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                            ->orWhere('property_masters.block_no', 'like', "%$searchValue%")  // Correctly reference block
                            ->orWhere('property_masters.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhere('property_lease_details.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhere(DB::raw("'LandUseChangeApplication'"), 'like', "%$searchValue%") // Search by model_name
                            ->orWhere('lca.created_at', 'like', "%$searchValue%");
                    });
                }

                //Query for Deed Of Apartment applications
                $serviceType3 = getServiceType('DOA');
                $query3 = DB::table('deed_of_apartment_applications as doa')
                    ->where('doa.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
                    ->leftJoin('property_masters', 'doa.property_master_id', '=', 'property_masters.id')->join('property_section_mappings as psm', function ($join) {
                        $join->on('property_masters.new_colony_name', 'psm.colony_id');
                        $join->whereColumn('property_masters.property_type', 'psm.property_type');
                        $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
                    })
                    ->join('sections', 'psm.section_id', 'sections.id')
                    ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
                    ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
                    ->leftJoin('flats', 'doa.flat_id', '=', 'flats.id')
                    ->leftJoin('applications as app', 'doa.application_no', '=', 'app.application_no')
                    ->leftJoinSub(
                        DB::table('application_statuses')
                            ->select(
                                'id',
                                'model_id',
                                'reg_app_no',
                                'service_type',
                                'is_mis_checked',
                                'is_scan_file_checked',
                                'is_uploaded_doc_checked',
                                'mis_checked_by',
                                'scan_file_checked_by',
                                'uploaded_doc_checked_by',
                                'created_at',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                            )
                            ->where('service_type', $serviceType3),
                        'latest_statuses',
                        function ($join) {
                            $join->on('doa.id', '=', 'latest_statuses.model_id')
                                ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                        }
                    )
                    ->whereIn('doa.section_id', $sections) //need to add secton id in all queries
                    ->select(
                        'doa.id',
                        'doa.created_at',
                        'doa.application_no',
                        'doa.status',
                        'sections.section_code',
                        'latest_statuses.is_mis_checked',
                        'latest_statuses.is_scan_file_checked',
                        'latest_statuses.is_uploaded_doc_checked',
                        'latest_statuses.mis_checked_by',
                        'latest_statuses.scan_file_checked_by',
                        'latest_statuses.uploaded_doc_checked_by',
                        'property_masters.old_propert_id as old_property_id',
                        'property_masters.new_colony_name',
                        'old_colonies.name as colony_name',
                        'property_masters.block_no',
                        'property_masters.plot_or_property_no',
                        'property_lease_details.presently_known_as',
                        'flats.unique_flat_id as flat_id', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        'flats.flat_number as flat_number', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        'app.is_objected',
                        DB::raw("'DeedOfApartmentApplication' as model_name") // Add model_name for the first query
                    );
                if ($request->status) {
                    $query3 = $query3->where('doa.status', ($request->status));
                } else {
                    $query3 = $query3->whereIn('doa.status', ($itemsIdArr));
                }

                // Add search filter if search.value is present
                if ($request->input('search.value')) {
                    $searchValue = $request->input('search.value');
                    $query3->where(function ($query) use ($searchValue) {
                        $query->where('doa.application_no', 'like', "%$searchValue%")
                            ->orWhere('property_masters.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                            ->orWhere(DB::raw('LOWER(old_colonies.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                            ->orWhere('property_masters.block_no', 'like', "%$searchValue%")  // Correctly reference block
                            ->orWhere('property_masters.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere('flats.unique_flat_id', 'like', "%$searchValue%")  // Search by flat_id
                            ->orWhere('flats.flat_number', 'like', "%$searchValue%")    // Search by flat_number
                            ->orWhere('property_lease_details.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhere(DB::raw("'DeedOfApartmentApplication'"), 'like', "%$searchValue%") // Search by model_name
                            ->orWhere('doa.created_at', 'like', "%$searchValue%");
                    });
                }

                //Query for Conversion applications added by Nitin
                $serviceType4 = getServiceType('CONVERSION');
                $query4 = DB::table('conversion_applications as ca')
                    ->where('ca.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
                    ->leftJoin('property_masters', 'ca.property_master_id', '=', 'property_masters.id')->join('property_section_mappings as psm', function ($join) {
                        $join->on('property_masters.new_colony_name', 'psm.colony_id');
                        $join->whereColumn('property_masters.property_type', 'psm.property_type');
                        $join->whereColumn('property_masters.property_sub_type', 'psm.property_subtype');
                    })
                    ->join('sections', 'psm.section_id', 'sections.id')
                    ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
                    ->leftJoin('property_lease_details', 'property_masters.id', '=', 'property_lease_details.property_master_id')
                    ->leftJoin('applications as app', 'ca.application_no', '=', 'app.application_no')
                    ->leftJoinSub(
                        DB::table('application_statuses')
                            ->select(
                                'id',
                                'model_id',
                                'reg_app_no',
                                'service_type',
                                'is_mis_checked',
                                'is_scan_file_checked',
                                'is_uploaded_doc_checked',
                                'mis_checked_by',
                                'scan_file_checked_by',
                                'uploaded_doc_checked_by',
                                'created_at',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                            )
                            ->where('service_type', $serviceType4),
                        'latest_statuses',
                        function ($join) {
                            $join->on('ca.id', '=', 'latest_statuses.model_id')
                                ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                        }
                    )
                    ->whereIn('ca.section_id', $sections) //need to add secton id in all queries
                    ->select(
                        'ca.id',
                        'ca.created_at',
                        'ca.application_no',
                        'ca.status',
                        'sections.section_code',
                        'latest_statuses.is_mis_checked',
                        'latest_statuses.is_scan_file_checked',
                        'latest_statuses.is_uploaded_doc_checked',
                        'latest_statuses.mis_checked_by',
                        'latest_statuses.scan_file_checked_by',
                        'latest_statuses.uploaded_doc_checked_by',
                        'property_masters.old_propert_id as old_property_id',
                        'property_masters.new_colony_name',
                        'old_colonies.name as colony_name',
                        'property_masters.block_no',
                        'property_masters.plot_or_property_no',
                        'property_lease_details.presently_known_as',
                        'app.is_objected',
                        DB::raw('NULL as flat_id'), // Add NULL for flat_id
                        DB::raw('NULL as flat_number'), // Add NULL for flat_number on dated 07/01/25 By Lalit Tiwari
                        DB::raw("'ConversionApplication' as model_name") // Add model_name for the first query
                    );

                if ($request->status) {
                    $query4 = $query4->where('ca.status', ($request->status));
                } else {
                    $query4 = $query4->whereIn('ca.status', ($itemsIdArr));
                }

                // Add search filter if search.value is present
                if ($request->input('search.value')) {
                    $searchValue = $request->input('search.value');
                    $query4->where(function ($query) use ($searchValue) {
                        $query->where('ca.application_no', 'like', "%$searchValue%")
                            ->orWhere('property_masters.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                            ->orWhere(DB::raw('LOWER(old_colonies.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                            ->orWhere('property_masters.block_no', 'like', "%$searchValue%")  // Correctly reference block
                            ->orWhere('property_masters.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere(DB::raw('NULL'), 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhere('property_lease_details.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhere(DB::raw("'ConversionApplication'"), 'like', "%$searchValue%") // Search by model_name
                            ->orWhere('ca.created_at', 'like', "%$searchValue%");
                    });
                }

                //Add Noc Query to Display Noc Applicaiton in disposed listing - Lalit Tiwari (02/04/2025)
                $serviceType5 = getServiceType('NOC'); // Ensure this function is defined and works properly.
                $query5 = DB::table('noc_applications as noc')
                    ->where('noc.status', '<>', getServiceType('APP_WD')) //check added by nitin withdrawn applications should nnot be visibale to officials -- Nitin 21-02-2025
                    ->leftJoin('property_masters as pm', 'noc.property_master_id', '=', 'pm.id')
                    ->join('property_section_mappings as psm', function ($join) {
                        $join->on('pm.new_colony_name', '=', 'psm.colony_id')
                            ->whereColumn('pm.property_type', 'psm.property_type')
                            ->whereColumn('pm.property_sub_type', 'psm.property_subtype');
                    })
                    ->join('sections', 'psm.section_id', '=', 'sections.id')
                    ->leftJoin('flats', 'noc.flat_id', '=', 'flats.id')
                    ->leftJoin('old_colonies as oc', 'pm.new_colony_name', '=', 'oc.id')
                    ->leftJoin('property_lease_details as pld', 'pm.id', '=', 'pld.property_master_id')
                    ->leftJoin('applications as app', 'noc.application_no', '=', 'app.application_no')
                    ->leftJoinSub(
                        DB::table('application_statuses')
                            ->select(
                                'id',
                                'model_id',
                                'reg_app_no',
                                'service_type',
                                'is_mis_checked',
                                'is_scan_file_checked',
                                'is_uploaded_doc_checked',
                                'mis_checked_by',
                                'scan_file_checked_by',
                                'uploaded_doc_checked_by',
                                'created_at',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY model_id ORDER BY created_at DESC) as row_num')
                            )
                            ->where('service_type', $serviceType5),
                        'latest_statuses',
                        function ($join) {
                            $join->on('noc.id', '=', 'latest_statuses.model_id')
                                ->where('latest_statuses.row_num', '=', 1); // Ensures only the latest record
                        }
                    )
                    ->whereIn('noc.section_id', $sections) // Verify $sections is an array
                    ->select(
                        'noc.id',
                        'noc.created_at',
                        'noc.application_no',
                        'noc.status',
                        'sections.section_code',
                        'latest_statuses.is_mis_checked',
                        'latest_statuses.is_scan_file_checked',
                        'latest_statuses.is_uploaded_doc_checked',
                        'latest_statuses.mis_checked_by',
                        'latest_statuses.scan_file_checked_by',
                        'latest_statuses.uploaded_doc_checked_by',
                        'pm.old_propert_id as old_property_id', // Fixed alias
                        'pm.new_colony_name',
                        'oc.name as colony_name',
                        'pm.block_no',
                        'pm.plot_or_property_no',
                        'pld.presently_known_as',
                        'app.is_objected',
                        'flats.unique_flat_id as flat_id', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        'flats.flat_number as flat_number', // Add NULL for flat_id on dated 07/01/25 By Lalit Tiwari
                        DB::raw("'NocApplication' as model_name") // Add model_name for the first query
                    );
                if ($request->status) {
                    $query5 = $query5->where('noc.status', ($request->status));
                } else {
                    $query5 = $query5->whereIn('noc.status', ($itemsIdArr));
                }

                // Add search filter if search.value is present
                if ($request->input('search.value')) {
                    $searchValue = $request->input('search.value');
                    $query5->where(function ($query) use ($searchValue) {
                        $query->where('noc.application_no', 'like', "%$searchValue%")
                            ->orWhere('pm.old_propert_id', 'like', "%$searchValue%")  // Use the correct column alias here
                            ->orWhere(DB::raw('LOWER(oc.name)'), 'like', '%' . strtolower($searchValue) . '%')  // Use the correct column alias
                            ->orWhere('pm.block_no', 'like', "%$searchValue%")  // Correctly reference block
                            ->orWhere('pm.plot_or_property_no', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere('sections.section_code', 'like', "%$searchValue%")  // Correctly reference plot
                            ->orWhere('flats.unique_flat_id', 'like', "%$searchValue%")  // Search by flat_id
                            ->orWhere('flats.flat_number', 'like', "%$searchValue%")    // Search by flat_number
                            ->orWhere('pld.presently_known_as', 'like', "%$searchValue%")  // flat_id is NULL in this query
                            ->orWhere(DB::raw("'MutationApplication'"), 'like', "%$searchValue%") // Search by model_name
                            ->orWhere('noc.created_at', 'like', "%$searchValue%");
                    });
                }

                $clonedQuery1 = (clone $query1);
                $clonedQuery2 = (clone $query2);
                $clonedQuery3 = (clone $query3);
                $clonedQuery4 = (clone $query4);
                $clonedQuery5 = (clone $query5);

                // Combine all three queries using UNION
                $combinedQuery = $clonedQuery1->union($clonedQuery2)->union($clonedQuery3)->union($clonedQuery4)->union($clonedQuery5);
                break;
        }

        $limit = $request->input('length');
        $start = $request->input('start');
        if ($request->input('order.0.column')) {
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
            // dd($order, $dir);
        } else {
            $order = 'created_at';
            $dir = 'desc';
        }

        $totalData = $combinedQuery->count();
        $totalFiltered = $totalData;

        $applications = $combinedQuery->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
        $data = [];
        $showSendProofReadingLink = false;
        // dd($applications);
        foreach ($applications as $key => $application) {
            if ($application->status) {
                // Get the service code only once to avoid repetitive calls
                $serviceCode = getServiceCodeById($application->status);

                // Check if the application status is 'objected', 'rejected', or 'approved'
                if (in_array($serviceCode, ['APP_OBJ', 'APP_REJ', 'APP_APR'])) {
                    $showSendProofReadingLink = false;
                } else {
                    // Check if the proof reading link has been sent at least once
                    $isProofReadingLinkSent = ApplicationAppointmentLink::where('application_no', $application->application_no)->exists();

                    // Show the proof reading link if it has been sent at least once
                    $showSendProofReadingLink = $isProofReadingLinkSent;
                }
            }
            $mis_checked_by = User::find($application->mis_checked_by);
            $scan_file_checked_by = User::find($application->scan_file_checked_by);
            $uploaded_doc_checked_by = User::find($application->uploaded_doc_checked_by);
            $nestedData['id'] = $key + 1;
            $applicationNumber = $application->application_no;

            $appMovementCount = ApplicationMovement::where('application_no', $application->application_no)->count();
            if (Auth::user()->roles[0]->name == 'section-officer' && getServiceCodeById($application->status) == 'APP_NEW') {
                $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertDot"></div></div>';
            } else if (Auth::user()->roles[0]->name == 'section-officer' && getServiceCodeById($application->status) == 'APP_IP' &&     $appMovementCount == 1) {
                $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertDot"></div></div>';
            } else {
                $latestRecord = ApplicationMovement::where('application_no', $application->application_no)
                    ->latest('created_at')
                    ->first();
                if (!is_null($latestRecord) && $latestRecord->assigned_to == Auth::user()->id) {
                    $applicationNumber = '<div class="d-flex gap-2 align-items-center">' . $application->application_no . '<div class="alertDot"></div></div>';
                } else {
                    $applicationNumber = $application->application_no;
                }
            }
            $nestedData['application_no'] = $applicationNumber;
            $nestedData['old_property_id'] = $application->old_property_id;
            $nestedData['new_colony_name'] = $application->colony_name;
            $nestedData['block_no'] = $application->block_no;
            $nestedData['plot_or_property_no'] = $application->plot_or_property_no;
            $nestedData['presently_known_as'] = $application->presently_known_as;
            $flatHTML = '';
            if (!empty($application->flat_id)) {
                $flatHTML .= '<div class="d-flex gap-2 align-items-center">' . $application->flat_number . '</div><span class="text-secondary">(' . $application->flat_id . ')</span>';
            } else {
                $flatHTML .= '<div>NA</div>';
            }
            $nestedData['flat_id'] =   $flatHTML;
            $nestedData['section'] = $application->section_code;

            switch ($application->model_name) {
                case 'MutationApplication':
                    $appliedFor = 'Mutation';
                    break;
                case 'LandUseChangeApplication':
                    $appliedFor = 'LUC';
                    break;
                case 'DeedOfApartmentApplication':
                    $appliedFor = 'DOA';
                    break;
                case 'ConversionApplication':
                    $appliedFor = 'CONVERSION';
                    break;
                case 'NocApplication':
                    $appliedFor = 'NOC';
                    break;
                default:
                    // Default action
                    break;
            }
            //for getting status
            $item = getStatusDetailsById($application->status);
            $itemCode = $item->item_code;
            $itemName = $item->item_name;
            $itemColor = $item->color_code;
            $statusClasses = [
                'APP_REJ' => 'statusRejected',
                'APP_NEW' => 'statusNew',
                'APP_IP' => 'statusSecondary',
                'RS_REW' => 'text-white bg-secondary',
                'RS_PEN' => 'text-info bg-light-info',
                'APP_APR' => 'landtypeFreeH',
                'APP_OBJ' => 'statusObject',
            ];
            $class = $statusClasses[$itemCode] ?? 'text-secondary bg-light';
            $nestedData['applied_for'] = '<div class="d-flex flex-column gap-1">
                <label class="badge bg-info mx-1">' . $appliedFor . '</label>';

            if ($application->is_objected == 1) {
                $nestedData['applied_for'] .= '<label class="badge bg-danger mx-1">Objected</label>';
            }

            $nestedData['applied_for'] .= '</div>';
            $nestedData['activity'] = [
                'mis' => !empty($application->is_mis_checked) ? $application->is_mis_checked : 'NA',
                'scanned_files' => !empty($application->is_scan_file_checked) ? $application->is_scan_file_checked : 'NA',
                'uploaded_doc' => !empty($application->is_uploaded_doc_checked) ? $application->is_uploaded_doc_checked : 'NA',
                'mis_checked_by' => !empty($application->mis_checked_by) ? $mis_checked_by->name : '',
                'scan_file_checked_by' => !empty($application->scan_file_checked_by) ? $scan_file_checked_by->name : '',
                'uploaded_doc_checked_by' => !empty($application->uploaded_doc_checked_by) ? $uploaded_doc_checked_by->name : '',
                'mis_color_code' => !empty(getServiceTypeColorCode('MIS_CHECK')) ? getServiceTypeColorCode('MIS_CHECK') : '',
                'scan_file_color_code' => !empty(getServiceTypeColorCode('SCAN_CHECK')) ? getServiceTypeColorCode('SCAN_CHECK') : '',
                'uploaded_doc_color_code' => !empty(getServiceTypeColorCode('UP_DOC_CHE')) ? getServiceTypeColorCode('UP_DOC_CHE') : '',
            ];

            $nestedData['status'] = '<span class="highlight_value ' . $class . '">' . ucwords($itemName) . '</span>';
            $model = base64_encode($application->model_name);

            // Prepare actions
            $action = '<div class="d-flex gap-2">';
            $action .= '<a href="'. route('applications.view', ['id' => $application->id, 'type' => $model]).' "
                            <button type="button" class="btn btn-primary px-5" onclick="handleViewApplication()">View</button>
                        </a>
                        <button type="button" class="btn btn-success" onclick="getFileMovement(\'' . $application->application_no . '\', this)">
                            File Movement
                        </button>';
            // Add meeting link button
            if (Auth::user()->roles[0]->name == 'deputy-lndo' && $appliedFor != "LUC" && $showSendProofReadingLink) {
                $action .= '<button type="button" class="btn btn-secondary px-5 send-meeting-link" data-application-id="' . $application->id . '" data-application-model_name="' . $application->model_name . '" data-application-no="' . $application->application_no . '">Send Meeting Link</button>';
            }
            $action .= '</div>';

            $nestedData['action'] = $action;
            $nestedData['created_at'] = Carbon::parse($application->created_at)
                ->setTimezone('Asia/Kolkata')
                ->format('d M Y h:m:s');

            $data[] = $nestedData;
        }

        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        ];

        return response()->json($json_data);
    }

    private function isUnpaidDemandForApplication($userId, $applicationNo)
    {
        return Demand::where('app_no', $applicationNo)
            ->where('created_by', $userId)
            ->whereIn('status', [getServiceType('DEM_DRAFT'), getServiceType('DEM_PENDING'), getServiceType('DEM_PART_PAID')])
            ->exists();
    }

    private function checkOfficalCanViewTheApplication($applicationNo)
    {
        $user = Auth::user();
        $isSectionOfficer = $user->hasRole('section-officer');
        //added by swati on 24022026
        $fifoLimit = 10; // fixed fifo limit for all roles
        $application = Application::where('application_no', $applicationNo)->first();
        $canView = true;
        if ($this->isUnpaidDemandForApplication($user->id, $applicationNo)) { //if pending demand then user can see application but can not take any further action
            return $canView;
        }
         if (in_array($application->status, [getServiceType('APP_APR'), getServiceType('APP_REJ'), getServiceType('APP_CAN')])) { //if application is already disposed then allow access
            return $canView;
        }
        if ($application->status == getServiceType('APP_HOLD') && $user->roles[0]->name == 'CDV') {
            return $canView;
        }
        $appAssignedTo = ApplicationMovement::where('application_no', $applicationNo)->latest()?->first()?->assigned_to;
        // dd($appAssignedTo);
        if (
            $appAssignedTo !== $user->id &&       // not assigned to this user
            !($isSectionOfficer && is_null($appAssignedTo)) // exclude: SectionOfficer + null
        ) //If assigned to someone else then can view but not take action
        {
            return $canView;
        }

        $appServiceType = $application->service_type;
        if (getServiceCodeById($appServiceType) == "NOC") 
							      {
							      	$dateStart = '2006-02-14';
								    $dateEnd   = '2017-05-01';
								    $allApplicationNos = Application::where('service_type', $appServiceType)
										    ->whereIn('status', [
										        getServiceType('APP_NEW'),
										        getServiceType('APP_IP'),
										        getServiceType('APP_PEN')
										    ])
										    ->pluck('application_no')
										    ->toArray();
							// $withDemandApplications = DB::table('noc_applications as noc')
							//     ->join('property_masters as pm', 'pm.id', '=', 'noc.property_master_id')

							//     ->join(DB::raw("
							//         (
							//             SELECT ptld1.*
							//             FROM property_transferred_lessee_details ptld1
							//             INNER JOIN (
							//                 SELECT MAX(id) as max_id
							//                 FROM property_transferred_lessee_details
							//                 WHERE process_of_transfer = 'Conversion'
							//                 AND transferDate BETWEEN '$dateStart' AND '$dateEnd'
                            //                 AND deleted_at IS NULL  -- Only non-deleted records
							//                 GROUP BY property_master_id
							//             ) ptld2 ON ptld1.id = ptld2.max_id
							//         ) as pld
							//     "), 'pld.property_master_id', '=', 'pm.id')
                             $withDemandApplications = DB::table('noc_applications as noc')
							    ->join('property_masters as pm', 'pm.id', '=', 'noc.property_master_id')
							    ->leftJoin('splited_property_details as spd', 'noc.splitted_id', '=', 'spd.id')
							    ->join(DB::raw("
							        (
							            SELECT ptld1.*
							            FROM property_transferred_lessee_details ptld1
							            INNER JOIN (
							                SELECT MAX(id) as max_id
							                FROM property_transferred_lessee_details
							                WHERE process_of_transfer = 'Conversion'
							                AND transferDate BETWEEN '$dateStart' AND '$dateEnd'
                                             AND deleted_at IS NULL  -- Only non-deleted records
							                GROUP BY property_master_id,splited_property_detail_id
							            ) ptld2 ON ptld1.id = ptld2.max_id
							        ) as pld
							    "),
							    function ($join) {

							        $join->on(function ($query) {
							            $query->on('pm.id', '=', 'pld.property_master_id')
							                  ->whereNull('noc.splitted_id');
							        })
							        ->orOn(function ($query) {
							            $query->on('spd.id', '=', 'pld.splited_property_detail_id')
							                  ->whereNotNull('noc.splitted_id');
							        });

							    })
							    ->whereIn('noc.application_no', $allApplicationNos)
							    ->distinct()
							    ->pluck('noc.application_no')
							    ->toArray();
							    
								    $withoutDemandApplications = array_values(
								        array_diff($allApplicationNos, $withDemandApplications)
								    );
								    $result = [
								        'with_demand'    => $withDemandApplications,
								        'without_demand' => $withoutDemandApplications,
								    ];
								    $currentApplicationNo = $application->application_no;
								    $isWithDemand = in_array($currentApplicationNo, $withDemandApplications);
								    $otherApplications = $isWithDemand
														    ? $withDemandApplications
														    : $withoutDemandApplications;
					}

		      else {
		        $otherApplications = Application::where('service_type', $appServiceType)->whereIn('status', [getServiceType('APP_NEW'), getServiceType('APP_IP'), getServiceType('APP_PEN')])->pluck('application_no')->toArray();
			}

       // $otherApplications = Application::where('service_type', $appServiceType)->whereIn('status', [getServiceType('APP_NEW'), getServiceType('APP_IP'), getServiceType('APP_PEN')])->pluck('application_no')->toArray();
        // dd($otherApplications);
        if (count($otherApplications) <= $fifoLimit) {
            return $canView;
        }
        $appAssignedToUser = [];
        $latestMovements = ApplicationMovement::whereIn('application_no', $otherApplications)
            ->whereIn('id', function ($q) {
                $q->select(DB::raw('MAX(id)'))
                    ->from('application_movements')
                    ->groupBy('application_no');
            })
            // dd($latestMovements->toSql(), $latestMovements->getBindings());
            ->get();

        $userAssigned = $latestMovements->filter(function ($movement) use ($user) {
            return ($user->hasRole('section-officer') && is_null($movement->assigned_to)) || $movement->assigned_to == $user->id;
        });

        // dd($userAssigned);
        //this code is commented as fifo logic chnged on 19-08-2025
        /* $latestAssigned = $userAssigned->sortBy('created_at')->take(3)->pluck('application_no')->toArray();
        $canView = in_array($applicationNo, $latestAssigned); */

        //updated fifo logic - 19-08-2025
        $latestAssigned = $userAssigned->pluck('application_no')->toArray();
        // $earliestApplications = Application::whereIn('application_no', $latestAssigned)->orderBy('created_at')->take($fifoLimit)->pluck('application_no')->toArray();
   
        //added by swati on 24022026
        // FIFO should be applied per assigned section of the user
        $assignedSectionIds = [];
        if (method_exists($user, 'sections')) {
            //added by swati on 24022026
            $assignedSectionIds = $user->sections()->pluck('sections.id')->toArray(); // fix ambiguous id
        }
        if (empty($assignedSectionIds) && !empty($user->section_id)) {
            $assignedSectionIds = [$user->section_id];
        }
        $useSectionScope = !empty($assignedSectionIds);

        //added by swati on 24022026
        if ($useSectionScope) {
            $earliestApplications = Application::whereIn('application_no', $latestAssigned)
                ->whereIn('section_id', $assignedSectionIds)
                ->select('application_no', 'section_id', 'created_at')
                ->get()
                ->groupBy('section_id')
                ->flatMap(function ($apps) use ($fifoLimit) {
                    return $apps->sortBy('created_at')->take($fifoLimit)->pluck('application_no');
                })
                ->values()
                ->toArray();
        } else {
            // fallback to existing behavior if section is not configured
            $earliestApplications = Application::whereIn('application_no', $latestAssigned)->orderBy('created_at')->take($fifoLimit)->pluck('application_no')->toArray();
        }

        // dd($earliestApplications);


        $canView = in_array($applicationNo, $earliestApplications);
        if ($user->roles[0]->name == 'applicant') {
            $canView  = true;
        }
        return $canView;
    }
    

     public function applicationSummary(Request $request)
    {
        $user = Auth::user();
        $sections = $user->hasAnyRole(['super-admin', 'lndo', 'minister']) ? getRequiredSections() : $user->sections;
        $filters = $request->all();
        //dd($filters);
        $filterSectionIds = [];
        if (isset($filters['section_id'])) {
            $filterSectionIds = [$filters['section_id']];
        } else if (!$user->hasAnyRole(['super-admin', 'lndo', 'minister'])) {
            $filterSectionIds = $user->sections->pluck('id')->toArray();
        }
        $filterDateFrom = $filters['date_from'] ?? null;
        $filterDateTo = $filters['date_to'] ?? null;
        // dd($filterDateFrom, $filterDateTo);
        $queryResult = Application::where('status', '<>', getServiceType('APP_WD'))
            ->when(!empty($filterSectionIds), function ($quer) use ($filterSectionIds) {
                return $quer->whereIn('section_id', $filterSectionIds);
            })
            ->when(!is_null($filterDateFrom), function ($quer) use ($filterDateFrom) {
                return $quer->whereDate('applications.created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
            })
            ->when(!is_null($filterDateTo), function ($quer) use ($filterDateTo) {
                return $quer->whereDate('applications.created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
            })->get();

        $disposeQueryResult = Application::whereIn('status', [getServiceType('APP_APR'), getServiceType('APP_REJ'), getServiceType('APP_CAN')])
            ->whereNotNull('disposed_at')
            ->when(!empty($filterSectionIds), function ($quer) use ($filterSectionIds) {
                return $quer->whereIn('section_id', $filterSectionIds);
            })
            ->when(!is_null($filterDateFrom), function ($quer) use ($filterDateFrom) {
                return $quer->whereDate('disposed_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
            })
            ->when(!is_null($filterDateTo), function ($quer) use ($filterDateTo) {
                return $quer->whereDate('disposed_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
            })
            ->get();
           
        $inProgressQueryResult = Application::whereIn('status', [getServiceType('APP_IP'), getServiceType('APP_OBJ'), getServiceType('APP_HOLD')])
            ->whereNotNull('created_at')
            ->when(!empty($filterSectionIds), function ($quer) use ($filterSectionIds) {
                return $quer->whereIn('section_id', $filterSectionIds);
            })
            ->when(!is_null($filterDateFrom), function ($quer) use ($filterDateFrom) {
                return $quer->whereDate('created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
            })
            ->when(!is_null($filterDateTo), function ($quer) use ($filterDateTo) {
                return $quer->whereDate('created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
            })
            ->get();
            
            
		$serviceTypeWiseApplicationQueryResult = DB::table('applications as a')
				   ->where('status', '<>', getServiceType('APP_WD'))
				    ->when(!empty($filterSectionIds), function ($quer) use ($filterSectionIds) {
				        return $quer->whereIn('section_id', $filterSectionIds);
				    })
				    ->when(!is_null($filterDateFrom), function ($quer) use ($filterDateFrom) {
				        return $quer->whereDate('a.created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
				    })
				    ->when(!is_null($filterDateTo), function ($quer) use ($filterDateTo) {
				        return $quer->whereDate('a.created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
				    })
				    ->select(
				        'service_names.item_name as service',
				        'status_names.item_name as status',
				        'a.status as status_id',
				        'a.service_type as service_type_id',
				        DB::raw('COUNT(a.id) as count')
				    )
				    ->join('items as service_names', 'a.service_type', '=', 'service_names.id')
				    ->join('items as status_names', 'a.status', '=', 'status_names.id')
				    ->groupBy('a.service_type', 'a.status', 'service_names.item_name', 'status_names.item_name')
				    ->get();


        $serviceTypeWiseDisposeQueryResult = DB::table('applications as a')
            ->whereIn('status', [getServiceType('APP_APR'), getServiceType('APP_REJ')])
            ->when(!empty($filterSectionIds), function ($quer) use ($filterSectionIds) {
                return $quer->whereIn('section_id', $filterSectionIds);
            })
           ->when(!is_null($filterDateFrom), function ($quer) use ($filterDateFrom) {
				    return $quer->whereDate(
				        'a.disposed_at',
				        '>=',
				        Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d')
				    );
				})
				->when(!is_null($filterDateTo), function ($quer) use ($filterDateTo) {
				    return $quer->whereDate(
				        'a.disposed_at',
				        '<=',
				        Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d')
				    );
				})
            ->select(
                'service_names.item_name as service',
                'status_names.item_name as status',
                DB::raw('COUNT(a.id) as count')
            )
            ->join('items as service_names', 'a.service_type', '=', 'service_names.id')
            ->join('items as status_names', 'a.status', '=', 'status_names.id')
            ->groupBy('service_names.item_name', 'status_names.item_name')
            ->get();

        $serviceTypeWiseApplicationStatus = [];

        // collect all unique statuses first
        $statuses = $serviceTypeWiseApplicationQueryResult->pluck('status')->unique();

        foreach ($serviceTypeWiseApplicationQueryResult as $row) {
            $service = $row->service;
            $status  = $row->status;

            // ensure the service exists with all statuses initialized
            if (!isset($serviceTypeWiseApplicationStatus[$service])) {
                $serviceTypeWiseApplicationStatus[$service] = array_fill_keys($statuses->toArray(), 0);
                $serviceTypeWiseApplicationStatus[$service]['total'] = 0;
            }

            // set the count
            $serviceTypeWiseApplicationStatus[$service][$status] = $row->count;
            $serviceTypeWiseApplicationStatus[$service]['total'] += $row->count;
        }

        // optional: reset array keys for cleaner output
        $serviceTypeWiseApplicationStatus = collect($serviceTypeWiseApplicationStatus)->map(function ($row) use ($statuses) {
            // make sure every status key is present, even if count=0
            return array_merge(array_fill_keys($statuses->toArray(), 0), $row);
        });
        
        $serviceTypeWiseDisposeStatus = [];

        // collect all unique statuses first
        $statuses = $serviceTypeWiseDisposeQueryResult->pluck('status')->unique();

        foreach ($serviceTypeWiseDisposeQueryResult as $row) {
            $service = $row->service;
            $status  = $row->status;

            // ensure the service exists with all statuses initialized
            if (!isset($serviceTypeWiseDisposeStatus[$service])) {
                $serviceTypeWiseDisposeStatus[$service] = array_fill_keys($statuses->toArray(), 0);
                $serviceTypeWiseDisposeStatus[$service]['total'] = 0;
            }

            // set the count
            $serviceTypeWiseDisposeStatus[$service][$status] = $row->count;
            $serviceTypeWiseDisposeStatus[$service]['total'] += $row->count;
        }

        // optional: reset array keys for cleaner output
        $serviceTypeWiseDisposeStatus = collect($serviceTypeWiseDisposeStatus)->map(function ($row) use ($statuses) {
            // make sure every status key is present, even if count=0
            return array_merge(array_fill_keys($statuses->toArray(), 0), $row);
        });
       
        
        $totalApplications = $queryResult->count();
        $newOrPendingApplications = $queryResult->whereIn('status', [getServiceType('APP_NEW'), getServiceType('APP_PEN')])->count();
        $applicationsReceived = $totalApplications - $newOrPendingApplications;


        $totalDisposedApplicationCount = $disposeQueryResult->count();
        $totalinProgressApplicationCount = $inProgressQueryResult->count();
        $approvedApplicationCount = $rejectedApplicationCount = $canceledApplicationCount = 0;
        if ($totalDisposedApplicationCount > 0) {
            $approvedApplicationCount = $disposeQueryResult->where('status', getServiceType('APP_APR'))->count();
            $rejectedApplicationCount = $disposeQueryResult->where('status', getServiceType('APP_REJ'))->count();
            $canceledApplicationCount = $disposeQueryResult->where('status', getServiceType('APP_CAN'))->count();
        }
       // dd($totalApplications."- ".$newOrPendingApplications."-".$applicationsReceived."-".$totalDisposedApplicationCount."-".$totalinProgressApplicationCount);

        $data['sections'] = $sections;
        $data['totalApplications'] = $totalApplications;
        //dd($data['totalApplications']);
        $data['newOrPendingApplications'] = $newOrPendingApplications;
        $data['applicationsReceived'] = $applicationsReceived;
        $data['totalDisposedApplicationCount'] = $totalDisposedApplicationCount;
        $data['totalinProgressApplicationCount']= $totalinProgressApplicationCount;
        
//        $data['approvedApplicationCount'] = $approvedApplicationCount;
//        $data['rejectedApplicationCount'] = $rejectedApplicationCount;
//        $data['canceledApplicationCount'] = $canceledApplicationCount;
        $data['serviceTypeWiseApplicationStatus'] = $serviceTypeWiseApplicationStatus;
        $data['serviceTypeWiseDisposeStatus'] = $serviceTypeWiseDisposeStatus;

        if (empty($filters)) {
        	$data['applications'] = $queryResult;
            return view('application.summary', $data);
        } 
        else {
            /**Service types */
            $serviceTypes = getItemsByGroupId(17001);
            $serviceTypesFormatted = $serviceTypes->pluck('item_name', 'id')->toArray();            
            $statuses = getItemsByGroupId(1031);
            $statusList = $statuses->pluck('item_name', 'id')->toArray();

            
            $data['serviceTypes'] = $serviceTypesFormatted;
            $data['statusList'] = $statusList;
        }
        //dd($statusList);
        return empty($filters) ? view('application.summary', $data) : response()->json(['success' => true, 'data' => $data]);
    }
    public function applicationSummaryDetails(Request $request)
			{
			    $user = Auth::user();
			    $filters = $request->all();

			    $filterSectionIds = [];

			    if (!empty($filters['section_id'])) {
			        $filterSectionIds = [$filters['section_id']];
			    } elseif (!$user->hasAnyRole(['super-admin', 'lndo', 'minister'])) {
			        $filterSectionIds = $user->sections->pluck('id')->toArray();
			    }

			    $filterService = null;
			    if (!empty($filters['service'])) {
			        $filterService = getServiceType($filters['service']);
			    }

			    $filterStatus = [];
			    if (!empty($filters['status'])) {
			        $normalizedStatus = preg_replace('/\s*,\s*/', ',', $filters['status']);
			        $filterStatus = array_map('getServiceType', explode(',', $normalizedStatus));
			    }

			    $filterDateFrom = $filters['date_from'] ?? null;
			    $filterDateTo   = $filters['date_to'] ?? null;

			    // Disposed statuses
			    $disposedStatuses = [
			        getServiceType('APP_APR'),
			        getServiceType('APP_REJ'),
			        getServiceType('APP_CAN'),
			    ];

			    // Check if only disposed records are requested
			    $useDisposedDate = !empty($filterStatus)
			        && empty(array_diff($filterStatus, $disposedStatuses));

			    $queryResult = Application::query()

			        ->when(!empty($filterStatus), function ($q) use ($filterStatus) {
			            $q->whereIn('status', $filterStatus);
			        }, function ($q) {
			            $q->where('status', '<>', getServiceType('APP_WD'));
			        })

			        ->when($filterService, function ($q) use ($filterService) {
			            $q->where('service_type', $filterService);
			        })

			        ->when(!empty($filterSectionIds), function ($q) use ($filterSectionIds) {
			            $q->whereIn('section_id', $filterSectionIds);
			        })

			        // Date From
			        ->when($filterDateFrom, function ($q) use ($filterDateFrom, $useDisposedDate) {

			            $date = Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d');

			            if ($useDisposedDate) {
			                $q->whereDate('disposed_at', '>=', $date);
			            } else {
			                $q->whereDate('created_at', '>=', $date);
			            }
			        })

			        // Date To
			        ->when($filterDateTo, function ($q) use ($filterDateTo, $useDisposedDate) {

			            $date = Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d');

			            if ($useDisposedDate) {
			                $q->whereDate('disposed_at', '<=', $date);
			            } else {
			                $q->whereDate('created_at', '<=', $date);
			            }
			        })

			        ->latest()
			        ->get();

			    return view('application.summary-details', [
			        'applications' => $queryResult
			    ]);
			}


//      public function applicationSummary(Request $request)
//     {
//         $user = Auth::user();
//         $sections = $user->hasAnyRole(['super-admin', 'lndo', 'minister']) ? getRequiredSections() : $user->sections;
//         $filters = $request->all();
//         //dd($filters);
//         $filterSectionIds = [];
//         if (isset($filters['section_id'])) {
//             $filterSectionIds = [$filters['section_id']];
//         } else if (!$user->hasAnyRole(['super-admin', 'lndo', 'minister'])) {
//             $filterSectionIds = $user->sections->pluck('id')->toArray();
//         }
//         $filterDateFrom = $filters['date_from'] ?? null;
//         $filterDateTo = $filters['date_to'] ?? null;
//         // dd($filterDateFrom, $filterDateTo);
//         $queryResult = Application::where('status', '<>', getServiceType('APP_WD'))
//             ->when(!empty($filterSectionIds), function ($quer) use ($filterSectionIds) {
//                 return $quer->whereIn('section_id', $filterSectionIds);
//             })
//             ->when(!is_null($filterDateFrom), function ($quer) use ($filterDateFrom) {
//                 return $quer->whereDate('applications.created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
//             })
//             ->when(!is_null($filterDateTo), function ($quer) use ($filterDateTo) {
//                 return $quer->whereDate('applications.created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
//             }) //;
//             //dd(vsprintf(str_replace('?', "'%s'", $queryResult->toSql()), $queryResult->getBindings()));
//             ->get();

//         $disposeQueryResult = Application::whereIn('status', [getServiceType('APP_APR'), getServiceType('APP_REJ'), getServiceType('APP_CAN')])
//             ->whereNotNull('disposed_at')
//             ->when(!empty($filterSectionIds), function ($quer) use ($filterSectionIds) {
//                 return $quer->whereIn('section_id', $filterSectionIds);
//             })
//             ->when(!is_null($filterDateFrom), function ($quer) use ($filterDateFrom) {
//                 return $quer->whereDate('created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
//             })
//             ->when(!is_null($filterDateTo), function ($quer) use ($filterDateTo) {
//                 return $quer->whereDate('created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
//             })
//             ->get();
           
//             $inProgressQueryResult = Application::whereIn('status', [getServiceType('APP_IP'), getServiceType('APP_OBJ'), getServiceType('APP_HOLD')])
//             ->whereNotNull('created_at')
//             ->when(!empty($filterSectionIds), function ($quer) use ($filterSectionIds) {
//                 return $quer->whereIn('section_id', $filterSectionIds);
//             })
//             ->when(!is_null($filterDateFrom), function ($quer) use ($filterDateFrom) {
//                 return $quer->whereDate('created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
//             })
//             ->when(!is_null($filterDateTo), function ($quer) use ($filterDateTo) {
//                 return $quer->whereDate('created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
//             })
//             ->get();
// 				$serviceTypeWiseApplicationQueryResult = DB::table('applications as a')
// 				    ->where('status', '<>', getServiceType('APP_WD'))
// 				    ->when(!empty($filterSectionIds), function ($quer) use ($filterSectionIds) {
// 				        return $quer->whereIn('section_id', $filterSectionIds);
// 				    })
// 				    ->when(!is_null($filterDateFrom), function ($quer) use ($filterDateFrom) {
// 				        return $quer->whereDate('a.created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
// 				    })
// 				    ->when(!is_null($filterDateTo), function ($quer) use ($filterDateTo) {
// 				        return $quer->whereDate('a.created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
// 				    })
// 				    ->select(
// 				        'service_names.item_name as service',
// 				        'status_names.item_name as status',
// 				        'a.status as status_id',
// 				        'a.service_type as service_type_id',
// 				        DB::raw('COUNT(a.id) as count')
// 				    )
// 				    ->join('items as service_names', 'a.service_type', '=', 'service_names.id')
// 				    ->join('items as status_names', 'a.status', '=', 'status_names.id')
// 				    ->groupBy('a.service_type', 'a.status', 'service_names.item_name', 'status_names.item_name')
// 				    ->get();


//         $serviceTypeWiseDisposeQueryResult = DB::table('applications as a')
//             ->whereIn('status', [getServiceType('APP_APR'), getServiceType('APP_REJ')])
//             ->when(!empty($filterSectionIds), function ($quer) use ($filterSectionIds) {
//                 return $quer->whereIn('section_id', $filterSectionIds);
//             })
//             ->when(!is_null($filterDateFrom), function ($quer) use ($filterDateFrom) {
//                 return $quer->whereDate('disposed_at', '>=', $filterDateFrom);
//             })
//             ->when(!is_null($filterDateTo), function ($quer) use ($filterDateTo) {
//                 return $quer->whereDate('disposed_at', '<=', $filterDateTo);
//             })
//             ->select(
//                 'service_names.item_name as service',
//                 'status_names.item_name as status',
//                 DB::raw('COUNT(a.id) as count')
//             )
//             ->join('items as service_names', 'a.service_type', '=', 'service_names.id')
//             ->join('items as status_names', 'a.status', '=', 'status_names.id')
//             ->groupBy('service_names.item_name', 'status_names.item_name')
//             ->get();

//         $serviceTypeWiseApplicationStatus = [];

//         // collect all unique statuses first
//         $statuses = $serviceTypeWiseApplicationQueryResult->pluck('status')->unique();
// //dd($statuses);
//         foreach ($serviceTypeWiseApplicationQueryResult as $row) {
//             $service = $row->service;
//             $status  = $row->status;

//             // ensure the service exists with all statuses initialized
//             if (!isset($serviceTypeWiseApplicationStatus[$service])) {
//                 $serviceTypeWiseApplicationStatus[$service] = array_fill_keys($statuses->toArray(), 0);
//                 $serviceTypeWiseApplicationStatus[$service]['total'] = 0;
//             }

//             // set the count
//             $serviceTypeWiseApplicationStatus[$service][$status] = $row->count;
//             $serviceTypeWiseApplicationStatus[$service]['total'] += $row->count;
//         }

//         // optional: reset array keys for cleaner output
//         $serviceTypeWiseApplicationStatus = collect($serviceTypeWiseApplicationStatus)->map(function ($row) use ($statuses) {
//             // make sure every status key is present, even if count=0
//             return array_merge(array_fill_keys($statuses->toArray(), 0), $row);
//         });
//          //dd($serviceTypeWiseApplicationStatus);
//         $serviceTypeWiseDisposeStatus = [];

//         // collect all unique statuses first
//         $statuses = $serviceTypeWiseDisposeQueryResult->pluck('status')->unique();

//         foreach ($serviceTypeWiseDisposeQueryResult as $row) {
//             $service = $row->service;
//             $status  = $row->status;

//             // ensure the service exists with all statuses initialized
//             if (!isset($serviceTypeWiseDisposeStatus[$service])) {
//                 $serviceTypeWiseDisposeStatus[$service] = array_fill_keys($statuses->toArray(), 0);
//                 $serviceTypeWiseDisposeStatus[$service]['total'] = 0;
//             }

//             // set the count
//             $serviceTypeWiseDisposeStatus[$service][$status] = $row->count;
//             $serviceTypeWiseDisposeStatus[$service]['total'] += $row->count;
//         }

//         // optional: reset array keys for cleaner output
//         $serviceTypeWiseDisposeStatus = collect($serviceTypeWiseDisposeStatus)->map(function ($row) use ($statuses) {
//             // make sure every status key is present, even if count=0
//             return array_merge(array_fill_keys($statuses->toArray(), 0), $row);
//         });

//         // dd($serviceTypeWiseDisposeStatus);
//         $totalApplications = $queryResult->count();
//         //dd($totalApplications);
//         $newOrPendingApplications = $queryResult->whereIn('status', [getServiceType('APP_NEW'), getServiceType('APP_PEN')])->count();
//         $applicationsReceived = $totalApplications - $newOrPendingApplications;


//         $totalDisposedApplicationCount = $disposeQueryResult->count();
//         $totalinProgressApplicationCount = $inProgressQueryResult->count();
//         $approvedApplicationCount = $rejectedApplicationCount = $canceledApplicationCount = 0;
//         if ($totalDisposedApplicationCount > 0) {
//             $approvedApplicationCount = $disposeQueryResult->where('status', getServiceType('APP_APR'))->count();
//             $rejectedApplicationCount = $disposeQueryResult->where('status', getServiceType('APP_REJ'))->count();
//             $canceledApplicationCount = $disposeQueryResult->where('status', getServiceType('APP_CAN'))->count();
//         }


//         $data['sections'] = $sections;
//         $data['totalApplications'] = $totalApplications;
//         //dd($data['totalApplications']);
//         $data['newOrPendingApplications'] = $newOrPendingApplications;
//         $data['applicationsReceived'] = $applicationsReceived;
//         $data['totalDisposedApplicationCount'] = $totalDisposedApplicationCount;
//         $data['totalinProgressApplicationCount']= $totalinProgressApplicationCount;
//         $data['approvedApplicationCount'] = $approvedApplicationCount;
//         $data['rejectedApplicationCount'] = $rejectedApplicationCount;
//         $data['canceledApplicationCount'] = $canceledApplicationCount;
//         $data['serviceTypeWiseApplicationStatus'] = $serviceTypeWiseApplicationStatus;
//         $data['serviceTypeWiseDisposeStatus'] = $serviceTypeWiseDisposeStatus;

//         if (empty($filters)) {
//         	$data['applications'] = $queryResult;
//             return view('application.summary', $data);
//         } else {
//             /**Service types */
//             $serviceTypes = getItemsByGroupId(17001);
//             $serviceTypesFormatted = $serviceTypes->pluck('item_name', 'id')->toArray();            
//             $statuses = getItemsByGroupId(1031);
//             $statusList = $statuses->pluck('item_name', 'id')->toArray();

            
//             $data['serviceTypes'] = $serviceTypesFormatted;
//             $data['statusList'] = $statusList;
//         }
//         //dd($statusList);
//         return empty($filters) ? view('application.summary', $data) : response()->json(['success' => true, 'data' => $data]);
//     }
//     public function applicationSummaryDetails(Request $request)
//     {
//         $user = Auth::user();
//         $filters = $request->all();
//         //dd($filters);
//         $filterSectionIds = [];
//         if (isset($filters['section_id'])) {
//             $filterSectionIds = [$filters['section_id']];
//         } else if (!$user->hasAnyRole(['super-admin', 'lndo', 'minister'])) {
//             $filterSectionIds = $user->sections->pluck('id')->toArray();
//         }

//         $filterService = null;
//         if (isset($filters['service'])) {
//             $requestedServicee = $filters['service'];
//             $filterService = getServiceType($filters['service']);
//         }
//         //dd($filterService);
//         $filterStatus = [];
// 			if (isset($filters['status'])) {
// 			    // Normalize spaces after commas, then explode
// 			    $normalizedStatus = preg_replace('/\s*,\s*/', ',', $filters['status']); // replaces comma + optional space with just comma
// 			    $filterStatus = array_map('getServiceType', explode(',', $normalizedStatus));
// 			}       
//         $filterDateFrom = $filters['date_from'] ?? null;
//         $filterDateTo = $filters['date_to'] ?? null;
//         // dd($filterDateFrom, $filterDateTo);

//         $queryResult = Application::when(!empty($filterStatus), function ($quer) use ($filterStatus) {
//             return $quer->whereIn('status', $filterStatus); //applications with given status
//         }, function ($quer) {
//             $quer->where('status', '<>', getServiceType('APP_WD')); //all except withdrawn
//         })
//             ->when(!is_null($filterService), function ($quer) use ($filterService) {
//                 return $quer->where('service_type', $filterService);
//             })
//             ->when(!empty($filterSectionIds), function ($quer) use ($filterSectionIds) {
//                 return $quer->whereIn('section_id', $filterSectionIds);
//             })
//             ->when(!is_null($filterDateFrom), function ($quer) use ($filterDateFrom) {
//                 return $quer->whereDate('applications.created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
//             })
//             ->when(!is_null($filterDateTo), function ($quer) use ($filterDateTo) {
//                 return $quer->whereDate('applications.created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
//             }) //;
//             //dd(vsprintf(str_replace('?', "'%s'", $queryResult->toSql()), $queryResult->getBindings()));
//             ->get();
//         $data['applications'] = $queryResult;
//         //dd($queryResult);
//         return  view('application.summary-details', $data);
//     }

//         private function getLocalScannedFiles($property_master_id, $splited_proprty_id = null)
//     {
//         $scannedFiles = PropertyScannedFile::where('property_master_id', $property_master_id)
//             ->when(!is_null($splited_proprty_id), function ($query) use ($splited_proprty_id) {
//                 return $query->where('splited_property_detail_id', $splited_proprty_id);
//             })->select('document_name', 'document_path')->get()->toArray();
//         return [
//             'baseUrl' => url('/').'/storage/',
//             'files' => $scannedFiles
//         ];
//     }

    public function removeApplication(Request $request)
    {
        return view('admin.application_remove');
    }

    public function removeApplicationAction(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'applicationNumber' => 'required|string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $applicationNumber = strtoupper(trim($request->applicationNumber));
        // Extract and validate prefix
        $prefix = GeneralFunctions::extractPrefix($applicationNumber);
        if ($prefix !== 'APP' && $prefix !== 'APL') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid application number. Must start with APP Or APL.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            if ($prefix == 'APP') {
                $result = $this->applicationDelete($applicationNumber);
            } else {
                $result = $this->registrationDelete($applicationNumber);
            }

            DB::commit();
            $message = '';
            // Log the removal (optional)
            if ($prefix == 'APP') {
                $message = "Application {$applicationNumber} has been removed successfully!";
            } else {
                $message = "Registration {$applicationNumber} has been removed successfully!";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'details' => $result
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to remove application: ' . $e->getMessage(), [
                'application_number' => $applicationNumber,
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove application. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function applicantDetailsUpdateByCdv(Request $request)
    {
       
        $name = $request->name;
        $gender = $request->gender;
        $relation = $request->relation;
        $second_name = $request->second_name;
        $address = $request->address;
        $share = $request->share;

        $user = User::findOrFail($request->applicant_id);
        $user->update([
            'name' => $name,
        ]);

        $applicantShare = ApplicantShare::where('application_no', $request->application_no)->first();
        $applicantShare->update([
            'share' => $share
        ]);

        $applicantDetails = ApplicantUserDetail::where('user_id', $request->applicant_id)->first();
        $applicantDetails->update([
            'gender' => $gender,
            'so_do_spouse' => $relation,
            'second_name' => $second_name,
            'address' => $address,
        ]);
        $userRegistration = UserRegistration::where('applicant_number', $request->applicant_no)->first();
        $userRegistration->update([
            'name' => $name,
            'gender' => $gender,
            'prefix' => $relation,
            'second_name' => $second_name,
            'comm_address' => $address,
        ]);
        return redirect()->back()->with('success', 'Applicant details updated successfully.');

    }

    private function applicationDelete($applicationNumber)
    {
        // Find application
        $application = GeneralFunctions::findApplication($applicationNumber);
        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application number not found in our records.'
            ], 200);
        }

        // Check if application can be deleted
        $removalCheck = GeneralFunctions::isApplicantCanDeleteForApplications($application);

        if (!$removalCheck['can_remove']) {
            return response()->json([
                'success' => false,
                'message' => $removalCheck['message']
            ], 200);
        }

        // Delete application data using GeneralFunctions
        $result = GeneralFunctions::deleteApplicationData(
            $application,
            $application->model_name,
            $application->model_id
        );

        // Delete application letter from storage with improved file deletion
        if ($application->letter) {
            GeneralFunctions::deleteFileFromStorage($application->letter);
        }

        // Delete application signed letter from storage
        if ($application->Signed_letter) {
            GeneralFunctions::deleteFileFromStorage($application->Signed_letter);
        }

        // Delete the main application record
        $result = $application->forceDelete();
        return $result;
    }

    private function registrationDelete($applicationNumber)
    {
        // Find application
        $registration = GeneralFunctions::findRegistration($applicationNumber);
        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Application number not found in our records.'
            ], 200);
        }

        //Get User Id if to fetch all user application data
        $userId = User::where('mobile_no', $registration->mobile)
            ->orWhere('email', $registration->email)
            ->value('id');
        if (!empty($userId)) {
            //Get all application for user
            $applications = Application::where('created_by', $userId)->get();
            if ($applications) {
                foreach ($applications as $application) {
                    $this->applicationDelete($application->application_no);
                }
            }
        }

        // Check if user registration is approve then is applicant delete flag should be set as true to delete approve registration
        if ($registration->status == getServiceType('RS_APP')) {
            // Check if application can be deleted
            $removalCheck = GeneralFunctions::isApplicantCanDeleteForRegistraions($applicationNumber);

            if (!$removalCheck['can_remove']) {
                return response()->json([
                    'success' => false,
                    'message' => $removalCheck['message']
                ], 200);
            }

            // Delete application data using GeneralFunctions
            GeneralFunctions::deleteRegistrationData($registration);
        }

        // Delete files from storage with improved file deletion
        if ($registration->profile_photo) {
            GeneralFunctions::deleteFileFromStorage($registration->profile_photo);
        }

        if ($registration->lease_deed_doc) {
            GeneralFunctions::deleteFileFromStorage($registration->lease_deed_doc);
        }

        // Delete the main application record
        $result = $registration->forceDelete();
        return $result;
    }
}


