<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Flat;
use App\Models\SplitedPropertyDetail;
use App\Models\PropertyMaster;
use App\Models\PropertyLeaseDetail;
use App\Models\OldColony;
use Illuminate\Support\Facades\Storage;
use App\Models\PropertyScannedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Helpers\GeneralFunctions;
use App\Models\PropertyScannedRequest;
use Carbon\Carbon;
use App\Helpers\UserActionLogHelper;
use Illuminate\Support\Facades\Auth;
class PropertyScannedFileController extends Controller
{
    // public function create()
    // {
    //     $colonies = OldColony::orderBy('name')->get();
    //     return view('property_scanning.input-form', compact('colonies'));
    // }

    public function create(Request $request)
    {
        $colonies = OldColony::orderBy('name')->get();
        $prefillPropertyId = $request->query('property_id');

        return view('property_scanning.input-form', [
            'colonies' => $colonies,
            'prefillPropertyId' => $prefillPropertyId
        ]);
    }


    public function searchProperty(Request $request)
    {
        $propertyId = $request->input('property_id');

        // 1. Search in flats
        $flat = Flat::where('unique_flat_id', $propertyId)->first();
        if ($flat) {
            $propertyMaster = PropertyMaster::find($flat->property_master_id);
            $colonyName = $propertyMaster?->new_colony_name ? OldColony::find($propertyMaster->new_colony_name)?->name : null;

            return response()->json([
                'status' => 'found',
                'type' => 'flat',
                'file_no' => $propertyMaster?->file_no,
                'colony_name' => $colonyName,
                'flat_no' => $flat->flat_number,
                'plot' => $flat->plot,
                'block' => $flat->block,
                'section' => $propertyMaster?->section_code,
                'property_status' => $propertyMaster?->status_name,
                'presently_known_as' => $flat->known_as,
                'property_master_id' => $flat->property_master_id,
                'flat_id' => $flat->id,
                'split_id' => null,
                'old_property_id' => $flat->old_property_id ?? $propertyId,
                // 'uploaded_files' => PropertyScannedFile::where('old_property_id', $propertyId)->get(['document_name', 'document_path']),
                'uploaded_files' => PropertyScannedFile::where('old_property_id', $propertyId)->get(['old_property_file_name', 'document_path'])//done on 15012026 by swati
,
            ]);
        }

        // 2. Search in splitted_property_details
        $split = SplitedPropertyDetail::where('old_property_id', $propertyId)->first();
        if ($split) {
            $propertyMaster = PropertyMaster::find($split->property_master_id);
            $colonyName = $propertyMaster?->new_colony_name ? OldColony::find($propertyMaster->new_colony_name)?->name : null;
            $leaseDetail = PropertyLeaseDetail::where('property_master_id', $split->property_master_id)->first();

            return response()->json([
                'status' => 'found',
                'type' => 'split',
                'file_no' => $propertyMaster?->file_no,
                'colony_name' => $colonyName,
                'block' => $propertyMaster?->block_no,
                'plot' => $split?->plot_flat_no,
                'section' => $propertyMaster?->section_code,
                'property_status' => $propertyMaster?->status_name,
                'presently_known_as' => $leaseDetail?->presently_known_as,
                'property_master_id' => $split->property_master_id,
                'flat_id' => null,
                'split_id' => $split->id,
                'old_property_id' => $split->old_property_id,
                // 'uploaded_files' => PropertyScannedFile::where('old_property_id', $propertyId)->get(['document_name', 'document_path']),
                'uploaded_files' => PropertyScannedFile::where('old_property_id', $propertyId)->get(['old_property_file_name', 'document_path'])//done on 15012026 by swati
,
            ]);
        }

        // 3. Search in property_masters
        $master = PropertyMaster::where('old_propert_id', $propertyId)->first();
        if ($master) {
            $colonyName = $master->new_colony_name ? OldColony::find($master->new_colony_name)?->name : null;
            $leaseDetail = PropertyLeaseDetail::where('property_master_id', $master->id)->first();

            return response()->json([
                'status' => 'found',
                'type' => 'master',
                'file_no' => $master->file_no,
                'colony_name' => $colonyName,
                'block' => $master?->block_no,
                'plot' => $master?->plot_or_property_no,
                'section' => $master?->section_code,
                'property_status' => $master?->status_name,
                'presently_known_as' => $leaseDetail?->presently_known_as,
                'property_master_id' => $master->id,
                'flat_id' => null,
                'split_id' => null,
                'old_property_id' => $master->old_propert_id,
                // 'uploaded_files' => PropertyScannedFile::where('old_property_id', $propertyId)->get(['document_name', 'document_path']),
                'uploaded_files' => PropertyScannedFile::where('old_property_id', $propertyId)->get(['old_property_file_name', 'document_path'])//done on 15012026 by swati
,
            ]);
        }

        return response()->json(['status' => 'not_found']);
    }


