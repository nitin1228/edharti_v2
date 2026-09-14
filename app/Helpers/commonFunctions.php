<?php

use App\Models\Item;
use App\Models\PropertyLeaseDetail;
use App\Models\PropertyMaster;
use App\Models\SplitedPropertyDetail;
use Illuminate\Support\Facades\Log;
use App\Models\ApplicationCharge;
use App\Models\Configuration;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Template;
use App\Models\Section;
use App\Models\ApplicationMovement;
use App\Models\Payment;
use App\Models\Application;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Demand;
use App\Models\DemandDetail;
use App\Services\PaymentService;

if (!function_exists('customNumFormat')) {
    function customNumFormat($num)
    {
        $isNegative = $num < 0;
        $num = abs($num);
        if ($num < 1000) {
            return $isNegative ? '-' . $num : $num;
        } else {
            $numStr = (string)$num;
            $decArray = explode('.', $numStr);
            $decimalPart = (count($decArray) > 1) ? $decArray[1] : '';
            $numParts = [];
            $devideBy = 1000;
            $intPart = (int)($num / $devideBy);
            $numParts[] = str_pad($num % $devideBy, 3, '0', STR_PAD_LEFT); //initially we need saperator before three digits from right, ones , thens, hundreds
            $devideBy = 100;
            while ($intPart > 99) {
                $tempInt = (int)($intPart / $devideBy);
                $numParts[] = str_pad($intPart % $devideBy, 2, '0', STR_PAD_LEFT); // add ',' after every two digits from rightafter 
                $intPart = $tempInt;
            }
            $intPart =  $intPart . ',' . implode(',', array_reverse($numParts));
            if (strlen($decimalPart) > 0) {

                $returning = $intPart . '.' . $decimalPart;
            } else {
                $returning =  $intPart;
            }
            return $isNegative ? '-' . $returning : $returning;
        }
    }
}

if (!function_exists('dateDiffInYears')) {
    function dateDiffInYears($date1, $date2)
    {

        // Convert strings to DateTime objects
        $d1 = new \DateTime($date1);
        $d2 = new \DateTime($date2);

        // Calculate the difference between the two dates
        $interval = $d1->diff($d2);

        // Get the difference in years
        return $interval->y;
    }
}
if (!function_exists('getLoggedInUserSections')) {
    function getLoggedInUserSections()
    {
        $user = Auth::user();
        $sections = $user->sections->pluck('id')->toArray();
        return $sections;
    }
}


if (!function_exists('getApplicationTypeByApplicationNo')) {
    function getApplicationTypeByApplicationNo($applicationNo)
    {
        $application = Application::where('application_no', $applicationNo)->first();
        if ($application->service_type == getServiceType('SUB_MUT')) {
            $applicationType = 'Mutation';
        } else if ($application->service_type == getServiceType('DOA')) {
            $applicationType = 'Deed Of Apartment';
        } else if ($application->service_type == getServiceType('CONVERSION')) {
            $applicationType = 'Conversion';
        } else if ($application->service_type == getServiceType('LUC')) {
            $applicationType = 'LUC';
        } else if ($application->service_type == getServiceType('NOC')) {
            $applicationType = 'NOC';
        }
        return $applicationType;
    }
}
if (!function_exists('getServiceType')) {
    function getServiceType($code)
    {
        $item = Item::where('item_code', $code)->first();
        if ($item) {
            return $item->id;
        } else {
            Log::info("Item not available for " . $code);
        }
    }
}

if (!function_exists('getServiceNameByCode')) {
    function getServiceNameByCode($code)
    {
        $item = Item::where('item_code', $code)->first();
        if ($item) {
            return $item->item_name;
        } else {
            Log::info("Item not available for " . $code);
        }
    }
}

if (!function_exists('getServiceCodeById')) {
    function getServiceCodeById($id)
    {
        $item = Item::where('id', $id)->first();
        if ($item) {
            return $item->item_code;
        } else {
            Log::info("Item not available for " . $id);
        }
    }
}

if (!function_exists('getServiceNameById')) {
    function getServiceNameById($id)
    {
        $item = Item::find($id);
        if ($item) {
            return $item->item_name;
        } else {
            Log::info("Item available for " . $id);
        }
    }
}

if (!function_exists('getServiceTypeColorCode')) {
    function getServiceTypeColorCode($code)
    {
        $item = Item::where('item_code', $code)->first();
        if ($item) {
            return $item->color_code;
        } else {
            Log::info("Color Code not available for " . $code);
        }
    }
}

if (!function_exists('getStatusName')) {
    function getStatusName($code)
    {
        $item = Item::where('item_code', $code)->first();
        if ($item) {
            return $item->id;
        } else {
            Log::info("Item not available for " . $code);
        }
    }
}

