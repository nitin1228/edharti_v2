<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ColonyService;
use App\Models\RecordRoomFile;
use App\Models\Section;
use App\Models\OldColony;
use Yajra\DataTables\Facades\DataTables;

class RecordController extends Controller
{

    public function index()
    {
         return view('admin.record.index');
    }



     public function getRecordRoomFilesData(Request $request)
    {

        $sections = getLoggedInUserSections();
        $sectionCodes = Section::whereIn('id', $sections)->pluck('section_code')->toArray();

        $data = RecordRoomFile::select(
            'record_room_files.record_id',
            'record_room_files.colony_code',
            'record_room_files.block',
            'record_room_files.plot',
            'record_room_files.file_location',
            'record_room_files.section_code',
            'record_room_files.transaction_section_code',
        );
        $data->whereIn('record_room_files.section_code', $sectionCodes);

        // if ($request->has('state') && $request->state != '') {
        //     $data->where('property_outsides.state_id', $request->state);
        // }
        // if ($request->has('status') && $request->status != '') {
        //     $data->where('property_outsides.present_status', $request->status);
        // }
// dd($data);
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
}
