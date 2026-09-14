<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\CommonMail; // Make sure CommonMail exists and works

class TestMailController extends Controller
{
    public function send(Request $request)
    {
        // Set a test recipient email
        $testEmail =  'swati96m@gmail.com';

        // Dummy data (match structure expected by your CommonMail template)
        $data = [
            'application_id' => 'TEST123',
            'date' => now()->format('Y-m-d'),
            'time' => now()->format('H:i'),
            'remark' => 'This is a test email.',
'otp' => 2345
        ];

        $action = 'OTP_VALID'; 
 //     dd(new CommonMail($data, $action));
        try {
            $mail = Mail::to($testEmail)->send(new CommonMail($data, $action));
        //   dd("Job created Successfully", $mail);
            return response()->json(['status' => 'success', 'message' => "Test email sent to {$testEmail}"]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}