if (!function_exists('getBlockThroughLocality')) {
    function getBlockThroughLocality($locality)
    {
        $blocks = PropertyMaster::select('block_no')
            ->where('new_colony_name', $locality)
            ->orderByRaw("CAST(block_no AS UNSIGNED), block_no")
            ->distinct()
            ->get();
        return $blocks;
    }
}

if (!function_exists('getPlotThroughBlock')) {
    function getPlotThroughBlock($locality, $block)
    {
        $plots = PropertyMaster::where('new_colony_name', $locality)
            ->where('block_no', $block)
            ->get();
        $data = [];
        foreach ($plots as $plot) {
            if ($plot->is_joint_property) {
                $splited = SplitedPropertyDetail::select('plot_flat_no')->where('property_master_id', $plot->id)->get();
                // dd($splited);
                foreach ($splited as $split) {
                    $data[] = $split->plot_flat_no;
                }
            } else {
                $data[] = $plot->plot_or_property_no;
            }
        }
        return array_unique($data);
    }
}

if (!function_exists('getKnownAsThroughPlot')) {
    function getKnownAsThroughPlot($locality, $block, $plot)
    {
        $property = PropertyMaster::where('new_colony_name', $locality)
            ->where('block_no', $block)
            ->where('plot_or_property_no', $plot)
            ->first();

        if ($property) {
            // If property is found, retrieve the presently known names
            $property_master_id = $property->id;
            $knownAs = PropertyLeaseDetail::where('property_master_id', $property_master_id)
                ->pluck('presently_known_as')
                ->toArray();  // Convert collection to array
        } else {
            // If property not found, retrieve the plot/flat numbers from Splited Property Detail table
            $knownAs = [];
            $data = SplitedPropertyDetail::where('plot_flat_no', $plot)
                ->get();

            foreach ($data as $known) {
                $knownAs[] = $known->plot_flat_no;
            }
        }
        return array_unique($knownAs);
    }
}



if (!function_exists('getStatusDetailsById')) {
    function getStatusDetailsById($id)
    {
        $item = Item::find($id);
        if ($item) {
            return $item;
        } else {
            Log::info("Item not available for " . $id);
        }
    }
}

if (!function_exists('truncate_url')) {
    function truncate_url($url, $length = 20, $ellipsis = '....')
    {
        if (strlen($url) <= $length) {
            return $url;
        }

        return substr($url, 0, $length) . $ellipsis;
    }
}
if (!function_exists('getAge')) {
    function getAge($dob)
    {
        $dobDate = new DateTime($dob);
        $today = new DateTime('today');
        $age = $dobDate->diff($today)->y;
        return $age;
    }
}
if (!function_exists('getItemsByGroupId')) {
    function getItemsByGroupId($id)
    {
       return Item::where('group_id', $id)->where('is_active', 1)->orderByRaw('CAST(item_order AS UNSIGNED) ASC')->get();
    }
}

if (!function_exists('getAllItemsByGroupId')) {
    function getAllItemsByGroupId($id)
    {
        return Item::where('group_id', $id)->orderBy('item_order')->whereIn('item_code',['NOC','SUB_MUT','CONVERSION','DOA'])->get();
    }
}

if (!function_exists('getApplicationStatusList')) {
    function getApplicationStatusList($withDisposed = false, $removeDisposedStatusesFromList = false)
    {
        $applicationStatusList = getItemsByGroupId(1031);
        if ($withDisposed) {
            //remove aproved and rejected status
            if ($removeDisposedStatusesFromList) {
                $applicationStatusList = $applicationStatusList->whereNotIn('item_code', ['APP_APR', 'APP_REJ', 'APP_WD']); // APP_WD added as withrawn applications are not need to show to official
            } else {
                $applicationStatusList = $applicationStatusList->whereIn('item_code', ['APP_APR', 'APP_REJ']); // Show only Approved & Reject
            }

            // Manually create a new Item model instance for "Disposed"
            $disposedStatus = new Item();
            $disposedStatus->item_code = 'APP_DES';
            $disposedStatus->id = 0;
            $disposedStatus->item_name = 'Disposed';
            $disposedStatus->item_order = 4;
            $disposedStatus->additional_data = json_encode(['color' => 'btn-light-brown', 'icon' => 'fa-solid fa-trash-arrow-up']);

            // Append the new model instance to the existing collection
            $applicationStatusList = $applicationStatusList->push($disposedStatus);
        }
        return $applicationStatusList->sortBy('item_order')  // use sort by to get in ascending item order
            ->values();  // Reset the keys again after push
    }
}


