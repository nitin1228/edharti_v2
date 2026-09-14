<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Litigation;
use App\Models\Item;
use App\Models\Section;
use App\Models\CaseHearing;
use App\Services\CommonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LitigationController extends Controller
{
    private function getLitigationFormData(): array
    {
        $judicialAuthorities = Item::where('group_id', 12001)
            ->whereRaw('LOWER(item_name) != ?', ['select'])
            ->pluck('item_name');

        $caseTypes = Item::where('group_id', 12003)
            ->whereRaw('LOWER(item_name) != ?', ['select'])
            ->pluck('item_name');

        $designationTypes = Item::where('group_id', 12012)
            ->whereRaw('LOWER(item_name) != ?', ['select'])
            ->pluck('item_name');

        $partyTypes = Item::where('group_id', 12002)
            ->whereRaw('LOWER(item_name) != ?', ['select'])
            ->pluck('item_name');

        $partySubtypes = Item::where('group_id', 12005)
            ->whereRaw('LOWER(item_name) != ?', ['select'])
            ->pluck('item_name');

        $sections = getRequiredSections();

        [$filterUserSections, $userSectionIdList] = getUserAssignedSections();

        $additionalSections = collect([
            new Section(['id' => 10, 'section_code' => 'ADMN', 'name' => 'Administration Section']),
            new Section(['id' => 12, 'section_code' => 'VIG', 'name' => 'Vigilance Section']),
            new Section(['id' => 14, 'section_code' => 'CDN', 'name' => 'Coordination Section']),
            new Section(['id' => 17, 'section_code' => 'ENF', 'name' => 'Enforcement Section']),
        ]);

        if ($filterUserSections) {
            $additionalSectionIds = $additionalSections->pluck('id')->toArray();

            $sections = $sections->filter(function ($section) use ($userSectionIdList, $additionalSectionIds) {
                return in_array($section->id, $userSectionIdList)
                    || in_array($section->id, $additionalSectionIds);
            });
        }

        $sections = $sections
            ->merge($additionalSections)
            ->unique('id')
            ->sortBy('name')
            ->values();

        return compact(
            'judicialAuthorities',
            'caseTypes',
            'designationTypes',
            'partyTypes',
            'partySubtypes',
            'sections'
        );
    }

    private function validateLitigationRequest(Request $request): array
    {
        return $request->validate([
            'property_id' => ['nullable', 'size:5'],
            'property_known_as' => ['nullable', 'string', 'max:255'],
            'case_number' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:litigations,case_number,' . $request->route('id'),
                ],
            'associated_case_number' => ['nullable', 'string', 'max:255'],
            'case_type' => ['required', 'string'],
            'judicial_authority' => ['required', 'string'],
            'court_location' => ['required', 'string', 'max:255'],
            'case_brief' => ['required', 'string', 'max:500'],

            'parties' => ['required', 'array'],
            'parties.*.party_name' => ['required', 'string', 'max:255'],
            'parties.*.party_type' => ['required', 'string'],
            'parties.*.party_subtype' => ['required', 'string'],

            'counsels' => ['required', 'array'],
            'counsels.*.counsel_name' => ['required', 'string', 'max:255'],
            'counsels.*.designation' => ['required', 'string'],
            'counsels.*.mobile' => ['nullable', 'string', 'max:20'],
            'counsels.*.email' => ['nullable', 'email', 'max:255'],
            'counsels.*.appointment_date' => ['nullable', 'date'],
            'counsels.*.address' => ['nullable', 'string'],
            'counsels.*.is_existing' => ['nullable'],

            'hearings' => ['nullable', 'array'],
            'hearings.*.id' => ['nullable', 'integer'],
            'hearings.*.is_existing' => ['nullable'],
            'hearings.*.ldoh' => ['nullable', 'date'],
            'hearings.*.ndoh' => ['nullable', 'date'],
            'hearings.*.ldoh_hearing_details' => ['nullable', 'string', 'max:500'],

            'section' => ['required'],
            'section_counsel' => ['required', 'string', 'max:255'],
            'section_remarks' => ['required', 'string', 'max:255'],
        ]);
    }

    private function prepareLitigationRepeaters(Request $request): array
    {
        $parties = collect($request->parties ?? [])
            ->filter(fn ($party) =>
                !empty($party['party_name']) ||
                !empty($party['party_type']) ||
                !empty($party['party_subtype'])
            )
            ->values()
            ->toArray();

        $counsels = collect($request->counsels ?? [])
            ->filter(fn ($counsel) =>
                !empty($counsel['counsel_name']) ||
                !empty($counsel['designation']) ||
                !empty($counsel['mobile']) ||
                !empty($counsel['email']) ||
                !empty($counsel['appointment_date']) ||
                !empty($counsel['address'])
            )
            ->map(function ($counsel) {
                unset($counsel['is_existing']);
                return $counsel;
            })
            ->values()
            ->toArray();

        $hearings = collect($request->hearings ?? [])
            ->filter(fn ($hearing) =>
                !empty($hearing['ldoh']) ||
                !empty($hearing['ndoh']) ||
                !empty($hearing['ldoh_hearing_details'])
            )
            ->values()
            ->toArray();

        return compact('parties', 'counsels', 'hearings');
    }
    public function create(Request $request, CommonService $commonService)
    {
        $uniqueCaseId = $commonService->getUniqueID(
            Litigation::class,
            'LIT',
            'unique_case_id'
        );

        return view('litigation.input-form', array_merge(
            $this->getLitigationFormData(),
            [
                'uniqueCaseId' => $uniqueCaseId,
                'viewOnly' => false,
            ]
        ));
    }

    public function store(Request $request, CommonService $commonService)
    {
        Log::info('Litigation Store Request Started');
        Log::info($request->all());

        $validated = $this->validateLitigationRequest($request);

        $repeaters = $this->prepareLitigationRepeaters($request);

        $parties = $repeaters['parties'];
        $counsels = $repeaters['counsels'];
        $hearings = $repeaters['hearings'];

        DB::beginTransaction();

        try {
            $uniqueCaseId = $commonService->getUniqueID(
                Litigation::class,
                'LIT',
                'unique_case_id'
            );

            $litigation = Litigation::create([
                'unique_case_id' => $uniqueCaseId,
                'property_id' => $validated['property_id'] ?? null,
                'property_known_as' => $validated['property_known_as'] ?? null,
                'case_number' => $validated['case_number'],
                'associated_case_number' => $validated['associated_case_number'] ?? null,
                'case_type' => $validated['case_type'],
                'judicial_authority' => $validated['judicial_authority'],
                'court_location' => $validated['court_location'],
                'case_brief' => $validated['case_brief'],
                'section' => $validated['section'],
                'section_counsel' => $validated['section_counsel'],
                'parties' => $parties,
                'counsels' => $counsels,
                'section_remarks' => $validated['section_remarks'],
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            foreach ($hearings as $hearing) {
                CaseHearing::create([
                    'litigation_id' => $litigation->id,
                    'ldoh' => $hearing['ldoh'] ?? null,
                    'ndoh' => $hearing['ndoh'] ?? null,
                    'ldoh_hearing_details' => $hearing['ldoh_hearing_details'] ?? null,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('litigation.create')
                ->with('success', 'Litigation created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Litigation Store Error');
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function getPropertySection(Request $request)
    {
        $section = getPropertySectionDetails(
            $request->property_id
        );

        if (!$section) {

            return response()->json([
                'success' => false
            ]);
        }

        return response()->json([
            'success' => true,
            'section_id' => $section['section_id'],
            'section_name' => $section['section_name'],
        ]);
    }

    public function index(Request $request)
    {
        $formData = $this->getLitigationFormData();

        return view('litigation.indexDatatable', [
            'caseTypes' => $formData['caseTypes'],
            'sections' => $formData['sections'],
        ]);
    }

    public function getLitigations(Request $request)
    {
        $columns = [
            'id',
            'unique_case_id',
            'case_number',
            'property_known_as',
            'judicial_authority',
            'section_counsel',
            'latest_ndoh',
            'id',
        ];

        $query = Litigation::withMax('hearings as latest_ndoh', 'ndoh');

        if ($request->filled('case_type') && $request->case_type !== 'All') {
            $query->where('case_type', $request->case_type);
        }

        if ($request->filled('section') && $request->section !== 'All') {
            $query->where('section', $request->section);
        }

        if ($request->filled('property_id')) {
            $query->where('property_id', 'like', '%' . $request->property_id . '%');
        }

        if ($request->filled('date') && $request->filled('dateEnd')) {

            $fromDate = Carbon::parse($request->date)->format('Y-m-d');
            $toDate = Carbon::parse($request->dateEnd)->format('Y-m-d');

            $query->havingBetween('latest_ndoh', [$fromDate, $toDate]);

        } elseif ($request->filled('date')) {

            $fromDate = Carbon::parse($request->date)->format('Y-m-d');

            $query->having('latest_ndoh', '>=', $fromDate);

        } elseif ($request->filled('dateEnd')) {

            $toDate = Carbon::parse($request->dateEnd)->format('Y-m-d');

            $query->having('latest_ndoh', '<=', $toDate);
        }

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $query->where(function ($q) use ($search) {
                $q->where('unique_case_id', 'like', "%{$search}%")
                    ->orWhere('case_number', 'like', "%{$search}%")
                    ->orWhere('associated_case_number', 'like', "%{$search}%")
                    ->orWhere('property_id', 'like', "%{$search}%")
                    ->orWhere('property_known_as', 'like', "%{$search}%")
                    ->orWhere('case_type', 'like', "%{$search}%")
                    ->orWhere('judicial_authority', 'like', "%{$search}%")
                    ->orWhere('court_location', 'like', "%{$search}%")
                    ->orWhere('case_brief', 'like', "%{$search}%")
                    ->orWhere('section_counsel', 'like', "%{$search}%")
                    ->orWhere('section_remarks', 'like', "%{$search}%");
            });
        }

        $totalFiltered = $query->count();
        $totalData = $totalFiltered;

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);

        $orderColumnIndex = $request->input('order.0.column', 1);
        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';
        $orderDir = $request->input('order.0.dir', 'desc');

        $litigations = $query->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($limit)
            ->get();

        $data = [];

        foreach ($litigations as $litigation) {
            $nestedData['id'] = $litigation->id;

            $nestedData['unique_case_id'] = e($litigation->unique_case_id);

            $nestedData['case_details'] =
                    e($litigation->case_number) .
                    '<br><span style="color:#6c757d;">' .
                    e($litigation->case_type) .
                    '</span>';

            $nestedData['property_details'] =
                e($litigation->property_known_as ?? 'N/A') .
                '<br><span style="color:#6c757d;">(' . e($litigation->property_id ?? 'N/A') . ')</span>';

            $nestedData['court_details'] =
                e($litigation->judicial_authority) .
                '<br><span style="color:#6c757d;">' . e($litigation->court_location) . '</span>';

            $nestedData['section_details'] =
                e($litigation->section_counsel) .
                '<br><span style="color:#6c757d;">' . e(getSectionsName($litigation->section)) . '</span>';

            $nestedData['latest_ndoh'] = $litigation->latest_ndoh
                ? Carbon::parse($litigation->latest_ndoh)->format('d-m-Y')
                : 'N/A';

            $nestedData['action'] =
                '<div class="d-flex gap-1">

                    <a href="' . route('litigation.show', $litigation->id) . '" 
                        class="btn btn-info btn-sm">
                        View
                    </a>

                    <a href="' . route('litigation.edit', $litigation->id) . '" 
                        class="btn btn-primary btn-sm">
                        Edit
                    </a>

                    <button 
                        type="button"
                        class="btn btn-danger btn-sm delete-litigation"
                        data-id="' . $litigation->id . '">
                        Delete
                    </button>

                </div>';

            $data[] = $nestedData;
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ]);
    }

    public function edit($id)
    {
        $litigation = Litigation::with('hearings')->findOrFail($id);
        $uniqueCaseId = $litigation->unique_case_id;

        return view('litigation.input-form', array_merge(
            $this->getLitigationFormData(),
            [
                'litigation' => $litigation,
                'uniqueCaseId' => $uniqueCaseId,
                'viewOnly' => false,
            ]
        ));
    }

    public function update(Request $request, $id)
    {
        Log::info('Litigation Update Request Started');
        Log::info($request->all());

        $litigation = Litigation::findOrFail($id);

        $validated = $this->validateLitigationRequest($request);

        $repeaters = $this->prepareLitigationRepeaters($request);

        $parties = $repeaters['parties'];
        $counsels = $repeaters['counsels'];
        $hearings = $repeaters['hearings'];

        DB::beginTransaction();

        try {
            $litigation->update([
                'property_id' => $validated['property_id'] ?? null,
                'property_known_as' => $validated['property_known_as'] ?? null,
                'case_number' => $validated['case_number'],
                'associated_case_number' => $validated['associated_case_number'] ?? null,
                'case_type' => $validated['case_type'],
                'judicial_authority' => $validated['judicial_authority'],
                'court_location' => $validated['court_location'],
                'case_brief' => $validated['case_brief'],
                'section' => $validated['section'],
                'section_counsel' => $validated['section_counsel'],
                'parties' => $parties,
                'counsels' => $counsels,
                'section_remarks' => $validated['section_remarks'],
                'updated_by' => auth()->id(),
            ]);

            foreach ($hearings as $hearing) {
                if (!empty($hearing['id'])) {
                    CaseHearing::where('id', $hearing['id'])
                        ->where('litigation_id', $litigation->id)
                        ->update([
                            'ldoh' => $hearing['ldoh'] ?? null,
                            'ndoh' => $hearing['ndoh'] ?? null,
                            'ldoh_hearing_details' => $hearing['ldoh_hearing_details'] ?? null,
                            'updated_by' => auth()->id(),
                        ]);
                } else {
                    CaseHearing::create([
                        'litigation_id' => $litigation->id,
                        'ldoh' => $hearing['ldoh'] ?? null,
                        'ndoh' => $hearing['ndoh'] ?? null,
                        'ldoh_hearing_details' => $hearing['ldoh_hearing_details'] ?? null,
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('litigation.index')
                ->with('success', 'Litigation updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Litigation Update Error');
            Log::error($e->getMessage());

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $litigation = Litigation::with('hearings')->findOrFail($id);

        return view('litigation.input-form', array_merge(
            $this->getLitigationFormData(),
            [
                'litigation' => $litigation,
                'uniqueCaseId' => $litigation->unique_case_id,
                'viewOnly' => true,
            ]
        ));
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $litigation = Litigation::findOrFail($id);

            // delete hearings first
            CaseHearing::where('litigation_id', $litigation->id)->delete();

            // delete litigation
            $litigation->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Court case deleted successfully.'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while deleting.'
            ], 500);
        }
    }
}
