<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralFunctions;
use App\Models\ApplicationCharge;
use App\Models\Country;
use App\Models\Demand;
use App\Models\DemandDetail;
use App\Models\Payment;
use App\Models\PropertyMaster;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\SplitedPropertyDetail;
use Carbon\Carbon;
use App\Models\OldPayment;
use App\Models\OldDemand;
use App\Models\OldDemandSubhead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;


class PaymentController extends Controller
{
    public function paymentResponse(Request $request)
    {
        
        $returned = $request->BharatkoshResponse;
        //  $returned = 'PD94bWwgdmVyc2lvbj0iMS4wIj8+PHBheW1lbnRTZXJ2aWNlIHZlcnNpb249IjEuMCIgbWVyY2hhbnRDb2RlPSJNRVJDSEFOVCI+PHJlcGx5PjxvcmRlclN0YXR1cyBvcmRlckNvZGU9IlBBQzIwMjUwOTE3MTE0OTQwIiBzdGF0dXM9IlNVQ0NFU1MiPjxyZWZlcmVuY2UgaWQ9IjE3MDkyNTAwMTM2NjAiIEJhbmtUcmFuc2Fjc3Rpb25EYXRlPSIwOS8xNy8yMDI1IDExOjUwOjU5IiBUb3RhbEFtb3VudD0iMSI+PC9yZWZlcmVuY2U+PC9vcmRlclN0YXR1cz48L3JlcGx5PjxTaWduYXR1cmUgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyMiPjxTaWduZWRJbmZvPjxDYW5vbmljYWxpemF0aW9uTWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvVFIvMjAwMS9SRUMteG1sLWMxNG4tMjAwMTAzMTUiIC8+PFNpZ25hdHVyZU1ldGhvZCBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyNyc2Etc2hhMSIgLz48UmVmZXJlbmNlIFVSST0iIj48VHJhbnNmb3Jtcz48VHJhbnNmb3JtIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnI2VudmVsb3BlZC1zaWduYXR1cmUiIC8+PC9UcmFuc2Zvcm1zPjxEaWdlc3RNZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjc2hhMSIgLz48RGlnZXN0VmFsdWU+UUVsK1VoNCtOM0FVM3pnNEJIQ2hEWEdmRElVPTwvRGlnZXN0VmFsdWU+PC9SZWZlcmVuY2U+PC9TaWduZWRJbmZvPjxTaWduYXR1cmVWYWx1ZT56Z0tBYXYwVHphN1gyTys0aDhMdjRPZDBZOVdlK0Vod0VlR3EyV3pYV2NBME5wc0hLOWtLdzF5L2xmcDVnT0s3SFU2NmZGcE5Vajl1c0NtSlhoZUtJSjlJaTk3RDMxQ0h5d0ZGY1E1bVBTU1Q4cnVWTFVQckIwb3U2QjIzUWlsaExoeDZrVWxlNnhKZVdpbG44NzNDU3ZGd1pvY2xhRjdTOHhwS0preW1ESVUxQzc2OE9ERXFvMEVFWmNiZGlIazFBZ3IrdUNFcnZXRWloMVBoMmZoRUpybldKL3paaEd5Ukc1SGhUU0JGdUU5SG5oUE9EelRMaEMrMlRyQWFrSHNFRCtBaW05MDl4Qnk0SnZ0elhIclFrRWU0eTlzSHBmYjlTaEVqMCtqeFAvaGI0eFJ6K09PaWNRR0Nka3pFTml2bzNmT2Jxd3BrV1RhUmlrS092OHAxRXc9PTwvU2lnbmF0dXJlVmFsdWU+PEtleUluZm8+PFg1MDlEYXRhPjxYNTA5SXNzdWVyU2VyaWFsPjxYNTA5SXNzdWVyTmFtZT5DTj1HZW9UcnVzdCBUTFMgUlNBIENBIEcxLCBPVT13d3cuZGlnaWNlcnQuY29tLCBPPURpZ2lDZXJ0IEluYywgQz1VUzwvWDUwOUlzc3Vlck5hbWU+PFg1MDlTZXJpYWxOdW1iZXI+MTk3NjE1NjQwMDQ1MDUyNjMxMDg3MTA0NjI5MTIzOTc4MDM3NzA8L1g1MDlTZXJpYWxOdW1iZXI+PC9YNTA5SXNzdWVyU2VyaWFsPjxYNTA5Q2VydGlmaWNhdGU+TUlJR2hqQ0NCVzZnQXdJQkFnSVFEdDN3VkU2TWhxM0FENFVJdlIrQStqQU5CZ2txaGtpRzl3MEJBUXNGQURCZ01Rc3dDUVlEVlFRR0V3SlZVekVWTUJNR0ExVUVDaE1NUkdsbmFVTmxjblFnU1c1ak1Sa3dGd1lEVlFRTEV4QjNkM2N1WkdsbmFXTmxjblF1WTI5dE1SOHdIUVlEVlFRREV4WkhaVzlVY25WemRDQlVURk1nVWxOQklFTkJJRWN4TUI0WERUSTFNRFl3TkRBd01EQXdNRm9YRFRJMk1EWXpNREl6TlRrMU9Wb3dkREVMTUFrR0ExVUVCaE1DU1U0eERqQU1CZ05WQkFnVEJVUmxiR2hwTVJJd0VBWURWUVFIRXdsT1pYY2dSR1ZzYUdreEt6QXBCZ05WQkFvVElsQjFZbXhwWXlCR2FXNWhibU5wWVd3Z1RXRnVZV2RsYldWdWRDQlRlWE4wWlcweEZEQVNCZ05WQkFNVEMzQm1iWE11Ym1sakxtbHVNSUlCSWpBTkJna3Foa2lHOXcwQkFRRUZBQU9DQVE4QU1JSUJDZ0tDQVFFQTRPamFnWjRCc1ZjejdKTGxLd1poc2cwSE9aTVdZVk56R0pFRmQ1ZUdlU3lKVzQ0L0NYbU13TkUrWjJvbXlYc0RvYlVkeS9KZWZCQVFFLzhqQWFSQVBuL3BlMjBOTHZHemNkNWFodUQzaVlZVjc2OGZHQmk5Z21MY09vQmxGZGNSSk1qUlM5RTYxQUx1MzliUGJZdDh4WVdyc2JqNGJnVTVPWTlHdzRxR25jams3TGxKWGF5bEpsK2ZJMnFtMWJ0dFg0VzZYTTA5b3d6RFM5ZVNnQlh3YXlQL2ZLY1djMEJhR3ZoR2IxRVpVajRieWJGcWZmWWEyM0hQR2VWNnNYNnRjMzNyQnUxY1lxZVRnQTgyYWU3WUNKMzlYWW9FSldwendZLzBVQzZmajF5TVhuUXBnc2J3UFZrR2N6R1g1Y0hMcTNyVGFJTlVEZnBud0RWSk9BdnBlUUlEQVFBQm80SURKakNDQXlJd0h3WURWUjBqQkJnd0ZvQVVsRS9VWFl2a3BPS21nUDc5MlBrQTc2TytBbGN3SFFZRFZSME9CQllFRkRuYUh4RFpPVHlGSlpxV2FGQ1BzYkV2VG80Yk1DY0dBMVVkRVFRZ01CNkNDM0JtYlhNdWJtbGpMbWx1Z2c5M2QzY3VjR1p0Y3k1dWFXTXVhVzR3UGdZRFZSMGdCRGN3TlRBekJnWm5nUXdCQWdJd0tUQW5CZ2dyQmdFRkJRY0NBUlliYUhSMGNEb3ZMM2QzZHk1a2FXZHBZMlZ5ZEM1amIyMHZRMUJUTUE0R0ExVWREd0VCL3dRRUF3SUZvREFkQmdOVkhTVUVGakFVQmdnckJnRUZCUWNEQVFZSUt3WUJCUVVIQXdJd1B3WURWUjBmQkRnd05qQTBvREtnTUlZdWFIUjBjRG92TDJOa2NDNW5aVzkwY25WemRDNWpiMjB2UjJWdlZISjFjM1JVVEZOU1UwRkRRVWN4TG1OeWJEQjJCZ2dyQmdFRkJRY0JBUVJxTUdnd0pnWUlLd1lCQlFVSE1BR0dHbWgwZEhBNkx5OXpkR0YwZFhNdVoyVnZkSEoxYzNRdVkyOXRNRDRHQ0NzR0FRVUZCekFDaGpKb2RIUndPaTh2WTJGalpYSjBjeTVuWlc5MGNuVnpkQzVqYjIwdlIyVnZWSEoxYzNSVVRGTlNVMEZEUVVjeExtTnlkREFNQmdOVkhSTUJBZjhFQWpBQU1JSUJmd1lLS3dZQkJBSFdlUUlFQWdTQ0FXOEVnZ0ZyQVdrQWR3QU9WNVM4ODY2cFBqTWJMSmtIcy9lUTM1dkNQWEV5SmQwaHFTV3NZY1ZPSVFBQUFaYzZxUTBMQUFBRUF3QklNRVlDSVFDaUdRVHZoWjNWQnI4dVU3NjlLS1lsNzFFV0pwcDRVRWJYRFFqYlhsSytNUUloQU1NMWxTM0VrbWM5eUZJOFI5MTE3RlVGVlkxNUJ0VjJWa3hUb0VWRlBlSXpBSFlBWkJIRWJLUVM3S2VKSEtJQ0xnQzhxMDhvQjlRZU5TZXI2djdWQThsOXpmQUFBQUdYT3FrTlNBQUFCQU1BUnpCRkFpRUF5MzlnMm1tdCszbW9kd3NqZm5tQm5Vc1VaM0cvdDFreG95QW1seFIxZEhFQ0lBcnNuL1cyVGVLem5MVlVyc244dlBoNFR4MjFoMXkyOS83Q2Q0aTdKZlFCQUhZQVNaeWJhZDRkZk96OE50N05oMlNtdUZ1dkNvZUFHZEZWVXZ2cDZ5bmQrTU1BQUFHWE9xa05Zd0FBQkFNQVJ6QkZBaUJKVURtemFUN1VqWHZNRHRzV0VyaTJnelVjNmRmRTl2Qks3TnNLWS8zcU5nSWhBTDRnRk9sRU5kRGZ3MWczTWZiSmxCajFQanBIM0UwblBJOHdVYytZbzhNcU1BMEdDU3FHU0liM0RRRUJDd1VBQTRJQkFRQWZCY1krSXJiSjhmdC9MMkF5VE1na21ETFRKa0RYYis3WTFIMU56N0NnQmhQbVo4cXFDVTg3aExvTURFbTJiUWtpY2E2L3N1bTdkOGVDQTJWaWd3U05TZ29zcVcwRzJ4ZDVtYXVFMzYvMGx4UjV3THNxOWRCbGE5NFlPYlZTK29CaXlKMlhOQzZWcFN4OVI1UzNHWktMZFplOFZHWCtDcnZZdk1oRTd6YlhyTlFpeVZNNnpLaksxMlQ1MWFhejZLbEc3anBaT2hDaHFPWnd0S1V0MEVHc1VQQ3BGN0hKMzZqdHd4OVZKNGI1T3hHd1Rlc3Q4SnNZRWZsZWVWN3FmYitzUHdQSDBjbmNBNjYyVEJrMXUwWExwL0kxem5uWDlIWjFtQ2c4bGEzdjFGWlJWeW9XWDBTd0QxYXlrLzA1L3M1bTJoanNKeXBRQmFQWlpBMktLR0wzPC9YNTA5Q2VydGlmaWNhdGU+PC9YNTA5RGF0YT48L0tleUluZm8+PC9TaWduYXR1cmU+PC9wYXltZW50U2VydmljZT4=';
        $decoded = base64_decode($returned); //urldecode($returned)
        $xml = simplexml_load_string($decoded);
        // dd($xml);
       if ($xml === false) {
            //return redirect()->route('paymentStatusDisplay', 'Something went wrong. Invalid response from payment gateway');
            return view('payment.payment-response', ['status' => 'FAILURE', 'orderCode' => 'Something went wrong. Invalid response from payment gateway']);
        }
        $orderStatusData = $xml->reply->orderStatus;
        $orderCode = $orderStatusData['orderCode'];
        $orderStatus = $orderStatusData['status'];
        $orderStatus = strpos("FAIL",$orderStatus) != false? "FAILED": $orderStatus; // found  "FAIL" in responsed insted of "FAILED"
        $orderRefId = $orderStatusData->reference['id'];
        // dd($orderStatus);

        $paymentRecord = Payment::where('unique_payment_id', $orderCode)
        ->whereNull('response')
        ->first();      
        if (!empty($paymentRecord)) {
                    if (!is_null($paymentRecord->created_by)) {
                        $authUser = User::find($paymentRecord->created_by);
        // dd($authUser);                
                 if (!empty($authUser)) {
                            // $request->session()->invalidate();
                            $request->session()->regenerate();
                            Auth::login($authUser,true);
                } 
            }
        // dd($xml,$orderCode,$paymentRecord,$authUser,Auth::check(),Auth::user()); 
            $paymentRecord->update([
                'status' => getServiceType('PAY_' . $orderStatus) ?? getServiceType('PAY_PENDING') ,
                'response' => $request->BharatkoshResponse,
                'transaction_id' => $orderRefId,
            ]);
        } else {
            // dd('inside else');
            //return redirect()->route('paymentStatusDisplay', 'Something went wrong. Payment data is not found');
            return view('payment.payment-response', ['status' => 'FAILURE', 'orderCode' => 'Something went wrong. Payment data is not found or already processed.']);
        }

        if ((string)$orderStatus == "SUCCESS") {
            // dd("inside if");
            $payemntService = new PaymentService();
            $payemntService->processSuccessfulPayment($paymentRecord);
        }
        return view('payment.payment-response', ['status' => $orderStatus, 'orderCode' => $orderCode]);
    }

