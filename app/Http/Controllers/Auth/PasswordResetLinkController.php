<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\GeneralFunctions;
use App\Http\Controllers\Controller;
use App\Mail\CommonMail;
use App\Models\Otp;
use App\Models\User;
use App\Services\CommunicationService;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Log;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function sendOtp(Request $request, CommunicationService $communicationService, SettingsService $settingsService)
    {
        $request->validate([
            'email' => 'nullable|email|required_without:phone',
            'phone' => 'nullable|string|required_without:email',
        ]);
        $action = 'PASS_FORGET';
        if ($request->email) {
            $user = User::where('email', $request->email)->first();
            if (empty($user)) {
                return redirect()->back()->withErrors(['email' => 'Given email is not found in our records.']);
            } else {
                $generateOtp = GeneralFunctions::generateUniqueRandomNumber(4);
                $otp = Otp::updateOrCreate(
                    ['email' => $request->email],
                    ['email_otp' => $generateOtp, 'email_otp_sent_at' => now()]
                );
                $data = ['otp' => $generateOtp];
                try {
                    $mailSettings = app(SettingsService::class)->getMailSettings($action);
                    $mailer = new \App\Mail\CommonPHPMail($data, $action, $communicationTrackingId ?? null);
                    $mailResponse = $mailer->send($request->email, $mailSettings);

                    Log::info("Email sent successfully.", [
                        'action' => $action,
                        'email'  => $request->email,
                        'data'   => $data,
                    ]);
                } catch (\Exception $e) {
                    Log::error("Email sending failed.", [
                        'action' => $action,
                        'email'  => $request->email,
                        'error'  => $e->getMessage(),
                    ]);
                }
                // Mail::to($request->email)->send(new CommonMail($data, $action));
                if ($otp) {
                    return view('auth.otpVerify', ['email' => $request->email]);
                }
            }
        }
        if ($request->phone) {
            $user = User::where('mobile_no', $request->phone)->first();
            if (empty($user)) {
                return redirect()->back()->withErrors(['phone' => 'Given phone number is not found in our records.']);
            } else {
                $generateOtp = GeneralFunctions::generateUniqueRandomNumber(4);
                $otp = Otp::updateOrCreate(
                    ['mobile' => $request->phone],
                    ['mobile_otp' => $generateOtp, 'mobile_otp_sent_at' => now()]
                );
                $data = ['otp' => $generateOtp];
                $communicationService->sendSmsMessage($data, $request->phone, $action);
                if ($otp) {
                    return view('auth.otpVerify', ['phone' => $request->phone]);
                }
            }
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
    }

    public function verify(Request $request)
    {
        $request->validate(['otp' => 'required']);
        if ($request->email) {
            $otpRow = Otp::where('email', $request->email)->first();
            if ($otpRow) {
                $otp = $otpRow->email_otp;
                if ($otp == $request->otp) {
                    $user = $this->getUserFromEmail($request->email);
                    if ($user) {
                        $userId = $user->id;
                    }
                } else {
                    return redirect()->back()->with('failure', 'Given OTP do not match please try again');
                }
            } else {
                return redirect()->back()->with('failure', config('messages.general.error.unknown'));
            }
        }
        if ($request->phone) {
            $otpRow = Otp::where('mobile', $request->phone)->first();
            if ($otpRow) {
                $otp = $otpRow->mobile_otp;
                if ($otp == $request->otp) {
                    $user = $this->getUserFromMobile($request->phone);
                    if ($user) {
                        $userId = $user->id;
                    }
                } else {
                    return redirect()->back()->with('failure', 'Given OTP do not match please try again');
                }
            } else {
                return redirect()->back()->with('failure', config('messages.general.error.unknown'));
            }
        }
        return view('auth.forgot-password-reset', compact('userId'));
    }
    private function getUserFromMobile($mobile)
    {
        return User::where('mobile_no', $mobile)->first();
    }
    private function getUserFromEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function store(Request $request)
    {
        $request->validate([
            'new_password' => ['required', 'confirmed', 'min:8', Rules\Password::defaults()],
        ]);

        $user = User::find($request->userId);
        if (!$user) {
            return response()->json(['status' => true, 'message' => config('messages.general.error.unknown')]);
            // return redirect()->route('login')->with('failure', config('messages.general.error.unknown'));
        }
        $passwordUpdated = $user->update(['password' => Hash::make($request->new_password)]);
        if ($passwordUpdated) {
            // return redirect()->route('login')->with('success', 'Password changed successfully.');
            return response()->json(['status' => true, 'message' => 'Password changed successfully.']);
        } else {
            return response()->json(['status' => false, 'message' => config('messages.general.error.unknown')]);
            // return redirect()->route('login')->with('failure', config('messages.general.error.unknown'));
        }
    }
}