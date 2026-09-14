<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PropertyScannedRequest;
use Illuminate\Support\Facades\DB;
use App\Exports\ScanningRequestsExport;
use Maatwebsite\Excel\Facades\Excel;

class PropertyScannedRequestController extends Controller
{
     public function index()
    {
        // Same logic as PropertyScannedFileController@index()
        $sections = getRequiredSections();

        // Limit to user-assigned sections if role is section-officer / deputy-lndo
        [$filterUserSections, $userSectionIds] = getUserAssignedSections();
        if ($filterUserSections) {
            $sections = $sections->whereIn('id', $userSectionIds);
        }

        return view('property_scanning.request-index', compact('sections'));
    }

    public function getScannedRequests(Request $request)
    {
        $columns = [
                    'property_scanned_requests.created_at', 'old_property_id', 'plot_or_flat', 'colony_name', 'file_no',
                    'record_file_location', 'property_status', 'status', 'reason', 'section'
                    ];


        $user = auth()->user();
        $userRole = $user->getRoleNames()->first();
        $sendToScanItemId = DB::table('items')->where('item_code', 'SEND_TO_SCAN')->value('id');
        $scanNewItemId    = DB::table('items')->where('item_code', 'SCAN_NEW')->value('id');

        $query = PropertyScannedRequest::with([
                        'flat',
                        'splitProperty',
                        'propertyMaster.propertyLeaseDetail',
                        'colony'
                    ])
                    ->join('property_masters', 'property_masters.id', '=', 'property_scanned_requests.property_master_id')
                    ->leftJoin('items as status_items', 'status_items.id', '=', 'property_scanned_requests.status')
                    ->leftJoin('applications', 'applications.id', '=', 'property_scanned_requests.application_id')
                    ->leftJoin('items as reason_items', 'reason_items.id', '=', 'applications.service_type')
                    ->leftJoin('record_room_files', 'record_room_files.id', '=', 'property_scanned_requests.record_id')
                    ->select('property_scanned_requests.*',
                            'property_masters.section_code as section',
                            'property_masters.file_no',
                            'record_room_files.file_location as record_file_location',
                            'status_items.item_name as request_status_name',
                            'status_items.item_code as request_status_code',
                            'reason_items.item_name as reason'
                        );
         // Show only SEND_TO_SCAN for scan-admin
    // if ($userRole === 'scan-admin' && $sendToScanItemId) {
    //     $query->where('property_scanned_requests.status', $sendToScanItemId);
    // }

    // Optional UI filter by status code (item_code)
    if ($request->filled('status_code')) {
        $query->where('status_items.item_code', $request->input('status_code'));
    }

    // Default restriction for section-officer / deputy-lndo: show only their assigned sections
    if (in_array($userRole, ['section-officer', 'deputy-lndo'], true)) {
        $sectionCodes = $user->sections->pluck('section_code')->filter()->values();
        if ($sectionCodes->isNotEmpty()) {
            $query->whereIn('property_masters.section_code', $sectionCodes);
        } else {
            // If no sections assigned, return empty result
            $query->whereRaw('1=0');
        }
    }

    if ($request->filled('section_code')) {
        $query->where('property_masters.section_code', $request->input('section_code'));
    }


        // if (in_array($userRole, ['section-officer', 'deputy-lndo'])) {
        //     $sectionCodes = $user->sections->pluck('section_code');
        //     $query->whereIn('property_masters.section_code', $sectionCodes);
        // }

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $query->leftJoin('property_lease_details', 'property_lease_details.property_master_id', '=', 'property_masters.id');

            $query->where(function ($q) use ($search) {
                $q->WhereDate('property_scanned_requests.created_at', $search)
                  ->orWhere('property_scanned_requests.old_property_id', 'like', "%{$search}%")
                  ->orWhere('property_scanned_requests.colony_id', 'like', "%{$search}%")
                  ->orWhere('property_masters.file_no', 'like', "%{$search}%")
                  ->orWhere('status_items.item_name', 'like', "%{$search}%")
                  ->orWhere('reason_items.item_name', 'like', "%{$search}%")
                  ->orWhere('property_masters.section_code', 'like', "%{$search}%");
            });
        }

        $totalQuery = clone $query;
        $totalData = DB::table(DB::raw("({$totalQuery->toSql()}) as sub"))
            ->mergeBindings($totalQuery->getQuery())
            ->count();

        // $limit = $request->input('length');
        // $start = $request->input('start');
        // $order = $columns[$request->input('order.0.column')] ?? 'property_scanned_requests.id';
        // $dir = $request->input('order.0.dir', 'desc');

        // $records = $query->offset($start)->limit($limit)->orderBy($order, $dir)->get();

        $orderIndex = (int) $request->input('order.0.column', 1);
        $dir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        // Allow sorting only for: 0(S.No), 1(Request Date), 10(Section)
        if (!in_array($orderIndex, [0, 1, 7], true)) {
            $orderIndex = 1;
        }