// Comment above code To Get status option for Received & Disposed Application - Lalit tiwari (27/02/2025)
if (!function_exists('getApplicationTypeList')) {
    function getApplicationTypeList()
    {
        $applicationTypeList = getItemsByGroupId(17001);
        $applicationTypeList = $applicationTypeList->whereIn('item_code', ['SUB_MUT', 'CONVERSION', 'NOC', 'LUC', 'DOA']);
        $values = $applicationTypeList->sortBy('item_order')->values();
        return $values;  // Reset the keys again after push
    }
}

/** function added by Nitin */
// if (!function_exists('getApplicationStatusList')) {
//     function getApplicationStatusList($withDisposed = false)
//     {
//         $applicationStatusList = getItemsByGroupId(1031);
//         if ($withDisposed) {
//             //remove aproved and rejected status
//             $applicationStatusList = $applicationStatusList->whereNotIn('item_code', ['APP_APR', 'APP_REJ', 'APP_WD']); // APP_WD added as withrawn applications are not need to show to official

//             // Manually create a new Item model instance for "Disposed"
//             $disposedStatus = new Item();
//             $disposedStatus->item_code = 'APP_DES';
//             $disposedStatus->item_name = 'Disposed';
//             $disposedStatus->item_order = 4;

//             // Append the new model instance to the existing collection
//             $applicationStatusList = $applicationStatusList->push($disposedStatus);
//         }
//         return $applicationStatusList->sortBy('item_order')  // use sort by to get in ascending item order
//             ->values();  // Reset the keys again after push
//     }
// }
// //SwatiMishra Create and Update OTP functions 14-11-2024 Start

// if (!function_exists('createOtp')) {
//     function createOtp($type, $value, $serviceType, $action, $countryCode = null, CommunicationService $communicationService = null)
//     {
//         $generateOtp = app(GeneralFunctions::class)->generateUniqueRandomNumber(4);
//         $otpData = [
//             'service_type' => getServiceType($serviceType),
//             "{$type}" => $value,
//             "{$type}_otp" => $generateOtp,
//             "{$type}_otp_sent_at" => now(),
//         ];

//         if ($type === 'mobile' /* && $countryCode */) {
//             $otpData['country_code'] = $countryCode; // Include country code
//         }

//         $otp = Otp::create($otpData); 

//         Log::info("OTP generated: " . $generateOtp);

//         // Prepare data for sending the OTP
//         $data = ['otp' => $generateOtp];

//         // Use CommunicationService instance, or get it from the app container if not provided
//         $communicationService = $communicationService ?? app(CommunicationService::class);

//         // Handle sending OTP based on type
//         if ($type === 'mobile') {
//             $communicationService->sendSmsMessage($data, $value, $action);
//             $communicationService->sendWhatsAppMessage($data, $value, $action);
//         } elseif ($type === 'email') {
//             app(SettingsService::class)->applyMailSettings($action); // Access SettingsService via app()
//             Mail::to($value)->send(new CommonMail($data, $action));
//         }

//         return response()->json(['success' => true, 'message' => "OTP sent to {$type} " . $value . " successfully"]);
//     }
// }

// if (!function_exists('updateOtp')) {
//     function updateOtp($otpRecord, $type, $value, $action, $countryCode = null, CommunicationService $communicationService = null)
//     {
//         $generateOtp = app(GeneralFunctions::class)->generateUniqueRandomNumber(4);

//         if ($type === 'mobile') {
//             $otpRecord->country_code = $countryCode; // Update country code
//             $otpRecord->mobile = $value;
//             $otpRecord->mobile_otp = $generateOtp;
//             $otpRecord->mobile_otp_sent_at = now();

//         } else {
//             $otpRecord->email = $value;
//             $otpRecord->email_otp = $generateOtp;
//             $otpRecord->email_otp_sent_at = now();
//         }

//         $otpRecord->save();

//         Log::info("OTP updated: " . $generateOtp);

//         $data = ['otp' => $generateOtp];
//         $communicationService = $communicationService ?? app(CommunicationService::class);

//         if ($type === 'mobile') {
//             $communicationService->sendSmsMessage($data, $value, $action);
//             $communicationService->sendWhatsAppMessage($data, $value, $action);
//         } elseif ($type === 'email') {
//             app(SettingsService::class)->applyMailSettings($action);
//             Mail::to($value)->send(new CommonMail($data, $action));
//         }

//         return response()->json(['success' => true, 'message' => "OTP sent to {$type} " . $value . " successfully"]);
//     }

// }
//SwatiMishra Create and Update OTP functions 14-11-2024 End

