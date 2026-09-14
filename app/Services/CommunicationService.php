<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Http;
use App\Models\Configuration;
use App\Models\Template;
use Illuminate\Support\Facades\Log;

class CommunicationService
{
    protected $variables = [];

    public function __construct()
    {
    }

    //to send whatsapp message
    public function sendWhatsAppMessage($data,$number,$action, $countryCode = null)
    {
        return true; //till we are not using any whatsapp service
        $sameConfiguration = Configuration::where('type', 'whatsapp')
        ->where('action', $action)
        ->where('status', 1)
        ->first();
        if($sameConfiguration){
            $configuration = $sameConfiguration;
        } else {
            $defaultConfiguration = Configuration::where('type', 'whatsapp')->where('action','DEFAULT' )->where('status', 1)->first();
            $configuration = $defaultConfiguration;
        }
        if ($configuration) {
            $message = $this->getTemplate($data,'whatsapp',$action);
            $key = $configuration->key;
            $token = $configuration->auth_token;
            $whatsAppFrom = $configuration->whatsapp_number;
            $baseUrl = $configuration->api;
            $vendor = $configuration->vendor;
            switch ($vendor) {
                case 'Twilio':
                    $response = Http::withBasicAuth($key, $token)
                    ->asForm()
                    ->post($baseUrl, [
                        'To' => "whatsapp:+91{$number}",
                        // 'To' => "whatsapp:+{$countryCode}{$number}",
                        'From' => $whatsAppFrom,
                        'Body' => $message,
                    ]);
                    break;
                    
                    case 'Nexmo':
                    $response = Http::withBasicAuth($key, $token)
                                ->withHeaders([
                                    'Accept' => 'application/json',
                                ])
                                ->post($baseUrl, [
                                    'from' => $whatsAppFrom,
                                    'to' => "91{$number}",
                                    // 'to' => "{$countryCode}{$number}",
                                    'message_type' => 'text',
                                    'text' => $message,
                                    'channel' => 'whatsapp',
                                ]);
                    break;
            
                default:
                    // Handle unsupported vendor or throw an exception
                    throw new \Exception("Unsupported vendor: {$vendor}");
            }

            if ($response->successful()) {
                return true;
            } elseif ($response->failed()) {
                return false;
            } else {
                return false;
            }
        }
    }

    //to send text sms
    public function sendSmsMessage($data,$number,$action, $countryCode = null)
    {
        $sameConfiguration = Configuration::where('type', 'sms')
        ->where('action', $action)
        ->where('status', 1)
        ->first();
        if($sameConfiguration){
            $configuration = $sameConfiguration;
        } else {
            $defaultConfiguration = Configuration::where('type', 'sms')->where('action','DEFAULT' )->where('status', 1)->first();
            $configuration = $defaultConfiguration;
        }
        if ($configuration) {
           
            $templateData = $this->getTemplate($this->shrinkForSms($data), 'sms', $action);
                    Log::info("Preparing SMS template.", [
                'action'       => $action,
                'templateData' => $templateData,
            ]);
            $message = $templateData['message'];
              
           $templateId = $templateData['template_id'];
            $key = $configuration->key;
            $token = $configuration->auth_token;
            $smsFrom = $configuration->sms_number;
            $baseUrl = $configuration->api;
            $vendor = $configuration->vendor;
    //    dd($baseUrl, $key, $token, $message, $number, $smsFrom, $templateId); 
    // Request Params
                    /*Log::info("Request Params", [
                        'baseUrl'   => $baseUrl,
                        'key'   => $key,
                        'token'   => $token,
                        'message' => $message,
                        'number' => $number,
                        'smsFrom' => $smsFrom,
                        'templateId' => $templateId,
                    ]);*/
          switch ($vendor) {
                case 'Twilio':
                    $response = Http::withBasicAuth($key, $token)
                        ->asForm()
                        ->post($baseUrl, [
                            'To' => "+91{$number}",
                            // 'To'   => "+{$countryCode}{$number}", // Clean string interpolation
                            'From' => $smsFrom,
                            'Body' => $message,
                        ]);
                    break;
            
                case 'Nexmo':
                    $response = Http::post($baseUrl, [
                        'to' => "+91{$number}",
                        // 'to' => "+{$countryCode}{$number}",
                        'from' => 'EDHARTI',
                        'text' => $message,
                        'api_key' => $key,
                        'api_secret' => $token
                    ]);
                    break;
            case 'AirtelDLT':
                // Log response
                    /*Log::info("Request Before Send HTTP Request.", [
                        'baseUrl'   =>$baseUrl,
                        'username'         => $key,
                        'pin'              => $token,
                        'message'   =>   $message,  
                        'mnumber'          => $number,
                        'signature'        => 'LDODPT',
                        'dlt_entity_id'    => $smsFrom,
                        'dlt_template_id'  => $templateId,
                    ]);*/
   
                $response = Http::withoutVerifying()->asForm()->post($baseUrl, [
                        'username'         => $key,
                        'pin'              => $token,
                        'message'   =>   $message,  
                        'mnumber'          => $number,
                        'signature'        => 'LDODPT',
                        'dlt_entity_id'    => $smsFrom,
                        'dlt_template_id'  => $templateId,
                    ]);
                    // Log response
                    /*Log::info("Response After Send HTTP Request.", [
                        'action'   => $action,
                        'mobile'   => $number,
                        'status'   => $response->status(),
                        'response' => $response->body(),
                    ]);*/
//dd($response);
                break;
                default:
                    // Handle unsupported vendor or throw an exception
                    throw new \Exception("Unsupported vendor: {$vendor}");
            }
            
            if ($response->successful()) {
                return true;
            } elseif ($response->failed()) {
                return false;
            } else {
                return false;
            }
        }
    }

    public function getTemplate($data,$type,$action)
    {
        $template = Template::where('action', $action)
                        ->where('type', $type)
                        ->where('status', 1)
                        ->first();
//dd($template);
        $smsData = [];
        $smsData['message'] = $this->createTemplate($template['template'],$data);   
        $smsData['template_id'] = $template['template_id'];
       return $smsData;
    }


    public function createTemplate($template,$data)
    {
        foreach ($data as $key => $value) {
            $template = str_replace("@[{$key}]", $value, $template);
        }
	$message = str_replace(['\n', '%0A'],"\n",$template);
        return $message;
    }
  /**
     * Multibyte-safe trim for SMS, appends "..." only if needed.
     */
    private function smsTrim(?string $value, int $limit = 40): string
    {
        $value = trim((string) $value);
        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            return mb_strlen($value) <= $limit
                ? $value
                : rtrim(mb_substr($value, 0, $limit)) . '...';
        }
        return strlen($value) <= $limit
            ? $value
            : rtrim(substr($value, 0, $limit)) . '...';
    }

    /**
     * Only for SMS payloads: trim selected keys (e.g., remarks/reason/links).
     * Extend the list if you need more keys in the future.
     */
    private function shrinkForSms(array $data, array $keys = ['reason','remarks', 'reasons', 'remark']): array
    {
        foreach ($keys as $k) {
            if (array_key_exists($k, $data) && is_string($data[$k])) {
                $data[$k] = $this->smsTrim($data[$k], 30);
            }
        }
        return $data;
    }

}