    public function store(Request $request)
    {
        $request->validate([
            'property_id'        => 'required|digits:5',
            'property_master_id' => 'required|integer',
            'documents'          => 'required|array|min:1',
            'documents.*'        => 'required|file|mimes:pdf|max:20480', // 20MB each
        ]);

        return DB::transaction(function () use ($request) {

            $propertyId = $request->input('property_id'); // old_property_id
            $colonyName = $request->input('present_colony_name');
            $safeColony = Str::slug($colonyName ?? '', '_') ?: 'unknown_colony';

            foreach ($request->file('documents') as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $oldFileName  = Str::slug($originalName, '_') ?: ('FILE_' . time());
                $ext          = $file->getClientOriginalExtension() ?: 'pdf';

                $newFilePath = "documents/{$safeColony}/{$propertyId}/scannedFiles/{$oldFileName}.{$ext}";
                Storage::disk('public')->put($newFilePath, file_get_contents($file->getRealPath()));

                PropertyScannedFile::create([
                    'property_master_id'         => $request->input('property_master_id'),
                    'splited_property_detail_id' => $request->input('splited_property_detail_id') ?: null,
                    'flat_id'                    => $request->input('flat_id') ?: null,
                    'colony_name'                => $colonyName,
                    'old_property_id'            => $propertyId,
                    'old_property_file_name'     => $oldFileName,
                    'document_path'              => $newFilePath,
                ]);
            }

            // ✅ Close scanning request ONLY if it exists and is not already closed
            $closedItemId = DB::table('items')->where('item_code', 'SCAN_CLOSED')->value('id');
            $requestClosed = false;

            if ($closedItemId) {
                $openRequests = PropertyScannedRequest::where('old_property_id', $propertyId)
                    ->where('status', '!=', $closedItemId);

                if ($openRequests->exists()) {
                    $openRequests->update([
                        'status'     => $closedItemId,
                        'updated_at' => now(),
                    ]);
                    $requestClosed = true;
                }
            }

            $msg = $requestClosed
                ? 'All scanned documents saved successfully. Request closed.'
                : 'All scanned documents saved successfully.';

            return redirect()->route('property.scanning.create')->with('success', $msg);
        });
    }



    public function index()
    {
        $sections = getRequiredSections();
        [$filterUserSections, $userSectionIds] = getUserAssignedSections();
        //check if user has full access
        $hasFullAccess = auth()->user()->hasPermissionTo('view.all.sections'); 
        // dd($hasFullAccess);
        if ($filterUserSections && !$hasFullAccess) {
            $sections = $sections->whereIn('id', $userSectionIds);
        }
        return view('property_scanning.indexDatatable', compact('sections'));
    }