if (!function_exists('getApplicationCharge')) {
    function getApplicationCharge($serviceType)
    {
        // Get today's date
        $today = Carbon::today();
        $amount = ApplicationCharge::where('service_type', $serviceType)->whereDate('effective_date_from', '<=', $today)
            ->whereDate('effective_date_to', '>=', $today)
            ->value('amount');
        if (!empty($amount)) {
            return $amount;
        } else {
            Log::info("No Amount record exists in Application Charge Table for Service " . $serviceType);
            return 0;
        }
    }
}


//SwatiMishra Create and Update OTP functions 14-11-2024 End

/** Function Added By Nitin */
if (!function_exists('userHasAccessToProperty')) {
    function userHasAccessToProperty($propertyId)
    {
        $user = Auth::user();
        if ($user->user_type == 'applicant') {
            $userProperites = $user->userProperties;
            $allowAccess = false;
            foreach ($userProperites as $prop) {
                if ($prop->old_property_id == $propertyId) {
                    $allowAccess = true;
                    break;
                }
            }
            return $allowAccess;
        } else {
            return true;
        }
    }
}

/** Function Added By Nitin -- 03-12-2024*/
if (!function_exists('getUserNameById')) {
    function getUserNameById($id)
    {
        $user = User::find($id);

        if (!empty($user)) {
            return $user->name;
        } else {
            return null;
        }
    }
}
/** Function Added By Nitin -- 12-12-2024*/
if (!function_exists('getUserRoleName')) {
    function getUserRoleName($Userid)
    {
        $user = User::find($Userid);

        if (!empty($user)) {
            return strtoupper($user->roles[0]->name);
        } else {
            return null;
        }
    }
}

if (!function_exists('getUserAssignedSections')) {
    function getUserAssignedSections()
    {
        $user = Auth::user();
        $filterUserSections = $user->hasAnyRole('section-officer','assistant-section-officer', 'deputy-lndo', 'super-admin', 'minister', 'lndo');
        $userSectionIdList = $user->sections->where('has_property', 1)->pluck('id')->toArray();
        return [$filterUserSections, $userSectionIdList];
    }
}

if (!function_exists('getAssignedSections')) {
    function getAssignedSections()
    {
        $user = Auth::user();
        // return  $user->sections->where('has_property', 1);
        return $user->sections()->where('has_property', 1)->orderBy('name')->get();
    }
}

if (!function_exists('checkTemplateExists')) {
    function checkTemplateExists($type, $action)
    {
        $template = Template::where('type', $type)->where('action', $action)->where('status', '1')->first();
        if ($template) {
            return $template->id;
        } else {
            Log::info("Template not available for Type :" . $type . " and Action" . $action);
            return false;
        }
    }
}

//This function is created to get the count of colonies in which MIS has been completed --Amita [15-01-2025]
if (!function_exists('getMisDoneColoniesCount')) {
    function getMisDoneColoniesCount()
    {
        $count_of_colonies = PropertyMaster::distinct('new_colony_name')->count('new_colony_name');
       return $count_of_colonies ?? 0;
    }
}
if (!function_exists('getAddressDropdownData')) {
    function getAddressDropdownData()
    {
        $countries = DB::table('countries')->get();
        $states = DB::table('states')->where('country_id', 101)->get();
        return ['countries' => $countries, 'states' => $states];
    }
}

/** function added by Nitin to get current financial year - 02 Jan 2025 */
if (!function_exists('getFinancialYear')) {
    function getFinancialYear(): string
    {
        return strtotime('now') > strtotime(date('Y-04-01'))
            ? date('Y') . '-' . (date('Y') + 1)
            : (date('Y') - 1) . '-' . date('Y');
    }
}


/** this function is added by Swati to get lease and property sections- 20-03-2025 */
if (!function_exists('getRequiredSections')) {
    function getRequiredSections()
    {
        $requiredSections = ['LS1', 'LS2A', 'LS2B', 'LS3', 'LS4', 'LS5', 'PS1', 'PS2', 'PS3', 'RPC'];
        return Section::whereIn('section_code', $requiredSections)->get();
    }
}

