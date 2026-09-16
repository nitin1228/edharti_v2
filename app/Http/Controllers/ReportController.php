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
use App\Models\Item;
//use App\Models\User;
use App\Models\OldColony;
use App\Models\CurrentLesseeDetail;
//use App\Models\PropertyRevivisedGroundRent;
use App\Models\UnallottedPropertyDetail;
use App\Models\PropertyTransferredLesseeDetail;
use App\Models\SplitedPropertyDetail;
use App\Models\PropertyLeaseDetail;
use App\Models\PropertySectionMapping;
use App\Models\Section;
use Illuminate\Support\Facades\Auth;
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
use Illuminate\Support\Facades\Mail;
use App\Mail\DownloadReady;


class ReportController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view reports', ['only' => ['index', 'tabularRecord']]);
    }

    public function index(MisService $misService, ColonyService $colonyService)
    {
        $data['landTypes'] = $misService->getItemsByGroupId(1051);
        $data['propertyTypes'] = $misService->getItemsByGroupId(1052);
        $data['leaseTypes'] = $misService->getItemsByGroupId(102);
        $data['propertyStatus'] = $misService->getItemsByGroupId(109);
        // $data['results'] = $reportService->filterResults();
        $data['results'] = [];
        // $data['colonyList'] = $colonyService->getAllColonies();//changed by swati as mis colony name data needs to be fetched with new name
        $data['colonyList'] = $colonyService->misDoneForColonies(true); //true added by nitin 14022025
        $user = Auth::user();
        if ($user->sections->count() > 0) {
            $sectionIds = $user->sections->pluck('id')->toArray();
            $data['sectionPropertyTpes'] = PropertySectionMapping::whereIn('section_id', $sectionIds)->pluck('property_type')->toArray();
        }
        return view('report.report', $data);
    }


    public function getPropertyResults(Request $request, ReportService $reportService)
    {
        // dd($request->all());
        $filters = $request->filters;
        $user = Auth::check() ? Auth::user() : null;
        $sections = $user->hasAnyRole('section-officer', 'deputy-lndo') ? $user->sections->pluck('section_code')->toArray() : [];
        $filters['section_id'] = $sections;
        $results = $reportService->filterResults($filters, true);

        // Start :- Worked on adding filter report user action logs - Lalit Tiwari (29/April/2025)
        $authUserName = Auth::user()->name;
        $descriptionParts = [];
        if (!empty($results['rows'])) {
            $descriptionParts = [];
            $authUserName = Auth::user()->name;

            // Helper function to fetch names from Items
            $pluckItemNames = fn($ids) => Item::whereIn('id', (array)$ids)->pluck('item_name')->toArray();

            // Helper for range values
            $rangeText = fn($label, $range) => "{$label}: (Min: {$range['min']}, Max: {$range['max']})";

            // Land Type (single value)
            if (!empty($filters['land_type'])) {
                $landName = Item::where('id', $filters['land_type'])->value('item_name');
                if ($landName) {
                    $descriptionParts[] = "Land: {$landName}";
                }
            }

            // Multi-value fields with IDs
            $multiFields = [
                'property_type' => 'Property Type',
                'property_sub_type' => 'Property Sub Type',
                'land_status' => 'Property Status',
                'leaseDeed' => 'Lease Deed'
            ];
            foreach ($multiFields as $field => $label) {
                if (!empty($filters[$field])) {
                    $names = $pluckItemNames($filters[$field]);
                    if ($names) {
                        $descriptionParts[] = "{$label}: " . implode(', ', $names);
                    }
                }
            }

            // Colony names
            if (!empty($filters['colony'])) {
                $colonies = OldColony::whereIn('id', (array)$filters['colony'])->pluck('name')->toArray();
                if ($colonies) {
                    $descriptionParts[] = 'Colony: ' . implode(', ', $colonies);
                }
            }

            // Direct single-value fields
            $simpleFields = [
                'name' => 'Name',
                'contact' => 'Contact',
                'propertyId' => 'Property ID',
                'propertyAddress' => 'Property Address'
            ];
            foreach ($simpleFields as $field => $label) {
                if (!empty($filters[$field])) {
                    $descriptionParts[] = "{$label}: {$filters[$field]}";
                }
            }

            // Range fields
            $rangeFields = [
                'land_size' => 'Land Size',
                'land_value' => 'Land Value',
                'groundRent' => 'Ground Rent',
                'outstandingDues' => 'Outstanding Dues',
                'date_of_execution' => 'Date Of Execution'
            ];
            foreach ($rangeFields as $field => $label) {
                if (!empty($filters[$field]['min']) || !empty($filters[$field]['max'])) {
                    $descriptionParts[] = $rangeText($label, $filters[$field]);
                }
            }

            // List-based text filters
            if (!empty($filters['reEnteredSince']) && is_array($filters['reEnteredSince'])) {
                $descriptionParts[] = 'Re-entered Since: ' . implode(', ', $filters['reEnteredSince']);
            }
            if (!empty($filters['leaseTenure']) && is_array($filters['leaseTenure'])) {
                $descriptionParts[] = 'Lease Tenure: ' . implode(', ', $filters['leaseTenure']);
            }

            // Final description string
            $description = implode(' | ', $descriptionParts);

            if (!empty($description)) {
                UserActionLogHelper::UserActionLog(
                    'filterReport',
                    url("/reports/filter-report"),
                    'filterReport',
                    "{$authUserName} has filtered the report by {$description}"
                );
            }
        }
        // End :- Worked on adding filter report user action logs - Lalit Tiwari (29/April/2025)

        return response()->json($results);
    }

    public function tabularRecord(ReportService $reportService)
    {
        $tabularRecord = $reportService->tabularRecord();
        return view('tabular_record', compact(['tabularRecord']));
    }

    public function getDistinctSubTypes(Request $request, ReportService $reportService)
    {
        $types = $request->types;
        $subtypes = $reportService->getDistinctSubTypes($types);
        return response()->json($subtypes);
    }
    /* public function reportExport(Request $request, ReportService $reportService)
    {
        $format = $request->format;
        $filters = $request->filters ?? [];
        $results = $reportService->filterResults($filters, false);
        /*$chunkSize = 10000; // Number of rows per chunk
        $chunks = array_chunk($results, $chunkSize); // Split data into chunks
        $export = new ReportExport($chunks); /
        $rows = []; //header rows
        foreach ($results as $index => $item) {

            $rows[] = [
                'old_propert_id' => $item->old_propert_id,
                'unique_propert_id' => $item->unique_propert_id,
                'land_type' => $item->land_type,
                'status' => $item->status,
                'lease_tenure' => $item->lease_tenure,
                'land_use' => $item->land_use,
                'area' => $item->area_in_sqm,
                'address' => $item->address,
                'lesse_name' => $item->lesse_name,
                'gr_in_re_rs' => $item->gr_in_re_rs,
                'gr' => $item->gr,
            ];
        };
        $export = new ReportExport([
            $rows

        ]);
        /* if ($format == 'csv') {
            return Excel::download($export, 'report.csv', \Maatwebsite\Excel\Excel::CSV, [
                'Content-Type' => 'text/csv',
            ]);
        } /
        if ($format == 'xls') {
            return Excel::download($export, 'report.xls', \Maatwebsite\Excel\Excel::XLS);
        }
        /* if ($format == 'pdf') {
            return Excel::download($export, 'report.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        } *
    } */
    public function reportExport(Request $request)
    {
        $filters = $request->filters ?? [];
        $filters = $request->filters;
        $filters['section_id'] = Auth::check() ? Auth::user()->sections->pluck('id')->toArray() : [];
        $email = Auth::user()->email;
        $withLimit = isset($filters['page']);
        if ($withLimit) {
            $service = new ReportService();
            $result = $service->filterResults($filters, true);
            return $result;
        }
        // $email = 'swati96m@gmail.com';

        /* $fileName = 'public/exports/' . date('YmdHis') . '.xlsx';
        $service = new ReportService();
        $rows = [];
        $results = $service->filterResults($filters, false);

        foreach ($results as $index => $item) {
            $rows[] = [
                'old property id' => $item->old_propert_id,
                'unique property id' => $item->unique_propert_id,
                'land type' => $item->land_type,
                'status' => $item->status,
                'lease tenure' => $item->lease_tenure,
                'land use' => $item->land_use,
                'area' => $item->area_in_sqm,
                'address' => $item->address,
                'lesse name' => $item->lesse_name,
                'gr in re rs' => $item->gr_in_re_rs,
                'gr' => $item->gr,
            ];
        }
        // } while ($page < 3); //while ($page < 3); while (count($results) == $chunkSize);
        if (!empty($rows)) {
            Storage::makeDirectory('public/exports');
            (new FastExcel($rows))->export(Storage::path($fileName));
            if (!is_null($email)) {
                $link = '/download/' . base64_encode($fileName);
                Mail::to($email)->send(new DownloadReady($link));
            }
        } */

        dispatch(new JobsReportExport($filters, $email));
        // dispatch(new \App\Jobs\ReportExport($filters, $email));

        return response()->json(['status' => 'Creating export. You will recieve the email when export is ready']);
        /* $fileName = 'public/exports/' . now()->timestamp . '.xls';
        $page = 1;
        $service = new ReportService();
        $rows = [];
        $chunkSize = 1000;
        $filters['limit'] = $chunkSize;
        $results = $service->filterResults($filters)['rows'];
        do {
            $results = $service->filterResults($filters)['rows'];
            $page++;
            if (is_null($results)) break;

            foreach ($results as $index => $item) {
                // dd($chunk);
                // foreach ($chunk as $item) {
                $rows[] = [
                    'old_propert_id' => $item->old_propert_id,
                    'unique_propert_id' => $item->unique_propert_id,
                    'land_type' => $item->land_type,
                    'status' => $item->status,
                    'lease_tenure' => $item->lease_tenure,
                    'land_use' => $item->land_use,
                    'area' => $item->area_in_sqm,
                    'address' => $item->address,
                    'lesse_name' => $item->lesse_name,
                    'gr_in_re_rs' => $item->gr_in_re_rs,
                    'gr' => $item->gr,
                ];
                // }
            }
            // If you want to write each chunk to the file progressively
            // You can do so here depending on the format.

        } while ($page < 3); //while (count($results) == $chunkSize);
        $export = new ReportExport(collect($rows));
        Excel::store($export, $fileName, 'local'); */
    }


    //For exporting the property details START ***************************************************************************
    //Sourav Chauhan - 4/June/2024
    /*public function detailsExport(Request $request, ReportService $reportService)
    {
        try {
            $format = $request->format;
            $rows = []; //header rows
            $filters = $request->filters ?? [];


            $loginUser = Auth::user();

            if (isset($filters['colonyNameOld'])) {
                // dd("inside if");
                if ($loginUser->can('view.all.details')) {
                    $propertyDetails = PropertyMaster::query()
                        ->when($filters['colonyNameOld'], function ($q) use ($filters) {
                            $q->where('new_colony_name', $filters['colonyNameOld']);
                        })->with(['propertyLeaseDetail', 'propertyTransferredLesseeDetails', 'propertyInspectionDemandDetail', 'propertyMiscDetail', 'propertyContactDetail', 'splitedPropertyDetail', 'oldColony', 'user'])
                        ->get();

                } else {
                    $propertyDetails = PropertyMaster::query()
                        ->when($filters['colonyNameOld'], function ($q) use ($filters) {
                            $q->where('new_colony_name', $filters['colonyNameOld']);
                        })->where('created_by', $loginUser->id)
                        ->with(['propertyLeaseDetail', 'propertyTransferredLesseeDetails', 'propertyInspectionDemandDetail', 'propertyMiscDetail', 'propertyContactDetail', 'splitedPropertyDetail', 'oldColony', 'user'])
                        ->get();

                }

            } else {
                if (isset($filters['propId']) || isset($filters['date'])) {
                    // $propertyDetails = PropertyMaster::where('old_propert_id', 'like', '%'.$filters['propId'].'%')
                    // ->get();
                    if (isset($filters['date'])) {
                        // $dateTime = new \DateTime($filters['date']);
                        // $dateTime2 = new \DateTime($filters['dateEnd']);
                        // $formattedDate = $dateTime->format('d-m-Y');//production
                        $formattedDate = $filters['date'];//development
                        if (isset($filters['dateEnd'])) {

                            $formattedDateEnd = $filters['dateEnd'];//development
                        }
                        if ($loginUser->can('view.all.details')) {
                            if (isset($filters['dateEnd']) && isset($filters['date'])) {
                                $propertyDetails = PropertyMaster::query()
                                ->when(isset($formattedDate) && isset($formattedDateEnd), function ($q) use ($formattedDate, $formattedDateEnd) {
                                    $q->whereDate('created_at', '>=', $formattedDate)
                                      ->whereDate('created_at', '<=', $formattedDateEnd);
                                })->get();
                            
                            } else if (isset($filters['date'])) {
                                $propertyDetails = PropertyMaster::query()                          
                                ->when($filters['date'], function ($q) use ($formattedDate) {
                                    $q->where('created_at', 'like', '%' . $formattedDate . '%');
                                })->get();
                            }

                        } else {
                            if (isset($filters['dateEnd']) && isset($filters['date'])) {
                                $propertyDetails = PropertyMaster::query()
                                ->when(isset($formattedDate) && isset($formattedDateEnd), function ($q) use ($formattedDate, $formattedDateEnd) {
                                    $q->whereDate('created_at', '>=', $formattedDate)
                                      ->whereDate('created_at', '<=', $formattedDateEnd);
                                })->where('created_by', $loginUser->id)->get();
                            
                            } else if (isset($filters['date'])) {
                                $propertyDetails = PropertyMaster::query()                          
                                ->when($filters['date'], function ($q) use ($formattedDate) {
                                    $q->where('created_at', 'like', '%' . $formattedDate . '%');
                                })->where('created_by', $loginUser->id)->get();
                            }
                        }
                        // dd($propertyDetails);
                    } else {

                        if ($loginUser->can('view.all.details')) {
                            $propertyDetails = PropertyMaster::query()
                                ->when($filters['propId'], function ($q) use ($filters) {
                                    $q->where('old_propert_id', 'like', '%' . $filters['propId'] . '%')
                                        ->orWhere('unique_propert_id', 'like', '%' . $filters['propId'] . '%');
                                })->with(['propertyLeaseDetail', 'propertyTransferredLesseeDetails', 'propertyInspectionDemandDetail', 'propertyMiscDetail', 'propertyContactDetail', 'splitedPropertyDetail', 'oldColony', 'user'])
                                ->get();

                        } else {
                            $propertyDetails = PropertyMaster::query()
                                ->when($filters['propId'], function ($q) use ($filters) {
                                    $q->where('old_propert_id', 'like', '%' . $filters['propId'] . '%')
                                        ->orWhere('unique_propert_id', 'like', '%' . $filters['propId'] . '%');
                                })->where('created_by', $loginUser->id)
                                ->with(['propertyLeaseDetail', 'propertyTransferredLesseeDetails', 'propertyInspectionDemandDetail', 'propertyMiscDetail', 'propertyContactDetail', 'splitedPropertyDetail', 'oldColony', 'user'])
                                ->get();
                        }

                    }

                } else {
                    if ($loginUser->can('view.all.details')) {
                        $propertyDetails = PropertyMaster::with(['propertyLeaseDetail', 'propertyTransferredLesseeDetails', 'propertyInspectionDemandDetail', 'propertyMiscDetail', 'propertyContactDetail', 'splitedPropertyDetail', 'oldColony', 'user'])->get();
                    } else {
                        $propertyDetails = PropertyMaster::where('created_by', $loginUser->id)->with(['propertyLeaseDetail', 'propertyTransferredLesseeDetails', 'propertyInspectionDemandDetail', 'propertyMiscDetail', 'propertyContactDetail', 'splitedPropertyDetail', 'oldColony', 'user'])->get();
                    }
                }
            }
            // dd($propertyDetails);

            foreach ($propertyDetails as $propertyDetail) {
                // dd($propertyDetail);
                $name = '';
                $colconyName = '';
                $name = $propertyDetail->user->name;
                if(isset($filters['colonyNameOld'])){
                    $colconyName = OldColony::where('id', $filters['colonyNameOld'])->first();
                } else {
                    $colconyName = OldColony::where('id', $propertyDetail->new_colony_name)->first();
                }

                // $data = Item::whereIn('id', [$propertyDetail->property_type, $propertyDetail->property_sub_type, $propertyDetail->status, $propertyDetail->propertyLeaseDetail->unit])->get();
                $propertyType = Item::where('id', $propertyDetail->property_type)->first();
                $propertySubType = Item::where('id', $propertyDetail->property_sub_type)->first();
                $status = Item::where('id', $propertyDetail->status)->first();
                if(isset($propertyDetail->propertyLeaseDetail)){

                    $unit = Item::where('id', $propertyDetail->propertyLeaseDetail->unit)->first();
                } else {
                    $unit = '';
                }

                // Convert UTC time to IST using Carbon
                $utcTime = $propertyDetail->created_at; // Assuming $propertyDetail contains your UTC timestamp
                $istTime = $utcTime->setTimezone('Asia/Kolkata');

                //Latest Lessee
                $allLessees = $propertyDetail->propertyTransferredLesseeDetails;
                $lengthOfLessees = count($allLessees);
                $latestLessee = $allLessees[$lengthOfLessees - 1];

                //address
                $blockNo = isset($propertyDetail->block_no) ? $propertyDetail->block_no . '/' : '';
                $plotOrPropertyNo = isset( $propertyDetail->plot_or_property_no) ? $propertyDetail->plot_or_property_no . '/' : '';
                $colconyName = $colconyName->name ?? '';

                $rows[] = [
                    'Property Id' => $propertyDetail->unique_propert_id ?? '',
                    'Old Property Id' => $propertyDetail->old_propert_id ?? '',
                    'File Number' => $propertyDetail->unique_file_no ?? '',
                    'Old File Number' => $propertyDetail->file_no ?? '',
                    'Property Type' => isset($propertyDetail->property_type) ? isset($propertyType) ? $propertyType['item_name'] : '' : '',
                    'Property SubType' => isset($propertyDetail->property_sub_type) ? isset($propertySubType) ? $propertySubType['item_name'] : '' : '',
                    'Property Status' => isset($propertyDetail->status) ? isset($status) ? $status['item_name'] : '' : '',
                    'Section' => $propertyDetail->section_code ?? '',
                    'Address' => $blockNo . $plotOrPropertyNo . $colconyName,
                    'Premium (₹)' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->premium . '.' . $propertyDetail->propertyLeaseDetail->premium_in_paisa ?? $propertyDetail->propertyLeaseDetail->premium_in_aana : '',
                    'Ground Rent (₹)' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->gr_in_re_rs . '.' . $propertyDetail->propertyLeaseDetail->gr_in_paisa ?? $propertyDetail->propertyLeaseDetail->gr_in_aana : '',
                    'Area' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->plot_area . ' ' . (isset($unit) ? $unit['item_name'] : '') : '',
                    'Area in Sqm' => $propertyDetail->propertyLeaseDetail->plot_area_in_sqm ?? '',
                    'Block' => $propertyDetail->block_no ?? '',
                    'Plot' => $propertyDetail->plot_or_property_no ?? '',
                    'Colony' => $colconyName->name ?? '',
                    'Date Of Expiration' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->date_of_expiration : '',
                    'Date Of Execution' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->doe : '',
                    'Date Of Allotment' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->doa : '',
                    'Presently Known As' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->presently_known_as : '',
                    'Start Date Of GR' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->start_date_of_gr : '',
                    'RGR Duration' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->rgr_duration : '',
                    'First RGR Due On' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->first_rgr_due_on : '',
                    'Last Inspection Date' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->last_inspection_ir_date : '',
                    'Last Demand Letter Date' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->last_demand_letter_date : '',
                    'Last Demand Id' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->last_demand_id : '',
                    'Last Demand Amount' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->last_demand_amount : '',
                    'Last Amount Received' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->last_amount_received : '',
                    'Last Amount Received Date' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->last_amount_received_date : '',
                    'Total Dues' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->total_dues : '',
                    'Latest Lessee Name' => $latestLessee['lessee_name'] ?? '',
                    'Lessee Address' => $propertyDetail->propertyContactDetail->address ?? '',
                    'Lessee Phone' => $propertyDetail->propertyContactDetail->phone_no ?? '',
                    'Lessee Email' => $propertyDetail->propertyContactDetail->email ?? '',
                    'Entry By' => $name,
                    'Entry At' => $istTime->format('Y-m-d H:i:s')
                ];
            }

            // dd($rows);

            $export = new DetailExport([
                $rows

            ]);

            if ($format == 'csv') {
                return Excel::download($export, 'details.csv', \Maatwebsite\Excel\Excel::CSV, [
                    'Content-Type' => 'text/csv',
                ]);
            }
            if ($format == 'xls') {
                return Excel::download($export, 'report.xls', \Maatwebsite\Excel\Excel::XLS);
            }
            if ($format == 'pdf') {
                return Excel::download($export, 'report.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
            }
        } catch (\Exception $e) {
            dd($e);
            // return redirect()->back()->with('failure', $e->getMessage());
        }
    }*/
    //For exporting the property details END ***************************************************************************

    /*public function detailsExport(Request $request, ReportService $reportService)
    {
        try {
            $format = $request->format;
            $filters = $request->filters ?? [];
            $loginUser = Auth::user();

            // Determine the query to use based on the filters and user permissions
            $query = PropertyMaster::query()
                ->when(isset($filters['colonyNameOld']), function ($q) use ($filters, $loginUser) {
                    $q->where('new_colony_name', $filters['colonyNameOld']);
                    if (!$loginUser->can('view.all.details')) {
                        $q->where('created_by', $loginUser->id);
                    }
                })
                ->when(isset($filters['date']), function ($q) use ($filters, $loginUser) {
                    $formattedDate = $filters['date'];
                    $q->where('created_at', 'like', '%' . $formattedDate . '%');
                    if (!$loginUser->can('view.all.details')) {
                        $q->where('created_by', $loginUser->id);
                    }
                })
                ->when(isset($filters['propId']), function ($q) use ($filters, $loginUser) {
                    $q->where(function ($query) use ($filters) {
                        $query->where('old_propert_id', 'like', '%' . $filters['propId'] . '%')
                            ->orWhere('unique_propert_id', 'like', '%' . $filters['propId'] . '%');
                    });
                    if (!$loginUser->can('view.all.details')) {
                        $q->where('created_by', $loginUser->id);
                    }
                });

            // Add eager loading
            $query->with(['propertyLeaseDetail', 'propertyTransferredLesseeDetails', 'propertyInspectionDemandDetail', 'propertyMiscDetail', 'propertyContactDetail', 'splitedPropertyDetail', 'oldColony', 'user']);

            $rows = []; // Initialize rows array

            // Chunk the results to handle large datasets
            $query->chunk(5000, function ($propertyDetails) use (&$rows, $filters) {
                foreach ($propertyDetails as $propertyDetail) {
                    $name = '';
                    $colonyName = '';
                    $propertyType = $propertySubType = $status = $unit = '';
                    $name = $propertyDetail->user->name;
                    // if (isset($filters['colonyNameOld'])) {
                    //     $colonyName = OldColony::where('id', $filters['colonyNameOld'])->first();
                    // } else {
                    //     $colonyName = OldColony::where('id', $propertyDetail->new_colony_name)->first();
                    // }
                    // if (!empty($propertyDetail->property_type)) {
                    //     $propertyType = Item::where('id', $propertyDetail->property_type)->first();
                    // }
                    // if (!empty($propertyDetail->property_sub_type)) {
                    //     $propertySubType = Item::where('id', $propertyDetail->property_sub_type)->first();
                    // }
                    // if (!empty($propertyDetail->status)) {
                    //     $status = Item::where('id', $propertyDetail->status)->first();
                    // }
                    // if (!empty($propertyDetail->propertyLeaseDetail->unit)) {
                    //     $unit = Item::where('id', $propertyDetail->propertyLeaseDetail->unit)->first();
                    // }

                    // Convert UTC time to IST using Carbon
                    $utcTime = $propertyDetail->created_at; // Assuming $propertyDetail contains your UTC timestamp
                    $istTime = $utcTime->setTimezone('Asia/Kolkata');

                    // Latest Lessee
                    $allLessees = $propertyDetail->propertyTransferredLesseeDetails;
                    $latestLessee = $allLessees->isNotEmpty() ? $allLessees->last() : null;

                    $rows[] = [
                        'Property Id' => $propertyDetail->unique_propert_id ?? '',
                        'Old Property Id' => $propertyDetail->old_propert_id ?? '',
                        'File Number' => $propertyDetail->unique_file_no ?? '',
                        'Old File Number' => $propertyDetail->file_no ?? '',
                        'Property Type' => isset($propertyDetail->property_type) ? (isset($propertyType) ? '' : '') : '',
                        'Property SubType' => isset($propertyDetail->property_sub_type) ? (isset($propertySubType) ? '' : '') : '',
                        'Property Status' => isset($propertyDetail->status) ? (isset($status) ? '' : '') : '',
                        'Section' => $propertyDetail->section_code ?? '',
                        'Address' => $propertyDetail->block_no . '/' . $propertyDetail->plot_or_property_no  ?? '',
                        'Premium (₹)' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->premium . '.' . $propertyDetail->propertyLeaseDetail->premium_in_paisa ?? $propertyDetail->propertyLeaseDetail->premium_in_aana : '',
                        'Ground Rent (₹)' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->gr_in_re_rs . '.' . $propertyDetail->propertyLeaseDetail->gr_in_paisa ?? $propertyDetail->propertyLeaseDetail->gr_in_aana : '',
                        'Area' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->plot_area . ' ' . (isset($unit) ? '' : '') : '',
                        'Area in Sqm' => $propertyDetail->propertyLeaseDetail->plot_area_in_sqm ?? '',
                        'Block' => $propertyDetail->block_no ?? '',
                        'Plot' => $propertyDetail->plot_or_property_no ?? '',
                        'Colony' => '',
                        'Date Of Expiration' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->date_of_expiration : '',
                        'Date Of Execution' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->doe : '',
                        'Date Of Allotment' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->doa : '',
                        'Presently Known As' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->presently_known_as : '',
                        'Start Date Of GR' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->start_date_of_gr : '',
                        'RGR Duration' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->rgr_duration : '',
                        'First RGR Due On' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->first_rgr_due_on : '',
                        'Last Inspection Date' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->last_inspection_ir_date : '',
                        'Last Demand Letter Date' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->last_demand_letter_date : '',
                        'Last Demand Id' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->last_demand_id : '',
                        'Last Demand Amount' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->last_demand_amount : '',
                        'Last Amount Received' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->last_amount_received : '',
                        'Last Amount Received Date' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->last_amount_received_date : '',
                        'Total Dues' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->total_dues : '',
                        'Latest Lessee Name' => $latestLessee ? $latestLessee['lessee_name'] : '',
                        'Lessee Address' => $propertyDetail->propertyContactDetail->address ?? '',
                        'Lessee Phone' => $propertyDetail->propertyContactDetail->phone_no ?? '',
                        'Lessee Email' => $propertyDetail->propertyContactDetail->email ?? '',
                        'Entry By' => $name,
                        'Entry At' => $istTime->format('Y-m-d H:i:s')
                    ];
                }
            });

            $export = new DetailExport([
                $rows
            ]);

            if ($format == 'csv') {
                return Excel::download($export, 'details.csv', \Maatwebsite\Excel\Excel::CSV, [
                    'Content-Type' => 'text/csv',
                ]);
            }
            if ($format == 'xls') {
                return Excel::download($export, 'report.xls', \Maatwebsite\Excel\Excel::XLS);
            }
            if ($format == 'pdf') {
                return Excel::download($export, 'report.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
            }
        } catch (\Exception $e) {
            dd($e);
            // return redirect()->back()->with('failure', $e->getMessage());
        }
    }*/

    public function detailsExport(Request $request)
    {
        try {
            $format = $request->format;
            $filters = $request->filters ?? [];
            $loginUser = Auth::user();

            // Determine the query to use based on the filters and user permissions
            $query = PropertyMaster::query()
                ->when(isset($filters['colonyNameOld']), function ($q) use ($filters, $loginUser) {
                    $q->where('new_colony_name', $filters['colonyNameOld']);
                    if (!$loginUser->can('view.all.details')) {
                        $q->where('created_by', $loginUser->id);
                    }
                })
                ->when(isset($filters['date']), function ($q) use ($filters, $loginUser) {
                    $formattedDate = $filters['date'];
                    $q->where('created_at', 'like', '%' . $formattedDate . '%');
                    if (!$loginUser->can('view.all.details')) {
                        $q->where('created_by', $loginUser->id);
                    }
                })
                ->when(isset($filters['propId']), function ($q) use ($filters, $loginUser) {
                    $q->where(function ($query) use ($filters) {
                        $query->where('old_propert_id', 'like', '%' . $filters['propId'] . '%')
                            ->orWhere('unique_propert_id', 'like', '%' . $filters['propId'] . '%');
                    });
                    if (!$loginUser->can('view.all.details')) {
                        $q->where('created_by', $loginUser->id);
                    }
                    if ($loginUser->roles[0]->id == 7 || $loginUser->roles[0]->id == 8 || $loginUser->roles[0]->id == 9 || $loginUser->roles[0]->id == 10) {
                        $loginUserSections = $loginUser->sections;
                        $allSections = [];
                        $allSectionIds = [];
                        $allTypes = [];
                        $allSubTypes = [];
                        foreach ($loginUserSections as $loginUserSection) {
                            $sectionCode = $loginUserSection->section_code;
                            $allSections[] = $sectionCode;
                            $sectionId = $loginUserSection->id;
                            $allSectionIds[] = $sectionId;
                        }
                        $propertySectionMappings = PropertySectionMapping::whereIn('section_id', $allSectionIds)->get();
                        foreach ($propertySectionMappings as $propertySectionMapping) {
                            $type = $propertySectionMapping->property_type;
                            $allTypes[] = $type;
                            $subType = $propertySectionMapping->property_subtype;
                            $allSubTypes[] = $subType;
                        }
                        $q->whereIn('section_code', $allSections)->whereIn('property_type', $allTypes)->whereIn('property_sub_type', $allSubTypes);
                    }
                });
            // ->where('file_no', 'like', 'L-IIA%');
            // ->orWhere('file_no', 'like', 'L-IIB%')
            // ->orWhere('file_no', 'like', 'L-III%')
            // ->orWhere('file_no', 'like', 'L-IV%')
            // ->orWhere('file_no', 'like', 'L-V%')

            // Add eager loading
            $query->with(['propertyLeaseDetail', 'propertyTransferredLesseeDetails', 'propertyInspectionDemandDetail', 'propertyMiscDetail', 'propertyContactDetail', 'splitedPropertyDetail', 'oldColony', 'user']);
            $rows = []; // Initialize rows array

            // Chunk the results to handle large datasets
            $query->chunk(1000, function ($propertyDetails) use (&$rows, $filters) {
                foreach ($propertyDetails as $propertyDetail) {
                    $name = '';
                    $colonyName = '';
                    $propertyType = $propertySubType = $propertyTypeNew = $propertySubTypeNew = $status = $unit = '';
                    $name = $propertyDetail->user ? $propertyDetail->user->name : '';
                    if (isset($filters['colonyNameOld'])) {
                        $colonyName = OldColony::where('id', $filters['colonyNameOld'])->first();
                    } else {
                        $colonyName = OldColony::where('id', $propertyDetail->new_colony_name)->first();
                    }


                    if (!empty($propertyDetail->propertyLeaseDetail)) {
                        $propertyTypeData = Item::where('id', $propertyDetail->propertyLeaseDetail->property_type_as_per_lease)->first();
                        $propertyType = $propertyTypeData['item_name'];

                        $propertySubTypeData = Item::where('id', $propertyDetail->propertyLeaseDetail->property_sub_type_as_per_lease)->first();
                        $propertySubType = $propertySubTypeData['item_name'];
                        if (!empty($propertyDetail->propertyLeaseDetail->property_type_at_present)) {
                            $propertyTypeNewData = Item::where('id', $propertyDetail->propertyLeaseDetail->property_type_at_present)->first();
                            $propertyTypeNew = $propertyTypeNewData['item_name'];

                            $propertySubTypeNewData = Item::where('id', $propertyDetail->propertyLeaseDetail->property_sub_type_at_present)->first();
                            $propertySubTypeNew = $propertySubTypeNewData['item_name'];
                        }
                    }


                    // if (!empty($propertyDetail->property_sub_type)) {
                    //     $propertySubType = Item::where('id', $propertyDetail->property_sub_type)->first();
                    // }
                    if (!empty($propertyDetail->status)) {
                        $status = Item::where('id', $propertyDetail->status)->first();
                    }
                    if (!empty($propertyDetail->propertyLeaseDetail->unit)) {
                        $unit = Item::where('id', $propertyDetail->propertyLeaseDetail->unit)->first();
                    }
                    if (!empty($propertyDetail->propertyLeaseDetail->unit)) {
                        $typeOfLease = Item::where('id', $propertyDetail->propertyLeaseDetail->type_of_lease)->first();
                    }
                    if (!empty($propertyDetail->land_type)) {
                        $landType = Item::where('id', $propertyDetail->land_type)->first();
                    }

                    // Convert UTC time to IST using Carbon
                    $utcTime = $propertyDetail->created_at; // Assuming $propertyDetail contains your UTC timestamp
                    $istTime = $utcTime->setTimezone('Asia/Kolkata');

                    // Latest Lessee
                    // $allLessees = $propertyDetail->propertyTransferredLesseeDetails;
                    // $latestLessee = $allLessees->isNotEmpty() ? $allLessees->last() : null;

                    //added for getting the current lesse from current lessees table - SOURAV CHAUHAN (15/July/2024)
                    $latestLessee = CurrentLesseeDetail::where('property_master_id', $propertyDetail->id)->first();


                    //Splitted properties START - SOURAV CHAUHAN (20/sep/2024)****************
                    $jointPropertiesArray = [];
                    if ($propertyDetail->splitedPropertyDetail->isEmpty()) {
                    } else {
                        foreach ($propertyDetail->splitedPropertyDetail as $key => $chldProperty) {
                            if (!empty($chldProperty->old_property_id)) {
                                $jointPropertiesArray[] = $chldProperty->old_property_id;
                            }
                        }
                    }
                    $jointProperties = implode('/', $jointPropertiesArray);
                    //Splitted properties END - SOURAV CHAUHAN (20/sep/2024)****************

                    $rows[] = [
                        'Property Id' => $propertyDetail->unique_propert_id ?? '',
                        'Old Property Id' => $propertyDetail->old_propert_id ?? '',
                        'Joint Properties' => $jointProperties ?? '',
                        'File Number' => $propertyDetail->unique_file_no ?? '',
                        'Old File Number' => $propertyDetail->file_no ?? '',
                        'Land Type' => isset($landType) ? $landType['item_name'] : '',
                        'Property Status' => isset($propertyDetail->status) ? (isset($status) ? $status['item_name'] : '') : '',
                        'Property Type' => $propertyType,
                        'Property SubType' => $propertySubType,
                        'Is Land Use Changed' => isset($propertyDetail->propertyLeaseDetail->is_land_use_changed) ? 'Yes'  : 'No',
                        'Latest Property Type' => $propertyTypeNew,
                        'Latest Property SubType' => $propertySubTypeNew,
                        'Section' => $propertyDetail->section_code ?? '',
                        'Address' => $propertyDetail->block_no . '/' . $propertyDetail->plot_or_property_no . '/' . $colonyName->name ?? '',
                        'Premium (₹)' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->premium . '.' . $propertyDetail->propertyLeaseDetail->premium_in_paisa ?? $propertyDetail->propertyLeaseDetail->premium_in_aana : '',
                        'Ground Rent (₹)' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->gr_in_re_rs . '.' . $propertyDetail->propertyLeaseDetail->gr_in_paisa ?? $propertyDetail->propertyLeaseDetail->gr_in_aana : '',
                        'Area' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->plot_area . ' ' . (isset($unit) ? $unit['item_name'] : '') : '',
                        'Area in Sqm' => $propertyDetail->propertyLeaseDetail->plot_area_in_sqm ?? '',
                        'Colony' => $colonyName->name ?? '',
                        'Block' => $propertyDetail->block_no ?? '',
                        'Plot' => $propertyDetail->plot_or_property_no ?? '',
                        'Presently Known As' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->presently_known_as : '',
                        'Lease Type' => isset($typeOfLease) ? $typeOfLease['item_name'] : '',
                        'Date Of Allotment' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->doa : '',
                        'Date Of Execution' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->doe : '',
                        'Date Of Expiration' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->date_of_expiration : '',
                        'Start Date Of GR' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->start_date_of_gr : '',
                        'RGR Duration' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->rgr_duration : '',
                        'First RGR Due On' => isset($propertyDetail->propertyLeaseDetail) ? $propertyDetail->propertyLeaseDetail->first_rgr_due_on : '',
                        'Last Inspection Date' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->last_inspection_ir_date : '',
                        'Last Demand Letter Date' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->last_demand_letter_date : '',
                        'Last Demand Id' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->last_demand_id : '',
                        'Last Demand Amount' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->last_demand_amount : '',
                        'Last Amount Received' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->last_amount_received : '',
                        'Last Amount Received Date' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->last_amount_received_date : '',
                        'Total Dues' => isset($propertyDetail->propertyInspectionDemandDetail) ? $propertyDetail->propertyInspectionDemandDetail->total_dues : '',
                        'Latest Lessee Name' => $latestLessee ? $latestLessee['lessees_name'] : '',
                        'Lessee Address' => $propertyDetail->propertyContactDetail->address ?? '',
                        'Lessee Phone' => $propertyDetail->propertyContactDetail->phone_no ?? '',
                        'Lessee Email' => $propertyDetail->propertyContactDetail->email ?? '',
                        'Entry By' => $name,
                        'Entry At' => $istTime->format('Y-m-d H:i:s')
                    ];
                }
            });

            if (!empty($rows)) {
                return (new FastExcel($rows))->download('details.csv');
            }
        } catch (\Exception $e) {
            dd($e);
            // return redirect()->back()->with('failure', $e->getMessage());
        }
    }

    public function detailedReport(Request $request, ColonyService $colonyService, MisService $misService, ReportService $reportService)
    {
        /* if (!empty($request->all())) {
            dd($request->all());
        } */
        $filters = [];
        $user = Auth::user();
        $filters = $request->except(['export']);
        $data['sections'] = getRequiredSections(); //changed by Swati as common function to fetch lese and property section is added in common function on 20-03-2025
        $sections = $user->hasAnyRole('section-officer', 'deputy-lndo') ? $user->sections : $data['sections'];
        $clearSectionIdFilter = false;
        // $sections = $user->hasAnyRole('section-officer', 'deputy-lndo') ? $user->sections : Section::all();


        //modified by Nitin on 21-04-2025. This was selecting all the sections in frontend
        /* if (!empty($sections) && !(isset($filters['section_id']) && count($filters['section_id']) > 0)) {
            //if section is not included in filter request
            $filters['section_id'] = $sections->pluck('id')->toArray();
            $clearSectionIdFilter = true;
        } */

        if ($request->export == 1) {
            $email = $user->email;
            // $email = Auth::user()->email;
            dispatch(new DetailedReportExport($filters, $email));
            return redirect()->back()->with('success', 'Generating the report. Download link will be sent to your email id');
        }
        $properties = $reportService->detailedReport($filters);
        $subtypes = [];
        if (isset($filters['property_type'])) {
            $subtypes = $reportService->getDistinctSubTypes($filters['property_type']);
        }
        if ($clearSectionIdFilter) {
            unset($filters['section_id']);
        }
        $data['colonyList'] = $colonyService->misDoneForColonies($user->hasAnyRole('section-officer', 'deputy-lndo'));
        $data['propertyStatus'] = $misService->getItemsByGroupId(109);
        $data['landTypes'] = $misService->getItemsByGroupId(1051);
        $data['leaseTypes'] = $misService->getItemsByGroupId(102);
        $data['propertyTypes'] = $misService->getItemsByGroupId(1052);
        $data['propertySubtypes'] = $subtypes;
        $data['properties'] = $properties;
        $data['sections'] = $sections;
        $data['total'] = $properties['total'];
        $data['filters'] = $filters;
        return view('report.detailed-report', $data);
    }
    public function detailedReportExport(Request $request, ReportService $reportService)
    {
        $filters = [];
        $user = Auth::user();
        $filters = $request->except(['page', 'perpage']);
        $data['sections'] = getRequiredSections(); //changed by Swati as common function to fetch lese and property section is added in common function on 20-03-2025
        $sections = $user->hasAnyRole('section-officer', 'deputy-lndo') ? $user->sections : $data['sections'];
        $page = (int)$request->page;
        $perpage = (int)$request->perpage;


        //modified by Nitin on 21-04-2025. This was selecting all the sections in frontend
        // if (!empty($sections) && !(isset($filters['section_id']) && count($filters['section_id']) > 0)) {
        //     //if section is not included in filter request
        //     $filters['section_id'] = $sections->pluck('id')->toArray();
        // }
        $data = $reportService->detailedReport($filters, false, $perpage, $page);
        return $data;
    }
    public function download($file)
    {
        $fileName = base64_decode($file);
        if (Storage::exists($fileName)) {
            return Storage::download($fileName);
        } else {
            abort(404);
        }
    }

    /* public function customizeReport(Request $request, ReportService $reportService)
    {
       
        $filters = [];

        $filters = $request->except(['export']);
        
        if (count($request->all()) == 0) {
            $data['reportTypes'] = ['SWPC' => 'Sectionwise property count', "CLHFHUA" => "Colonywise lease hold, free hold, unallotted count", "TWPC" => 'Typewise property count', "PIAS" => 'Property in a section'];
            $data['sections'] = getRequiredSections(); //changed by Swati as common function to fetch lese and property section is added in common function on 20-03-2025
            return view('report.customize-report', $data);
        } else {
            $reportType = $request->report_type;
            $section = $request->section;
            $export = $request->export == 1;
            $page = $request->page_no ?? false;
            switch ($reportType) {
                case 'SWPC':
                    $data = $reportService->sectionwisePropertyCount($export, $page);
                    if (!$export)
                        return response()->json($data);
                    else {
                        $exportArray = [];
                        foreach ($data as $i => $row) {
                            // $row = $row->except('id');
                            unset($row->id);
                            foreach ($row as $key => $value) {
                                $exportArray[$i][ucwords(str_replace('_', ' ', $key))] = $value;
                            }
                        }
                        if (!empty($exportArray)) {
                            return (new FastExcel($exportArray))->download('SWPC.xlsx');
                        } else {
                            return redirect()->back()->with('failure', 'No data found');
                        }
                    }
                    break;
                case "CLHFHUA":
                    $data = $reportService->colonyWisePropertyReport($export, $page);
                    if (!$export)
                        return response()->json($data);
                    else {
                        if (!empty($data)) {
                            return (new FastExcel($data))->download('CLHFHUA.xlsx');
                        } else {
                            return redirect()->back()->with('failure', 'No data found');
                        }
                    }
                    break;
                case "TWPC":
                    $data = $reportService->typewisePropertyCount($export);
                    if (!$export)
                        return response()->json($data);
                    else {
                        $exportArray = [];
                        foreach ($data as $i => $row) {
                            foreach ($row as $key => $value) {
                                $exportArray[$i][ucwords(str_replace('_', ' ', $key))] = $value;
                            }
                        }
                        if (!empty($exportArray)) {
                            return (new FastExcel($exportArray))->download('TWPC.xlsx');
                        } else {
                            return redirect()->back()->with('failure', 'No data found');
                        }
                    }
                    break;
                //Add new case for report type Property In A Section - Lalit (11/March/2025)
                case 'PIAS':
                    $data = $reportService->propertyInASectionCount($export, $section, $page);
                    if (!$export)
                        return response()->json($data);
                    else {
                        //Get Section Code
                        if (!empty($section)) {
                            $sectionCode = Section::where('id', $section)->value('section_code');
                        } else {
                            $sectionCode = 'section';
                        }

                        if (!empty($data)) {
                            return (new FastExcel($data))->download('Property_In_A_' . $sectionCode . '.xlsx');
                        } else {
                            return redirect()->back()->with('failure', 'No data found');
                        }
                    }
                    break;

                default:
                    # code...
                    break;
            }
        }
        
    } */

    public function customizeReport(Request $request, ReportService $reportService)
    {
        if ($request->export == 1) {
            $rules = [
                'report_type' => 'required',
            ];

            if ($request->report_type === 'PIAS') {
                $rules['section'] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $reportType = $request->report_type;
            $section = $request->section;
            $export = $request->export == 1;
            $page = $request->page_no ?? false;
            $format = $request->export_format ?? 'csv';
            switch ($reportType) {
                case 'SWPC':
                    $data = $reportService->sectionwisePropertyCount($export, $page);
                    $exportArray = [];
                    foreach ($data as $i => $row) {
                        unset($row->id);
                        $serial = $i + 1;
                        $exportArray[$i]['S.No'] = $serial; // Add serial number
                        foreach ($row as $key => $value) {
                            $exportArray[$i][ucwords(str_replace('_', ' ', $key))] = $value;
                        }
                    }

                    if (!empty($exportArray)) {
                        if ($format === 'csv') {
                            return (new FastExcel($exportArray))->download("Section_Wise_Property_Count.csv");
                        }
                    } else {
                        return redirect()->back()->with('failure', 'No data available to export.');
                    }
                    break;
                case "CLHFHUA":
                    $data = $reportService->colonyWisePropertyReport($export, $page);

                    if (!empty($data)) {
                        $exportArray = [];
                        $serial = 1;

                        foreach ($data as $row) {
                            $entry = [
                                'S.No' => $serial++,
                                'Colony Name' => $row['Colony Name'] ?? '',
                                'Lease Hold'  => $row['Lease Hold'] ?? 0,
                                'Free Hold'   => $row['Free Hold'] ?? 0,
                                'Unallotted'  => $row['Unallotted'] ?? 0,
                                'Total'       => $row['Total'] ?? 0,
                            ];
                            $exportArray[] = $entry;
                        }

                        if (!empty($exportArray)) {
                            if ($format === 'csv') {
                                return (new FastExcel($exportArray))->download("Colony_Wise_Lease_Hold_Free_Hold_And_Unalloted_Property_Count.csv");
                            }
                        }
                    } else {
                        return redirect()->back()->with('failure', 'No data available to export.');
                    }

                    break;
                case "TWPC":
                    $data = $reportService->typewisePropertyCount($export);
                    $exportArray = [];
                    foreach ($data as $i => $row) {
                        $serial = $i + 1;
                        $exportArray[$i]['S.No'] = $serial;
                        foreach ($row as $key => $value) {
                            $exportArray[$i][ucwords(str_replace('_', ' ', $key))] = $value;
                        }
                    }

                    if (!empty($exportArray)) {
                        if ($format === 'csv') {
                            return (new FastExcel($exportArray))->download("Property_Type_Wise_Count.csv");
                        }
                    } else {
                        return redirect()->back()->with('failure', 'No data available to export.');
                    }
                    break;
                //Add new case for report type Property In A Section - Lalit (11/March/2025)
                case 'PIAS':
                    /* $data = $reportService->propertyInASectionCount($export, $section, $page);
                    $formattedExport = collect($data)->map(function ($item, $index) {
                        return [
                            'S.No'               => $index + 1,
                            'Unique Property ID' => $item->unique_propert_id ?? '',
                            'Property ID'        => $item->property_id ?? '',
                            'Land Type'          => $item->land_type ?? '',
                            'Property Status'    => $item->property_status ?? '',
                            'Property Type'      => $item->property_type ?? '',
                            'Property Sub Type'  => $item->property_sub_type ?? '',
                            'Area in SQM'        => isset($item->area_in_sqm) ? number_format((float) $item->area_in_sqm, 2) : '0.00',
                            'Date of Execution'  => !empty($item->date_of_execution) ? \Carbon\Carbon::parse($item->date_of_execution)->format('d/m/Y') : '',
                            'Current Lesse Name' => $item->current_lesse_name ?? '',
                            'Is Joint Property'  => isset($item->is_joint_property) ? ($item->is_joint_property == 1 ? 'Yes' : 'No') : 'No',
                        ];
                    })->toArray();

                    if (!empty($formattedExport)) {
                        if ($format === 'csv') {
                            return (new FastExcel($formattedExport))->download('Property_In_A_Section.csv');
                        }
                    } else {
                        return redirect()->back()->with('failure', 'No data available to export.');
                    }
                    break; */

                    // Alternative using the new function
                    $data = $reportService->propertyInASectionCountNew($section);

                    if ($data && $data->isNotEmpty()) {
                        $formattedExport = $data->map(function ($item, $index) {
                            return [
                                'S.No'               => $index + 1,
                                'Unique Property ID' => $item['unique_propert_id'] ?? '',
                                'Property ID'        => $item['property_id'] ?? '',
                                'Land Type'          => $item['land_type'] ?? '', // This already contains the name
                                'Property Status'    => $item['property_status'] ?? '', // This already contains the name
                                'Property Type'      => $item['property_type'] ?? '', // This already contains the name
                                'Property Sub Type'  => $item['property_sub_type'] ?? '', // This already contains the name
                                'Area in SQM'        => $item['area_in_sqm'] ?? '0.00',
                                'Date of Execution'  => $item['date_of_execution'] ?? '',
                                'Current Lesse Name' => $item['current_lesse_name'] ?? '',
                                'Is Joint Property'  => isset($item['is_joint_property']) ? ($item['is_joint_property'] == 1 ? 'Yes' : 'No') : 'No',
                                'Section Code'       => $item['section_code'] ?? '',
                                'Address'            => $item['address'] ?? '',
                            ];
                        })->toArray();

                        if (!empty($formattedExport)) {
                            if ($format === 'csv') {
                                return (new FastExcel($formattedExport))->download('Property_In_A_Section.csv');
                            }
                        } else {
                            return redirect()->back()->with('failure', 'No data available to export.');
                        }
                    }
                    break;

                default:
                    # code...
                    break;
            }
        } else {
            $data['reportTypes'] = ['SWPC' => 'Section-wise Property Count', "CLHFHUA" => "Colony-wise Leasehold, Freehold, and Unallotted Count", "TWPC" => 'Property Type-wise Count', "PIAS" => 'Properties in a Section'];
            $data['sections'] = getRequiredSections(); //changed by Swati as common function to fetch lese and property section is added in common function on 20-03-2025
            return view('report.customize-report-new', $data);
        }
    }

    public function getCustomizeReportData(Request $request, ReportService $reportService)
    {
        $reportType = $request->input('report_type');
        $section = $request->input('section');
        $search = $request->input('search.value');

        switch ($reportType) {
            case 'SWPC':
                $query = $reportService->sectionwisePropertyCountNew($search);
                break;
            case 'CLHFHUA':
                $query = $reportService->colonyWisePropertyReportNew($search);
                break;
            case 'TWPC':
                $query = $reportService->typewisePropertyCountNew($search);
                break;
            case 'PIAS':
                $query = $reportService->propertyInASectionCountNew($section, $search);
                // Apply default order if not set by DataTables request
                if (!$request->has('order')) {
                    $query = $query->sortBy(function ($item) {
                        // return $item->unique_propert_id; // This is the 2nd column (index 1)
                        return $item['unique_propert_id']; // Use array syntax [] instead of object syntax ->
                    });
                }
                break;
            default:
                return response()->json(['error' => 'Invalid report type'], 400);
        }

        return DataTables::of($query)->make(true);
    }

    public function unallotedPropertyView(Request $request, ColonyService $colonyService)
    {
        $unallotedColonyIds = PropertyMaster::where('status', 1476)
            ->pluck('new_colony_name');

        $colonyList = $colonyService->getColonyList()
            ->whereIn('id', $unallotedColonyIds)
            ->values();
        return view('report.unalloted-properties-index', compact(['colonyList']));
    }

    /* public function getUnallotedProperties(Request $request)
    {
        $query = UnallottedPropertyDetail::query()
            ->leftJoin('property_masters', 'unallotted_property_details.property_master_id', '=', 'property_masters.id')
            ->leftJoin('items', 'property_masters.land_type', '=', 'items.id')
            ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
            ->leftJoin('departments', 'unallotted_property_details.transferred_to', '=', 'departments.id')
            ->select(
                'unallotted_property_details.old_property_id',
                'unallotted_property_details.plot_area_in_sqm',
                'unallotted_property_details.is_litigation',
                'unallotted_property_details.is_encrached',
                'unallotted_property_details.is_vaccant',
                'unallotted_property_details.is_transferred',
                'unallotted_property_details.transferred_to',
                'unallotted_property_details.is_property_document_exist',
                'unallotted_property_details.date_of_transfer',
                'unallotted_property_details.purpose',
                'unallotted_property_details.created_at',
                'property_masters.unique_propert_id',
                'property_masters.land_type',
                'property_masters.new_colony_name',
                'items.item_name as landType',
                'old_colonies.name as colonyName',
                'departments.name as departmentName',
            );

        if ($request->has('locality_record') && $request->locality_record != '') {
            $query->where('property_masters.new_colony_name', $request->locality_record);
        }

        // Define the searchable columns
        $searchableColumns = [
            'unallotted_property_details.old_property_id',
            'unallotted_property_details.date_of_transfer',
            'unallotted_property_details.purpose',
            'property_masters.unique_propert_id',
            'property_masters.land_type',
            'items.item_name',
            'old_colonies.name',
            'departments.name',
        ];

        $totalData = $query->count();
        $totalFiltered = $totalData;

        // Handle pagination, ordering, and filtering
        $limit = $request->input('length');
        $start = $request->input('start');
        $orderColumnIndex = $request->input('order.0.column');
        $dir = $request->input('order.0.dir');

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'LIKE', "%{$search}%");
                }
            });
            $totalFiltered = $query->count();
        }

        $getUnallotedPropertyData = $query->offset($start)
            ->limit($limit)
            ->orderBy($searchableColumns[$orderColumnIndex] ?? 'unallotted_property_details.created_at', $dir)
            ->get();

        $counter = 1; // Initialize counter for auto-increment
        $data = [];
        foreach ($getUnallotedPropertyData as $property) {
            $propertyHTML = $documentHTML = '';
            $nestedData['id'] = $counter++; // Auto-incremented ID
            $propertyHTML .= '<div class="text-primary">' . $property->unique_propert_id . '</div><span class="text-secondary">(' . $property->old_property_id . ')</span>';
            $nestedData['unique_propert_id'] = $propertyHTML;
            // $nestedData['unique_propert_id'] = $property->unique_propert_id;
            // $nestedData['old_property_id'] = $property->old_property_id;
            $nestedData['landType'] = $property->landType;
            $nestedData['colonyName'] = $property->colonyName;
            $nestedData['is_property_document_exist'] = $property->is_property_document_exist ? 'Yes' : 'No';
            $nestedData['plot_area_in_sqm'] = $property->plot_area_in_sqm;
            $nestedData['is_vaccant'] = $property->is_vaccant ? 'Yes' : 'No';
            if (!empty($property->is_transferred)) {
                $documentHTML .= !empty($property->departmentName)
                    ? '<div class="text-secondary">' . htmlspecialchars($property->departmentName) . '</div>'
                    : '';

                $documentHTML .= !empty($property->date_of_transfer)
                    ? '<div><span class="text-secondary">Transfer Date : ' . \Carbon\Carbon::parse($property->date_of_transfer)->format('d/m/Y') . '</span></div>'
                    : '';

                $documentHTML .= !empty($property->purpose)
                    ? '<div><span class="text-secondary">Purpose : ' . htmlspecialchars($property->purpose) . '</span></div>'
                    : '';
            } else {
                $documentHTML .= '<span>No</span>';
            }

            $nestedData['is_transferred'] = $documentHTML;
            // $nestedData['is_transferred'] = $property->is_transferred ? $property->departmentName : 'Null';
            $nestedData['is_encrached'] = $property->is_encrached ? 'Yes' : 'No';
            $nestedData['is_litigation'] = $property->is_litigation ? 'Yes' : 'No';
            $nestedData['old_property_id_raw'] = $property->old_property_id; // added by swati mishra on 23052025 for adding survey details
            $data[] = $nestedData;
        }

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        ];

        return response()->json($json_data);
    } */

    public function getUnallotedProperties(Request $request)
    {
        $query = UnallottedPropertyDetail::query()
            ->leftJoin('property_masters', 'unallotted_property_details.property_master_id', '=', 'property_masters.id')
            ->leftJoin('items', 'property_masters.land_type', '=', 'items.id')
            ->leftJoin('old_colonies', 'property_masters.new_colony_name', '=', 'old_colonies.id')
            ->leftJoin('departments', 'unallotted_property_details.transferred_to', '=', 'departments.id')
            ->select(
                'unallotted_property_details.old_property_id',
                'unallotted_property_details.plot_area_in_sqm',
                'unallotted_property_details.is_litigation',
                'unallotted_property_details.is_encrached',
                'unallotted_property_details.is_vaccant',
                'unallotted_property_details.is_transferred',
                'unallotted_property_details.transferred_to',
                'unallotted_property_details.is_property_document_exist',
                'unallotted_property_details.date_of_transfer',
                'unallotted_property_details.purpose',
                'unallotted_property_details.created_at',
                'property_masters.unique_propert_id',
                'property_masters.land_type',
                'property_masters.new_colony_name',
                'items.item_name as landType',
                'old_colonies.name as colonyName',
                'departments.name as departmentName',
            );

        if ($request->has('locality_record') && $request->locality_record != '') {
            $query->where('property_masters.new_colony_name', $request->locality_record);
        }

        // Define the searchable columns
        $searchableColumns = [
            'unallotted_property_details.old_property_id',
            'unallotted_property_details.date_of_transfer',
            'unallotted_property_details.purpose',
            'property_masters.unique_propert_id',
            'property_masters.land_type',
            'items.item_name',
            'old_colonies.name',
            'departments.name',
        ];

        $totalData = $query->count();
        $totalFiltered = $totalData;

        // Handle pagination, ordering, and filtering
        $limit = $request->input('length');
        $start = $request->input('start');
        $orderColumnIndex = $request->input('order.0.column');
        $dir = $request->input('order.0.dir');

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'LIKE', "%{$search}%");
                }
            });
            $totalFiltered = $query->count();
        }

        $getUnallotedPropertyData = $query->offset($start)
            ->limit($limit)
            ->orderBy($searchableColumns[$orderColumnIndex] ?? 'unallotted_property_details.created_at', $dir)
            ->get();

        // Calculate starting counter based on current page
        $counter = $start + 1; // This ensures continuous numbering across pages

        $data = [];
        foreach ($getUnallotedPropertyData as $property) {
            $propertyHTML = $documentHTML = '';
            $nestedData['id'] = $counter++; // Auto-incremented ID with continuous numbering
            $propertyHTML .= '<div class="text-primary">' . $property->unique_propert_id . '</div><span class="text-secondary">(' . $property->old_property_id . ')</span>';
            $nestedData['unique_propert_id'] = $propertyHTML;
            $nestedData['landType'] = $property->landType;
            $nestedData['colonyName'] = $property->colonyName;
            $nestedData['is_property_document_exist'] = $property->is_property_document_exist ? 'Yes' : 'No';
            $nestedData['plot_area_in_sqm'] = $property->plot_area_in_sqm;
            $nestedData['is_vaccant'] = $property->is_vaccant ? 'Yes' : 'No';

            if (!empty($property->is_transferred)) {
                $documentHTML .= !empty($property->departmentName)
                    ? '<div class="text-secondary">' . htmlspecialchars($property->departmentName) . '</div>'
                    : '';

                $documentHTML .= !empty($property->date_of_transfer)
                    ? '<div><span class="text-secondary">Transfer Date : ' . \Carbon\Carbon::parse($property->date_of_transfer)->format('d/m/Y') . '</span></div>'
                    : '';

                $documentHTML .= !empty($property->purpose)
                    ? '<div><span class="text-secondary">Purpose : ' . htmlspecialchars($property->purpose) . '</span></div>'
                    : '';
            } else {
                $documentHTML .= '<span>No</span>';
            }

            $nestedData['is_transferred'] = $documentHTML;
            $nestedData['is_encrached'] = $property->is_encrached ? 'Yes' : 'No';
            $nestedData['is_litigation'] = $property->is_litigation ? 'Yes' : 'No';
            $nestedData['old_property_id_raw'] = $property->old_property_id;
            $data[] = $nestedData;
        }

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        ];

        return response()->json($json_data);
    }
    // added by Swati Mishra on 23052025 for fetching survey details of unalloted properties

    public function getSurveyDetails($property_id)
    {
        $survey = SurveyDetail::with(['colony', 'observations'])
            ->where('property_id', $property_id)
            ->first();

        if (!$survey) {
            return response()->json(['html' => '<p>No survey records found.</p>']);
        }

        $colonyName = $survey->colony->name ?? 'N/A';

        $html = '<div class="table-responsive">';
        $html .= '<table class="table table-bordered table-striped w-100">';
        $html .= '<tr>
                    <th style="width: 25%;">Colony</th>
                    <td style="width: 25%;">' . $colonyName . ' <span class="text-muted">(' . $survey->property_id . ')</span></td>
                    <th style="width: 25%;">Survey ID</th>
                    <td style="width: 25%;">' . $survey->survey_id . '</td>
                </tr>';
        $html .= '<tr>
                    <th>Surveyor Name</th>
                    <td>' . $survey->surveyor_name . '</td>
                    <th>Survey Date</th>
                    <td>' . \Carbon\Carbon::parse($survey->surveyed_at)->format('d-m-Y') . '</td>
                </tr>';
        $html .= '</table></div>';

        if ($survey->observations->isNotEmpty()) {
            $html .= '<div class="mt-3"><h6>Observations</h6></div>';
            foreach ($survey->observations as $index => $obs) {
                $html .= '<div class="border rounded p-2 mb-3 d-flex justify-content-between align-items-start flex-wrap">';
                $html .= '<div class="me-3" style="flex: 1 1 65%;">';
                $html .= '<div class="mb-1"><strong>' . ($index + 1) . '.</strong> <strong>Category:</strong> ' . htmlspecialchars($obs->observation_category) . '</div>';
                $html .= '<div class="mb-1"><strong>Remarks:</strong> ' . htmlspecialchars($obs->remarks) . '</div>';
                $html .= '</div>';

                $html .= '<div style="flex: 0 0 auto; display: flex; gap: 10px;">';
                if (!empty($obs->image1)) {
                    $html .= '<img src="' . $obs->image1 . '" alt="Image 1" class="img-thumbnail expandable-img me-2" style="height: 100px; width: 150px; object-fit: cover;">';
                }
                if (!empty($obs->image2)) {
                    $html .= '<img src="' . $obs->image2 . '" alt="Image 2" class="img-thumbnail expandable-img me-2" style="height: 100px; width: 150px; object-fit: cover;">';
                }
                $html .= '</div>';
                $html .= '</div>';
            }
        }

        if (!empty($survey->latitude) && !empty($survey->longitude) && $survey->latitude != 0 && $survey->longitude != 0) {
            $html .= '<div class="mt-4"><h6>Location on Map</h6></div>';
            $html .= '<div id="propertyMap" style="height: 300px;" class="mt-2 rounded border"></div>';
            $html .= '<script>
                setTimeout(function() {
                    const map = new google.maps.Map(document.getElementById("propertyMap"), {
                        zoom: 16,
                        center: { lat: ' . floatval($survey->latitude) . ', lng: ' . floatval($survey->longitude) . ' }
                    });

                    new google.maps.Marker({
                        position: { lat: ' . floatval($survey->latitude) . ', lng: ' . floatval($survey->longitude) . ' },
                        map: map,
                        title: "' . addslashes($colonyName . ' (' . $survey->property_id . ')') . '"
                    });
                }, 500);
            </script>';
        }

        return response()->json(['html' => $html]);
    }


    /*Writing this function to update the current lessees of leased properties where conversion record got entered as blank and current lessee as blank 
    as blank string --Amita Srivastava & Sourav [09-01-2025]*/
    public function updateCurrentLesseeOfLeasedProperty()
    {

        $properties = PropertyMaster::where('status', 951)->get();
        foreach ($properties as $property) {
            $propertyId = $property->id;
            $isConversionRecordAvailable = PropertyTransferredLesseeDetail::where('property_master_id', $propertyId)->where('process_of_transfer', 'Conversion')->where('lessee_name', NULL)->get();
            if (count($isConversionRecordAvailable) > 0) {
                foreach ($isConversionRecordAvailable as $conversionRecord) {
                    if ($conversionRecord->delete()) {
                        echo "Conversion deleted for property ID:- " . $propertyId . "<br>";
                    }
                }
                self::updateNewCurrentLessee($propertyId);
            }
        }
    }

    //for updating the current lessee in seperate table
    public function updateNewCurrentLessee($id)
    {
        try {
            $propertyId = $id;
            $properties = PropertyMaster::where('id', $propertyId)->get();

            if ($properties->isEmpty()) {
                echo "No properties found for the given ID.";
            }

            foreach ($properties as $property) {

                //if property is joint
                if ($property->is_joint_property == 1) {

                    $splitedPropertyDetails = SplitedPropertyDetail::where('property_master_id', $property->id)->get();
                    foreach ($splitedPropertyDetails as $splitedPropertyDetail) {
                        // Property Details
                        $propertyMasterIdChild = $property->id;
                        $splitedPropertyDetailId = $splitedPropertyDetail->id;
                        $oldPropertyIdChild = $splitedPropertyDetail->old_property_id;
                        $propertyStatusChild = $splitedPropertyDetail->property_status;
                        $currentArea = $splitedPropertyDetail->current_area;
                        $areaInSqm = $splitedPropertyDetail->area_in_sqm;
                        $unitChild = $splitedPropertyDetail->unit;
                        $presentlyKnownAsChild = $splitedPropertyDetail->presently_known_as;
                        $createdByChild = $splitedPropertyDetail->created_by;


                        // Find lessees
                        $latestBatchIdChild = PropertyTransferredLesseeDetail::where('property_master_id', $property->id)->where('splited_property_detail_id', $splitedPropertyDetailId)->max('batch_transfer_id');
                        // if ($latestBatchIdChild === null) {
                        //     return "No lessee details found for property ID: {$property->id} and Splitted property id {$splitedPropertyDetailId}";
                        // }

                        if ($latestBatchIdChild) {

                            $lesseesWithLatestBatchIdChild = PropertyTransferredLesseeDetail::where('property_master_id', $property->id)
                                ->where('splited_property_detail_id', $splitedPropertyDetailId)
                                ->where('batch_transfer_id', $latestBatchIdChild)
                                ->pluck('lessee_name')
                                ->toArray();

                            $lesseesNamesChild = implode(",", $lesseesWithLatestBatchIdChild);
                            $currentLesseeDetailChild = CurrentLesseeDetail::where('property_master_id', $property->id)->where('splited_property_detail_id', $splitedPropertyDetail->id)->first();
                            // If current lessee details already saved
                            if (!empty($currentLesseeDetailChild)) {
                                if ($currentLesseeDetailChild['lessees_name'] != $lesseesNamesChild) {
                                    $currentLesseeDetailChild->property_status = $propertyStatusChild;
                                    $currentLesseeDetailChild->lessees_name = $lesseesNamesChild;
                                    $currentLesseeDetailChild->property_known_as = $presentlyKnownAsChild;
                                    $currentLesseeDetailChild->area = $currentArea;
                                    $currentLesseeDetailChild->unit = $unitChild;
                                    $currentLesseeDetailChild->area_in_sqm = $areaInSqm;
                                    $currentLesseeDetailChild->save();
                                }
                            } else {
                                CurrentLesseeDetail::create([
                                    'property_master_id' => $propertyMasterIdChild,
                                    'splited_property_detail_id' => $splitedPropertyDetailId,
                                    'old_property_id' => $oldPropertyIdChild,
                                    'property_status' => $propertyStatusChild,
                                    'lessees_name' => $lesseesNamesChild,
                                    'property_known_as' => $presentlyKnownAsChild,
                                    'area' => $currentArea,
                                    'unit' => $unitChild,
                                    'area_in_sqm' => $areaInSqm,
                                    'created_by' => $createdByChild
                                ]);
                            }
                        }
                    }
                } else {

                    // Property Details
                    $propertyMasterId = $property->id;
                    $oldPropertyId = $property->old_propert_id;
                    $propertyStatus = $property->status;

                    // Find lessees
                    $latestBatchId = PropertyTransferredLesseeDetail::where('property_master_id', $property->id)->max('batch_transfer_id');
                    if ($latestBatchId === null) {
                        echo "No lessee details found for property ID: {$property->id}";
                    }

                    $lesseesWithLatestBatchId = PropertyTransferredLesseeDetail::where('property_master_id', $property->id)
                        ->where('batch_transfer_id', $latestBatchId)
                        ->pluck('lessee_name')
                        ->toArray();

                    $lesseesNames = implode(",", $lesseesWithLatestBatchId);

                    // Details from lease details
                    $leaseDetails = PropertyLeaseDetail::where('property_master_id', $property->id)->first();
                    if ($leaseDetails === null) {
                        echo "No lease details found for property ID: {$property->id}";
                    }

                    $presentlyKnown = $leaseDetails['presently_known_as'] ?? '';
                    $plotArea = $leaseDetails['plot_area'] ?? 0;
                    $unit = $leaseDetails['unit'] ?? '';
                    $plotAreaInSqm = round($leaseDetails['plot_area_in_sqm'] ?? 0, 2);
                    $createdBy = $leaseDetails['created_by'] ?? '';

                    $currentLesseeDetail = CurrentLesseeDetail::where('property_master_id', $property->id)->first();

                    // If current lessee details already saved
                    if (!empty($currentLesseeDetail)) {
                        if ($currentLesseeDetail['lessees_name'] != $lesseesNames) {
                            $currentLesseeDetail->property_status = $propertyStatus;
                            $currentLesseeDetail->lessees_name = $lesseesNames;
                            $currentLesseeDetail->property_known_as = $presentlyKnown;
                            $currentLesseeDetail->area = $plotArea;
                            $currentLesseeDetail->unit = $unit;
                            $currentLesseeDetail->area_in_sqm = $plotAreaInSqm;
                            $currentLesseeDetail->save();
                        }
                    } else {
                        CurrentLesseeDetail::create([
                            'property_master_id' => $propertyMasterId,
                            'splited_property_detail_id' => null,
                            'old_property_id' => $oldPropertyId,
                            'property_status' => $propertyStatus,
                            'lessees_name' => $lesseesNames,
                            'property_known_as' => $presentlyKnown,
                            'area' => $plotArea,
                            'unit' => $unit,
                            'area_in_sqm' => $plotAreaInSqm,
                            'created_by' => $createdBy
                        ]);
                    }
                }
            }

            echo "Current lessee details updated successfully.";
        } catch (\Exception $e) {
            echo "An error occurred: " . $e->getMessage();
        }
    }

    /** function to show colonywise breakup of section properies to section user - Added by Nitin on 19-02-2025*/
    public function colonywiseSectionReport($sectionId)
    {
        $sectionCode = Section::where('id', $sectionId)->value('section_code');
        /* $data = DB::table('property_section_mappings', 'psm')
            ->join('property_masters as pm', function ($join) use ($sectionId) {
                return $join->on('pm.new_colony_name', '=', 'psm.colony_id')
                    ->on('pm.property_type', '=', 'psm.property_type')
                    ->on('pm.property_sub_type', '=', 'psm.property_subtype');
            })
            ->leftJoin('old_colonies as colony', 'pm.new_colony_name', '=', 'colony.id')
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->where('psm.section_id', $sectionId)
            ->select('colony.id as colonyId', 'colony.name as colony_name', DB::raw('count(pm.id) as counter'))
            ->groupBy('colony.id', 'colony.name')
            ->orderBy('colony.name')
            ->get(); */
        $data = DB::table('property_masters as pm')
            ->leftJoin('old_colonies as colony', 'pm.new_colony_name', '=', 'colony.id')
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->leftJoin('sections as s', 'pm.section_code', '=', 's.id') // Join sections to get section details
            ->where('pm.section_code', $sectionCode) // Filter by section_code in property_masters
            ->select(
                'colony.id as colonyId',
                'colony.name as colony_name',
                DB::raw('count(pm.id) as counter'),
                's.section_code' // Get section_code from sections table
            )
            ->groupBy('colony.id', 'colony.name', 's.section_code')
            ->orderBy('colony.name')
            ->get();
        return view('report.section-porperties-colonywise', ['data' => $data, 'sectionId' => $sectionId]);
    }
    //added by swati mishra for displaying dues in filter report by Swati Mishra 11-04-2025
    public function getDemandDetails(Request $request)
    {
        // dd($request);
        $propertyId = $request->property_id; // This is the old_property_id

        // Step 1: Check local demand using old_property_id
        $demand = Demand::where('old_property_id', $propertyId)
            ->whereIn('status', [
                getServiceType('DEM_PENDING'),
                getServiceType('DEM_PART_PAID')
            ])
            ->first();

        // dd($demand);

        if ($demand) {
            return response()->json([
                'status' => true,
                'source' => 'local',
                'data' => [
                    'demand_id' => $demand->unique_id,
                    'demand_date' => Carbon::parse($demand->created_at)->format('d-m-Y'),
                    'amount' => $demand->net_total,
                    'paid' => $demand->paid_amount,
                    'outstanding' => $demand->balance_amount
                ]
            ]);
        }


        // Step 2: Fetch from API using PropertyID (same as old_property_id)
        $pms = new PropertyMasterService();
        $apiData = $pms->getPreviousDemands($propertyId);

        if (
            $apiData &&
            isset($apiData->LatestDemanddetails) &&
            is_array($apiData->LatestDemanddetails) &&
            count($apiData->LatestDemanddetails) > 0
        ) {
            $first = $apiData->LatestDemanddetails[0];
            $paid = $first->AlreadyPaid_asperSection + $first->NTRP_Paid;

            // dd($apiData);

            return response()->json([
                'status' => true,
                'source' => 'live_api',
                'data' => [
                    'demand_id' => $first->DemandID,
                    'demand_date' => Carbon::parse($first->DemandDate)->format('d-m-Y'),
                    'amount' => $first->Amount,
                    'paid' => $paid,
                    'outstanding' => $first->Outstanding
                ]
            ]);
        }

        // Final fallback
        return response()->json([
            'status' => false,
            'message' => 'No demand records found.'
        ]);
    }

    public function colonyWiseIndex(MisService $misService, ColonyService $colonyService)
    {
        $colonyList = $colonyService->getColonyList();
        return view('report.colony-wise-report', compact(['colonyList']));
    }


    public function getColonyWisePropertyResults(Request $request, ReportService $reportService)
    {
        $filters = $request->filters;
        $user = Auth::check() ? Auth::user() : null;
        // $sections = $user->hasAnyRole('section-officer', 'deputy-lndo') ? $user->sections->pluck('section_code')->toArray() : [];
        // $filters['section_id'] = $sections;
        // $results = $reportService->colonyWiseFilterResults($filters, true);


        // Comment given below code by lalit on dated 12/08/2026
        /* if ($user->hasRole('it-cell')) {

            // IT-CELL: all sections where has_property = 1
            $filters['section_id'] = Section::where('has_property', 1)
                ->pluck('section_code')
                ->toArray();
        } elseif ($user->hasAnyRole('section-officer', 'deputy-lndo')) {

            // Section-based restriction
            $filters['section_id'] = $user->sections
                ->pluck('section_code')
                ->toArray();
        } else {

            // Optional: block other roles
            $filters['section_id'] = [];
        } */

        $filters['section_id'] = [];
        $filters['section_id'] = Section::where('has_property', 1)
            ->pluck('section_code')
            ->toArray();

        $results = $reportService->colonyWiseFilterResults($filters, true);

        // Start :- Worked on adding filter report user action logs - Lalit Tiwari (29/April/2025)
        $authUserName = Auth::user()->name;
        $descriptionParts = [];
        if (!empty($results['rows'])) {
            $descriptionParts = [];
            $authUserName = Auth::user()->name;

            // Helper function to fetch names from Items
            $pluckItemNames = fn($ids) => Item::whereIn('id', (array)$ids)->pluck('item_name')->toArray();

            // Helper for range values
            $rangeText = fn($label, $range) => "{$label}: (Min: {$range['min']}, Max: {$range['max']})";

            // Land Type (single value)
            if (!empty($filters['land_type'])) {
                $landName = Item::where('id', $filters['land_type'])->value('item_name');
                if ($landName) {
                    $descriptionParts[] = "Land: {$landName}";
                }
            }

            // Multi-value fields with IDs
            $multiFields = [
                'property_type' => 'Property Type',
                'property_sub_type' => 'Property Sub Type',
                'land_status' => 'Property Status',
                'leaseDeed' => 'Lease Deed'
            ];
            foreach ($multiFields as $field => $label) {
                if (!empty($filters[$field])) {
                    $names = $pluckItemNames($filters[$field]);
                    if ($names) {
                        $descriptionParts[] = "{$label}: " . implode(', ', $names);
                    }
                }
            }

            // Colony names
            if (!empty($filters['colony'])) {
                $colonies = OldColony::whereIn('id', (array)$filters['colony'])->pluck('name')->toArray();
                if ($colonies) {
                    $descriptionParts[] = 'Colony: ' . implode(', ', $colonies);
                }
            }

            // Direct single-value fields
            $simpleFields = [
                'name' => 'Name',
                'contact' => 'Contact',
                'propertyId' => 'Property ID',
                'propertyAddress' => 'Property Address'
            ];
            foreach ($simpleFields as $field => $label) {
                if (!empty($filters[$field])) {
                    $descriptionParts[] = "{$label}: {$filters[$field]}";
                }
            }

            // Range fields
            $rangeFields = [
                'land_size' => 'Land Size',
                'land_value' => 'Land Value',
                'groundRent' => 'Ground Rent',
                'outstandingDues' => 'Outstanding Dues',
                'date_of_execution' => 'Date Of Execution'
            ];
            foreach ($rangeFields as $field => $label) {
                if (!empty($filters[$field]['min']) || !empty($filters[$field]['max'])) {
                    $descriptionParts[] = $rangeText($label, $filters[$field]);
                }
            }

            // List-based text filters
            if (!empty($filters['reEnteredSince']) && is_array($filters['reEnteredSince'])) {
                $descriptionParts[] = 'Re-entered Since: ' . implode(', ', $filters['reEnteredSince']);
            }
            if (!empty($filters['leaseTenure']) && is_array($filters['leaseTenure'])) {
                $descriptionParts[] = 'Lease Tenure: ' . implode(', ', $filters['leaseTenure']);
            }

            // Final description string
            $description = implode(' | ', $descriptionParts);

            if (!empty($description)) {
                UserActionLogHelper::UserActionLog(
                    'filterReport',
                    url("/reports/filter-report"),
                    'filterReport',
                    "{$authUserName} has filtered the report by {$description}"
                );
            }
        }
        // End :- Worked on adding filter report user action logs - Lalit Tiwari (29/April/2025)

        return response()->json($results);
    }

    public function colonyWiseReportExport(Request $request, ReportService $reportService)
    {
        $filters = $request->filters;
        $user = Auth::check() ? Auth::user() : null;
        $sections = $user->hasAnyRole('section-officer', 'deputy-lndo') ? $user->sections->pluck('section_code')->toArray() : [];
        $filters['section_id'] = $sections;
        $results = $reportService->colonyWiseFilterResults($filters, true);
        return $results;
    }

    /* public function leaseHoldDemands()
    {
        $data['properties']  = DB::table('property_masters as pm')
            ->leftJoin('property_lease_details as pld', 'pm.id', '=', 'pld.property_master_id')
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->leftJoin('sections', 'pm.section_code', '=', 'sections.section_code')
            ->where(function ($q) {
                $q->where(function ($qu1) {
                    $qu1->whereNotNull('pm.is_joint_property')->where('spd.property_status', 951);
                })
                    ->orwhere(function ($qu2) {
                        $qu2->whereNull('pm.is_joint_property')->where('pm.status', 951);
                    });
            })
            ->leftJoin(
                DB::raw('(select * from demands where flat_id is null and status in (' . getServiceType('DEM_PENDING') . ',' . getserviceType('DEM_PART_PAID') . ')) as d'),
                function ($join) {
                    $join->on('pm.id', '=', 'd.property_master_id')
                        ->where(function ($q) {
                            $q->where(function ($q) {
                                $q->whereNull('pm.is_joint_property')
                                    ->whereNull('d.splited_property_detail_id');
                            })
                                ->orWhere(function ($q) {
                                    $q->whereNotNull('pm.is_joint_property')
                                        ->whereColumn('d.splited_property_detail_id', 'spd.id');
                                });
                        });
                }
            )
            ->leftJoin(
                DB::raw('( select property_id, sum(outstanding) as outstanding from old_demands where new_demand_id is null group by property_id) as od'),
                function ($join) {
                    $join->on(
                        'od.property_id',
                        '=',
                        DB::raw('COALESCE(spd.old_property_id, pm.old_propert_id)')
                    );
                }
            )
            ->select(
                DB::raw('coalesce(spd.old_property_id,pm.old_propert_id) as old_property_id'),
                DB::raw('coalesce(spd.presently_known_as, pld.presently_known_as) as known_as'),
                DB::raw('coalesce(spd.area_in_sqm, pld.plot_area_in_sqm) as area_in_sqm'),
                DB::raw('COALESCE(d.balance_amount, 0) + COALESCE(od.outstanding, 0) as outstanding'),
                DB::raw('COALESCE(sections.name, pm.section_code) as section'),
            )
            ->orderBy('outstanding', 'desc')
            ->get();

        return view('report.lease-hold-demand-report', $data);
    } */

    public function leaseHoldDemands()
    {
        return view('report.lease-hold-demand-report');
    }

    public function leaseHoldDemandsData(Request $request)
    {
        /*
     * ---------------------------------------------------------
     * BASE PROPERTY QUERY
     * ---------------------------------------------------------
     */
        $baseQuery = DB::table('property_masters as pm')
            ->leftJoin(
                'property_lease_details as pld',
                'pm.id',
                '=',
                'pld.property_master_id'
            )
            ->leftJoin(
                'splited_property_details as spd',
                'pm.id',
                '=',
                'spd.property_master_id'
            )
            ->leftJoin(
                'sections',
                'pm.section_code',
                '=',
                'sections.section_code'
            )
            ->where(function ($q) {

                $q->where(function ($q) {
                    $q->whereNotNull('pm.is_joint_property')
                        ->where('spd.property_status', 951);
                })

                    ->orWhere(function ($q) {
                        $q->whereNull('pm.is_joint_property')
                            ->where('pm.status', 951);
                    });
            });


        /*
     * ---------------------------------------------------------
     * TOTAL RECORDS
     * ---------------------------------------------------------
     */
        $recordsTotal = (clone $baseQuery)->count();


        /*
     * ---------------------------------------------------------
     * SELECT
     * ---------------------------------------------------------
     */
        $query = (clone $baseQuery)
            ->select(
                'pm.id as property_master_id',
                'spd.id as split_property_id',

                DB::raw('
                COALESCE(
                    spd.old_property_id,
                    pm.old_propert_id
                ) as old_property_id
            '),

                DB::raw('
                COALESCE(
                    spd.presently_known_as,
                    pld.presently_known_as
                ) as known_as
            '),

                DB::raw('
                COALESCE(
                    spd.area_in_sqm,
                    pld.plot_area_in_sqm
                ) as area_in_sqm
            '),

                DB::raw('
                COALESCE(
                    sections.name,
                    pm.section_code
                ) as section
            ')
            );


        /*
     * ---------------------------------------------------------
     * SEARCH
     * Property ID + Known As+ Section name only
     * ---------------------------------------------------------
     */
        $search = $request->input('search.value');

        if ($search !== null && $search !== '') {

            $query->where(function ($q) use ($search) {

                $q->whereRaw(
                    'COALESCE(spd.old_property_id, pm.old_propert_id) LIKE ?',
                    ["%{$search}%"]
                )

                    ->orWhereRaw(
                        'COALESCE(spd.presently_known_as, pld.presently_known_as) LIKE ?',
                        ["%{$search}%"]
                    )
                    ->orWhereRaw('
                        COALESCE(
                            sections.name,
                            pm.section_code
                        ) like ?', ["%{$search}%"]);
            });
        }


        /*
     * ---------------------------------------------------------
     * FILTERED COUNT
     * ---------------------------------------------------------
     */
        $recordsFiltered = (clone $query)->count();


        /*
     * ---------------------------------------------------------
     * SORTING
     * ---------------------------------------------------------
     *
     * 0 = S.No.          -> NOT sortable
     * 1 = Property ID    -> sortable
     * 2 = Known As       -> sortable
     * 3 = Section        -> sortable
     * 4 = Area           -> sortable
     * 5 = Outstanding    -> NOT sortable
     * 6 = View           -> NOT sortable
     */
        $sortableColumns = [
            1 => DB::raw(
                'COALESCE(spd.old_property_id, pm.old_propert_id)'
            ),

            2 => DB::raw(
                'COALESCE(spd.presently_known_as, pld.presently_known_as)'
            ),

            3 => DB::raw(
                'COALESCE(sections.name, pm.section_code)'
            ),

            4 => DB::raw(
                'COALESCE(spd.area_in_sqm, pld.plot_area_in_sqm)'
            ),
        ];

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        if (
            isset($sortableColumns[$orderColumn]) &&
            in_array($orderDirection, ['asc', 'desc'])
        ) {

            $query->orderBy(
                $sortableColumns[$orderColumn],
                $orderDirection
            );
        } else {

            $query->orderBy('pm.id', 'asc');
        }


        /*
     * ---------------------------------------------------------
     * PAGINATION
     * ---------------------------------------------------------
     */
        $start = max(
            0,
            (int) $request->input('start', 0)
        );

        $length = (int) $request->input('length', 10);

        if ($length < 1) {
            $length = 10;
        }

        $properties = $query
            ->offset($start)
            ->limit($length)
            ->get();


        /*
     * ---------------------------------------------------------
     * IDs FROM CURRENT PAGE ONLY
     * ---------------------------------------------------------
     */
        $propertyIds = $properties
            ->pluck('property_master_id')
            ->unique()
            ->values();

        $oldPropertyIds = $properties
            ->pluck('old_property_id')
            ->filter()
            ->unique()
            ->values();


        /*
     * ---------------------------------------------------------
     * CURRENT DEMANDS
     * One active demand per property/split combination
     * ---------------------------------------------------------
     */
        $demands = DB::table('demands')
            ->whereIn('property_master_id', $propertyIds)
            ->whereNull('flat_id')
            ->whereIn('status', [
                getServiceType('DEM_PENDING'),
                getServiceType('DEM_PART_PAID'),
            ])
            ->select(
                'property_master_id',
                'splited_property_detail_id',
                DB::raw('SUM(balance_amount) as balance_amount')
            )
            ->groupBy(
                'property_master_id',
                'splited_property_detail_id'
            )
            ->get()
            ->keyBy(function ($demand) {
                return $demand->property_master_id . '_' .
                    ($demand->splited_property_detail_id ?? 'normal');
            });


        /*
     * ---------------------------------------------------------
     * OLD DEMANDS
     * Multiple rows possible
     * Only rows not migrated to new demand
     * ---------------------------------------------------------
     */
        $oldDemands = DB::table('old_demands')
            ->whereIn('property_id', $oldPropertyIds)
            ->whereNull('new_demand_id')
            ->select(
                'property_id',
                DB::raw('SUM(outstanding) as outstanding')
            )
            ->groupBy('property_id')
            ->get()
            ->keyBy('property_id');


        /*
     * ---------------------------------------------------------
     * ATTACH OUTSTANDING
     * ---------------------------------------------------------
     */
        $properties->transform(function ($property) use (
            $demands,
            $oldDemands
        ) {

            $demandKey = $property->property_master_id . '_' .
                ($property->split_property_id ?? 'normal');

            $newOutstanding =
                $demands[$demandKey]->balance_amount ?? 0;

            $oldOutstanding =
                $oldDemands[$property->old_property_id]->outstanding ?? 0;

            $property->outstanding =
                $newOutstanding + $oldOutstanding;

            return $property;
        });


        /*
     * ---------------------------------------------------------
     * DATATABLES RESPONSE
     * ---------------------------------------------------------
     */
        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $properties,
        ]);
    }
}
