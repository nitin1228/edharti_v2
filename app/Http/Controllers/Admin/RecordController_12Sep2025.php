<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ColonyService;
use App\Models\RecordRoomFile;
use App\Models\Section;
use App\Models\OldColony;
use App\Models\FileRequest;
use Yajra\DataTables\Facades\DataTables;

class RecordController extends Controller
{

    public function index(ColonyService $colonyService)
    {
        $colonyList = $colonyService->getColonyList();
        return view('admin.record.index',compact(['colonyList']));
    }



     public function getRecordRoomFilesData(Request $request)
    {

        $sections = getLoggedInUserSections();
        $sectionCodes = Section::whereIn('id', $sections)->pluck('section_code')->toArray();

        $data = RecordRoomFile::select(
            'record_room_files.record_id',
            'record_room_files.colony_code',
            'record_room_files.colony_id',
            'record_room_files.block',
            'record_room_files.plot',
            'record_room_files.file_location',
            'record_room_files.section_code',
            'record_room_files.transaction_section_code',
        );
        $data->whereIn('record_room_files.section_code', $sectionCodes);

        if ($request->has('locality_record') && $request->locality_record != '') {
            $data->where('record_room_files.colony_id', $request->locality_record);
        }
        if ($request->has('block_record') && $request->block_record != '') {
            $data->where('record_room_files.block', $request->block_record);
        }
        if ($request->has('plot_record') && $request->plot_record != '') {
            $data->where('record_room_files.plot', $request->plot_record);
        }

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }



    //
    public function create(ColonyService $colonyService)
    {

         $colonyList = $colonyService->getColonyList();
        return view('admin.record.create',compact(['colonyList']));
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
        $colonyCode = OldColony::find($request->localityRecord)->pluck('code')->first();
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

        $sections = getLoggedInUserSections();
        $sectionCodes = Section::whereIn('id', $sections)->pluck('section_code')->toArray();

        $data = FileRequest::select(
            'file_requests.record_room_file_id',
            'file_requests.request_section',
            'file_requests.date_of_request',
            'file_requests.current_section',
            'file_requests.request_remark',
            'file_requests.created_by',
            'record_room_files.colony_code',
            'record_room_files.block',
            'record_room_files.plot',
            'record_room_files.file_location',
            'record_room_files.record_id',
        )
        ->leftJoin('record_room_files', 'file_requests.record_room_file_id', '=', 'record_room_files.id');
        $data->whereIn('record_room_files.section_code', $sectionCodes);
// dd($data);
        // if ($request->has('state') && $request->state != '') {
        //     $data->where('property_outsides.state_id', $request->state);
        // }
        // if ($request->has('status') && $request->status != '') {
        //     $data->where('property_outsides.present_status', $request->status);
        // }
        return DataTables::of($data)
            ->addIndexColumn()
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
}