/** this function is added by nitin for backend validation of date - 26-03-2025 */
if (!function_exists('isValidDate')) {
    function isValidDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
/** this function is added by nitin return the property status of a property - 26-03-2025 */
if (!function_exists('getProperyStatusFromOldPropetyId')) {
    function getProperyStatusFromOldPropetyId($oldPropertyId)
    {
        $property_status = false;
        $masterProperty = DB::table('property_masters')->where('old_propert_id', $oldPropertyId)->first();
        if (!empty($masterProperty)) {
            return $masterProperty->status;
        }
        $childProperty = DB::table('splited_property_details')->where('old_property_id', $oldPropertyId)->first();
        if (!empty($childProperty)) {
            return $childProperty->property_status;
        }
        return $property_status;
    }
}


//For checking is user can view the application - SOURAV CHAUHAN 01-05-2025
if(!function_exists('isOfficeViewTheApplication')){
    function isOfficeViewTheApplication($applicationNo)
    {

        $authuser = Auth::user();
        $userId = $authuser->id;
        $allMovements = ApplicationMovement::whereNotIn('service_type',[1478,1479])->orderBy('created_at', 'desc')
        ->get();
        $latestMovements = $allMovements->unique('application_no');
        // dd($latestMovements);
        $userAssigned = $latestMovements->where('assigned_to', $userId);
        $canView = false;
        $currentApplicationDate = null;

        foreach ($userAssigned as $movement) {
            if ($movement->application_no == $applicationNo) {
                $currentApplicationDate = $movement->created_at;
                break; 
            } 
        }


        $otherApplicationDate = null;
        $currentApplicationNo = null;
        $currentActionableApplicationNo = null;
        if(count($userAssigned) > 1){
            foreach ($userAssigned as $movement) {
                if ($movement->application_no != $applicationNo) {
                    $otherApplicationDate = $movement->created_at;
                    $currentApplicationNo = $movement->application_no;
                } 
                if($currentApplicationDate < $otherApplicationDate){
                    $canView =  true;
                } else {
                    $canView =  false;
                    $currentActionableApplicationNo = $currentApplicationNo;

                }
            }
        } else {
            $canView = true;
        }

        $data = [
            'canView' => $canView,
            'currentActionableApplicationNo' => $currentActionableApplicationNo
        ];
        // dd($canView);
        return $data;
    }
}


if(!function_exists('userCurrentActionableApplication')){
    function userCurrentActionableApplication()
    {

        $authuser = Auth::user();
        $userId = $authuser->id;
        $allMovements = ApplicationMovement::where('service_type','!=',1479)->orderBy('created_at', 'desc')->get();
        $latestMovements = $allMovements->unique('application_no');
        $userAssigned = $latestMovements->where('assigned_to', $userId);
        $latestAssigned = $userAssigned->sortBy('created_at')->first();
        if(is_null($latestAssigned)){
            return null;
        }
        return $latestAssigned['application_no'];
    }
}
if (!function_exists('decryptString')) {
    function decryptString($str)
    {
        if (strlen($str) == 0)
            return $str;
         $decoded = base64_decode($str, true);
        if ($decoded === false || strlen($decoded) < 16) {
            return $str; // not encrypted (too short or invalid base64)
        }
        $key = "aa11ss22dd33ff44gg55hh66jj77kk88";
        $iv = "a1s2d3f4g5h6j7k8";
        $decrypted = openssl_decrypt(
            base64_decode($str),
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
        return $decrypted;
    }
}
//Payment reciept function added by Swati Mishra on 14-07-2025

if (!function_exists('downloadPaymentReceiptPdf')) {
    function downloadPaymentReceiptPdf($unique_payment_id, $application_no = null)
    {
        $successStatusId = getServiceType('PAY_SUCCESS');
        $payment = Payment::with([
            'payerDetails',
            'demand',
            'paymentModeItem',
            'paymentTypeItem',
            'property.newColony',
            'application.serviceTypeItem'
        ])->where('unique_payment_id', $unique_payment_id)->first();

        if (!$payment) {
            abort(404, 'Payment not found');
        }
        // If application_no is passed in URL, fetch ALL payments for that application_no
        if (!empty($application_no)) {
            $payments = Payment::with([
                'payerDetails',
                'demand',
                'paymentModeItem',
                'paymentTypeItem',
                'property.newColony',
                'application.serviceTypeItem'
            ])->where('application_no', $application_no)
              ->where('status', $successStatusId) // ✅ only success
              ->orderBy('created_at', 'asc')
              ->get();
        } else {
            // Otherwise keep single record as current behavior
            $payments = collect([$payment]);
        }

   
        // Convert amount to words (with paisa support)
        $amount = number_format((float) $payment->amount, 2, '.', '');
        $amountParts = explode('.', $amount);
        $rupees = (int) $amountParts[0];
        $paise = isset($amountParts[1]) ? (int) $amountParts[1] : 0;

        $words = convertNumberToWords($rupees);
        if ($paise > 0) {
            $words .= ' and ' . convertNumberToWords($paise) . ' paise';
        }

        $amountInWords = 'Rupees ' . ucfirst($words) . ' only';

        // Generate PDF
        $pdf = Pdf::loadView('payment.payment_receipt', [
            'payment' => $payment,
            'payments' => $payments,  
            'amount_in_words' => $amountInWords
        ]);

        $filename = "PaymentReceipt_{$unique_payment_id}.pdf";

        return $pdf->download($filename);
    }
}

//Payment Amount in words function added by Swati Mishra on 14-07-2025

if (!function_exists('convertNumberToWords')) {
    function convertNumberToWords($number)
    {
        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = [
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'forty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            100000 => 'lakh',
            10000000 => 'crore'
        ];

        if (!is_numeric($number)) {
            return false;
        }

        if ($number < 0) {
            return $negative . convertNumberToWords(abs($number));
        }

        $string = '';
        $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        $number = (int)$number;

        if ($number < 21) {
            $string = $dictionary[$number];
        } elseif ($number < 100) {
            $tens = ((int) ($number / 10)) * 10;
            $units = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
        } elseif ($number < 1000) {
            $hundreds = (int) ($number / 100);
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convertNumberToWords($remainder);
            }
        } else {
            foreach ([10000000 => 'crore', 100000 => 'lakh', 1000 => 'thousand', 100 => 'hundred'] as $value => $name) {
                if ($number >= $value) {
                    $count = (int) ($number / $value);
                    $remainder = $number % $value;
                    $string .= convertNumberToWords($count) . ' ' . $name;
                    if ($remainder) {
                        $string .= $remainder < 100 ? $conjunction : $separator;
                        $string .= convertNumberToWords($remainder);
                    }
                    break;
                }
            }
        }

        if ($fraction !== null && (int)$fraction > 0) {
            $fraction = str_pad($fraction, 2, '0'); // Make sure it's 2 digits
            $fractionWords = convertNumberToWords((int)$fraction);
            $string .= $conjunction . $fractionWords . ' paise';
        }

        return $string;
    }

}

// This is use less, we are not using this.
if (!function_exists('checkUpdatedPaymentStatus')) {
     function checkUpdatedPaymentStatus($paymentId)
        {

            $payment = Payment::find($paymentId);
            $orderId = $payment->unique_payment_id;
            $purposeId = '15777';
            // $url = "http://164.100.129.32/bharatkosh/getstatus";
            $url = config('constants.paymentStatusURL');
            $data = array("OrderId" => $orderId, "PurposeId" => $purposeId);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            if (curl_errno($ch)) {
                echo 'Curl error: ' . curl_error($ch);
            } else {
                $curl_output = trim(curl_exec($ch), '"');
                $apiResponse = explode('|', $curl_output);
                // dd($apiResponse);
                curl_close($ch);
                if (count($apiResponse) > 1) {
                    list($orderId, $orderStatus, $transactionId) = $apiResponse;
                    // dd($orderId, $orderStatus, $transactionId);
                    /* if($paymentId == 24 && strtoupper($orderStatus) == "SUCCESS"){
                        Log::info("Payment inside if");
                        Log::info($paymentId." _ ". strtoupper($orderStatus));
                    } else {
                        Log::info("Payment inside else");
                        Log::info($paymentId." _ ". strtoupper($orderStatus));
                    } */
                    if (strtoupper($orderStatus) == "SUCCESS") {
                        $paymentData = Payment::where('unique_payment_id', $orderId)->first();
                        if (!empty($paymentData)) {
                            $paymentData->update([
                                'status' => getServiceType('PAY_' . $orderStatus) ?? getServiceType('PAY_PENDING') ,
                                'response' => $curl_output,
                                'transaction_id' => $transactionId,
                            ]);

                            $paymentService = new PaymentService();
                            $paymentService->processSuccessfulPayment($paymentData);
                        }
                    } else {
                    }
                } else {
                    dd($curl_output, $apiResponse);
                    /* print_r($response_api);
                    echo "</pre>"; */
                }
            }
        }
    }


function convertToIndianCurrencyWords($number)
{
    $no = floor($number);
    $point = round($number - $no, 2) * 100;

    $words = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six',
        'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve',
        'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen',
        'Eighteen', 'Nineteen'
    ];

    $tens = [
        '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty',
        'Sixty', 'Seventy', 'Eighty', 'Ninety'
    ];

    $digits = ['', 'Thousand', 'Lakh', 'Crore'];

    $str = [];

    // Grouping according to Indian numbering system
    $i = 0;
    while ($no > 0) {
        if ($i == 0) {
            $divider = 1000;
            $number = $no % 1000;
        } else {
            $divider = 100;
            $number = $no % 100;
        }

        $no = floor($no / $divider);

        if ($number) {
            $hundreds = '';
            if ($number > 99) {
                $hundreds = $words[floor($number / 100)] . ' Hundred ';
                $number = $number % 100;
            }

            if ($number < 20) {
                $num_word = $words[$number];
            } else {
                $num_word = $tens[floor($number / 10)];
                if ($number % 10 != 0) {
                    $num_word .= '-' . $words[$number % 10];
                }
            }

            $unit = $digits[$i] ? ' ' . $digits[$i] : '';
            $str[] = trim($hundreds . $num_word . $unit);
        }
        $i++;
    }

    $result = implode(' ', array_reverse($str));
    $result = trim($result) . ' Rupees';

    if ($point > 0) {
        if ($point < 20) {
            $point_words = $words[$point];
        } else {
            $point_words = $tens[floor($point / 10)];
            if ($point % 10 != 0) {
                $point_words .= '-' . $words[$point % 10];
            }
        }
        $result .= ' and ' . $point_words . ' Paise';
    }

    return $result . ' Only';
}

