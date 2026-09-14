<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ColonyService;
use App\Models\RecordRoomFile;
use App\Models\Section;
use App\Models\OldColony;
use App\Models\User;
use App\Models\FileRequest;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Helpers\GeneralFunctions;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\RecordRoomFilesExport;
use Maatwebsite\Excel\Facades\Excel;

class RecordController extends Controller
{

    public function index(ColonyService $colonyService)
    {
        $colonyList = $colonyService->getColonyList();

        // Same pattern as scanning
        $sections = getRequiredSections();
        // dd($sections);

        // 🔹 Apply user-assigned section filter ONLY if user is NOT record-room
        if (!auth()->user()->hasRole('record-room')) {

            // Limit to user-assigned sections if role is section-officer / deputy-lndo
            [$filterUserSections, $userSectionIds] = getUserAssignedSections();

            if ($filterUserSections) {
                $sections = $sections->whereIn('id', $userSectionIds);
            }
            // dd($sections);
        }

        // 🔹 Show only sections that actually exist in record_room_files
        $availableSectionCodes = RecordRoomFile::query()
            ->whereNotNull('section_code')
            ->pluck('section_code')
            ->map(function ($code) {
                $code = trim((string) $code);
                $code = strtoupper($code); // optional but recommended if your codes are uppercase
                return $code;
            })
            ->filter(function ($code) {
                return $code !== '' && $code !== 'SECTION_CODE'; // remove garbage
            })
            ->unique()
            ->values()
            ->toArray();

        $sections = $sections->whereIn('section_code', $availableSectionCodes);


        return view('admin.record.index', compact('colonyList', 'sections'));
    }



     public function getRecordRoomFilesData(Request $request)
    {

        $sections = getLoggedInUserSections();
       

        $data =  RecordRoomFile::query()
        ->leftJoin('old_colonies', 'old_colonies.code', '=', 'record_room_files.colony_code')
        ->select(
            'record_room_files.id',
            'record_room_files.old_property_id',
            'record_room_files.colony_code',
            'old_colonies.name as colony_name',   
            'record_room_files.colony_id',
            'record_room_files.block',
            'record_room_files.plot',
            'record_room_files.file_location',
            'record_room_files.section_code',
            'record_room_files.transaction_section_code'
        );
        $sectionCodes = Section::whereIn('id', $sections)->pluck('section_code')->toArray();
         if($sectionCodes[0] != "REC"){
            $data->whereIn('record_room_files.section_code', $sectionCodes);
        }

        if ($request->has('locality_record') && $request->locality_record != '') {
            $data->where('record_room_files.colony_id', $request->locality_record);
        }
        if ($request->has('block_record') && $request->block_record != '') {
            $data->where('record_room_files.block', $request->block_record);
        }
        if ($request->has('plot_record') && $request->plot_record != '') {
            $data->where('record_room_files.plot', $request->plot_record);
        }

         if ($request->filled('section_code')) {
            $data->where('record_room_files.section_code', $request->section_code);
        }
        return DataTables::of($data)
            ->editColumn('colony_code', function ($row) {
                    return $row->colony_name ?? $row->colony_code;
                })
                
            ->addColumn('action', function ($row) {
                // dd($row->id);

                $alreadyRequested = \App\Models\FileRequest::where('record_room_file_id', $row->id)
                                    ->where('status', 'pending') // optional condition
                                    ->exists();

                if ($alreadyRequested) {
                    return '<span class="badge bg-success">Already Requested</span>';
                }

                return '<button class="btn btn-primary btn-sm" onclick="openRequestFileModal(' . $row->id . ')">Request File</button>';
            })
           ->addColumn('record_action', function ($row) {
                return '<a href="' . route('recordRoom.editRecordFile', $row->id) . '">
                            <button class="btn btn-danger btn-sm">Edit File</button>
                        </a>';
            })
            ->addIndexColumn()
            ->rawColumns(['action','record_action'])
            ->make(true);
    }



    //
    public function create(ColonyService $colonyService)
    {
         $colonyList = $colonyService->getColonyList();
        return view('admin.record.create',compact(['colonyList']));
    }

