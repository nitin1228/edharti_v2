<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\PropertyMaster;
use App\Models\PropertyOutSide;
use App\Models\State;
use App\Services\ColonyService;
use App\Services\MisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PropertyOutSideController extends Controller
{

    public function index(Request $request)
    {
        return view('properties_out_side.indexDatatable');
    }

    public function getUnallotedOutsideDelhiPropertyData(Request $request)
    {
        // Get the logged-in user
        $user = Auth::user();
        $data = PropertyOutSide::with(['state', 'city', 'propertyStatus'])->select('property_outsides.*');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                return $row->propertyStatus->item_name ?? 'N/A';
            })
            ->addColumn('land_type', function ($row) {
                return $row->landType->item_name ?? 'N/A';
            })
            ->editColumn('user_by_any_department', function ($row) {
                if ($row->user_by_any_department) {
                    $departmentName = $row->department ?? 'N/A';
                    return "Yes<br><small class='text-muted'>Department Name: {$departmentName}</small>";
                } else {
                    return 'No';
                }
            })
            ->editColumn('encroached', function ($row) {
                return $row->encroached ? 'Yes' : 'No';
            })
            ->editColumn('custody_date', function ($row) {
                return $row->custody_date ? date('d-m-Y', strtotime($row->custody_date)) : 'N/A';
            })
            ->addColumn('land_use', function ($row) {
                return $row->landUse->item_name ?? 'N/A';
            })
            ->addColumn('state', function ($row) {
                return $row->state->name ?? 'N/A';
            })
            ->addColumn('city', function ($row) {
                return $row->city->name ?? 'N/A';
            })
            ->addColumn('action', function ($row) {
                $url = route('vacant.land.view', $row->id);
                return '<a href="' . $url . '" class="btn btn-sm btn-primary">View</a>';
            })

            ->rawColumns(['action', 'user_by_any_department'])
            ->make(true);

        return response()->json($json_data);
    }

    public function create(Request $request, MisService $misService, ColonyService $colonyService)
    {
        $colonyList = $colonyService->getColonyList();
        $propertyStatus = $misService->getItemsByGroupId(109);
        $landTypes = $misService->getItemsByGroupId(1051);
        $states = State::where('country_id', 101)->orderBy('name')->get();
        $propertyTypes = $misService->getItemsByGroupId(1052);
        return view('properties_out_side.create', compact(['colonyList', 'propertyStatus', 'propertyTypes', 'landTypes', 'states']));
    }

    public function getCities(Request $request)
    {
        $stateId = $request->state_id;
        $cities = City::where('state_id', $stateId)->get(['id', 'name']);
        return response()->json(['cities' => $cities]);
    }

    //store the MIS Form data
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                // 'property_id' => 'required|unique:property_outsides,property_master_id',
                // 'file_number' => 'required',
                // 'present_colony_name' => 'required|exists:old_colonies,id',
                'property_status' => 'required',
                'land_type' => 'required',
                'state' => 'required|exists:states,id',
                'city' => 'required|exists:cities,id',
                'address' => 'required|string',
                'area' => 'required|numeric',
                'received_from' => 'nullable|string',
                'custody_date' => 'nullable|date',
                // 'land_use' => 'nullable|string',
                'user_by_any_department' => 'required|in:Yes,No',
                'department' => 'nullable|required_if:user_by_any_department,Yes|string',
                'encroached' => 'required|in:Yes,No',
                'remarks' => 'nullable|string',
            ]);

            // Check for duplicate in PropertyMaster (optional if enforced only by unique in PropertyOutside)
            if (!empty($request->property_id)) {
                if (PropertyMaster::where('old_propert_id', $request->property_id)->exists()) {
                    $msg = "Property Id already exists: " . $request->property_id;
                    Log::info($msg);
                    return response()->json(['status' => false, 'message' => $msg]);
                }
            }

            // Check for duplicate in PropertyMaster (optional if enforced only by unique in PropertyOutside)
            if (!empty($request->property_id)) {
                if (PropertyOutSide::where('old_property_id', $request->property_id)->exists()) {
                    $msg = "Duplicate Property Id in outside Delhi: " . $request->property_id;
                    Log::info($msg);
                    return response()->json(['status' => false, 'message' => $msg]);
                }
            }

            // Start Transaction
            DB::beginTransaction();

            /* $propertyMaster = PropertyMaster::create([
                'old_propert_id' => $validated['property_id'],
                'unique_propert_id' => self::getProppertyId(),
                'file_no' => $validated['file_number'] ?? '',
                'land_type' => $validated['land_type'] ?? '',
                'new_colony_name' => $validated['present_colony_name'] ?? '',
                'status' => $validated['property_status'] ?? '',
                'additional_remark' => $validated['remarks'] ?? '',
                'created_by' => Auth::id(),
            ]);

            if (!$propertyMaster) {
                DB::rollBack();
                Log::error("Failed to create PropertyMaster");
                return response()->json(['status' => false, 'message' => 'Failed to save property master data.']);
            } 

            $propertyOutSide = PropertyOutSide::create([
                'property_master_id' => $propertyMaster->id,
                'old_property_id' => $validated['property_id'],
                'state_id' => $validated['state'],
                'city_id' => $validated['city'],
                'address' => $validated['address'],
                'area' => $validated['area'],
                'received_from' => $validated['received_from'] ?? null,
                'custody_date' => $validated['custody_date'] ?? null,
                'land_use' => $validated['land_use'] ?? null,
                'user_by_any_department' => $validated['user_by_any_department'] === 'Yes' ? 1 : 0,
                'department' => $validated['department'] ?? null,
                'encroached' => $validated['encroached'] === 'Yes' ? 1 : 0,
                'remarks' => $validated['remarks'] ?? '',
                'created_by' => Auth::user()->id,
            ]);
            */
            $propertyOutSide = PropertyOutSide::create([
                'property_master_id' => null,
                'old_property_id' => $request->property_id ?? null,
                'file_no' => $request->file_number ?? null,
                'state_id' => $request->state,
                'city_id' => $request->city,
                'address' => $request->address,
                'land_type' => $request->land_type ?? null,
                'status' => $request->property_status ?? null,
                'area' => $request->area,
                'received_from' => $request->received_from ?? null,
                'custody_date' => $request->custody_date ?? null,
                'land_use' => $request->land_use ?? null,
                'user_by_any_department' => $request->user_by_any_department === 'Yes' ? 1 : 0,
                'department' => $request->department ?? null,
                'encroached' => $request->encroached === 'Yes' ? 1 : 0,
                'remarks' => $request->remarks ?? '',
                'created_by' => Auth::user()->id,
            ]);

            if (!$propertyOutSide) {
                DB::rollBack();
                Log::error("Failed to create PropertyOutSide");
                return response()->json(['status' => false, 'message' => 'Failed to save property outside data.']);
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Property Outside details saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack(); // ensure rollback on exception
            Log::error('Error saving property details: ' . $e->getMessage());
            return redirect()->back()->with('failure', 'Something went wrong. Please try again later.');
        }
    }

    //create a automated unique property ID
    public function getProppertyId()
    {
        $lastRecord = PropertyMaster::latest()->first();
        //dd($lastRecord);
        if ($lastRecord) {
            $lastId = (int) substr($lastRecord->unique_propert_id, 1);
            $nextId = 'L' . str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $nextId = 'L000001';
        }
        return $nextId;
    }

    public function show($id)
    {
        $property = PropertyOutSide::with(['state', 'city', 'propertyStatus', 'landType', 'landUse'])->findOrFail($id);
        return view('properties_out_side.view', compact('property'));
    }
}