    public function paymentInputForm()
    {
        $data['paymentTypes'] = getItemsByGroupId(17011);
        $data['guestUser'] = true; // flag to indicate this page is for guest user
        return view('payment.input-form', $data);
    }

    public function getPaymentDetails(Request $request)
    {
        $paymentType = $request->paymentType;
        $inputName = $request->inputName;
        $inputValue = $request->inputValue;
        switch ($paymentType) {
            // case 'PAY_DEMAND':
            //     if ($inputName != "demand_id") {
            //         return response()->json(['status' => false, 'details' => 'Data not available for this input']);
            //     }
            //     $demandId = $inputValue;
            //     $demand = Demand::where('unique_id', $demandId)->whereIn('status', [getServiceType('DEM_PENDING'), getServiceType('DEM_PART_PAID'), getServiceType('DEM_PAID')])->first();
            //     if (empty($demand)) {
            //         return response()->json(['status' => false, 'details' => 'Data not available for given demand id.']);
            //     }
            //     $countries = Country::all();
            //     $states = DB::table('states')->where('country_id', 101)->get();
            //     $view =  view('include.parts.demand-details', ['demand' => $demand, 'countries' => $countries, 'states' => $states])->render();
            //     return response()->json(['status' => true, 'html' => $view]);
            //     break;
			case 'PAY_DEMAND':
                if ($inputName != "demand_id") {
                    return response()->json(['status' => false, 'details' => 'Data not available for this input']);
                }
                $demandId = $inputValue;
                //$demand = Demand::where('unique_id', $demandId)->whereIn('status', [getServiceType('DEM_PENDING'), getServiceType('DEM_PART_PAID'), getServiceType('DEM_PAID')])->first();
              	 $demand = Demand::where('unique_id', $demandId)
					    ->whereIn('status', [
					        getServiceType('DEM_PENDING'),
					        getServiceType('DEM_PART_PAID'),
					        getServiceType('DEM_PAID')
					    ])
					    ->first();
					    $olddemand = 0;

					if (!$demand) {
					    $demand = OldDemand::where('demand_id', $demandId)
					        //->where('outstanding', '>', '0.00')
					        ->first();
					         $olddemand = 1;
					}
					//dd($demand);
                if (empty($demand)) {
                    return response()->json(['status' => false, 'details' => 'Data not available for given demand id.']);
                }
               
                $countries = Country::all();
                $states = DB::table('states')->where('country_id', 101)->get();
                $view =  view('include.parts.demand-details', ['demand' => $demand,'olddemand'=> $olddemand, 'countries' => $countries, 'states' => $states,'paymentType' => $paymentType])->render();
                return response()->json(['status' => true, 'html' => $view]);
                break;

            case 'GROUND_RENT':
            case 'PAY_RENT_SUB':
           
                // dd($request->all());
                if ($inputName != "property_id") {
                    return response()->json(['status' => false, 'details' => 'Data not available for this input']);
                }
                $propertyId = $inputValue;
                //$property = PropertyMaster::where('old_propert_id', $propertyId)->first();
				 $property = PropertyMaster::where('old_propert_id', $propertyId)->first();
				if (!$property) {
				    $property = SplitedPropertyDetail::where('old_property_id', $propertyId)->first();
				}
                if (empty($property)) {
                    return response()->json(['status' => false, 'details' => 'Property ID does not exist']);
                }
                $countries = Country::all();
                $states = DB::table('states')->where('country_id', 101)->get();
                $view =  view('include.parts.property-details', ['property' => $property, 'countries' => $countries, 'states' => $states,'paymentType' => $paymentType])->render();
                return response()->json(['status' => true, 'html' => $view]);
                break;
                case 'PAY_TEMP_ALLOT':
            case 'PAY_LAND_ALLOT':
			 case 'PAY_RTI':	
                //dd($inputName);
            if ($inputName != "reason/_purpose") {
                    return response()->json(['status' => false, 'details' => 'Please fill details.']);
                }
                $property = '1';
	//                $property = PropertyMaster::where('old_propert_id', $propertyId)->first();
	//                if (empty($property)) {
	//                    return response()->json(['status' => false, 'details' => 'Property ID does not exist']);
	//                }
            $countries = Country::all();
            $states = DB::table('states')->where('country_id', 101)->get();
            $view =  view('include.parts.property-details', ['property' => $property, 'countries' => $countries, 'states' => $states,'paymentType' => $paymentType])->render();
                return response()->json(['status' => true, 'html' => $view]);
                break;


            default:
                return response()->json(['status' => false, 'details' => 'Invalid payment type']);
                break;
        }
    }

   /* public function paymentStatusDisplay($status)
    {
        $statusMessage = 'Status of payemnt is <b>' . $status . '</b>';
        return view('payment.payment-response', ['data' => $statusMessage]);
    }*/
public function paymentStatusDisplay(Request $data)
    {
 //  dd($data->all());
        if (is_string($data)) {
            return view('payment.payment-response', ['status' => 'FAILURE', 'orderCode' => $data]);
        }
        $status = $data->orderStatus;
        $orderCode = $data->orderCode;
        return view('payment.payment-response', ['status' => $status,'orderCode' => $orderCode]);
    }

   

    public function applicationPayment($modelName, $modelId)
    {
        $modelClassName = base64_decode($modelName);
        $data['model'] = $modelClassName;
        $id = base64_decode($modelId);
        $data['id'] = $id;
        if ($modelClassName && $id) {
            $model = '\\App\\Models\\' . $modelClassName;
            $row = $model::find($id);
            if ($data) {
                $serviceType = $row->service_type->id;
                $applicationChargesData = ApplicationCharge::where('service_type', $serviceType)->where(function ($query) {
                    return $query->whereNull('effective_date_from')->orWhereDate('effective_date_from', '<=', date('Y-m-d'));
                })->where(function ($query) {
                    return $query->whereNull('effective_date_to')->orWhereDate('effective_date_to', '>=', date('Y-m-d'));
                })->first();
                $data['applicationCharges'] = $applicationChargesData->amount;
                $addressDropdownData = getAddressDropdownData();
                $data = $data + $addressDropdownData;
                return view('payment.application-input', $data);
            }
        } else {
            return back()->with(' failure', "Something went worong!!");
        }
    }