    //Edit Record File
    public function editRecordFile($id,ColonyService $colonyService)
    {
        $recordRoomFile = RecordRoomFile::find($id);
        if (!$recordRoomFile) {
            return redirect()->back()->with('error', 'Record not found.');
        }
         $colonyList = $colonyService->getColonyList();
        return view('admin.record.edit',compact(['colonyList','recordRoomFile']));
    }


    public function updateRecordFile(Request $request)
    {
        $request->validate([
            'record_id' => 'required',
            'filePlace' => 'required',
        ]);
        $recordRoomFile = RecordRoomFile::find($request->record_id);
        if (!$recordRoomFile) {
            return redirect()->back()->with('error', 'Record not found.');
        }
        $recordRoomFile->file_location = $request->filePlace;
        $recordRoomFile->save();
        return redirect()->back()->with('success', 'Record updated successfully.');
    }

    public function store(Request $request)
    {
    //   dd($request->all());
        $request->validate([
            'localityRecord' => 'required',
            'blockRecord' => 'required',
            'plotRecord' => 'required',
            'filePlace' => 'required',
            'sectionName' => 'required',
        ]);
        // $colonyCode = OldColony::find($request->localityRecord)->pluck('code')->first();
        $colonyCode = OldColony::where('id', $request->localityRecord)->value('code');
        RecordRoomFile::create([
            'colony_id' => $request->localityRecord,
            'colony_code' => $colonyCode,
            'block' => $request->blockRecord,
            'plot' => $request->plotRecord,
            'file_location' => $request->filePlace,
            'section_code' => $request->sectionName
        ]);

        return redirect()->route('recordRoom.create')->with('success', 'Record created successfully.');
    }

    public function fileRequest(ColonyService $colonyService)
    {
        $colonyList = $colonyService->getColonyList();
        return view('admin.record.file_request',compact(['colonyList']));
    }