if (!function_exists('sqmToSqyard'))
{
function sqmToSqyard($sqm) {
    return $sqm * 1.19599;
}
}

/** this function is added by nitin - 23-04-2025 */
if (!function_exists('camelToTitle')) {
    function camelToTitle($string)
    {
        return  ucwords(preg_replace('/([a-z])([A-Z])/', '$1 $2', $string));
    }
}


if (!function_exists('getPaymentSummaryByStatus')) {
    function getPaymentSummaryByStatus($payments, string $status)
    {
         $filtered = $payments->where('status', getServiceType($status));

        return [
            'count' => $filtered->count(),
            'amount' => $filtered->sum('amount'),
        ];
        
    }
}


if (!function_exists('getBreakup')) {
    function getBreakup($collection, string $needle)
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
}
if (!function_exists('getSectionsName')) {
 function getSectionsName($id)
    {        
    	$item=  Section::where('id', $id)->first();
         if ($item) {
            return $item->section_code;
        } 
    }
}

if (!function_exists('getRevenueDetails')) {
    function getRevenueDetails(Request $request = null)
    {
        $today = Carbon::today();

        // Financial year calculation
        if ($today->month < 4) {
            $fyStart = Carbon::create($today->year - 1, 4, 1);
            $fyEnd   = Carbon::create($today->year, 3, 31);
        } else {
            $fyStart = Carbon::create($today->year, 4, 1);
            $fyEnd   = Carbon::create($today->year + 1, 3, 31);
        }

        // Safely read optional request values
        $startDate = ($request && $request->filled('start_date'))
            ? Carbon::parse($request->start_date)->format('Y-m-d')
            : $fyStart->format('Y-m-d');

        $endDate = ($request && $request->filled('end_date'))
            ? Carbon::parse($request->end_date)->format('Y-m-d')
            : $fyEnd->format('Y-m-d');
       
        $paymentQuery = Payment::when($startDate, function ($q) use ($startDate) {
            return $q->whereDate('created_at', '>=', $startDate);
        })
        ->when($endDate, function ($q) use ($endDate) {
            return $q->whereDate('created_at', '<=', $endDate);
        });

        $payments = $paymentQuery->whereIn('status', [getServiceType('PAY_PENDING'), getServiceType('PAY_SUCCESS')])->get();

        $data['total_transactinos'] = $payments->count();
        $data['total_amount'] = $payments->sum('amount');

        // Use the helper for pending and success
        $data['statuswise'] = [
            'PAY_PENDING' => getPaymentSummaryByStatus($payments, 'PAY_PENDING'),
            'PAY_SUCCESS' => getPaymentSummaryByStatus($payments, 'PAY_SUCCESS'),
        ];

        $data['typewiseCount'] = $payments->groupBy('type')->map(fn($group) => $group->count());
        $data['typewiseAmount'] = $payments->groupBy('type')->map(fn($group) => $group->sum('amount'));
        $applicationPayments = $payments->where('type', getServiceType('PAY_APP_CHG'));
        $headwise = DemandDetail::query()
						    ->join('items as i', 'demand_details.subhead_id', '=', 'i.id')
						    ->select(
						        DB::raw('i.id as code'),  
						        'i.item_name as name',
						        DB::raw('SUM(demand_details.paid_amount) as amount')
						    )
						   ->when($startDate, function ($q) use ($startDate) {
                                $q->where('demand_details.updated_at', '>=',
                                    Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay()
                                );
                            })
                            ->when($endDate, function ($q) use ($endDate) {
                                $q->where('demand_details.updated_at', '<=',
                                    Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay()
                                );
                            })

						    ->groupBy('i.id', 'i.item_name')
						    ->get();


        $processingfee = Payment::select(
                            DB::raw('type as code'),
                            DB::raw('SUM(amount) as amount')
                        )
                        ->whereIn('type', [
                            getStatusName('GROUND_RENT'),
                            getStatusName('PAY_APP_CHG'),
                            getStatusName('PAY_RENT_SUB'),
                            getStatusName('PAY_TEMP_ALLOT'),
                            getStatusName('PAY_LAND_ALLOT')
                        ])
                        ->where('status', getStatusName('PAY_SUCCESS'))
                        ->when($startDate, function ($q) use ($startDate) {
                            $q->whereDate('updated_at', '>=', $startDate);
                        })
                        ->when($endDate, function ($q) use ($endDate) {
                            $q->whereDate('updated_at', '<=', $endDate);
                        })

                        ->groupBy('type')
                        ->get()
                        ->map(function ($row) {
                            return (object)[
                                'code'   => $row->code,
                                'name'   => getServiceNameById($row->code), 
                                'amount' => $row->amount,
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
                    ];
                })
                ->values()
                ->sortBy('name') 
                ->values(); 
        $data['summary'] = $final;
        $data['applicationwiseBreakup'] = [
                    'Conversion' => getBreakup($applicationPayments, 'conversion'),
                    'LUC' => getBreakup($applicationPayments, 'landUseChange'),
                    'Mutation' => getBreakup($applicationPayments, 'mutation'),
                    'NOC' => getBreakup($applicationPayments, 'NOC'),
                ];
        $demandQuery  = Demand::when($startDate, function ($q) use ($startDate) {
                            $q->whereDate('created_at', '>=', $startDate);
                        })
                        ->when($endDate, function ($q) use ($endDate) {
                            $q->whereDate('created_at', '<=', $endDate);
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
        return $data;
        
    }
}


if (!function_exists('getRoleIdByName')) {
/**
 * Get role id by role name (case-insensitive).
 *
 * @throws \Exception
 */
function getRoleIdByName(string $name): int
    {
        $id = DB::table('roles')
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->value('id');
            

        if (!$id) {
            throw new \Exception("Role '{$name}' not found in roles table.");
        }

        return (int) $id;
    }
}

/*
| GET PROPERTY SECTION DETAILS - added by swati on 13052026
*/

    if (!function_exists('getPropertySectionDetails')) {

        function getPropertySectionDetails($propertyId)
        {

            $property = PropertyMaster::where(
                'old_propert_id',
                $propertyId
            )->first();

            if (!$property) {

                $splitProperty = SplitedPropertyDetail::where(
                        'old_property_id',
                        $propertyId
                    )->first();

                if ($splitProperty) {

                    $property = PropertyMaster::find(
                            $splitProperty->property_master_id
                        );
                }
            }

            if (!$property || !$property->section_code) {

                return null;
            }

            $section = Section::where(
                'section_code',
                $property->section_code
            )->first();

            if (!$section) {

                return null;
            }

            return [
                'section_id' => $section->id,
                'section_code' => $section->section_code,
                'section_name' => $section->name,
            ];
        }
    }

    if (!function_exists('getItemsOnlyForDemand')) {
        function getItemsOnlyForDemand($id)
        {
            return Item::where('group_id', $id)->where('item_code', 'PAY_DEMAND')->where('is_active', 1)->orderBy('item_order')->get();
        }
    }

    if (!function_exists('getItemsOnlyForProperty')) {
        function getItemsOnlyForProperty($id)
        {
            return Item::where('group_id', $id)->whereIn('item_code', ['GROUND_RENT', 'PAY_RENT_SUB', 'PAY_TEMP_ALLOT', 'PAY_LAND_ALLOT'])->where('is_active', 1)->orderBy('item_order')->get();
        }
    }

    if (!function_exists('isUserCanAcessThisResource')) {
    function isUserCanAcessThisResource($oldPropertyId){
        //get property section
        $splittedProperty = DB::table('splited_property_details')->where('old_property_id', $oldPropertyId)->first();
        if($splittedProperty){
            $propertySection = DB::table('property_masters')->where('id', $splittedProperty->property_master_id)->value('section_code');
        } else {
            $propertySection = DB::table('property_masters')->where('old_propert_id', $oldPropertyId)->value('section_code');
        }
        if($propertySection){
             $user = Auth::user();
            $loggedinUserSections = $user->sections->where('has_property', 1)->pluck('section_code')->toArray();
            // dd(trim($propertySection), $loggedinUserSections);
            return in_array(trim($propertySection), $loggedinUserSections) ? true : false;
        } else {
            return false;   
        }
    }
}