    public function applicationPaymentSubmit(Request $request, PaymentService $paymentService)
    {
        $model = $request->model_name;
        $modelId = $request->id;
        $amount = $request->applicationCharges;
        if ($model && $modelId && $amount > 0) {
            $modelClass = '\\App\\Models\\' . $model;
            $application = $modelClass::find($modelId);
            if (!empty($application)) {
                $propertyMasterId = $application->property_master_id;
                if (is_null($application->splited_property_detail_id)) {
                    $master_old_property_id = $application->old_property_id;
                    $splited_old_property_id = null;
                } else {
                    $masterProperty = PropertyMaster::find($propertyMasterId);
                    $master_old_property_id = $masterProperty->old_propert_id;
                    $splited_old_property_id = $application->old_property_id;
                }
            } else {
                return redirect()->back()->with('failue', 'Something went wrong. Application data not found');
            }
            $uniquePayemntId = 'PAC' . date('YmdHis');
            $payment = Payment::create([
                'property_master_id' => $propertyMasterId,
                'type' => getServiceType('PAY_APP_CHG'),
                'application_no' => $application->application_no,
                'model' => $model,
                'model_id' => $modelId,
                'payment_mode' => getServiceType($request->payment_mode),
                'unique_payment_id' => $uniquePayemntId,
                'splited_property_detail_id' => $application->splited_property_detail_id,
                'master_old_property_id' => $master_old_property_id,
                'splited_old_property_id' => $splited_old_property_id,
                'amount' => $amount,
                'status' => getServiceType('PAY_PENDING'),
                'created_by' => Auth::id()
            ]);
            if ($payment) {

                //save payer details
                GeneralFunctions::savePayerDetails($request->all(), $payment->id);

                // Payment 
                list($countryName, $stateName, $cityName) =  GeneralFunctions::getAddressNames($request->only('country', 'state', 'city'));

                $orderCode = $uniquePayemntId;
                // $orderCode = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
                $paymentData = [
                    'order_code' => $orderCode,
                    'merchant_batch_code' => $orderCode,
                    'installation_id' => '11136',
                    'amount' => $amount,
                    'currency_code' => "INR",
                    'order_content' => '15777',
                    'payemnt_type_id' => config('constants.payment_type_id'),
                    'code'=> (isset($request->payment_mode) && $request->payment_mode == "PAY_OFFLINE") ? 'OffLine' : 'Online',
                    'email' => $request->payer_email,
                    'first_name' => $request->payer_first_name,
                    'last_name' => $request->payer_last_name,
                    'mobile' => $request->payer_mobile,
                    'address_1' => $request->address_1,
                    'address_2' => $request->address_2,
                    'postal_code' => $request->postal_code,
                    'region' => $request->region,
                    'city' => $cityName,
                    'state' => $stateName,
                    'country' => $countryName,
                ];
                // dd($paymentData);
                $transaction = $paymentService->makePayemnt($paymentData);
                // return redirect()->back()->with('success', 'Data saved successfully');
            }
        }
    }
   
//      public function paymentSummary(Request $request)
//     {
//         $startDate = isset($request->start_date) && !empty($request->start_date) ? $request->start_date : null;
//         $endDate = isset($request->end_date) && !empty($request->end_date) ? $request->end_date : null;
//         $paymentQuery = Payment::when(!is_null($startDate), function ($q) use ($startDate) {
//             return $q->where('created_at', '>=', Carbon::createFromFormat('d-m-Y', $startDate)->format('Y-m-d'));
//         })->when(!is_null($endDate), function ($q) use ($endDate) {
//             return $q->where('created_at', '<=', Carbon::createFromFormat('d-m-Y', $endDate)->format('Y-m-d'));
//         });
//         $payments = $paymentQuery->whereIn('status', [getServiceType('PAY_PENDING'), getServiceType('PAY_SUCCESS')])->get();

//         $data['total_transactinos'] = $payments->count();
//         $data['total_amount'] = $payments->sum('amount');

//         // Use the helper for pending and success
//         $data['statuswise'] = [
//             'PAY_PENDING' => $this->getPaymentSummaryByStatus($payments, 'PAY_PENDING'),
//             'PAY_SUCCESS' => $this->getPaymentSummaryByStatus($payments, 'PAY_SUCCESS'),
//         ];

//         $data['typewiseCount'] = $payments->groupBy('type')->map(fn($group) => $group->count());
//         $data['typewiseAmount'] = $payments->groupBy('type')->map(fn($group) => $group->sum('amount'));
//         $applicationPayments = $payments->where('type', getServiceType('PAY_APP_CHG'));   
// 				$headwise = DemandDetail::query()
// 						    ->join('items as i', 'demand_details.subhead_id', '=', 'i.id')
// 						    ->join('demands as d', 'd.id', '=', 'demand_details.demand_id')
// 						    ->select(
// 						        DB::raw('i.id as code'),  
// 						        'i.item_name as name',
// 						        DB::raw('SUM(demand_details.paid_amount) as amount'),
// 						         DB::raw('COUNT(DISTINCT d.property_master_id) as property_count'),
// 						    )
// 						    ->when($startDate, function ($q) use ($startDate) {
// 						        $q->where('demand_details.updated_at', '>=',
// 						            Carbon::createFromFormat('d-m-Y', $startDate)->startOfDay()
// 						        );
// 						    })
// 						    ->when($endDate, function ($q) use ($endDate) {
// 						        $q->where('demand_details.updated_at', '<=',
// 						            Carbon::createFromFormat('d-m-Y', $endDate)->endOfDay()
// 						        );
// 						    })
// 						    ->groupBy('i.id', 'i.item_name')
// 						    ->get();


// 				$processingfee = Payment::select(
// 					        DB::raw('type as code'),
// 					        DB::raw('SUM(amount) as amount'),
// 					         DB::raw('COUNT(DISTINCT property_master_id) as property_count')
// 					    )
// 					    ->whereIn('type', [
// 					        getStatusName('GROUND_RENT'),
// 					        getStatusName('PAY_APP_CHG'),
//                              getStatusName('PAY_RENT_SUB'),
//                              getStatusName('PAY_TEMP_ALLOT'),
//                              getStatusName('PAY_LAND_ALLOT')
// 					    ])
// 					    ->where('status', getStatusName('PAY_SUCCESS'))
// 					    ->when($startDate, function ($q) use ($startDate) {
// 					        $q->whereDate('updated_at', '>=',
// 					            Carbon::createFromFormat('d-m-Y', $startDate)->format('Y-m-d')
// 					        );
// 					    })
// 					    ->when($endDate, function ($q) use ($endDate) {
// 					        $q->whereDate('updated_at', '<=',
// 					            Carbon::createFromFormat('d-m-Y', $endDate)->format('Y-m-d')
// 					        );
// 					    })
// 					    ->groupBy('type')
// 					    ->get()
// 					    ->map(function ($row) {
// 					        return (object)[
// 					            'code'   => $row->code,
// 					            'name'   => getServiceNameById($row->code), 
// 					            'amount' => $row->amount,
// 					            'property_count' => $row->property_count,
					            
// 					        ];
// 					    });
// 						$final = $headwise
// 					    ->concat($processingfee)
// 					    ->groupBy('code')
// 					    ->map(function ($rows) {
// 					        return [
// 					            'code'   => $rows->first()->code,
// 					            'name'   => $rows->first()->name,
// 					            'amount' => $rows->sum('amount'),
// 					            'property_count' => $rows->sum('property_count'),
// 					        ];
// 					    })
// 					    ->values()
// 					    ->sortBy('name') 
// 					    ->values(); 
//                $data['summary'] = $final;
//               // dd($final);

// //        =========================End New Code==========================
//         $data['applicationwiseBreakup'] = [
//         	'Conversion' => $this->getBreakup($applicationPayments, 'conversion'),
//         	'LUC' => $this->getBreakup($applicationPayments, 'landUseChange'),
//             'Mutation' => $this->getBreakup($applicationPayments, 'mutation'),
//             'NOC' => $this->getBreakup($applicationPayments, 'NOC'),
//         ];
// 			       $demandQuery  = Demand::when(!is_null($startDate), function ($q) use ($startDate) {
// 			        return $q->where('created_at', '>=', Carbon::createFromFormat('d-m-Y', $startDate)->format('Y-m-d'));
// 			    })->when(!is_null($endDate), function ($q) use ($endDate) {
// 			        return $q->where('created_at', '<=', Carbon::createFromFormat('d-m-Y', $endDate)->format('Y-m-d'));
// 			    });			
// 			$demandTotal = $demandQuery->whereIn('status', [
// 			    getServiceType('DEM_PENDING'),
// 			    getServiceType('DEM_PAID'),
// 			    getServiceType('DEM_PART_PAID')
// 			])->sum('net_total');
// 			$demandPaid = $demandQuery->whereIn('status', [
// 			    getServiceType('DEM_PAID'),
// 			    getServiceType('DEM_PART_PAID')
// 			])->sum('paid_amount');
// 			$demandPending = $demandTotal - $demandPaid;
// 			$data['demandData'] = [
// 			    'total' => $demandTotal,
// 			    'paid' => $demandPaid,
// 			    'pending' => $demandPending
// 			];
//         $data['request'] = $request;
//         // dd($data);
//         return view('payment.summary', $data);
//     }
// 	 public function paymentSummaryDetails(Request $request)
//     {
//     	  $decoded = base64_decode($request->get('data'));
//           $user = Auth::user();
//           $sections = $user->sections->pluck('section_code');
//     	  //dd($decoded);
//     	 // dd($decoded);    	 
// 		if ($request->has('data')) {
// 		        $decoded = base64_decode($request->get('data'));
// 		        parse_str($decoded, $params);		       
// 		        $startDate = $params['start'] ?? null;
// 		        $endDate = $params['end'] ?? null;
// 		        $filterService = $params['service'] ?? null;
// 		        $filterStatus = $params['status'] ?? null;		       
// 		    } 
// 		    else {
// 		        $filterDateFrom = $request->from ?? null;
// 		        $startDate = $request->start ?? null;
// 		        $endDate = $request->end ?? null;
// 		        $filterService = $request->service ?? null;
// 		        $filterStatus = $request->status ?? null;
// 		    }	
		       
// 		    $normalizedStatus = preg_replace('/\s*,\s*/', ',', $filterStatus);
// 			//$filterStatus = array_map('getServiceType', explode(',', $normalizedStatus)); //old code line.
						
// //					$paymentQuery = Payment::where('type', getServiceType('PAY_APP_CHG'))
// //		    ->when(!empty($filterStatus), function ($q) use ($filterStatus) {
// //		        $q->whereIn('status', $filterStatus);
// //		    })
// //		    ->when(!empty($startDate), function ($q) use ($startDate) {
// //		        $q->whereDate('created_at', '>=', Carbon::createFromFormat('d-m-Y', $startDate));
// //		    })
// //		    ->when(!empty($endDate), function ($q) use ($endDate) {
// //		        $q->whereDate('created_at', '<=', Carbon::createFromFormat('d-m-Y', $endDate));
// //		    })
// //		    ->when(!empty($filterService), function ($q) use ($filterService) {
// //		        // filter by service column in database (model column)
// //		        $q->where('model', 'like', "%{$filterService}%");
// //		    })
// //		    ->get();	
// 					//dd($filterService);
// //					===============old Code======================
// //				$paymentQuery = Payment::select('payments.*', 'applications.application_no','applications.service_type','property_masters.unique_propert_id','property_masters.old_propert_id')
// //				    ->leftJoin('applications', 'payments.application_no', '=', 'applications.application_no')
// //				     ->leftJoin('property_masters', 'payments.property_master_id', '=', 'property_masters.id')
// //				    ->where('payments.type', getServiceType('PAY_APP_CHG'))
// //				    ->when(!empty($filterStatus), function ($q) use ($filterStatus) {
// //				        $q->whereIn('payments.status', $filterStatus);
// //				    })
// //				    ->when(!empty($startDate), function ($q) use ($startDate) {
// //				        $q->whereDate('payments.created_at', '>=', Carbon::createFromFormat('d-m-Y', $startDate));
// //				    })
// //				    ->when(!empty($endDate), function ($q) use ($endDate) {
// //				        $q->whereDate('payments.created_at', '<=', Carbon::createFromFormat('d-m-Y', $endDate));
// //				    })
// //				    ->when(!empty($filterService), function ($q) use ($filterService) {
// //				        $q->where('payments.model', 'like', "%{$filterService}%");
// //				    })
// //				    ->get();
// 				//==================Old Code End==========================
// 				//=============New Code ==================================
// //				if($filterStatus == "processingfee")
// //				{
// //				$paymentQuery = Payment::select('payments.*', 'applications.application_no','applications.service_type','property_masters.unique_propert_id','property_masters.old_propert_id')
// //				    ->leftJoin('applications', 'payments.application_no', '=', 'applications.application_no')
// //				     ->leftJoin('property_masters', 'payments.property_master_id', '=', 'property_masters.id')
// //				       ->where('type',$filterService)
// //				    //->where('payments.type', getServiceType('PAY_APP_CHG'))
// //					//->when(!empty($filterStatus), function ($q) use ($filterStatus) {
// //					//$q->whereIn('payments.status', $filterStatus);
// //					//})
// //				    ->when(!empty($startDate), function ($q) use ($startDate) {
// //				        $q->whereDate('payments.created_at', '>=', Carbon::createFromFormat('d-m-Y', $startDate));
// //				    })
// //				    ->when(!empty($endDate), function ($q) use ($endDate) {
// //				        $q->whereDate('payments.created_at', '<=', Carbon::createFromFormat('d-m-Y', $endDate));
// //				    })
// //				    ->when(!empty($filterService), function ($q) use ($filterService) {
// //				        $q->where('payments.status', '=', getStatusName('PAY_SUCCESS'));
// //				    })
// //				    ->get();
// //				     $data['query_type'] = "processingfee";	
// //					}
// //					else 
// //					{
// //					   $paymentQuery = DemandDetail::query()
// //					    ->join('demands as d', 'demand_details.demand_id', '=', 'd.id')
// //					    ->join('items as i', 'demand_details.subhead_id', '=', 'i.id')					   
// //					    ->join('property_masters as p', 'd.property_master_id', '=', 'p.id')
// //					    ->select(
// //					        'demand_details.*',
// //					        'i.item_name as subhead_name',
// //					        'd.unique_id',
// //					        'd.id',
// //					        'd.created_at',	
// //					        'd.status',
// //					        'p.old_propert_id', 
// //					        'p.section_code',
// //					        'p.unique_propert_id',
// //					        'd.old_property_id',
// //					        'd.current_fy'					        
// //					    )	
// //					    ->when(!empty($startDate), function ($q) use ($startDate) {
// //					        $start = Carbon::createFromFormat('d-m-Y', $startDate)->startOfDay();
// //					        return $q->where('demand_details.updated_at', '>=', $start);
// //					    })
// //
// //					    ->when(!empty($endDate), function ($q) use ($endDate) {
// //					        $end = Carbon::createFromFormat('d-m-Y', $endDate)->endOfDay();
// //					        return $q->where('demand_details.updated_at', '<=', $end);
// //					    })
// //					    ->when(!empty($filterService), function ($q) use ($filterService) {
// //					        return $q->where('demand_details.subhead_id', $filterService);
// //					    })
// //					    ->get();	
// //					     $data['query_type'] = "demand";
// //						if (!empty($paymentQuery) && isset($paymentQuery[0]['subhead_name'])) {
// //						    $data['subheadname'] = $paymentQuery[0]['subhead_name'];
// //						} else {
// //						    $data['subheadname'] = null; // ya default value
// //						}
// //					     //$data['subheadname'] = $paymentQuery[0]['subhead_name'];
// //					     				
// //					}			
// //					//dd($filterStatus)	;
// //				     $data['applications'] = $paymentQuery;
// //		        return  view('payment.payment-summary-details', $data);

// 							if ($filterService == getServiceType('PAY_APP_CHG')) {							  
// 							    $paymentQuery = Payment::select(
// 							            'payments.*',
// 							            'applications.application_no',
// 							            'applications.service_type',
// 							            'property_masters.unique_propert_id',
// 							            'property_masters.old_propert_id',
// 							            'payer_details.first_name',
// 							            'payer_details.mobile',
// 							            'payer_details.email',
// 							        )
// 							        ->leftJoin('applications', 'payments.application_no', '=', 'applications.application_no')
// 							        ->leftJoin('property_masters', 'payments.property_master_id', '=', 'property_masters.id')
// 							         ->leftJoin('payer_details', 'payments.id', '=', 'payer_details.payment_id')
// 							        ->where('payments.type', $filterService)
// 							        ->where('payments.status', getStatusName('PAY_SUCCESS'))
//                                     //->whereIn('property_masters.section_code',$sections)
// 							        ->when($startDate, function ($q) use ($startDate) {
// 							            $q->whereDate('payments.updated_at', '>=', Carbon::createFromFormat('d-m-Y', $startDate));
// 							        })
// 							        ->when($endDate, function ($q) use ($endDate) {
// 							            $q->whereDate('payments.updated_at', '<=', Carbon::createFromFormat('d-m-Y', $endDate));
// 							        })
// 							        ->get();
// 							    $data['query_type'] = 'processingfee';

// 							} 
// 							else if($filterService == "all")
// 							{ 
// 					    $paymentData = Payment::select(
//         'payments.*',
//         'property_masters.unique_propert_id',
//         'property_masters.section_code',
//         'property_masters.old_propert_id',
//         'payer_details.first_name',
//         'payer_details.mobile',
//         'payer_details.email',
//         DB::raw('payments.amount as paid_amount'),
//         'demands.unique_id as demand_unique_id',
//         'demand_details.id as demand_detail_id',
//         'demand_details.subhead_id',
//         'demand_details.paid_amount as demand_detail_amount'
//     )
//     ->leftJoin('property_masters', 'payments.property_master_id', '=', 'property_masters.id')
//     ->leftJoin('payer_details', 'payments.id', '=', 'payer_details.payment_id')
//     ->leftJoin('demands', 'demands.id', '=', 'payments.demand_id')
//     ->leftJoin(
//         'demand_details',
//         'demand_details.demand_id',
//         '=',
//         'payments.demand_id'
//     )

//     ->where('payments.status', getStatusName('PAY_SUCCESS'))
//    // ->whereIn('property_masters.section_code',$sections)

//     ->when($startDate, fn ($q) =>
//         $q->whereDate(
//             'payments.updated_at',
//             '>=',
//             Carbon::createFromFormat('d-m-Y', $startDate)
//         )
//     )
//     ->when($endDate, fn ($q) =>
//         $q->whereDate(
//             'payments.updated_at',
//             '<=',
//             Carbon::createFromFormat('d-m-Y', $endDate)
//         )
//     )

//     ->get();
// 					    $paymentQuery = $paymentData;					     
// 					    $data['query_type'] = 'summaryall';	
// 							}
							
// 							else {							   
// 							    $demandData = DemandDetail::query()
// 							        ->join('demands as d', 'demand_details.demand_id', '=', 'd.id')
// 							        ->join('items as i', 'demand_details.subhead_id', '=', 'i.id')
// 							        ->join('property_masters as p', 'd.property_master_id', '=', 'p.id')
// 							        ->select(
// 							            'demand_details.*',
//                                         'd.id as demaid',
//                                         'd.demand_path',
// 							            'i.item_name as subhead_name',
// 							            'd.unique_id',
// 							            'd.created_at',
// 							            'd.current_fy',
// 							            'p.old_propert_id',							           
// 							            'p.unique_propert_id'
// 							        )
// 							        ->when($startDate, function ($q) use ($startDate) {
// 							            $q->where('demand_details.updated_at', '>=', Carbon::createFromFormat('d-m-Y', $startDate)->startOfDay());
// 							        })
//                                    // ->whereIn('p.section_code',$sections)
// 							        ->when($endDate, function ($q) use ($endDate) {
// 							            $q->where('demand_details.updated_at', '<=', Carbon::createFromFormat('d-m-Y', $endDate)->endOfDay());
// 							        })
// 							        ->when(!empty($filterService), function ($q) use ($filterService) {
// 							            $q->where('demand_details.subhead_id', $filterService);
// 							        })
// 							        ->get();
// 							$paymentData = Payment::select(
// 					        'payments.*',
// 					        'property_masters.unique_propert_id',
// 					        'property_masters.old_propert_id',
// 					         'payer_details.first_name',
// 							 'payer_details.mobile',
// 							'payer_details.email',
// 							'property_masters.section_code',									 
// 					        DB::raw('0 as net_total'),
// 					        DB::raw('NULL as property_known_as'),
// 					        DB::raw('payments.amount as paid_amount'),
// 					        DB::raw('0 as balance_amount'),
// 					        DB::raw('payments.status as status')
// 					    )
// 					    ->leftJoin('property_masters', 'payments.property_master_id', '=', 'property_masters.id')
// 					     ->leftJoin('payer_details', 'payments.id', '=', 'payer_details.payment_id')
// 					    ->where('payments.type', $filterService)
// 					    ->where('payments.status',  getStatusName('PAY_SUCCESS'))
//                        // ->whereIn('property_masters.section_code',$sections)
// 					    ->when($startDate, function ($q) use ($startDate) {
// 					        $q->whereDate('payments.updated_at', '>=', Carbon::createFromFormat('d-m-Y', $startDate));
// 					    })
// 					    ->when($endDate, function ($q) use ($endDate) {
// 					        $q->whereDate('payments.updated_at', '<=', Carbon::createFromFormat('d-m-Y', $endDate));
// 					    })
// 					    ->get();
// 					    $paymentQuery = $demandData->concat($paymentData); 
// 						 $data['query_type'] = 'combined';
// 							}
// 							// Return to Blade
// 							$data['applications'] = $paymentQuery;
// 							return view('payment.payment-summary-details', $data);
							
		        
// 		}
// public function paymentSummary(Request $request)
//     {   
//         $user = Auth::user();
//         $sections = $user->sections->pluck('section_code');
//         $startDate = isset($request->start_date) && !empty($request->start_date) ? $request->start_date : null;
//         $endDate = isset($request->end_date) && !empty($request->end_date) ? $request->end_date : null;
//         $paymentQuery = Payment::when(!is_null($startDate), function ($q) use ($startDate) {
//             return $q->where('created_at', '>=', Carbon::createFromFormat('d-m-Y', $startDate)->format('Y-m-d'));
//         })->when(!is_null($endDate), function ($q) use ($endDate) {
//             return $q->where('created_at', '<=', Carbon::createFromFormat('d-m-Y', $endDate)->format('Y-m-d'));
//         });
//         $payments = $paymentQuery->whereIn('status', [getServiceType('PAY_PENDING'), getServiceType('PAY_SUCCESS')])->get();

//         $data['total_transactinos'] = $payments->count();
//         $data['total_amount'] = $payments->sum('amount');

//         // Use the helper for pending and success
//         $data['statuswise'] = [
//             'PAY_PENDING' => $this->getPaymentSummaryByStatus($payments, 'PAY_PENDING'),
//             'PAY_SUCCESS' => $this->getPaymentSummaryByStatus($payments, 'PAY_SUCCESS'),
//         ];

//         $data['typewiseCount'] = $payments->groupBy('type')->map(fn($group) => $group->count());
//         $data['typewiseAmount'] = $payments->groupBy('type')->map(fn($group) => $group->sum('amount'));
//         $applicationPayments = $payments->where('type', getServiceType('PAY_APP_CHG'));       
          
// 				$headwise = DemandDetail::query()
// 						    ->join('items as i', 'demand_details.subhead_id', '=', 'i.id')
// 						    ->join('demands as d', 'd.id', '=', 'demand_details.demand_id')
// 						    ->select(
// 						        DB::raw('i.id as code'),  
// 						        'i.item_name as name',
// 						        DB::raw('SUM(demand_details.paid_amount) as amount'),
// 						         DB::raw('COUNT(DISTINCT d.property_master_id) as property_count'),
// 						    )
// 						    ->when($startDate, function ($q) use ($startDate) {
// 						        $q->where('demand_details.updated_at', '>=',
// 						            Carbon::createFromFormat('d-m-Y', $startDate)->startOfDay()
// 						        );
// 						    })
// 						    ->when($endDate, function ($q) use ($endDate) {
// 						        $q->where('demand_details.updated_at', '<=',
// 						            Carbon::createFromFormat('d-m-Y', $endDate)->endOfDay()
// 						        );
// 						    })
// 						    //->where('demand_details.paid_amount', '!=','0')
// 						    ->groupBy('i.id', 'i.item_name')
// 						    ->get();


// 				$processingfee = Payment::select(
// 					        DB::raw('type as code'),
// 					        DB::raw('SUM(amount) as amount'),
// 					         DB::raw('COUNT(DISTINCT property_master_id) as property_count')
// 					    )
// 					    ->whereIn('type', [
// 					        getStatusName('GROUND_RENT'),
// 					        getStatusName('PAY_APP_CHG'),
//                              getStatusName('PAY_RENT_SUB'),
//                              getStatusName('PAY_TEMP_ALLOT'),
//                              getStatusName('PAY_LAND_ALLOT')
// 					    ])
// 					    ->where('status', getStatusName('PAY_SUCCESS'))
// 					    ->when($startDate, function ($q) use ($startDate) {
// 					        $q->whereDate('updated_at', '>=',
// 					            Carbon::createFromFormat('d-m-Y', $startDate)->format('Y-m-d')
// 					        );
// 					    })
// 					    ->when($endDate, function ($q) use ($endDate) {
// 					        $q->whereDate('updated_at', '<=',
// 					            Carbon::createFromFormat('d-m-Y', $endDate)->format('Y-m-d')
// 					        );
// 					    })
// 					    ->groupBy('type')
// 					    ->get()
// 					    ->map(function ($row) {
// 					        return (object)[
// 					            'code'   => $row->code,
// 					            'name'   => getServiceNameById($row->code), 
// 					            'amount' => $row->amount,
// 					            'property_count' => $row->property_count,
					            
// 					        ];
// 					    });
// 						$final = $headwise
// 					    ->concat($processingfee)
// 					    ->groupBy('code')
// 					    ->map(function ($rows) {
// 					        return [
// 					            'code'   => $rows->first()->code,
// 					            'name'   => $rows->first()->name,
// 					            'amount' => $rows->sum('amount'),
// 					            'property_count' => $rows->sum('property_count'),
					            
// 					        ];
// 					    })
// 					    ->values()
// 					    ->sortBy('name') 
// 					    ->values(); 
//                $data['summary'] = $final;
//               // dd($final);
//         $data['applicationwiseBreakup'] = [
//         	'Conversion' => $this->getBreakup($applicationPayments, 'conversion'),
//         	'LUC' => $this->getBreakup($applicationPayments, 'landUseChange'),
//             'Mutation' => $this->getBreakup($applicationPayments, 'mutation'),
//             'NOC' => $this->getBreakup($applicationPayments, 'NOC'),
//         ];
// 			       $demandQuery  = Demand::when(!is_null($startDate), function ($q) use ($startDate) {
// 			        return $q->where('updated_at', '>=', Carbon::createFromFormat('d-m-Y', $startDate)->format('Y-m-d'));
// 			    })->when(!is_null($endDate), function ($q) use ($endDate) {
// 			        return $q->where('updated_at', '<=', Carbon::createFromFormat('d-m-Y', $endDate)->format('Y-m-d'));
// 			    });			
// 			$demandTotal = $demandQuery->whereIn('status', [
// 			    getServiceType('DEM_PENDING'),
// 			    getServiceType('DEM_PAID'),
// 			    getServiceType('DEM_PART_PAID')
// 			])->sum('net_total');
// 			$demandPaid = $demandQuery->whereIn('status', [
// 			    getServiceType('DEM_PAID'),
// 			    getServiceType('DEM_PART_PAID')
// 			])->sum('paid_amount');
// 			$demandPending = $demandTotal - $demandPaid;
// 			$data['demandData'] = [
// 			    'total' => $demandTotal,
// 			    'paid' => $demandPaid,
// 			    'pending' => $demandPending
// 			];
//         $data['request'] = $request;
//         // dd($data);
//         return view('payment.summary', $data);
//     }
// 	 public function paymentSummaryDetails(Request $request)
//     {
//     	  $decoded = base64_decode($request->get('data'));
//     	  $user = Auth::user();
//          $sections = $user->sections->pluck('section_code');        	  	 
// 		if ($request->has('data')) {
// 		        $decoded = base64_decode($request->get('data'));
// 		        parse_str($decoded, $params);		       
// 		        $startDate = $params['start'] ?? null;
// 		        $endDate = $params['end'] ?? null;
// 		        $filterService = $params['service'] ?? null;
// 		        $filterStatus = $params['status'] ?? null;		       
// 		    } 
// 		    else {
// 		        $filterDateFrom = $request->from ?? null;
// 		        $startDate = $request->start ?? null;
// 		        $endDate = $request->end ?? null;
// 		        $filterService = $request->service ?? null;
// 		        $filterStatus = $request->status ?? null;
// 		    }	
		       
// 		    $normalizedStatus = preg_replace('/\s*,\s*/', ',', $filterStatus);
//                           if($filterStatus == 'summary_report')	{
//                           	 $paymentQuery = Payment::select(
// 							            'payments.*',
// 							            'applications.application_no',
// 							            'applications.service_type',
// 							            'property_masters.unique_propert_id',
// 							            'property_masters.old_propert_id',
// 							            'payer_details.first_name',
// 							            'payer_details.mobile',
// 							            'payer_details.email',
// 							        )
// 							        ->leftJoin('applications', 'payments.application_no', '=', 'applications.application_no')
// 							        ->leftJoin('property_masters', 'payments.property_master_id', '=', 'property_masters.id')
// 							         ->leftJoin('payer_details', 'payments.id', '=', 'payer_details.payment_id')
// 							        ->where('payments.type', $filterService)
// 							        ->where('payments.status', getStatusName('PAY_SUCCESS'))
// 							         //->whereIn('property_masters.section_code',$sections)
// 							        ->when($startDate, function ($q) use ($startDate) {
// 							            $q->whereDate('payments.updated_at', '>=', Carbon::createFromFormat('d-m-Y', $startDate));
// 							        })
// 							        ->when($endDate, function ($q) use ($endDate) {
// 							            $q->whereDate('payments.updated_at', '<=', Carbon::createFromFormat('d-m-Y', $endDate));
// 							        })
// 							        ->get();
// 							    $data['query_type'] = 'summary_report';
//                           }	
//                           elseif($filterStatus == 'summaryallreport')
//                           {
//                           	$paymentData = Payment::select(
// 								        'payments.*',
// 								        'property_masters.unique_propert_id',
// 								        'property_masters.section_code',
// 								        'property_masters.old_propert_id',
// 								        'payer_details.first_name',
// 								        'payer_details.mobile',
// 								        'payer_details.email',
// 								        'demands.unique_id as demand_unique_id',
// 								        'demand_details.id as demand_detail_id',
// 								        'demand_details.subhead_id as subhead',
// 								        DB::raw(" payments.amount as paid_amount")
// 								    )
// 								    ->leftJoin('property_masters', 'payments.property_master_id', '=', 'property_masters.id')
// 								    ->leftJoin('payer_details', 'payments.id', '=', 'payer_details.payment_id')
// 								    ->leftJoin('demands', 'demands.id', '=', 'payments.demand_id')
// 								    ->leftJoin('demand_details', function ($join) {
// 								        $join->on('demand_details.demand_id', '=', 'payments.demand_id')
// 								             ->on('demand_details.subhead_id', '=', 'payments.type'); 
// 								   })
// 								    ->where('payments.status', getStatusName('PAY_SUCCESS'))
// 								   // ->whereIn('property_masters.section_code',$sections)

// 								    ->when($startDate, fn ($q) =>
// 								        $q->whereDate(
// 								            'payments.updated_at',
// 								            '>=',
// 								            Carbon::createFromFormat('d-m-Y', $startDate)
// 								        )
// 								    )
// 								    ->when($endDate, fn ($q) =>
// 								        $q->whereDate(
// 								            'payments.updated_at',
// 								            '<=',
// 								            Carbon::createFromFormat('d-m-Y', $endDate)
// 								        )
// 								    )
// 								    ->get();
// 					    $paymentQuery = $paymentData;					     
// 					    $data['query_type'] = 'summaryallreport';	
//                           }	              	
//                           else
//                           {
// 							if ($filterService == getServiceType('PAY_APP_CHG')) {							  
// 							    $paymentQuery = Payment::select(
// 							            'payments.*',
// 							            'applications.application_no',
// 							            'applications.service_type',
// 							            'property_masters.unique_propert_id',
// 							            'property_masters.old_propert_id',
// 							            'payer_details.first_name',
// 							            'payer_details.mobile',
// 							            'payer_details.email',
// 							        )
// 							        ->leftJoin('applications', 'payments.application_no', '=', 'applications.application_no')
// 							        ->leftJoin('property_masters', 'payments.property_master_id', '=', 'property_masters.id')
// 							         ->leftJoin('payer_details', 'payments.id', '=', 'payer_details.payment_id')
// 							        ->where('payments.type', $filterService)
// 							        ->where('payments.status', getStatusName('PAY_SUCCESS'))
// 							         //->whereIn('property_masters.section_code',$sections)
// 							        ->when($startDate, function ($q) use ($startDate) {
// 							            $q->whereDate('payments.updated_at', '>=', Carbon::createFromFormat('d-m-Y', $startDate));
// 							        })
// 							        ->when($endDate, function ($q) use ($endDate) {
// 							            $q->whereDate('payments.updated_at', '<=', Carbon::createFromFormat('d-m-Y', $endDate));
// 							        })
// 							        ->get();
// 							    $data['query_type'] = 'processingfee';

// 							} 
// 							else if($filterService == "all")
// 							{ 							
// 							$paymentData = Payment::select(
// 								        'payments.*',
// 								        'property_masters.unique_propert_id',
// 								        'property_masters.section_code',
// 								        'property_masters.old_propert_id',
// 								        'payer_details.first_name',
// 								        'payer_details.mobile',
// 								        'payer_details.email',
// 								        'demands.unique_id as demand_unique_id',
// 								        'demand_details.id as demand_detail_id',
// 								        'demand_details.subhead_id as subhead',
// 								        DB::raw("
// 								            CASE 
// 								                WHEN payments.demand_id IS NOT NULL 
// 								                THEN demand_details.paid_amount
// 								                ELSE payments.amount
// 								            END as paid_amount
// 								        ")
// 								    )
// 								    ->leftJoin('property_masters', 'payments.property_master_id', '=', 'property_masters.id')
// 								    ->leftJoin('payer_details', 'payments.id', '=', 'payer_details.payment_id')
// 								    ->leftJoin('demands', 'demands.id', '=', 'payments.demand_id')
// 								    ->leftJoin('demand_details', function ($join) {
// 								        $join->on('demand_details.demand_id', '=', 'payments.demand_id')
// 								             ->on('demand_details.subhead_id', '=', 'payments.type'); 
// 								   })
// 								    ->where('payments.status', getStatusName('PAY_SUCCESS'))
// 								   // ->whereIn('property_masters.section_code',$sections)

// 								    ->when($startDate, fn ($q) =>
// 								        $q->whereDate(
// 								            'payments.updated_at',
// 								            '>=',
// 								            Carbon::createFromFormat('d-m-Y', $startDate)
// 								        )
// 								    )
// 								    ->when($endDate, fn ($q) =>
// 								        $q->whereDate(
// 								            'payments.updated_at',
// 								            '<=',
// 								            Carbon::createFromFormat('d-m-Y', $endDate)
// 								        )
// 								    )
// 								    ->get();
// 					    $paymentQuery = $paymentData;					     
// 					    $data['query_type'] = 'summaryall';	
// 							}					
// 							else {	
										
// 							$demandData = DemandDetail::query()
// 							    ->join('demands as d', 'demand_details.demand_id', '=', 'd.id')
// 							    ->join('items as i', 'demand_details.subhead_id', '=', 'i.id')
// 							    ->join('property_masters as p', 'd.property_master_id', '=', 'p.id')
							     
// 							    ->leftJoin('payments as pay', function ($join) {
// 							        $join->on('pay.demand_id', '=', 'd.id')
// 							             ->where('pay.status', getStatusName('PAY_SUCCESS'));
// 							    })	
// 							     ->leftJoin('payer_details', 'pay.id', '=', 'payer_details.payment_id')						    
// 							    ->where('demand_details.subhead_id', $filterService) 
// 							   // ->whereIn('p.section_code',$sections)
// 							    ->select(
// 							        'demand_details.*',
// 							        'd.id as demaid',
// 							        'd.unique_id',
// 							        'd.current_fy',
// 							        'p.old_propert_id',
// 							        'p.unique_propert_id',
// 							        DB::raw('MAX(payer_details.first_name) as first_name'),
// 									DB::raw('MAX(payer_details.mobile) as mobile'),
// 									DB::raw('MAX(payer_details.email) as email'),
// 							        DB::raw('GROUP_CONCAT(DISTINCT pay.transaction_id) as transaction_ids'),
// 							        DB::raw('GROUP_CONCAT(DISTINCT pay.unique_payment_id) as unique_payment_ids'),
// 							      // DB::raw('IFNULL(SUM(pay.amount),0) as paid_amount')
// 							    )
// 							    ->groupBy(
// 							        'demand_details.id',
// 							        'd.id',
// 							        'd.unique_id',
// 							        'd.current_fy',
// 							        'p.old_propert_id',
// 							        'p.unique_propert_id'
// 							    )
// 							    ->get();
// 								$paymentData = Payment::select(
// 								        'payments.*',
// 								        'property_masters.unique_propert_id',
// 								        'property_masters.old_propert_id',
// 								        'payer_details.first_name',
// 								        'payer_details.mobile',
// 								        'payer_details.email',
// 								        'property_masters.section_code',

// 								        DB::raw('0 as net_total'),
// 								        DB::raw('NULL as property_known_as'),
// 								        DB::raw('payments.amount as paid_amount'),
// 								        DB::raw('0 as balance_amount'),
// 								        DB::raw('payments.status as status')
// 								    )
// 								    ->leftJoin('property_masters', 'payments.property_master_id', '=', 'property_masters.id')
// 								    ->leftJoin('payer_details', 'payments.id', '=', 'payer_details.payment_id')
// 								    ->whereNull('payments.demand_id') // only direct
// 								    ->where('payments.type', $filterService) 
// 								    ->where('payments.status', getStatusName('PAY_SUCCESS'))
// 								     //->whereIn('property_masters.section_code',$sections)
// 								    ->when($startDate, function ($q) use ($startDate) {
// 								        $q->whereDate('payments.updated_at', '>=', Carbon::createFromFormat('d-m-Y', $startDate));
// 								    })
// 								    ->when($endDate, function ($q) use ($endDate) {
// 								        $q->whereDate('payments.updated_at', '<=', Carbon::createFromFormat('d-m-Y', $endDate));
// 								    })
// 								    ->get();
// 							$paymentQuery = $demandData->concat($paymentData);
// 						 $data['query_type'] = 'combined';
// 							}
// 							// Return to Blade
// 							}
// 							$data['applications'] = $paymentQuery;
// 							return view('payment.payment-summary-details', $data);
							
		        
// 		}
public function paymentSummary(Request $request)
    {   
        $user = Auth::user();
        $sections = $user->sections->pluck('section_code');
        $startDate = isset($request->start_date) && !empty($request->start_date) ? $request->start_date : null;
        $endDate = isset($request->end_date) && !empty($request->end_date) ? $request->end_date : null;
        $paymentQuery = Payment::when(!is_null($startDate), function ($q) use ($startDate) {
            return $q->where('created_at', '>=', Carbon::createFromFormat('d-m-Y', $startDate)->format('Y-m-d'));
        })->when(!is_null($endDate), function ($q) use ($endDate) {
            return $q->where('created_at', '<=', Carbon::createFromFormat('d-m-Y', $endDate)->format('Y-m-d'));
        });
        $payments = $paymentQuery->whereIn('status', [getServiceType('PAY_PENDING'), getServiceType('PAY_SUCCESS')])->get();

        $data['total_transactinos'] = $payments->count();
        $data['total_amount'] = $payments->sum('amount');

        // Use the helper for pending and success
        $data['statuswise'] = [
            'PAY_PENDING' => $this->getPaymentSummaryByStatus($payments, 'PAY_PENDING'),
            'PAY_SUCCESS' => $this->getPaymentSummaryByStatus($payments, 'PAY_SUCCESS'),
        ];

        $data['typewiseCount'] = $payments->groupBy('type')->map(fn($group) => $group->count());
        $data['typewiseAmount'] = $payments->groupBy('type')->map(fn($group) => $group->sum('amount'));
        $applicationPayments = $payments->where('type', getServiceType('PAY_APP_CHG')); 
				$headwise = DemandDetail::query()
						    ->join('items as i', 'demand_details.subhead_id', '=', 'i.id')
						    ->join('demands as d', 'd.id', '=', 'demand_details.demand_id')
						    ->select(
						        DB::raw('i.id as code'),  
						        'i.item_name as name',
						        DB::raw('SUM(demand_details.paid_amount) as amount'),
						         DB::raw('COUNT(DISTINCT d.property_master_id) as property_count'),
						    )
						    ->when($startDate, function ($q) use ($startDate) {
						        $q->where('demand_details.updated_at', '>=',
						            Carbon::createFromFormat('d-m-Y', $startDate)->startOfDay()
						        );
						    })
						    ->when($endDate, function ($q) use ($endDate) {
						        $q->where('demand_details.updated_at', '<=',
						            Carbon::createFromFormat('d-m-Y', $endDate)->endOfDay()
						        );
						    })
						    //->where('demand_details.paid_amount', '!=','0')
						    ->groupBy('i.id', 'i.item_name')
						    ->get();
				$processingfee = Payment::select(
					        DB::raw('type as code'),
					        DB::raw('SUM(amount) as amount'),
					         DB::raw('COUNT(DISTINCT property_master_id) as property_count')
					    )
					    ->whereIn('type', [
					        getStatusName('GROUND_RENT'),
					        getStatusName('PAY_APP_CHG'),
                             getStatusName('PAY_RENT_SUB'),
                             getStatusName('PAY_TEMP_ALLOT'),
                             getStatusName('PAY_LAND_ALLOT'),
							 getStatusName('PAY_RTI')
					    ])
					    ->where('status', getStatusName('PAY_SUCCESS'))
					    ->when($startDate, function ($q) use ($startDate) {
					        $q->whereDate('updated_at', '>=',
					            Carbon::createFromFormat('d-m-Y', $startDate)->format('Y-m-d')
					        );
					    })
					    ->when($endDate, function ($q) use ($endDate) {
					        $q->whereDate('updated_at', '<=',
					            Carbon::createFromFormat('d-m-Y', $endDate)->format('Y-m-d')
					        );
					    })
					    ->groupBy('type')
					    ->get()
					    ->map(function ($row) {
					        return (object)[
					            'code'   => $row->code,
					            'name'   => getServiceNameById($row->code), 
					            'amount' => $row->amount,
					            'property_count' => $row->property_count,
					            
					        ];
					    });
						$final = $headwise
					    ->concat($processingfee)
					    ->groupBy('code')
					    ->map(function ($rows) {
					        return [
					            'code'   => $rows->first()->code,
					            'name'   => $rows->first()->name,
					            'amount' => $rows->sum('amount'),
					            'property_count' => $rows->sum('property_count'),
					            
					        ];
					    })
					    ->values()
					    ->sortBy('name') 
					    ->values(); 
               $data['summary'] = $final;
               //================================Old Payment==================================================
               $fixedStartDate = Carbon::create(2026, 1, 1)->format('Y-m-d');
						$userStart = $startDate  ? Carbon::createFromFormat('d-m-Y', $startDate)->format('Y-m-d') : null;
						$userEnd = $endDate  ? Carbon::createFromFormat('d-m-Y', $endDate)->format('Y-m-d') : null;
               $oldPayments = OldPayment::query()
								    ->join('items as i', 'i.id', '=', 'old_payments.payment_type')
								    ->select(
								        'old_payments.payment_type as code',
								        'i.item_name as name',
								        DB::raw("
								            SUM(
								                CASE 
								                    WHEN old_payments.payment_type = 1586 
								                        THEN GREATEST(old_payments.amount - 500, 0)
								                    ELSE old_payments.amount
								                END
								            ) as amount
								        "),
								        DB::raw("
								            SUM(
								                CASE 
								                    WHEN old_payments.payment_type = 1586 
								                        THEN LEAST(old_payments.amount, 500)
								                    ELSE 0
								                END
								            ) as processing_fee
								        "),
								        DB::raw("
								            COUNT(DISTINCT 
								                CASE 
								                    WHEN old_payments.payment_type != 1586 
								                         OR old_payments.amount > 500
								                    THEN old_payments.PropertyID
								                END
								            ) as property_count
								        "),
								        DB::raw("
								            COUNT(DISTINCT 
								                CASE 
								                    WHEN old_payments.payment_type = 1586
								                    THEN old_payments.PropertyID
								                END
								            ) as processing_property_count
								        ")
								    )
								    ->whereDate('old_payments.Transaction_Date', '>=', Carbon::parse($fixedStartDate)->format('Y-m-d'))
								      ->where('old_payments.payment_type', '!=', '1549')
								       ->where('old_payments.payment_status', getStatusName('PAY_SUCCESS'))
								    ->when($userStart && $userStart > $fixedStartDate, fn($q) =>
								        $q->whereDate('old_payments.Transaction_Date', '>=', $userStart)
								    )
								    ->when($userEnd, fn($q) =>
								        $q->whereDate('old_payments.Transaction_Date', '<=', $userEnd)
								    )
								    ->groupBy('old_payments.payment_type', 'i.item_name')
								    ->get();
						                                 
						$oldProcessingAmount = $oldPayments->sum('processing_fee');
						$oldProcessingPropertyCount = $oldPayments->sum('processing_property_count');
									//=====Old Demand Payment Amount==============
						
									$oldDeamndPayPayments = OldPayment::query()
									    ->join('items as i', 'i.id', '=', 'old_payments.payment_type')						   
									   	 ->select(
									        'old_payments.payment_type as code',
									        'i.item_name as name',
									        DB::raw('SUM(old_payments.Amount) as amount'),
									        DB::raw('COUNT(DISTINCT old_payments.PropertyID) as property_count')
									    )
									   ->whereDate('old_payments.Transaction_Date', '>=', Carbon::parse($fixedStartDate)->format('Y-m-d'))
									      ->where('old_payments.payment_type', '=', '1549')
									       ->where('old_payments.payment_status', getStatusName('PAY_SUCCESS'))
									    ->when($userStart && $userStart > $fixedStartDate, fn($q) =>
									        $q->whereDate('old_payments.Transaction_Date', '>=', $userStart)
									    )
									    ->when($userEnd, fn($q) =>
									        $q->whereDate('old_payments.Transaction_Date', '<=', $userEnd)
									    )
									    ->groupBy('old_payments.payment_type', 'i.item_name')
									    ->first();
						$oldfinal = collect()
									    ->concat($oldPayments)
									    ->concat($oldDeamndPayPayments ? collect([$oldDeamndPayPayments]) : collect()) 
									    ->groupBy('code')
									    ->map(fn($rows) => [
									        'code'   => $rows->first()->code,
									        'name'   => $rows->first()->name,
									        'amount' => $rows->sum('amount'),
									        'property_count' => $rows->sum('property_count'),
									    ])
									    ->sortBy('name')
									    ->values();	
							 $data['summaryold'] = $oldfinal;		    
              // dd($final);
        $data['applicationwiseBreakup'] = [
        	'Conversion' => $this->getBreakup($applicationPayments, 'conversion'),
        	'LUC' => $this->getBreakup($applicationPayments, 'landUseChange'),
            'Mutation' => $this->getBreakup($applicationPayments, 'mutation'),
            'NOC' => $this->getBreakup($applicationPayments, 'NOC'),
        ];
			       $demandQuery  = Demand::when(!is_null($startDate), function ($q) use ($startDate) {
			        return $q->where('updated_at', '>=', Carbon::createFromFormat('d-m-Y', $startDate)->format('Y-m-d'));
			    })->when(!is_null($endDate), function ($q) use ($endDate) {
			        return $q->where('updated_at', '<=', Carbon::createFromFormat('d-m-Y', $endDate)->format('Y-m-d'));
			    });			
			$demandTotal = $demandQuery->whereIn('status', [
			    getServiceType('DEM_PENDING'),
			    getServiceType('DEM_PAID'),
			    getServiceType('DEM_PART_PAID')
			])->sum('net_total');
			$demandPaid = $demandQuery->whereIn('status', [
			    getServiceType('DEM_PAID'),
			    getServiceType('DEM_PART_PAID')
			])->sum('paid_amount');
			$demandPending = $demandTotal - $demandPaid;
			$data['demandData'] = [
			    'total' => $demandTotal,
			    'paid' => $demandPaid,
			    'pending' => $demandPending
			];
        $data['request'] = $request;
        // dd($data);
        return view('payment.summary', $data);
    }
	 public function paymentSummaryDetails(Request $request)
    {
    	  $decoded = base64_decode($request->get('data'));
    	  $user = Auth::user();
         $sections = $user->sections->pluck('section_code');        	  	 
		if ($request->has('data')) {
		        $decoded = base64_decode($request->get('data'));
		        parse_str($decoded, $params);		       
		        $startDate = $params['start'] ?? null;
		        $endDate = $params['end'] ?? null;
		        $filterService = $params['service'] ?? null;
		        $filterStatus = $params['status'] ?? null;		       
		    } 
		    else {
		        $filterDateFrom = $request->from ?? null;
		        $startDate = $request->start ?? null;
		        $endDate = $request->end ?? null;
		        $filterService = $request->service ?? null;
		        $filterStatus = $request->status ?? null;
		    }	
		       
		    $normalizedStatus = preg_replace('/\s*,\s*/', ',', $filterStatus);
		     $fixedStartDate = Carbon::createFromFormat('Y-m-d', '2026-01-01')->startOfDay();
                          if($filterStatus == 'summary_report')	
                          {
                          	 $paymentQuery = Payment::select(
							            'payments.*',
							            'applications.application_no',
							            'applications.service_type',
							            'property_masters.unique_propert_id',
							            'property_masters.old_propert_id',
							            'payer_details.first_name',
							            'payer_details.mobile',
							            'payer_details.email',
							        )
							        ->leftJoin('applications', 'payments.application_no', '=', 'applications.application_no')
							        ->leftJoin('property_masters', 'payments.property_master_id', '=', 'property_masters.id')
							         ->leftJoin('payer_details', 'payments.id', '=', 'payer_details.payment_id')
							        ->where('payments.type', $filterService)
							        ->where('payments.status', getStatusName('PAY_SUCCESS'))
							         //->whereIn('property_masters.section_code',$sections)
							        ->when($startDate, function ($q) use ($startDate) {
							            $q->whereDate('payments.updated_at', '>=', Carbon::createFromFormat('d-m-Y', $startDate));
							        })
							        ->when($endDate, function ($q) use ($endDate) {
							            $q->whereDate('payments.updated_at', '<=', Carbon::createFromFormat('d-m-Y', $endDate));
							        })
							        ->get();
							    $data['query_type'] = 'summary_report';
                          }	
                          elseif($filterStatus == 'summaryallreport')
                          {
                          	$paymentData = Payment::select(
								        'payments.*',
								        'property_masters.unique_propert_id',
								        'property_masters.section_code',
								        'property_masters.old_propert_id',
								        'payer_details.first_name',
								        'payer_details.mobile',
								        'payer_details.email',
								        'demands.unique_id as demand_unique_id',
										'demands.demand_path',
								        'demand_details.id as demand_detail_id',
								        'demand_details.subhead_id as subhead',
								        DB::raw(" payments.amount as paid_amount")
								    )
								    ->leftJoin('property_masters', 'payments.property_master_id', '=', 'property_masters.id')
								    ->leftJoin('payer_details', 'payments.id', '=', 'payer_details.payment_id')
								    ->leftJoin('demands', 'demands.id', '=', 'payments.demand_id')
								    ->leftJoin('demand_details', function ($join) {
								        $join->on('demand_details.demand_id', '=', 'payments.demand_id')
								             ->on('demand_details.subhead_id', '=', 'payments.type'); 
								   })
								    ->where('payments.status', getStatusName('PAY_SUCCESS'))
								   // ->whereIn('property_masters.section_code',$sections)

								    ->when($startDate, fn ($q) =>
								        $q->whereDate(
								            'payments.updated_at',
								            '>=',
								            Carbon::createFromFormat('d-m-Y', $startDate)
								        )
								    )
								    ->when($endDate, fn ($q) =>
								        $q->whereDate(
								            'payments.updated_at',
								            '<=',
								            Carbon::createFromFormat('d-m-Y', $endDate)
								        )
								    )
								    ->get();
					    $paymentQuery = $paymentData;					     
					    $data['query_type'] = 'summaryallreport';	
                          }	              	
                          else
                          {
                          	if ($filterService == getServiceType('PAY_DEMAND')) {	
									$oldDemandPaymentQuery = OldPayment::query()
									    ->select(
									      // 'old_demand_subheads.DemandID as demand_id',
//									       'old_demands.property_id',
//									       'old_demands.amount',
//									       'old_demands.paid_amount',
//									       'old_demands.outstanding',
//									       'old_demands.demand_date',									       
									        'old_payments.RowID',
									        'old_payments.DemandID',
									        'old_payments.PropertyID',
									        'old_payments.TransactionNo as transaction_id',
									        'old_payments.OrderCode as unique_payment_id',
									        'old_payments.payment_type as type',
									        'old_payments.payment_status as status',
									        'old_payments.Transaction_Date as updated_at',
									        'old_payments.Name as first_name',
									        'old_payments.address',
									        'old_payments.MobileNo as mobile',
									        'old_payments.EmailID as email',
									        'old_payments.IPAddress',
									        'p.unique_propert_id',
									        'p.section_code',
									        'p.old_propert_id',
									        'cld.property_known_as as property_known_as',
									        DB::raw("'OLD' as data_source"),									        
									        DB::raw('old_payments.Amount as paid_amount'),
									    )
									   // ->leftJoin('old_payments', 'old_payments.DemandID', '=', 'old_demand_subheads.DemandID')
									    ->leftJoin('property_masters as p', 'old_payments.PropertyID', '=', 'p.old_propert_id')
									    ->leftJoin('current_lessee_details as cld', 'cld.old_property_id', '=', 'p.old_propert_id')
									    ->where('old_payments.payment_status', getStatusName('PAY_SUCCESS'))
									    ->where('old_payments.payment_type', getStatusName('PAY_DEMAND'))
									    ->when(
									        empty($startDate) && empty($endDate),
									        function ($q) use ($fixedStartDate) {
									            $q->whereDate('old_payments.Transaction_Date', '>=', Carbon::parse($fixedStartDate)->format('Y-m-d'));
									        }
									    )
									    ->when(
									        !empty($startDate),
									        function ($q) use ($startDate) {
									            $q->whereDate(
									                'old_payments.Transaction_Date',
									                '>=',
									                Carbon::createFromFormat('d-m-Y', $startDate)->format('Y-m-d')
									            );
									        }
									    )
									    ->when(
									        !empty($endDate),
									        function ($q) use ($endDate) {
									            $q->whereDate(
									                'old_payments.Transaction_Date',
									                '<=',
									                Carbon::createFromFormat('d-m-Y', $endDate)->format('Y-m-d')
									            );
									        }
									    )
										->get();								  
							    $paymentQuery = $oldDemandPaymentQuery; //->concat($oldPaymentQuery);	
							    $data['query_type'] = 'eDhartiolddemands';

							} 
							else if($filterStatus == 'summaryold')
							{
								$oldPaymentQuery = OldPayment::query()
										    ->select(
										        'old_payments.RowID',
										        'old_payments.DemandID',
										        'old_payments.PropertyID',

										        DB::raw("
										            CASE 
										                WHEN old_payments.payment_type = 1586 
										                     AND old_payments.Amount > 500
										                THEN old_payments.Amount - 500
										                ELSE old_payments.Amount
										            END as paid_amount
										        "),

										        'old_payments.TransactionNo as transaction_id',
										        'old_payments.OrderCode as unique_payment_id',
										        'old_payments.payment_type',
										        'old_payments.payment_status',
										        'old_payments.Transaction_Date as updated_at',
										        'old_payments.Name as first_name',
										        'old_payments.address',
										        'old_payments.MobileNo as mobile',
										        'old_payments.EmailID as email',
										        'old_payments.IPAddress',

										        'p.unique_propert_id',
										        'p.section_code',
										        'p.old_propert_id',

										        DB::raw("'OLD' as data_source"),
										        DB::raw('NULL as application_no')
										    )
										    ->leftJoin('property_masters as p', 'old_payments.PropertyID', '=', 'p.old_propert_id')
										    ->where('old_payments.payment_type', $filterService)
										    ->where('old_payments.payment_status', getStatusName('PAY_SUCCESS'))
										    ->where(function ($q) {
										        $q->where('old_payments.payment_type', '!=', 1586)
										          ->orWhere(function ($q2) {
										              $q2->where('old_payments.payment_type', 1586)
										                 ->where('old_payments.Amount', '>', 500);
										          });
										    })
										    ->when(
										        empty($startDate) && empty($endDate),
										        fn ($q) => $q->where('old_payments.Transaction_Date', '>=', '2026-01-01 00:00:00')
										    )
										    ->when(
										        !empty($startDate),
										        fn ($q) => $q->where(
										            'old_payments.Transaction_Date',
										            '>=',
										            Carbon::createFromFormat('d-m-Y', $startDate)->format('Y-m-d') . ' 00:00:00'
										        )
										    )
										    ->when(
										        !empty($endDate),
										        fn ($q) => $q->where(
										            'old_payments.Transaction_Date',
										            '<=',
										            Carbon::createFromFormat('d-m-Y', $endDate)->format('Y-m-d') . ' 23:59:59'
										        )
										    )
										    ->get();
						   // dd($oldPaymentQuery);
						    $paymentQuery = $oldPaymentQuery;					    
							$data['query_type'] = 'combinedold';
							}
							else if($filterService == "allold")
							{ 							
							$cldSub = DB::raw("
						    (SELECT old_property_id, MAX(property_known_as) as property_known_as 
						     FROM current_lessee_details 
						     GROUP BY old_property_id) as cld
						");

						// ✅ BASE QUERY
						$baseQuery = OldPayment::query()
						    ->leftJoin('property_masters as p', 'old_payments.PropertyID', '=', 'p.old_propert_id')
						    ->leftJoin($cldSub, 'cld.old_property_id', '=', 'p.old_propert_id')
						    ->where('old_payments.payment_status', getStatusName('PAY_SUCCESS'))
						  // ->whereDate('old_payments.Transaction_Date', '>=', Carbon::parse($fixedStartDate)->format('Y-m-d'));
						   ->when(
									        empty($startDate) && empty($endDate),
									        function ($q) use ($fixedStartDate) {
									            $q->whereDate('old_payments.Transaction_Date', '>=', Carbon::parse($fixedStartDate)->format('Y-m-d'));
									        }
									    )
									    ->when(
									        !empty($startDate),
									        function ($q) use ($startDate) {
									            $q->whereDate(
									                'old_payments.Transaction_Date',
									                '>=',
									                Carbon::createFromFormat('d-m-Y', $startDate)->format('Y-m-d')
									            );
									        }
									    )
									    ->when(
									        !empty($endDate),
									        function ($q) use ($endDate) {
									            $q->whereDate(
									                'old_payments.Transaction_Date',
									                '<=',
									                Carbon::createFromFormat('d-m-Y', $endDate)->format('Y-m-d')
									            );
									        }
									    );
						   


						// ================= NORMAL =================
						$normalPayments = (clone $baseQuery)
						    ->where('old_payments.payment_type', '!=', 1586)
						    
						    ->select(
						        'old_payments.RowID',
						        'old_payments.DemandID',
						        'old_payments.PropertyID',
						        'old_payments.Amount as paid_amount',
						        'old_payments.TransactionNo as transaction_id',
						        'old_payments.OrderCode as unique_payment_id',
						        'old_payments.payment_type as type',
						        DB::raw("'NORMAL' as payment_part"),
						        'old_payments.payment_status as status',
						        'old_payments.Transaction_Date as updated_at',
						        'old_payments.Name as first_name',
						        'old_payments.address',
						        'old_payments.MobileNo as mobile',
						        'old_payments.EmailID as email',
						        'old_payments.IPAddress',
						        'p.unique_propert_id',
						        'p.section_code',
						        'p.old_propert_id',
						        'cld.property_known_as',
						        DB::raw("'OLD' as data_source"),
						        DB::raw('NULL as application_no')
						    );


						// ================= PROCESSING =================
						$processingPart = (clone $baseQuery)
						    ->where('old_payments.payment_type', 1586)
						    ->select(
						        'old_payments.RowID',
						        'old_payments.DemandID',
						        'old_payments.PropertyID',
						        DB::raw("
						            CASE 
						                WHEN old_payments.Amount > 500 THEN 500
						                ELSE old_payments.Amount
						            END as paid_amount
						        "),
						        'old_payments.TransactionNo as transaction_id',
						        'old_payments.OrderCode as unique_payment_id',
						        'old_payments.payment_type as type',
						        DB::raw("'PROCESSING' as payment_part"),
						        'old_payments.payment_status as status',
						        'old_payments.Transaction_Date as updated_at',
						        'old_payments.Name as first_name',
						        'old_payments.address',
						        'old_payments.MobileNo as mobile',
						        'old_payments.EmailID as email',
						        'old_payments.IPAddress',
						        'p.unique_propert_id',
						        'p.section_code',
						        'p.old_propert_id',
						        'cld.property_known_as',
						        DB::raw("'OLD' as data_source"),
						        DB::raw('NULL as application_no')
						    );


						// ================= CONVERSION (FIXED) =================
						$conversionPart = (clone $baseQuery)
						    ->where('old_payments.payment_type', 1586)
						    ->where('old_payments.Amount', '>', 500)
						    ->select(
						        'old_payments.RowID', // ✅ FIX: no MIN
						        'old_payments.DemandID',
						        'old_payments.PropertyID',

						        DB::raw('(old_payments.Amount - 500) as paid_amount'), // ✅ FIX: no SUM

						        'old_payments.TransactionNo as transaction_id',
						        'old_payments.OrderCode as unique_payment_id',
						        'old_payments.payment_type as type',
						        DB::raw("'CONVERSION' as payment_part"),
						        'old_payments.payment_status as status',
						        'old_payments.Transaction_Date as updated_at',
						        'old_payments.Name as first_name',
						        'old_payments.address',
						        'old_payments.MobileNo as mobile',
						        'old_payments.EmailID as email',
						        'old_payments.IPAddress',
						        'p.unique_propert_id',
						        'p.section_code',
						        'p.old_propert_id',
						        'cld.property_known_as',
						        DB::raw("'OLD' as data_source"),
						        DB::raw('NULL as application_no')
						    );


						// ================= FINAL UNION =================
						$oldPaymentQuery = $normalPayments
						    ->unionAll($processingPart)
						    ->unionAll($conversionPart)
						    ->get();
						    
   				    	
					    $paymentQuery =$oldPaymentQuery;		
					  $total = collect($oldPaymentQuery)->sum('paid_amount'); 
					   //dd($total);    
					    $data['query_type'] = 'summaryallold';	
							}
							elseif ($filterService == getServiceType('PAY_APP_CHG')) {							  
							    $paymentQuery = Payment::select(
							            'payments.*',
							            'applications.application_no',
							            'applications.service_type',
							            'property_masters.unique_propert_id',
							            'property_masters.old_propert_id',
							            'payer_details.first_name',
							            'payer_details.mobile',
							            'payer_details.email',
							        )
							        ->leftJoin('applications', 'payments.application_no', '=', 'applications.application_no')
							        ->leftJoin('property_masters', 'payments.property_master_id', '=', 'property_masters.id')
							         ->leftJoin('payer_details', 'payments.id', '=', 'payer_details.payment_id')
							        ->where('payments.type', $filterService)
							        ->where('payments.status', getStatusName('PAY_SUCCESS'))
							         //->whereIn('property_masters.section_code',$sections)
							        ->when($startDate, function ($q) use ($startDate) {
							            $q->whereDate('payments.updated_at', '>=', Carbon::createFromFormat('d-m-Y', $startDate));
							        })
							        ->when($endDate, function ($q) use ($endDate) {
							            $q->whereDate('payments.updated_at', '<=', Carbon::createFromFormat('d-m-Y', $endDate));
							        })
							        ->get();
							    $data['query_type'] = 'processingfee';

							} 
							else if($filterService == "all")
							{ 							
							$paymentData = Payment::select(
								        'payments.*',
								        'property_masters.unique_propert_id',
								        'property_masters.section_code',
								        'property_masters.old_propert_id',
								        'payer_details.first_name',
								        'payer_details.mobile',
								        'payer_details.email',
								        'demands.unique_id as demand_unique_id',
										'demands.demand_path',
								        'demand_details.id as demand_detail_id',
								        'demand_details.subhead_id as subhead',
								        DB::raw("
								            CASE 
								                WHEN payments.demand_id IS NOT NULL 
								                THEN demand_details.paid_amount
								                ELSE payments.amount
								            END as paid_amount
								        ")
								    )
								    ->leftJoin('property_masters', 'payments.property_master_id', '=', 'property_masters.id')
								    ->leftJoin('payer_details', 'payments.id', '=', 'payer_details.payment_id')
								    ->leftJoin('demands', 'demands.id', '=', 'payments.demand_id')
								    ->leftJoin('demand_details', function ($join) {
								        $join->on('demand_details.demand_id', '=', 'payments.demand_id')
								             ->on('demand_details.subhead_id', '=', 'payments.type'); 
								   })
								    ->where('payments.status', getStatusName('PAY_SUCCESS'))
								   // ->whereIn('property_masters.section_code',$sections)

								    ->when($startDate, fn ($q) =>
								        $q->whereDate(
								            'payments.updated_at',
								            '>=',
								            Carbon::createFromFormat('d-m-Y', $startDate)
								        )
								    )
								    ->when($endDate, fn ($q) =>
								        $q->whereDate(
								            'payments.updated_at',
								            '<=',
								            Carbon::createFromFormat('d-m-Y', $endDate)
								        )
								    )
								    ->get();
					    $paymentQuery = $paymentData;					     
					    $data['query_type'] = 'summaryall';	
							}					
							else {	
										
							$demandData = DemandDetail::query()
							    ->join('demands as d', 'demand_details.demand_id', '=', 'd.id')
							    ->join('items as i', 'demand_details.subhead_id', '=', 'i.id')
							    ->join('property_masters as p', 'd.property_master_id', '=', 'p.id')
							     
							    ->leftJoin('payments as pay', function ($join) {
							        $join->on('pay.demand_id', '=', 'd.id')
							             ->where('pay.status', getStatusName('PAY_SUCCESS'));
							    })	
							     ->leftJoin('payer_details', 'pay.id', '=', 'payer_details.payment_id')						    
							    ->where('demand_details.subhead_id', $filterService) 
							     ->when($startDate, function ($q) use ($startDate) {
								        $q->whereDate('demand_details.updated_at', '>=', Carbon::createFromFormat('d-m-Y', $startDate));
								    })
								    ->when($endDate, function ($q) use ($endDate) {
								        $q->whereDate('demand_details.updated_at', '<=', Carbon::createFromFormat('d-m-Y', $endDate));
								    })
							   // ->whereIn('p.section_code',$sections)
							    ->select(
							        'demand_details.*',
							        'd.id as demaid',
							        'd.unique_id',
									'd.demand_path',
							        'd.current_fy',
							        'p.old_propert_id',
							        'p.unique_propert_id',
							        DB::raw('MAX(payer_details.first_name) as first_name'),
									DB::raw('MAX(payer_details.mobile) as mobile'),
									DB::raw('MAX(payer_details.email) as email'),
							        DB::raw('GROUP_CONCAT(DISTINCT pay.transaction_id) as transaction_ids'),
							        DB::raw('GROUP_CONCAT(DISTINCT pay.unique_payment_id) as unique_payment_ids'),
							      // DB::raw('IFNULL(SUM(pay.amount),0) as paid_amount')
							    )
							    
							    ->groupBy(
							        'demand_details.id',
							        'd.id',
							        'd.unique_id',
							        'd.current_fy',
							        'p.old_propert_id',
							        'p.unique_propert_id'
							    )
							    ->get();
							    //dd($filterService);
								$paymentData = Payment::select(
								        'payments.*',
								        'property_masters.unique_propert_id',
								        'property_masters.old_propert_id',
								        'payer_details.first_name',
								        'payer_details.mobile',
								        'payer_details.email',
								        'property_masters.section_code',

								        DB::raw('0 as net_total'),
								        DB::raw('NULL as property_known_as'),
								        DB::raw('payments.amount as paid_amount'),
								        DB::raw('0 as balance_amount'),
								        DB::raw('payments.status as status')
								    )
								    ->leftJoin('property_masters', 'payments.property_master_id', '=', 'property_masters.id')
								    ->leftJoin('payer_details', 'payments.id', '=', 'payer_details.payment_id')
								    ->whereNull('payments.demand_id') // only direct
								    ->where('payments.type', $filterService) 
								    ->where('payments.status', getStatusName('PAY_SUCCESS'))
								     //->whereIn('property_masters.section_code',$sections)
								    ->when($startDate, function ($q) use ($startDate) {
								        $q->whereDate('payments.updated_at', '>=', Carbon::createFromFormat('d-m-Y', $startDate));
								    })
								    ->when($endDate, function ($q) use ($endDate) {
								        $q->whereDate('payments.updated_at', '<=', Carbon::createFromFormat('d-m-Y', $endDate));
								    })
								    ->get();
							$paymentQuery = $demandData->concat($paymentData);
						 $data['query_type'] = 'combined';
							}
							// Return to Blade
							}
							$data['applications'] = $paymentQuery;
							return view('payment.payment-summary-details', $data);
							
		        
		}
		
	 public function paymentReport(Request $request)
    {   
        $user = Auth::user();
        $sections = $user->sections->pluck('section_code');
        $startDate = isset($request->start_date) && !empty($request->start_date) ? $request->start_date : null;
        $endDate = isset($request->end_date) && !empty($request->end_date) ? $request->end_date : null; 
				$processingfee = Payment::select(
								        DB::raw('type as code'),
								        DB::raw('SUM(amount) as amount'),
								        DB::raw('COUNT(property_master_id) as property_count')
								    )
								    ->whereIn('type', [
								        getStatusName('GROUND_RENT'),
								        getStatusName('PAY_APP_CHG'),
								        getStatusName('PAY_RENT_SUB'),
								        getStatusName('PAY_TEMP_ALLOT'),
								        getStatusName('PAY_LAND_ALLOT'),
										 getStatusName('PAY_RTI'),
								        getStatusName('PAY_DEMAND')
								    ])
								    ->where('status', getStatusName('PAY_SUCCESS'))
								    ->when($startDate, function ($q) use ($startDate) {
								        $q->whereDate(
								            'updated_at',
								            '>=',
								            Carbon::createFromFormat('d-m-Y', $startDate)->format('Y-m-d')
								        );
								    })
								    ->when($endDate, function ($q) use ($endDate) {
								        $q->whereDate(
								            'updated_at',
								            '<=',
								            Carbon::createFromFormat('d-m-Y', $endDate)->format('Y-m-d')
								        );
								    })
								    ->groupBy('type')
								    ->get()
								    ->map(function ($row) {
								        return [
								            'code'   => $row->code,
								            'name'   => getServiceNameById($row->code),
								            'amount' => $row->amount,
								            'property_count' => $row->property_count,
								        ];
								    })
								    ->sortBy('name')
								    ->values();
								$data['summary'] = $processingfee;
								$data['request'] = $request;
        return view('payment.summaryreport', $data);
    }	




	public function paymentLiveStatusSearch()
    {
        return view('payment.payment-live-status-search', [
        'payment' => null,
        'liveStatus' => null
        ]);
    }


    public function paymentLiveStatusResult(Request $request)
    {

       $request->validate([
        'search_by' => 'required',
        'search_value' => 'required'
        ]);

        $allowedColumns = [
            'old_property_id',
            'unique_payment_id',
            'transaction_id',
            'demand_id'
            ];

        if (!in_array($request->search_by, $allowedColumns)) {
            return back()->with('error', 'Invalid Search Type');
        }

        switch ($request->search_by) {

            case 'old_property_id':

                $payment = Payment::with('demand')->where(function ($query) use ($request) {
                    $query->where('master_old_property_id', $request->search_value)
                        ->orWhere('splited_old_property_id', $request->search_value);
                })
                ->latest('id')
                ->first();

                break;

            case 'unique_payment_id':

                $payment = Payment::with('demand')->where(
                    'unique_payment_id',
                    $request->search_value
                )->first();

                break;

            case 'transaction_id':

                $payment = Payment::with('demand')->where(
                    'transaction_id',
                    $request->search_value
                )->first();

                break;

            case 'demand_id':

                $payment = Payment::with('demand')
                            ->whereHas('demand', function ($query) use ($request) {
                                $query->where('unique_id', $request->search_value);
                            })
                            ->latest('id')
                            ->first();

                break;

            default:

                $payment = null;
        }

        if (!$payment) {

            $labels = [
                'old_property_id'   => 'Property ID',
                'unique_payment_id' => 'Payment ID',
                'transaction_id'    => 'Transaction Number',
                'demand_id'         => 'Demand ID',
            ];

            $fieldName = $labels[$request->search_by] ?? 'Record';

            return view('payment.payment-live-status-search', [
                'payment' => null,
                'liveStatus' => null,
            ])->withErrors($fieldName . ' not found.');
        }

        // Payment already successful
        if (strtoupper($payment->status) == 'SUCCESS') {

            return view('payment.payment-live-status-search', [
                'payment' => $payment,
                'liveStatus' => null
            ]);
        }

        // Check live status
        return $this->getLivePaymentStatus($payment);

    }

    private function getLivePaymentStatus($payment)
    {

        $orderId = $payment->unique_payment_id;

        $purposeId = '15777';

        $url = config('constants.paymentStatusURL');

        $data = [

            "OrderId"=>$orderId,

            "PurposeId"=>$purposeId

        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if(curl_errno($ch))
        {
            return back()->with(
                'error',
                curl_error($ch)
            );
        }

        curl_close($ch);

        $response = trim($response,'"');

        $apiResponse = explode('|',$response);

        if(count($apiResponse)<3)
        {
            return back()->with(
                'error',
                'Invalid response received from BharatKosh.'
            );
        }
        // dd($apiResponse);

        list(
            $orderId,
            $orderStatus,
            $transactionId,
            $countryCode,
            $rupee,
            $method,
            $amount,
            $bankCode,
            $bankOrderId,
            $bankTransactionDate,
            $message
        ) = $apiResponse;

       return view('payment.payment-live-status-search', [
                        'payment' => $payment,
                        'liveStatus' => [
                            'orderId' => $orderId,
                            'status' => $orderStatus,
                            'transactionId' => $transactionId,
                            'method' => $method,
                            'amount' => $amount,
                            'transactionDate' => $bankTransactionDate,
                            'message' => $message

                        ]
                    ]);

    }

    // protected function getPaymentSummaryByStatus($payments, string $status): array
    // {
    //     $filtered = $payments->where('status', getServiceType($status));

    //     return [
    //         'count' => $filtered->count(),
    //         'amount' => $filtered->sum('amount'),
    //     ];
    // }
      protected function getPaymentSummaryByStatus($payments, string $status): array
    {
        $filtered = $payments->where('status', getServiceType($status));

        return [
            'count' => $filtered->count(),
            'amount' => $filtered->sum('amount'),
        ];
    }
		protected function getBreakup($collection, string $needle): array
		{
		    $filtered = $collection->filter(fn($item) => stripos($item->model, $needle) !== false);
		//dd($filtered);
		    return [
		        'total' => [
		            'count' => $filtered->count(),
		            'amount' => $filtered->sum('amount'),
		        ],
		        'pending' => [
		            'count' => $filtered->where('status', getServiceType('PAY_PENDING'))->count(),
		            'amount' => $filtered->where('status', getServiceType('PAY_PENDING'))->sum('amount'),
		        ],
		        'success' => [
		            'count' => $filtered->where('status', getServiceType('PAY_SUCCESS'))->count(),
		            'amount' => $filtered->where('status', getServiceType('PAY_SUCCESS'))->sum('amount'),
		        ],
		    ];
		}

    // protected function getBreakup($collection, string $needle): array
    // {
    //     $filtered = $collection->filter(fn($item) => stripos($item->model, $needle) !== false);

    //     return [
    //         'count' => $filtered->count(),
    //         'amount' => $filtered->sum('amount'),
    //     ];
    // }
// protected function getBreakup($collection, string $needle): array
// {
//     $filtered = $collection->filter(fn($item) => stripos($item->model, $needle) !== false);
// //dd($filtered);
//     return [
//         'total' => [
//             'count' => $filtered->count(),
//             'amount' => $filtered->sum('amount'),
//         ],
//         'pending' => [
//             'count' => $filtered->where('status', getServiceType('PAY_PENDING'))->count(),
//             'amount' => $filtered->where('status', getServiceType('PAY_PENDING'))->sum('amount'),
//         ],
//         'success' => [
//             'count' => $filtered->where('status', getServiceType('PAY_SUCCESS'))->count(),
//             'amount' => $filtered->where('status', getServiceType('PAY_SUCCESS'))->sum('amount'),
//         ],
//     ];
// }


     public function applicantPayment(Request $request, PaymentService $paymentService)
    {    
        // dd($request->all());
		$rules = [
      
        'paid_amount' => 'required|numeric|min:1|max:99999999999999',
        'payment_mode' => 'required|in:PAY_ONLINE,PAY_OFFLINE',

        // Payer details
        'payer_first_name' => 'required|regex:/^[a-zA-Z]+( [a-zA-Z]+)*$/|max:255',
        'payer_last_name' => 'nullable|regex:/^[a-zA-Z]+( [a-zA-Z]+)*$/|max:255',
        'payer_mobile' => 'required|regex:/^[1-9][0-9]{9}$/|digits:10',
        'payer_email' => 'required|email|max:255',

        'address_1' => 'required|regex:/^[a-zA-Z0-9 ,.\/()-]+$/|max:500',
        'address_2' => 'nullable|regex:/^[a-zA-Z0-9 ,.\/()-]+$/|max:500',
        'region' => 'nullable|regex:/^[a-zA-Z0-9 ,]+$/|max:255',
        'postal_code' => 'nullable|regex:/^[1-9][0-9]{5}$/|digits:6',

        'country' => 'required|exists:countries,id',
        'state' => 'required|exists:states,id',
        'city' => 'required|exists:cities,id',
    ];

    // Custom error messages
    $messages = [
      
        'paid_amount.required' => 'Paid amount is required',
        'paid_amount.numeric' => 'Paid amount must be a number',
        'paid_amount.min' => 'Paid amount must be at least :min',
        'paid_amount.max' => 'Paid amount must be less than :max',
        'payment_mode.required' => 'Payment mode is required',
        'payment_mode.in' => 'Invalid payment mode',

        'payer_first_name.required' => 'First name is required',
        'payer_first_name.regex' => 'First name can only contain letters and spaces',
        'payer_first_name.max' => 'First name cannot exceed 255 characters',

        'payer_last_name.regex' => 'Last name can only contain letters and spaces',
        'payer_last_name.max' => 'Last name cannot exceed 255 characters',

        'payer_mobile.required' => 'Mobile number is required',
        'payer_mobile.regex' => 'Please enter a valid 10-digit mobile number',
        'payer_mobile.digits' => 'Mobile number must be exactly 10 digits',

        'payer_email.required' => 'Email address is required',
        'payer_email.email' => 'Please enter a valid email address',
        'payer_email.max' => 'Email address cannot exceed 255 characters',

        'address_1.required' => 'Address Line 1 is required',
        'address_1.regex' => 'Please enter a valid address',
        'address_1.max' => 'Address cannot exceed 500 characters',

        'address_2.regex' => 'Please enter a valid address',
        'address_2.max' => 'Address cannot exceed 500 characters',

        'region.regex' => 'Region can only contain letters, numbers, and spaces',
        'region.max' => 'Region cannot exceed 255 characters',

        'postal_code.regex' => 'Please enter a valid 6-digit postal code',
        'postal_code.digits' => 'Postal code must be exactly 6 digits',

        'country.required' => 'Country is required',
        'country.exists' => 'Invalid country selected',
        'state.required' => 'State is required',
        'state.exists' => 'Invalid state selected',
        'city.required' => 'City is required',
        'city.exists' => 'Invalid city selected',
    ];
      $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {

        return back()
            ->withErrors($validator)
            ->withInput();
    } 
		$demand_type= 0; 
        if ($request->property_id){
            $propertyId = $request->property_id;
            $isSplitedproperty = SplitedPropertyDetail::where('old_property_id', $propertyId)->first();
            if ($isSplitedproperty) {
                $propertyMasterId = $isSplitedproperty->property_master_id;
                $propertyMaster = PropertyMaster::find($propertyMasterId);
                $master_old_property_id = $propertyMaster->old_propert_id;
                $splited_old_property_id = $isSplitedproperty->old_property_id;
            } else {
                $propertyMaster = PropertyMaster::where('old_propert_id', $propertyId)->first();
                if (empty($propertyMaster)) {
                    return redirect()->back()->with('failure', 'Property ID does not exist');
                }
                $propertyMasterId = $propertyMaster->id;
                $master_old_property_id = $propertyMaster->old_propert_id;
                $splited_old_property_id = null;
            }
        } 
        // dd($propertyMasterId,$master_old_property_id,$splited_old_property_id);
        
       $paymentIdPrefix = '';
        if($request->payment_type == 'GROUND_RENT'){
            $paymentIdPrefix = 'GR';
        } elseif($request->payment_type == 'PAY_RTI'){
            $paymentIdPrefix = 'RTI';
			 $propertyMasterId = 1;
            $master_old_property_id = 11;
        }
        elseif($request->payment_type == 'PAY_TEMP_ALLOT'){
            $paymentIdPrefix = 'TMALT';
            $propertyMasterId = 1;
            $master_old_property_id = 11;
        }
         elseif($request->payment_type == 'PAY_LAND_ALLOT'){
            $paymentIdPrefix = 'LDALT';
            $propertyMasterId = 1;
            $master_old_property_id = 11;
        }

        elseif($request->payment_type == 'PAY_RENT_SUB'){
            $paymentIdPrefix = 'SUBL';
        }
		 elseif($request->payment_type == 'PAY_DEMAND' && $request->olddemand){
            $paymentIdPrefix = 'OL';
			$demand_type= 1; 
        }
        $paidAmount = $request->paid_amount;
        $uniquePayemntId = $paymentIdPrefix . date('YmdHis');
       $payment = Payment::create([
            'property_master_id' => $propertyMasterId,
            'type' => getServiceType($request->payment_type),
            'payment_mode' => getServiceType($request->payment_mode),
            'unique_payment_id' => $uniquePayemntId,
            'splited_property_detail_id' => isset($isSplitedproperty) ? $isSplitedproperty->id : null,
            'master_old_property_id' => $master_old_property_id ,
            'splited_old_property_id' => $splited_old_property_id ?? null,
            'amount' => $paidAmount,
            'status' => 1,
			'demand_type'=> $demand_type,
            'demand_id'=> $request->demand_id ?? null,
            'created_by' => Auth::check() ? Auth::id() : null
        ]);


        if ($payment) {

            //save payer details
            GeneralFunctions::savePayerDetails($request->all(), $payment->id);

            // Payment 
            list($countryName, $stateName, $cityName) =  GeneralFunctions::getAddressNames($request->only('country', 'state', 'city'));

            $orderCode = $uniquePayemntId;
            // $orderCode = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
            $payementData = [
                'order_code' => $orderCode,
                'merchant_batch_code' => $orderCode,
                'installation_id' => '11136',
                'amount' => $paidAmount,
                'currency_code' => "INR",
                'order_content' => '15777',
                'payemnt_type_id' => config('constants.payment_type_id'),
                'code' => getServiceNameByCode($request->payment_mode),
                'email' => $request->payer_email,
                'first_name' => $request->payer_first_name,
                'last_name' => $request->payer_last_name,
                'mobile' => $request->payer_mobile,
                'address_1' => $request->address_1,
                'address_2' => $request->address_2,
                'postal_code' => $request->postal_code,
                'region' => $request->region,
                'city' => $cityName,
                'state' => $stateName,
                'country' => $countryName,
            ];

            $transaction = $paymentService->makePayemnt($payementData);
            // return redirect()->back()->with('success', 'Data saved successfully');
        }
    }

    public function userPaymentStatusCheck(Request $request)
	{
	   if ( $request->ajax()){
		// dd($request);

	       $type = $request->searchType;
				$query = Payment::leftJoin('payer_details', 'payer_details.payment_id', '=', 'payments.id')
				    ->leftJoin('property_masters', 'property_masters.id', '=', 'payments.property_master_id')
					    ->leftJoin(
							'splited_property_details',
							'splited_property_details.old_property_id',
							'=',
							'payments.splited_old_property_id'
						)
				    ->select(
				        'payments.*',
				        'payer_details.first_name',
				        'payer_details.email',
				        'payer_details.mobile',
				        'property_masters.old_propert_id',
				        'property_masters.unique_propert_id',
        				'splited_property_details.old_property_id as splited_old_property_id'
				    );
				switch ($type) {

				    case 'payment':
				        $query->where('payments.unique_payment_id', $request->payment_id);
				        break;
				    case 'transaction':
				        $query->where('payments.transaction_id', $request->transaction_no);
				        break;
				    // case 'property':
				    //     $query->where('payments.master_old_property_id', $request->property_id);
				    //     break;

					case 'property':
						$query->where(function ($q) use ($request) {
							$q->where('payments.master_old_property_id', $request->property_id)
							->orWhere('splited_property_details.old_property_id', $request->property_id);
						});
						break;
				   case 'payee':
					    $query->where(function ($q) use ($request) {
					        if (!empty($request->email) && !empty($request->mobile)) {					          
					            $q->where('payer_details.email', $request->email)
					              ->orWhere('payer_details.mobile', $request->mobile);

					        } elseif (!empty($request->email)) {
					            $q->where('payer_details.email', $request->email);

					        } elseif (!empty($request->mobile)) {
					            $q->where('payer_details.mobile', $request->mobile);
					        }
					    });
					    break;
				    default:
				        return view('include.partials.payment_result', ['data' => collect()])->render();
				}
				$data = $query->get();
				return view('include.partials.payment_result', compact('data'))->render();
	    }
	    $data['paymentTypes'] = getItemsByGroupId(17011);
	    $data['guestUser'] = true;
	    return view('payment.payment-status', $data);
	}
    
}