        // Map indexes to real SQL columns
        $orderMap = [
            0  => 'property_scanned_requests.created_at',  // S.No sorts by Request Date actually
            1  => 'property_scanned_requests.created_at',  // Request Date ✅
            7 => 'property_masters.section_code',         // Section ✅
        ];

        $orderBy = $orderMap[$orderIndex] ?? 'property_scanned_requests.created_at';

        // Apply paging like you already do
        $limit = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);

        if ($limit > 0) {
            $query->offset($start)->limit($limit);
        }

        $records = $query->orderBy($orderBy, $dir)->get();

        $data = [];

        foreach ($records as $latest) {
            $block = $plotOrFlat = $fileNo = $propertyStatus = $status = $reason = '-';

            if ($latest->flat) {
                $block = $latest->flat->block ?? '-';
                $plotOrFlat = $latest->flat->flat_number ?? $latest->flat->plot ?? '-';
                $fileNo = $latest->propertyMaster->file_no ?? '-';
                $propertyStatus = $latest->propertyMaster->status_name ?? '-';
            } elseif ($latest->splitProperty) {
                $master = $latest->propertyMaster;
                $block = $master?->block_no ?? '-';
                $plotOrFlat = $master?->plot_or_property_no ?? '-';
                $fileNo = $master?->file_no ?? '-';
                $propertyStatus = $master?->status_name ?? '-';
            } elseif ($latest->propertyMaster) {
                $master = $latest->propertyMaster;
                $block = $master->block_no ?? '-';
                $plotOrFlat = $master->plot_or_property_no ?? '-';
                $fileNo = $master->file_no ?? '-';
                $propertyStatus = $master->status_name ?? '-';
            }

            $status = $latest->request_status_name ?? '-';
            $reason = $latest->reason ?? '-';

            $blockPlotMerged = ($block !== '-' && $plotOrFlat !== '-') ? "{$block}/{$plotOrFlat}" : ($plotOrFlat !== '-' ? $plotOrFlat : '-');
            $section = $latest->propertyMaster?->section_code ?? '-';
            $colonyName = $latest->colony->name ?? '-';
            $recordFileLocation = $latest->record_file_location ?? '-';
            
            $hasScannedFiles = DB::table('property_scanned_files')
                                ->where('old_property_id', $latest->old_property_id)
                                ->exists();

            $data[] = [
                        'id' => $latest->id,
                        'request_date' => optional($latest->created_at)->format('d-m-Y'),
                        'old_property_id' => $latest->old_property_id,
                        // 'plot_or_flat' => $blockPlotMerged,
                        // 'colony_name' => $colonyName,
                        'file_no' => $fileNo,
                        'record_file_location' => $recordFileLocation,
                        // 'property_status' => $propertyStatus,
                        'status' => $status, // item_name
                        'status_code' => $latest->request_status_code ?? null, // item_code
                        'reason' => $reason,
                        'section' => $section,
                        'has_scanned_files' => $hasScannedFiles,
                    ];

        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalData,
            "data" => $data,
        ]);
    }

    public function sendToScan(Request $request)
    {
        $request->validate(['id' => 'required|exists:property_scanned_requests,id']);

        // Fetch the item ID for 'SENT_TO_SCAN'
        $sentToScanItemId = DB::table('items')->where('item_code', 'SEND_TO_SCAN')->value('id');

        if (!$sentToScanItemId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Status missing in items table',
            ],404);

        }

        $record = PropertyScannedRequest::findOrFail($request->id);
        $record->status = $sentToScanItemId;
        $record->save();

        return response()->json([
            'status' => 'success',
            'message' => 'File sent to scan.',
        ]);

    }

    public function closeScan(Request $request)
    {
        $request->validate(['id' => 'required|exists:property_scanned_requests,id']);

        $closedItemId = DB::table('items')->where('item_code', 'SCAN_CLOSED')->value('id');

        if (!$closedItemId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Status missing in items table',
            ], 404);
        }

        $record = PropertyScannedRequest::findOrFail($request->id);
        $record->status = $closedItemId;
        $record->save();

        return response()->json([
            'status' => 'success',
            'message' => 'File status set to closed.',
        ]);
    }

    // public function returnToRecord(Request $request)
    // {
    //     $request->validate(['id' => 'required|exists:property_scanned_requests,id']);

    //     $returnToRecordId = DB::table('items')->where('item_code', 'RETURNED_TO_RECORD')->value('id');

    //     if (!$returnToRecordId) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Status RETURNED_TO_RECORD missing in items table',
    //         ], 404);
    //     }

    //     $record = PropertyScannedRequest::findOrFail($request->id);

    //     $record->status = $returnToRecordId;
    //     $record->save();

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Request returned to record successfully.',
    //     ]);
    // }

    public function deleteRequest(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:property_scanned_requests,id',
        ]);

        // If you didn't protect the route with middleware, keep this guard:
        if (auth()->user()->getRoleNames()->first() !== 'super-admin') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        $record = PropertyScannedRequest::findOrFail($request->id);
        $record->delete(); // soft delete -> sets deleted_at, your model flips is_active=0

        return response()->json([
            'status'  => 'success',
            'message' => 'Request deleted successfully.',
        ]);
    }

    public function exportCsv(Request $request)
{
    $user = auth()->user();
    $userRole = $user->getRoleNames()->first();
    $sendToScanItemId = DB::table('items')->where('item_code', 'SEND_TO_SCAN')->value('id');

    $query = PropertyScannedRequest::query()
        ->join('property_masters', 'property_masters.id', '=', 'property_scanned_requests.property_master_id')
        ->leftJoin('items as status_items', 'status_items.id', '=', 'property_scanned_requests.status')
        ->leftJoin('applications', 'applications.id', '=', 'property_scanned_requests.application_id')
        ->leftJoin('items as reason_items', 'reason_items.id', '=', 'applications.service_type')
        ->leftJoin('record_room_files', 'record_room_files.id', '=', 'property_scanned_requests.record_id')
        ->leftJoin('old_colonies', 'old_colonies.id', '=', 'property_scanned_requests.colony_id')
        ->select([
            'property_scanned_requests.created_at',
            'property_scanned_requests.old_property_id',
            'property_masters.file_no',
            'record_room_files.file_location as record_file_location',
            'property_masters.section_code as section',
            'status_items.item_name as request_status',
            'status_items.item_code as request_status_code',
            'reason_items.item_name as reason',
            'old_colonies.name as colony_name',
        ]);

    // role filter (same as listing)
    if ($userRole === 'scan-admin' && $sendToScanItemId) {
        $query->where('property_scanned_requests.status', $sendToScanItemId);
    }

    // supports both ?search=abc and DataTables ?search[value]=abc
    // $search = $request->input('search.value') ?? $request->input('search');
    $search = $request->input('search.value');   // DataTables style

    if ($search === null) {
        $search = $request->input('search');     // fallback
    }

    // If search is still an array, extract its 'value'
    if (is_array($search)) {
        $search = $search['value'] ?? '';
    }

    // Ensure it's a string
    $search = trim((string) $search);

    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->WhereDate('property_scanned_requests.created_at', 'like', "%{$search}%")
              ->orWhere('property_scanned_requests.old_property_id', 'like', "%{$search}%")
              ->orWhere('property_masters.file_no', 'like', "%{$search}%")
              ->orWhere('record_room_files.file_location', 'like', "%{$search}%")
              ->orWhere('status_items.item_name', 'like', "%{$search}%")
              ->orWhere('reason_items.item_name', 'like', "%{$search}%")
              ->orWhere('property_masters.section_code', 'like', "%{$search}%")
              ->orWhere('old_colonies.name', 'like', "%{$search}%");
        });
    }

    // (optional) follow DataTables ordering if provided
    $orderColumnIndex = $request->input('order.0.column');
    $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';

    $orderMap = [
        1  => 'property_scanned_requests.created_at',
        2  => 'property_scanned_requests.old_property_id',
        4  => 'old_colonies.name',
        5  => 'property_masters.file_no',
        6  => 'record_room_files.file_location',
        8  => 'reason_items.item_name',
        9  => 'status_items.item_name',
        10 => 'property_masters.section_code',
    ];
    $orderBy = $orderMap[$orderColumnIndex] ?? 'property_scanned_requests.id';

    $rows = $query->orderBy($orderBy, $orderDir)->get();

    $filename = 'scanning_requests_' . now()->format('Ymd_His') . '.csv';

    $headers = [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
    ];

    $callback = function () use ($rows) {
        $out = fopen('php://output', 'w');

        // Excel-friendly UTF-8 BOM
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($out, [
            'Request Date',
            'Property ID',
            'Colony',
            'File No',
            'Record File Location',
            'Reason',
            'Request Status',
            'Status Code',
            'Section',
        ]);

        foreach ($rows as $r) {
            fputcsv($out, [
                optional($r->created_at)->format('d-m-Y') ?? '-',
                $r->old_property_id ?? '-',
                $r->colony_name ?? '-',
                $r->file_no ?? '-',
                $r->record_file_location ?? '-',
                $r->reason ?? '-',
                $r->request_status ?? '-',
                $r->request_status_code ?? '-',
                $r->section ?? '-',
            ]);
        }

        fclose($out);
    };

    return response()->stream($callback, 200, $headers);
}
public function exportExcel(Request $request)
{
    return Excel::download(
        new ScanningRequestsExport(auth()->user(), $request),
        'scanning_requests_' . now()->format('Ymd_His') . '.xlsx'
    );
}




}
