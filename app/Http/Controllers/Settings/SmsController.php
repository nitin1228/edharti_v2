<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Configuration;
use Auth;
use Illuminate\Support\Facades\Log;
use App\Services\SettingsService;
use App\Models\Item;

class SmsController extends Controller
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /* public function index(){
        $sms = Configuration::where('type','sms')->paginate(10);
        return view('settings.sms.index',compact(['sms']));
    } */
   
    public function index()
    {
        return view('settings.sms.indexDatatable');
    }

    public function getSmsSettings(Request $request)
    {
        $query = Configuration::query()->where('type', 'sms');

        // List only actual database columns here
        $columns = ['action', 'vendor', 'api', 'key', 'auth_token', 'status'];
        $totalData = $query->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $orderColumnIndex = $request->input('order.0.column');
        $order = $columns[$orderColumnIndex] ?? null;
        $dir = $request->input('order.0.dir', 'asc');

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('configurations.id', 'LIKE', "%{$search}%")
                    ->orWhere('configurations.api', 'LIKE', "%{$search}%")
                    ->orWhere('configurations.key', 'LIKE', "%{$search}%")
                    ->orWhere('configurations.auth_token', 'LIKE', "%{$search}%");
            });

            $totalFiltered = $query->count();
        }

        // Only apply orderBy if the selected column is a valid database column
        if ($order) {
            $query->orderBy($order, $dir);
        }

        // Apply pagination
        $smsData = $query->offset($start)->limit($limit)->get();

        $data = [];
        foreach ($smsData as $row) {
            $nestedData = [];

            // Generate custom content for the `test_sms` column
            $testSms = '<div class="testSms text-primary" data-id="' . $row->id . '">Test Sms</div>
                <div class="loader" data-loader="' . $row->id . '"></div>
                <div class="testSmsError text-danger text-capitalize"></div>
                <div class="testSmsSuccess text-success text-capitalize"></div>';
            $item = Item::where('item_code', $row->action)->first();
            $action = $item ? $item->getItemNameByItemCode($row->action) : 'Default';

            // Prepare data for the columns
            $nestedData['test_sms'] = $testSms;
            $nestedData['action'] = $action;
            $nestedData['vendor'] = $row->vendor;
            $nestedData['api'] = truncate_url($row->api, 30);

            // Mask sensitive data
            $nestedData['secretId'] = str_repeat('x', strlen($row->key) - 4) . substr($row->key, -4);
            $nestedData['secretToken'] = str_repeat('x', strlen($row->auth_token) - 4) . substr($row->auth_token, -4);

            // Prepare status badge with permissions
            $status = $row->status == 1;
            $badgeClass = $status ? 'text-success bg-light-success' : 'text-danger bg-light-danger';
            $statusText = $status ? 'Active' : 'In-Active';
            $badgeStatusHtml = '<div class="badge rounded-pill ' . $badgeClass . ' p-2 text-uppercase px-3">'
                . '<i class="bx bxs-circle me-1"></i>' . $statusText . '</div>';
            if (auth()->user()->can('settings.sms.status')) {
                $badgeStatusHtml = '<a href="' . route('settings.sms.status', $row->id) . '">' . $badgeStatusHtml . '</a>';
            }
            $nestedData['status'] = $badgeStatusHtml;

            // Prepare user action column with permissions
            $nestedData['userAction'] = auth()->user()->can('settings.sms.update') ?
                '<div class="d-flex gap-3">
                 <a href="' . route('settings.sms.edit', $row->id) . '">
                     <button type="button" class="btn btn-primary px-3">Edit</button>
                 </a>
             </div>' : '';

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

    public function create(){
        return view('settings.sms.create');
    }

    public function store(Request $request){
        try {
            $configuration = Configuration::create([
                'type' => 'sms',
                'action' => $request->smsAction,
                'vendor' => $request->smsVendor,
                'sms_number' => $request->smsNumber,
                'key' => $request->secretId,
                'auth_token' => $request->secretToken,
                'api' => $request->api,
                'status' => 0,
                'created_by' => Auth::user()->id,
            ]);
            if($configuration){
                return redirect()->back()->with('success', 'Sms Settings Saved successfully.');
            } else {
                return redirect()->back()->with('failure', 'Sms Settings Not Saved.');
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return redirect()->back()->with('failure', $e->getMessage());
        }

    }

    public function updateStatus($id){
        $configuration = Configuration::find($id);
        $type = $configuration->type;
        $action = $configuration->action;
        $status = $configuration->status;
        if($status == 0){
            $configurations = Configuration::where('type', $type)
                                ->where('action', $action)
                                ->where('id', '!=', $id)
                                ->get();
    
            // Update the status for the filtered records
            foreach ($configurations as $config) {
                $config->status = 0;
                $config->save();
            }

            $configuration->status = 1;
            $configuration->save();
            return redirect()->back()->with('success', 'status updated successfully');
        } else {
            return redirect()->back()->with('failure', "status can't be updated");
        }
    }



    public function edit($id){
        $configuration = Configuration::find($id);
        if($configuration){
            return view('settings.sms.edit',compact(['configuration']));

        } else {
            return redirect()->back()->with('failure', "Details not available.");
        }
    }

    public function update($id, Request $request){
        $configuration = Configuration::find($id);
        if($configuration){
            if($configuration->status == 0){
                $configuration->action = $request->smsAction;
                $configuration->vendor = $request->smsVendor;
                $configuration->sms_number = $request->smsNumber;
                $configuration->key = $request->secretId;
                $configuration->auth_token = $request->secretToken;
                $configuration->api = $request->api;
                if($configuration->save()){
                    return redirect()->back()->with('success', 'Sms Settings updated successfully');
                } else {
                    return redirect()->back()->with('failure', "Sms Settings can't be updated, Please try after some time.");
                }
            } else {
                return redirect()->back()->with('failure', "Can't be updated as sms already activated.");
            }
        } else {
            return redirect()->back()->with('failure', "Can't be updated as details not available.");
        }
    }


    public function smsTest($id){
       
        try {
            $mobile = Auth::user()->mobile_no;
            if($mobile){
                $smsSent = $this->settingsService->testSmsSettings($id,$mobile);
                 if($smsSent){
                    return response()->json(['success' => true, 'message' => 'success']);
                } else {
                    return response()->json(['success' => false, 'message' => 'failed']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'failed']);
            }
            } catch (\Exception $e) {
                Log::info($e->getMessage());
                return response()->json(['success' => false, 'message' => 'failed']);
        }

    }
}