    public function getScannedFiles(Request $request)
    {
        $columns = ['old_property_id', 'plot_or_flat', 'colony_name', 'known_as', 'file_no', 'status', 'section', 'total_files', 'actions'];

        $user = auth()->user(); 
        $userRole = $user->getRoleNames()->first();

        $query = PropertyScannedFile::selectRaw('
            property_scanned_files.old_property_id,
            property_scanned_files.colony_name,
            COUNT(*) as total_files,
            MAX(property_scanned_files.id) as latest_id
        ')
        ->join('property_masters', 'property_masters.id', '=', 'property_scanned_files.property_master_id')
        ->groupBy('property_scanned_files.old_property_id', 'property_scanned_files.colony_name');
          if ($request->filled('start_date')) {
		    $query->whereDate(
		        'property_scanned_files.created_at',
		        '>=',
		        \Carbon\Carbon::createFromFormat('d-m-Y', $request->start_date)->format('Y-m-d')
		    );
		}

		if ($request->filled('end_date')) {
		    $query->whereDate(
		        'property_scanned_files.created_at',
		        '<=',
		        \Carbon\Carbon::createFromFormat('d-m-Y', $request->end_date)->format('Y-m-d')
		    );
		}

        // Apply role-based filtering
        if (in_array($userRole, ['section-officer', 'deputy-lndo'])) {


            $requiredSections = getRequiredSections();
            [$limitToAssigned, $userSectionIds] = getUserAssignedSections();
        //check if user has full access
            $hasFullAccess = auth()->user()->hasPermissionTo('view.all.sections'); 
            // dd($hasFullAccess);
            if ($limitToAssigned && !$hasFullAccess) {
                $requiredSections = $requiredSections->whereIn('id', $userSectionIds);
            }
            $sectionCodes = $requiredSections->pluck('section_code')->filter()->values()->all();
            $query->whereIn('property_masters.section_code', $sectionCodes);
        }
        // If a specific section is selected, narrow further
        if ($request->filled('section_code')) {
            $query->where('property_masters.section_code', $request->input('section_code'));
        }


        // Determine requested sort (whitelist)
        // DataTables indices: 0=S.No., 1=Property ID, 2=Block/Plot/Flat, 3=Colony, 6=Status
        $allowedIdx = [0, 2, 3, 6];
        $orderIdx = (int) $request->input('order.0.column', 0);
        $dir = $request->input('order.0.dir', 'asc') === 'desc' ? 'desc' : 'asc';
        if (!in_array($orderIdx, $allowedIdx, true)) {
            // default to S.No.
            $orderIdx = 0;
            $dir = 'asc';
        }

        // Search filter
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $query
                ->leftJoin('property_lease_details', 'property_lease_details.property_master_id', '=', 'property_masters.id')
                ->leftJoin('items as status_items', 'status_items.id', '=', 'property_masters.status');

            $query->where(function ($q) use ($search) {
                $q->where('property_scanned_files.old_property_id', 'like', "%{$search}%")
                ->orWhere('property_scanned_files.colony_name', 'like', "%{$search}%")
                ->orWhere('property_masters.file_no', 'like', "%{$search}%")
                ->orWhere('status_items.item_name', 'like', "%{$search}%")
                ->orWhere('property_masters.section_code', 'like', "%{$search}%")
                ->orWhere('property_lease_details.presently_known_as', 'like', "%{$search}%");
            });
        } else {
            // If sorting by Status without search, we still need the join to order by status name
            if ($orderIdx === 6) {
                $query->leftJoin('items as status_items', 'status_items.id', '=', 'property_masters.status');
            }
        }

        // $totalData = $query->count();
        $totalQuery = clone $query;
        $totalData = DB::table(DB::raw("({$totalQuery->toSql()}) as sub"))
            ->mergeBindings($totalQuery->getQuery())
            ->count();

        $limit = $request->input('length');
        $start = $request->input('start');

        // Apply ordering based on whitelist
        // 0 => S.No. (map to old_property_id for a stable default)
        // 2 => Block/Plot/Flat (approximate via block_no then plot_or_property_no)
        // 3 => Colony (colony_name)
        // 6 => Status (status_items.item_name)
        switch ($orderIdx) {
            case 2:
                $query->orderBy('property_masters.block_no', $dir)
                    ->orderBy('property_masters.plot_or_property_no', $dir);
                break;
            case 3:
                $query->orderBy('property_scanned_files.colony_name', $dir);
                break;
            case 6:
                // If not joined (e.g., search absent), we added the join above when $orderIdx === 6
                $query->orderBy('status_items.item_name', $dir);
                break;
            case 0:
            default:
                $query->orderBy('property_scanned_files.old_property_id', 'asc'); // S.No. default
                break;
        }

        $records = $query->offset($start)->limit($limit)->get();

        // Fetch details for each grouped record
        $data = [];
        foreach ($records as $record) {
            $latestFile = PropertyScannedFile::with([
                'flat',
                'splitProperty',
                'propertyMaster.propertyLeaseDetail'
            ])->find($record->latest_id);

            $block = $plotOrFlat = $knownAs = $fileNo = $status = '-';
            $blockPlotMerged = '-';

            if ($latestFile->flat) {
                $block = $latestFile->flat->block ?? '-';
                $plotOrFlat = $latestFile->flat->flat_number ?? $latestFile->flat->plot ?? '-';
                $knownAs = $latestFile->flat->known_as ?? '-';
                $fileNo = $latestFile->propertyMaster->file_no ?? '-';
                $status = $latestFile->propertyMaster->status_name ?? '-';
            } elseif ($latestFile->splitProperty) {
                $master = $latestFile->propertyMaster;
                $block = $master?->block_no ?? '-';
                $plotOrFlat = $master?->plot_or_property_no ?? '-';
                $knownAs = $master?->propertyLeaseDetail?->presently_known_as ?? '-';
                $fileNo = $master?->file_no ?? '-';
                $status = $master?->status_name ?? '-';
            } elseif ($latestFile->propertyMaster) {
                $master = $latestFile->propertyMaster;
                $block = $master->block_no ?? '-';
                $plotOrFlat = $master->plot_or_property_no ?? '-';
                $knownAs = $master->propertyLeaseDetail?->presently_known_as ?? '-';
                $fileNo = $master->file_no ?? '-';
                $status = $master->status_name ?? '-';
            }

            $blockPlotMerged = ($block !== '-' && $plotOrFlat !== '-') ? "{$block}/{$plotOrFlat}" : ($plotOrFlat !== '-' ? $plotOrFlat : '-');
            $section = '-';
            if ($latestFile->propertyMaster) {
                $section = $latestFile->propertyMaster->section_code ?? '-';
            }

            $action = '';
            if (auth()->user()->can('view.scanning.files')) {
                $action = '<a href="' . route('property.scanning.view', $record->old_property_id) . '" class="btn btn-sm btn-primary">View</a>';
            }

            $data[] = [
                'old_property_id' => $record->old_property_id,
                'plot_or_flat' => $blockPlotMerged,
                'colony_name' => $record->colony_name ?? '-',
                'known_as' => $knownAs,
                'file_no' => $fileNo,
                'status' => $status,
                'section' => $section,
                'total_files' => $record->total_files,
                'action' => $action
            ];
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalData,
            "data" => $data,
        ]);
}


    public function view($propertyId)
    {
        UserActionLogHelper::UserActionLog('viewScannedFile', url("/scanning/view/{$propertyId}"), 'viewScannedFile', "User " . Auth::user()->name . " viewed scanned file for property {$propertyId}.");
        $request = new Request(['property_id' => $propertyId]);
        $searchResult = $this->searchProperty($request)->getData();

        if ($searchResult->status !== 'found') {
            return redirect()->route('property.scanning.index')->withErrors('Property not found.');
        }

        // $result = isUserCanAcessThisResource($searchResult->old_property_id);
        // if (!$result) {
        //     return redirect()->back()->withErrors('You do not have permission to view this property.');
        // }

        $colonies = OldColony::orderBy('name')->get();

        return view('property_scanning.input-form', [
            'colonies' => $colonies,
            'isViewOnly' => true,
            'propertyData' => $searchResult
        ]);
    }

    public function deleteByProperty(Request $request)
    {
        $request->validate([
            'old_property_id' => 'required|digits:5', // adjust rule if IDs differ
        ]);

        // Belt-and-suspenders auth check (keep even if you used route middleware)
        if (auth()->user()->getRoleNames()->first() !== 'super-admin') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        $oldPropertyId = $request->input('old_property_id');

        return DB::transaction(function () use ($oldPropertyId) {
            // 1) Touch records for audit before deletion
            PropertyScannedFile::where('old_property_id', $oldPropertyId)
                ->update([
                    'updated_by' => auth()->id(),
                    'updated_at' => now(),
                ]);

            // 2) Hard delete (purge)
            $deleted = PropertyScannedFile::where('old_property_id', $oldPropertyId)->delete();

            return response()->json([
                'status'       => 'success',
                'message'      => 'Files deleted successfully.',
                'deleted_rows' => $deleted,
            ]);
        });
    }