     public function getFilesRequestData(Request $request)
    {

        // dd($request->all());

        $sections = getLoggedInUserSections();
        // dd($sections);

        $data = FileRequest::query()
            
            ->leftJoin('record_room_files', 'file_requests.record_room_file_id', '=', 'record_room_files.id')
            ->leftJoin('old_colonies', 'old_colonies.code', '=', 'record_room_files.colony_code')
            ->select(
            'file_requests.id',
            'file_requests.status',
            'record_room_files.old_property_id',
            'file_requests.request_section',
            'file_requests.date_of_request',
            // 'file_requests.current_section',
            'file_requests.request_remark',
            'file_requests.created_by',
            'file_requests.requisition_file',
            'file_requests.returned_file_to_record',
            'old_colonies.name as colony_name', 
            'record_room_files.block',
            'record_room_files.plot',
            'record_room_files.file_location',
            'record_room_files.record_id',
            'file_requests.record_room_file_id',

            )
            
        
        ;
         $sectionCodes = Section::whereIn('id', $sections)->pluck('section_code')->toArray();
         if($sectionCodes[0] != "REC"){
            $data->whereIn('record_room_files.section_code', $sectionCodes);
        }

         if ($request->has('locality_record_at_record') && $request->locality_record_at_record != '') {
            $data->where('file_requests.colony_id', $request->locality_record_at_record);
        }
        if ($request->has('block_record_at_record') && $request->block_record_at_record != '') {
            $data->where('file_requests.block', $request->block_record_at_record);
        }
        if ($request->has('plot_record_at_record') && $request->plot_record_at_record != '') {
            $data->where('file_requests.plot', $request->plot_record_at_record);
        }

        

        // onclick="handleViewApplication(\'' . $application->application_no . '\', \'' . $model . '\', ' . $application->id . ')"

        return DataTables::of($data)
        ->editColumn('colony_code', function ($row) {
            return $row->colony_name ?? $row->colony_code;
        })
        ->addColumn('fileUpload', function ($row) {
                $html = '';

                // Show file link only if requisition_file exists
                if (!empty($row->requisition_file)) {
                   $html .= '<a href="' . asset('storage/' . ltrim(str_replace('record-room/', '', $row->requisition_file), '/')) . '" target="_blank" class="fs-4" title="View File">
            <i class="bx bx-file"></i>
          </a>';
                }

                // Always show file input
                if ($row->status != 'accepted') {
                    $html .= '<input type="file" name="fileUpload" 
                                id="fileUpload_' . $row->record_room_file_id . '" 
                                data-record-id="' . $row->record_room_file_id . '" 
                                onchange="uploadFile(this)">';
                }

                return $html;
            })
            ->addColumn('fileReceived', function ($row) {
                $fileReceived = '';
                if ($row->status != 'accepted' && $row->status != 'rejected') {
                    $fileReceived .= '<a href="' . route('file.request.requisition.letter', ['id' => $row->id]) . '" class="fs-4">
                                <i class="bx bx-file"></i>
                            </a>';
                }
                return $fileReceived;
                })

            ->addColumn('action', function ($row) {
                $action = '';

                if ($row->status !== 'accepted') {
                    if($row->status == 'rejected') {
                        $action = '<span class="highlight_value statusRejected">Rejected</span>';
                    } else{
                        $action .= '<div class="d-flex gap-2">';
                        $action .= '<button type="button" class="btn btn-primary px-5" onclick="acceptRequest(' . $row->id . ')">Accept</button>';
                        $action .= '<button type="button" class="btn btn-danger px-5" onclick="openCancelRequestFileModal(' . $row->id . ')">Cancel</button>';
                        $action .= '</div>';
                    }
                } else {
                    $action = '<span class="highlight_value statusSecondary">Accepted</span>';
                }

                return $action;
            })
            ->addColumn('sectionAction', function ($row) {

                 $sectionAction = '';

                if ($row->status == 'accepted') {
                    $sectionAction .= '<div class="d-flex gap-2">';
                    $sectionAction .= '<button type="button" class="btn btn-primary px-5" onclick="returnRequest(' . $row->id . ')">Return to Record</button>';
                    $sectionAction .= '</div>';
                } else if ($row->status == 'returned') {
                    $sectionAction = '<span class="highlight_value statusSecondary">Returned</span>';
                } else  if($row->status == 'rejected') {
                        $sectionAction = '<span class="highlight_value statusRejected">Rejected</span>';
                }

                return $sectionAction;
            })
            ->addColumn('sectionFileUpload', function ($row) {
                $html = '';

                // Show file link only if returned_file_to_record exists
                if (!empty($row->returned_file_to_record)) {
                    $html .= '<a href="' . asset('storage/' . ltrim(str_replace('record-room/', '', $row->returned_file_to_record), '/')) . '" target="_blank" class="fs-4" title="View File">
                                <i class="bx bx-file"></i>
                            </a>';
                }

                // Always show file input
                if($row->status != 'returned'){
                    $html .= '<input type="file" name="sectionfileUpload" 
                                id="fileUpload_' . $row->record_room_file_id . '" 
                                data-record-id="' . $row->record_room_file_id . '" 
                                onchange="uploadFile(this)">';
                }

                return $html;
            })
            ->addColumn('sectionFileReturned', function ($row) {
                $fileHtml = '';
                if($row->status != 'returned') {
                    $fileHtml .= '<a href="' . route('file.return.letter', ['id' => $row->id]) . '" class="fs-4">
                                <i class="bx bx-file"></i>
                            </a>';
                }
                return $fileHtml;
            })
            ->addIndexColumn()
            ->rawColumns(['fileUpload','fileReceived','action','sectionAction','sectionFileUpload','sectionFileReturned'])
            ->make(true);
    }



    public function fileRequestStore(Request $request)
    {
        $request->validate([
            'colony' => 'required',
            'block' => 'required',
            'plot' => 'required',
            'file_location' => 'required',
            'section_code' => 'required',
        ]);

        RecordRoomFile::create([
            'colony_id' => $request->colony,
            'colony_code' => OldColony::find($request->colony)->code,
            'block' => $request->block,
            'plot' => $request->plot,
            'file_location' => $request->file_location,
            'section_code' => $request->section_code
        ]);

        return redirect()->route('recordRoom.fileRequest')->with('success', 'File request created successfully.');
    }


