<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\PresentCustodian;
use App\Models\PropertyMaster;
use App\Models\PropertyOutside;
use App\Models\State;
use App\Services\ColonyService;
use App\Services\MisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PropertyOutsideController extends Controller
{

    public function index(Request $request)
    {
      //  return view('properties_out_side.indexDatatable');
       $uniqueStates = PropertyOutside::select(
            'states.id',
            'states.name as state_name'
        )
        ->leftJoin('states', 'property_outsides.state_id', '=', 'states.id')
        ->groupBy('states.id', 'states.name')
->orderBy('states.name')
        ->get();

        $uniqueStatuses = PropertyOutside::select(
            'items.id',
            'items.item_name'
        )
        ->leftJoin('items', 'property_outsides.present_status', '=', 'items.id')
        ->groupBy('items.id', 'items.item_name')
       
        ->get();        
        return view('properties_out_side.indexDatatable',compact(['uniqueStates','uniqueStatuses']));
    }

    public function getUnallotedOutsideDelhiPropertyData(Request $request)
    {
        $data = PropertyOutside::select(
            'property_outsides.id',
            'property_outsides.address',
            'property_outsides.area',
            'property_outsides.custody_date',
            'states.name as state_name',
            'cities.name as city_name',
            'present_custodians.item_name as custodian_name',
            'items.item_name as status_name'
        )
            ->leftJoin('states', 'property_outsides.state_id', '=', 'states.id')
            ->leftJoin('cities', 'property_outsides.city_id', '=', 'cities.id')
            ->leftJoin('present_custodians', 'property_outsides.present_custodian', '=', 'present_custodians.id')
            ->leftJoin('items', 'property_outsides.present_status', '=', 'items.id');
            
          if ($request->has('state') && $request->state != '') {
            $data->where('property_outsides.state_id', $request->state);
        }
        if ($request->has('city') && $request->city != '') {
            $data->where('property_outsides.city_id', $request->city);
        }
        if ($request->has('status') && $request->status != '') {
            $data->where('property_outsides.present_status', $request->status);
        }
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('custody_date', function ($row) {
                return $row->custody_date ? date('d-m-Y', strtotime($row->custody_date)) : 'N/A';
            })
            ->addColumn('action', function ($row) {
                $actions = '';

                if (auth()->user()->can('view.vacant.land')) {
                    $viewUrl = route('vacant.land.view', $row->id);
                    $actions .= '<a href="' . $viewUrl . '" class="btn btn-sm btn-primary me-2">View</a>';
                }

                if (auth()->user()->can('edit.vacant.land')) {
                    $editUrl = route('vacant.land.edit', $row->id);
                    $actions .= '<a href="' . $editUrl . '" class="btn btn-sm btn-secondary">Edit</a>';
                }

                return $actions;
            })

            ->rawColumns(['action'])
            ->make(true);
    }

    public function create(Request $request, MisService $misService, ColonyService $colonyService)
    {
        $colonyList = $colonyService->getColonyList();
        $presentStatus = $misService->getItemsByGroupId(17013);
        $landTypes = $misService->getItemsByGroupId(1051);
        $states = State::where('country_id', 101)->orderBy('name')->get();
        $propertyTypes = $misService->getItemsByGroupId(1052);
        $presentCustodians = PresentCustodian::where('is_active', 1)->get();
        return view('properties_out_side.create', compact(['colonyList', 'presentStatus', 'propertyTypes', 'landTypes', 'states', 'presentCustodians']));
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
            $rules = [
                'state' => 'required|exists:states,id',
                'city' => 'required|exists:cities,id',
                'address' => 'required|string',
                'area' => 'required|numeric',
                'present_custodian' => 'required',
                'present_status' => 'required',
                'present_status_details' => 'required|string',
                'court_case' => 'required|in:Yes,No',
                'court_case_details' => 'nullable|required_if:court_case,Yes|string',
                'user_by_any_department' => 'required|in:Yes,No',
                'department' => 'nullable|required_if:user_by_any_department,Yes|string',
                'remarks' => 'nullable|string',
            ];

            if ($request->filled('present_custodian')) {
                $itemName = PresentCustodian::where('id', $request->present_custodian)->value('item_name');

                if ($itemName === 'Other') {
                    $rules['present_custodian_details'] = 'required';
                }
            }

            $request->validate($rules);

            // Start Transaction
            DB::beginTransaction();


            $propertyOutSide = PropertyOutside::create([
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
                'present_custodian' => $request->present_custodian ?? null,
                'present_custodian_details' => $request->present_custodian_details ?? null,
                'present_status' => $request->present_status ?? null,
                'present_status_details' => $request->present_status_details ?? null,
                'court_case'  => $request->court_case === 'Yes' ? 1 : 0,
                'court_case_details' => $request->court_case_details ?? null,
                'remarks' => $request->remarks ?? '',
                'created_by' => Auth::user()->id,
            ]);

            if (!$propertyOutSide) {
                DB::rollBack();
                Log::error("Failed to create PropertyOutside");
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

    public function edit($id, MisService $misService, ColonyService $colonyService)
    {
        $property = PropertyOutside::findOrFail($id);
        $colonyList = $colonyService->getColonyList();
        $presentStatus = $misService->getItemsByGroupId(17013);
        $landTypes = $misService->getItemsByGroupId(1051);
        $states = State::where('country_id', 101)->orderBy('name')->get();
        $propertyTypes = $misService->getItemsByGroupId(1052);
        $presentCustodians = PresentCustodian::where('is_active', 1)->get();

        // Get cities for the selected state
        $cities = City::where('state_id', $property->state_id)->get();

        return view('properties_out_side.edit', compact(
            'property',
            'colonyList',
            'presentStatus',
            'landTypes',
            'states',
            'propertyTypes',
            'presentCustodians',
            'cities'
        ));
    }


    public function update(Request $request, $id)
    {
        $property = PropertyOutside::findOrFail($id);
        $rules = [
            'state' => 'required|exists:states,id',
            'city' => 'required|exists:cities,id',
            'address' => 'required|string',
            'area' => 'required|numeric',
            'present_custodian' => 'required',
            'present_status' => 'required',
            'court_case' => 'required|in:Yes,No',
            'court_case_details' => 'nullable|required_if:court_case,Yes|string',
            'user_by_any_department' => 'required|in:Yes,No',
            'department' => 'nullable|required_if:user_by_any_department,Yes|string',
            'remarks' => 'nullable|string',
        ];

        if ($request->present_custodian) {
            $name = PresentCustodian::find($request->present_custodian)?->item_name;
            if ($name === 'Other') {
                $rules['present_custodian_details'] = 'required|string';
            }
        }

        $request->validate($rules);
        try {
            DB::beginTransaction();

            $property->update([
                'state_id' => $request->state,
                'city_id' => $request->city,
                'address' => $request->address,
                'area' => $request->area,
                'present_custodian' => $request->present_custodian,
                'present_custodian_details' => $request->present_custodian_details,
                'custody_date' => $request->custody_date,
                'present_status' => $request->present_status,
                'present_status_details' => $request->present_status_details,
                'land_use' => $request->land_use,
                'court_case' => $request->court_case === 'Yes',
                'court_case_details' => $request->court_case_details,
                'user_by_any_department' => $request->user_by_any_department === 'Yes',
                'department' => $request->department,
                'remarks' => $request->remarks,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Property Outside details updated successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Update outside Delhi property failed: ' . $e->getMessage());
            return back()->with('failure', 'Something went wrong, please try again.');
        }
    }


    //create a automated unique property ID
    public function getProppertyId()
    {
        $lastRecord = PropertyMaster::latest()->first();
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
        $property = PropertyOutside::with(['state', 'city', 'presentStatus', 'presentCustodian', 'landUse'])->findOrFail($id);
        return view('properties_out_side.view', compact('property'));
    }
    public function getVacantLandCities($stateId)
    {
        $cities = PropertyOutside::select(
            'cities.id',
            'cities.name as city_name'
        )
            ->leftJoin('cities', 'property_outsides.city_id', '=', 'cities.id')
            ->where('property_outsides.state_id', $stateId)
            ->groupBy('cities.id', 'cities.name')
            ->orderBy('cities.name')
            ->get();
        return response()->json($cities);
    }
}