public function scanningReport(Request $request) 
{
    $requiredSections = getRequiredSections();
    [$limitToAssigned, $userSectionIds] = getUserAssignedSections();
   //check if user has full access
    $hasFullAccess = auth()->user()->hasPermissionTo('view.all.sections'); 
    // dd($hasFullAccess);
    if ($limitToAssigned && !$hasFullAccess) {
        $requiredSections = $requiredSections->whereIn('id', $userSectionIds);
    }
    $sectionCodes = $requiredSections->pluck('section_code')->filter()->values()->all();


    if (empty($sectionCodes)) {
        return view('property_scanning.scanning-report', [
            'totalCount'    => 0,
            'sectionCounts' => [],
        ]);
    }

    // 2) Latest PSF row per (old_property_id, colony_name)
    // NOTE: This mirrors the grouping in getScannedFiles
    $latestPerLegacy = DB::table('property_scanned_files as psf')
        ->selectRaw('psf.old_property_id, psf.colony_name, MAX(psf.id) as latest_id')
        ->groupBy('psf.old_property_id', 'psf.colony_name');

    // 3) Join those "groups" back to PSF + PM to get section of the latest row
    // $groups = DB::table(DB::raw("({$latestPerLegacy->toSql()}) as g"))
    //     ->mergeBindings($latestPerLegacy)
    //     ->join('property_scanned_files as psf', 'psf.id', '=', 'g.latest_id')
    //     ->join('property_masters as pm', 'pm.id', '=', 'psf.property_master_id')
    //     // limit to allowed sections
    //     ->whereIn('pm.section_code', $sectionCodes)
    //     ->selectRaw("UPPER(TRIM(pm.section_code)) as section_norm")
    //     ->selectRaw('g.old_property_id, g.colony_name');
    $groups = DB::table(DB::raw("({$latestPerLegacy->toSql()}) as g"))
        ->mergeBindings($latestPerLegacy)
        ->join('property_scanned_files as psf', 'psf.id', '=', 'g.latest_id')
        ->join('property_masters as pm', 'pm.id', '=', 'psf.property_master_id')
        ->whereIn('pm.section_code', $sectionCodes);

    // Date Filter
    if ($request->filled('start_date')) {
        $groups->whereDate(
            'psf.created_at',
            '>=',
            Carbon::createFromFormat('d-m-Y', $request->start_date)->format('Y-m-d')
        );
    }

    if ($request->filled('end_date')) {
        $groups->whereDate(
            'psf.created_at',
            '<=',
            Carbon::createFromFormat('d-m-Y', $request->end_date)->format('Y-m-d')
        );
    }

    $groups->selectRaw("UPPER(TRIM(pm.section_code)) as section_norm")
           ->selectRaw('g.old_property_id, g.colony_name');

    // 4) Total = number of legacy groups in scope
    $totalCount = DB::table(DB::raw("({$groups->toSql()}) as x"))
        ->mergeBindings($groups)
        ->count(); // counts rows, i.e., distinct legacy groups

    // 5) Per-section = count legacy groups bucketed by normalized section
    $sectionCounts = DB::table(DB::raw("({$groups->toSql()}) as x"))
        ->mergeBindings($groups)
        ->selectRaw('section_norm, COUNT(*) as property_count')
        ->groupBy('section_norm')
        ->pluck('property_count', 'section_norm')
        ->toArray();

    return view('property_scanning.scanning-report', compact('totalCount', 'sectionCounts'));
}

    public function downloadAll($propertyId)
    {
        UserActionLogHelper::UserActionLog('downloadScannedFile', url("/property-scanning/{$propertyId}/download-all/"), 'downloadScannedFile', "User " . Auth::user()->name . " Downloaded scanned file for property {$propertyId}.");
        return GeneralFunctions::downloadAllScannedFilesZipByOldPropertyId($propertyId);
    }

    public function downloadZipByMasterSplit($propertyMasterId, $splitId = null)
    {
        $query = PropertyScannedFile::where('property_master_id', $propertyMasterId)
            ->when($splitId, function ($q) use ($splitId) {
                return $q->where('splited_property_detail_id', $splitId);
            }, function ($q) {
                return $q->whereNull('splited_property_detail_id');
            })
            ->orderBy('id', 'asc');

        // Fetch files
        $files = $query->get(['document_name', 'document_path']);

        // 🔹 Get old_property_id safely (from first row)
        $oldPropertyId = $query->value('old_property_id') ?? $propertyMasterId;

        return GeneralFunctions::downloadZipFromScannedFiles(
            $files,
            "{$oldPropertyId}_scanned_files"
        );
    }


}