    public function requestFile(Request $request)
    {
        $request->validate([
            'record_id' => 'required',
            'request_remark' => 'nullable|string|max:500',
        ]);

        $recordRoomFile = RecordRoomFile::find($request->record_id);
        if (!$recordRoomFile) {
            return redirect()->back()->with('error', 'Record not found.');
        }
        // dd(getLoggedInUserSections());
        $sections = getLoggedInUserSections();
        if (empty($sections)) {
            return redirect()->back()->with('error', 'You do not have permission to request this file.');
        }
        $sectionCode = Section::where('id', $sections[0])->pluck('section_code')->first();

        FileRequest::create([
            'record_room_file_id' => $request->record_id,
            'request_section' => $sectionCode,
            'date_of_request' => Carbon::today(),
            'current_section' => !empty($recordRoomFile->current_section_code) 
                                    ? $recordRoomFile->current_section_code 
                                    : $recordRoomFile->transaction_section_code,
            'colony_id' => $recordRoomFile->colony_id,
            'block' => $recordRoomFile->block,
            'plot' => $recordRoomFile->plot,
            'request_remark' => $request->request_remark,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('recordRoom.fileRequest')->with('success', 'File request submitted successfully.');
    }


    public function cancelRequestFile(Request $request)
    {
        $request->validate([
            'cancel_record_id' => 'required',
            'cancel_request_remark' => 'nullable|string|max:500',
        ]);

        $forestRequest = FileRequest::where('id', $request->cancel_record_id)->first();
        $forestRequest->status = 'rejected';
        $forestRequest->rejection_reason = $request->cancel_request_remark;
        $forestRequest->updated_by = auth()->id();
        $forestRequest->save();
        return redirect()->route('recordRoom.fileRequest')->with('success', 'File request cancelled successfully.');
    }



    public function requestFileUpload(Request $request)
    {
            if ($request->hasFile('file')) {
            $file = $request->file('file');
           if ($request->name == 'sectionfileUpload') {
                $path = 'files/record_room/returned' . $request->record_id;
                $fileName = 'returned_file_to_record';
            } else {
                $path = 'files/record_room/request' . $request->record_id;
                $fileName = 'requisition_file';
            }

            $fileRequisition = GeneralFunctions::uploadFile($file, $path, $fileName); 
            
            if (!$fileRequisition) {
                return response()->json(['status' => 'error', 'message' => 'File upload failed'], 500);
            }
            $fileRequest = FileRequest::where('record_room_file_id', $request->record_id)
                ->first();
            if (!$fileRequest) {
                return response()->json(['status' => 'error', 'message' => 'File request not found'], 404);
            }
             if($request->name == 'sectionfileUpload'){
                $fileRequest->returned_file_to_record = $fileRequisition;
             } else {
                 $fileRequest->requisition_file = $fileRequisition;
             }
            $fileRequest->updated_by = auth()->id();
            $fileRequest->save();
            return response()->json(['status' => 'success', 'message' => 'File uploaded successfully'], 200);
        }

        return response()->json(['status' => 'error', 'message' => 'No file uploaded'], 400);
    }



    //create file requisition letters
    public function fileRequestRequisitionLetter(Request $request,$id)
    {
        $fieRequest = FileRequest::find($id);
        if (!$fieRequest) {
            return redirect()->back()->with('error', 'File request not found.');
        }
        $requestUser = User::where('id', $fieRequest->created_by)->first();
        $requestedUserName = $requestUser->name;
        $designation = $requestUser->designation->name;
        $recordRoomFile = RecordRoomFile::find($fieRequest->record_room_file_id);
        if (!$recordRoomFile) {
            return redirect()->back()->with('error', 'Record room file not found.');
        }
        $sections = getLoggedInUserSections();
        if (empty($sections)) {
            return redirect()->back()->with('error', 'You do not have permission to view this file.');
        }
        $sectionCode = Section::where('id', $sections[0])->pluck('section_code')->first();
        $colony = OldColony::find($recordRoomFile->colony_id);
        if (!$colony) {
            return redirect()->back()->with('error', 'Colony not found.');
        }
        $data = [
            'fileRequest' => $fieRequest,
            'recordRoomFile' => $recordRoomFile,
            'sectionCode' => $sectionCode,
            'colony' => $colony,
            'dateOfRequest' => Carbon::parse($fieRequest->date_of_request)->format('d-m-Y'),
            'currentSection' => Section::where('section_code', $fieRequest->current_section)->pluck('name')->first(),
        ];

        $pdf = Pdf::loadView('admin/record/file_requisition',[
            'block' => $fieRequest->block,
            'plot' => $fieRequest->plot,
            'colony' => $colony->name,
            'dateOfRequest' => $data['dateOfRequest'],
            'file_location' => $recordRoomFile->file_location,
            'requestSection' => $fieRequest->request_section,
            'requestedUserName' => $requestedUserName,
            'designation' => $designation,
        ]);
        $filename = "FileRequisition_".$id.".pdf";

        return $pdf->download($filename);
    }


    public function requestFileAccept(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $fileRequest = FileRequest::find($request->id);
        if (!$fileRequest) {
            return response()->json(['status' => 'failure', 'message' => 'File request not found.']);
        }

        if ($fileRequest->requisition_file == null) {
            return response()->json(['status' => 'failure', 'message' => 'Please upload the requisition file.']);
        }
        // Update the file request status or perform any other action as needed
        $fileRequest->status = 'accepted';
        $fileRequest->updated_by = auth()->id();
        $fileRequest->save();

        return response()->json(['status' => 'success', 'message' => 'File request accepted successfully']);
    }



    public function requestFileReturn(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $fileRequest = FileRequest::find($request->id);
        if (!$fileRequest) {
            return response()->json(['status' => 'failure', 'message' => 'File request not found.']);
        }

        if ($fileRequest->returned_file_to_record == null) {
            return response()->json(['status' => 'failure', 'message' => 'Please upload the letter for file return to record section.']);
        }
        // Update the file request status or perform any other action as needed
        $fileRequest->status = 'returned';
        $fileRequest->updated_by = auth()->id();
        $fileRequest->save();

        return response()->json(['status' => 'success', 'message' => 'File returned to record successfully']);
    }

    public function fileReturnLetter(Request $request, $id){
        $fieRequest = FileRequest::find($id);
        if (!$fieRequest) {
            return redirect()->back()->with('error', 'File request not found.');
        }
        $requestUser = User::where('id', $fieRequest->created_by)->first();
        $requestedUserName = $requestUser->name;
        $designation = $requestUser->designation->name;
        $recordRoomFile = RecordRoomFile::find($fieRequest->record_room_file_id);
        if (!$recordRoomFile) {
            return redirect()->back()->with('error', 'Record room file not found.');
        }
        $sections = getLoggedInUserSections();
        if (empty($sections)) {
            return redirect()->back()->with('error', 'You do not have permission to view this file.');
        }
        $sectionCode = Section::where('id', $sections[0])->pluck('section_code')->first();
        $colony = OldColony::find($recordRoomFile->colony_id);
        if (!$colony) {
            return redirect()->back()->with('error', 'Colony not found.');
        }
        $data = [
            'fileRequest' => $fieRequest,
            'recordRoomFile' => $recordRoomFile,
            'sectionCode' => $sectionCode,
            'colony' => $colony,
            'dateOfRequest' => Carbon::parse($fieRequest->date_of_request)->format('d-m-Y'),
            'currentSection' => Section::where('section_code', $fieRequest->current_section)->pluck('name')->first(),
        ];

        $pdf = Pdf::loadView('admin/record/file_return',[
            'block' => $fieRequest->block,
            'plot' => $fieRequest->plot,
            'colony' => $colony->name,
            'dateOfRequest' => $data['dateOfRequest'],
            'sectionCode' => $data['sectionCode'],
            'file_location' => $recordRoomFile->file_location,
            'requestSection' => $fieRequest->request_section,
            'requestedUserName' => $requestedUserName,
            'designation' => $designation,
        ]);
        // $pdf = Pdf::loadView('payment.payment_receipt', [
        //     'payment' => $payment,
        //     'amount_in_words' => $amountInWords
        // ]);

        $filename = "File_return_letter_".$id.".pdf";

        return $pdf->download($filename);
    }

     public function exportCsv(Request $request)
{
    $sections = getLoggedInUserSections();

    $query = RecordRoomFile::query()
        ->leftJoin('old_colonies', 'old_colonies.code', '=', 'record_room_files.colony_code')
        ->select([
            'record_room_files.old_property_id',
            'record_room_files.record_id',
            'old_colonies.name as colony_name',
            'record_room_files.block',
            'record_room_files.plot',
            'record_room_files.file_location',
            'record_room_files.section_code',
            'record_room_files.transaction_section_code',
        ]);

    // ✅ section restriction (same as listing)
    $sectionCodes = Section::whereIn('id', $sections)->pluck('section_code')->toArray();
    if (!empty($sectionCodes) && $sectionCodes[0] !== "REC") {
        $query->whereIn('record_room_files.section_code', $sectionCodes);
    }

    // ✅ dropdown filters (same as listing)
    if ($request->filled('locality_record')) {
        $query->where('record_room_files.colony_id', $request->locality_record);
    }
    if ($request->filled('block_record')) {
        $query->where('record_room_files.block', $request->block_record);
    }
    if ($request->filled('plot_record')) {
        $query->where('record_room_files.plot', $request->plot_record);
    }
    if ($request->filled('section_code')) {
        $query->where('record_room_files.section_code', $request->section_code);
    }

    // ✅ global search (DataTables style: search[value])
    $search = $request->input('search.value');
    if ($search === null) $search = $request->input('search');
    if (is_array($search)) $search = $search['value'] ?? '';
    $search = trim((string) $search);

    if ($search !== '') {
        $query->where(function ($q) use ($search) {
            $q->where('record_room_files.old_property_id', 'like', "%{$search}%")
              ->orWhere('old_colonies.name', 'like', "%{$search}%")
              ->orWhere('record_room_files.block', 'like', "%{$search}%")
              ->orWhere('record_room_files.plot', 'like', "%{$search}%")
              ->orWhere('record_room_files.file_location', 'like', "%{$search}%")
              ->orWhere('record_room_files.section_code', 'like', "%{$search}%")
              ->orWhere('record_room_files.transaction_section_code', 'like', "%{$search}%");
        });
    }

    // ✅ ordering (match your record-list DataTable column indexes)
    // 0 DT_RowIndex (S.No), 1 record_id, 2 colony_name, 3 block, 4 plot, 5 file_location, 6 section_code, 7 transaction_section_code
    $orderColumnIndex = (int) $request->input('order.0.column', 1);
    $orderDir = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';

    $orderMap = [
        0 => 'record_room_files.id',                     // S.No -> sort by ID
        1 => 'record_room_files.old_property_id',
        2 => 'old_colonies.name',
        3 => 'record_room_files.block',
        4 => 'record_room_files.plot',
        5 => 'record_room_files.file_location',
        6 => 'record_room_files.section_code',
        7 => 'record_room_files.transaction_section_code',
    ];

    $orderBy = $orderMap[$orderColumnIndex] ?? 'record_room_files.id';
    $query->orderBy($orderBy, $orderDir);

    // ✅ Fetch all rows (export ignores pagination)
    $rows = $query->get();

    $filename = 'record_room_files_' . now()->format('Ymd_His') . '.csv';

    $headers = [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
    ];

    $callback = function () use ($rows) {
        $out = fopen('php://output', 'w');

        // Excel-friendly UTF-8 BOM
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

        // ✅ Header with S.No.
        fputcsv($out, [
            'S.No.',
            'Property ID',
            'Colony Name',
            'Block',
            'Plot',
            'File Location',
            'Section',
            'Current Section',
        ]);

        $i = 1;
        foreach ($rows as $r) {
            fputcsv($out, [
                $i++,                                
                $r->old_property_id ?? '-',
                $r->colony_name ?? '-',
                $r->block ?? '-',
                $r->plot ?? '-',
                $r->file_location ?? '-',
                $r->section_code ?? '-',
                $r->transaction_section_code ?? '-',
            ]);
        }

        fclose($out);
    };

    return response()->stream($callback, 200, $headers);
}

public function exportExcel(Request $request)
{
    return Excel::download(
        new RecordRoomFilesExport(auth()->user(), $request),
        'record_room_files_' . now()->format('Ymd_His') . '.xlsx'
    );
}



}
