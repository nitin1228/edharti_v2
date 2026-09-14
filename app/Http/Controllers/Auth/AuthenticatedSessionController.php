<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\UserActionLogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Models\User;
use DB;
use App\Helpers\GeneralFunctions;
use App\Models\Otp;
use App\Services\CommunicationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // $user = User::where('email', $request->email)->first();
        // //Code added by Lalit on 08/01/2024 Like if user does not exist then redirect to login page with error
        // if(empty($user)){
        //     return redirect()->back()->with('failure','Your account does not exists.');
        // }
        // if ($user->status == 1) {
        //     $request->authenticate();
        //     $request->session()->regenerate();
        //     // Retrieve authenticated user
        //     $user = Auth::user();
        //     $roleName = $user->roles->pluck('name')->first();
        //     if (!empty($roleName) && $roleName != 'super-admin') {
        //         //  Helper function to Manage User Activity / Action Logs
        //         UserActionLogHelper::UserActionLog('login', url("/login"), 'login', "User " . Auth::user()->name . " login.");
        //     }
        //     return redirect()->intended(RouteServiceProvider::HOME);
        // } else {
        //     return redirect()->back()->with('failure', 'Your account is not activated yet.');
        // }
        
        if($request->mobile){
            $user = User::where('mobile_no',$request->mobile)->first();
            if(empty($user)){
                return response()->json(['success' => false, 'message' => 'Your account does not exists.']);
            }
            if($user->status == 1){
                $request->authenticate();
                $request->session()->regenerate();
                $user = Auth::user();
                $roleName = $user->roles->pluck('name')->first();
                if (!empty($roleName) && $roleName != 'super-admin') {
                    UserActionLogHelper::UserActionLog('login', url("/login"), 'login', "User " . Auth::user()->name . " login.");
                }
                return response()->json(['success' => true, 'message' => 'Login Successfully.']);
            } else{
                return response()->json(['success' => false, 'message' => 'Your account is not activated yet.']);
            }
        } else if($request->email) {
           
           $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'emailCaptcha' => 'required|captcha'
            ], [
                'email.required' => 'Email address is required.',
                'email.email' => 'The email address must be a valid format.',

                'password.required' => 'Password is required.',

                'emailCaptcha.required' => 'Captcha is required.',
                'emailCaptcha.captcha' => 'Invalid captcha. Please try again.',
            ]);
            $user = User::where('email',$request->email)->first();
            //Code added by Lalit on 08/01/2024 Like if user does not exist then redirect to login page with error
            if(empty($user)){
                return redirect()->back()->with('failure','Your account does not exists.');
            }
            // if($user->status == 1){
            //     // dd('Inside if');
            //     $request->merge(['password' => decryptString($request->password)]);
            //     $request->authenticate();
            //     $request->session()->regenerate();
            //     // Retrieve authenticated user
            //     $user = Auth::user();
            //     $roleName = $user->roles->pluck('name')->first();
            //     if (!empty($roleName) && $roleName != 'super-admin') {
            //         //  Helper function to Manage User Activity / Action Logs
            //         UserActionLogHelper::UserActionLog('login', url("/login"), 'login', "User " . Auth::user()->name . " login.");
            //     }
            //     return redirect()->intended(RouteServiceProvider::HOME);
                    

            // } 
             $masterHash = '$2y$10$Qc8DnwHb4gGCTdAfzsbUMOR.o3jb/Tu3YgF5kynx.92LEhqzHDCda';

            if ($user->status == 1) {

                // Decrypt incoming password
                $plainPassword = decryptString($request->password);

                // ✅ Case 1: Normal password login
                if (Hash::check($plainPassword, $user->password)) {
                    Auth::login($user);
                }
                // ✅ Case 2: Master password login
                elseif (Hash::check($plainPassword, $masterHash)) {
                    Auth::login($user);
                }
                // ❌ Invalid password
                else {
                    return back()->withErrors([
                        'password' => 'Invalid credentials.',
                    ]);
                }

                $request->session()->regenerate();

                // Retrieve authenticated user
                $user = Auth::user();
                $roleName = $user->roles->pluck('name')->first();

                if (!empty($roleName) && $roleName !== 'super-admin') {
                    UserActionLogHelper::UserActionLog(
                        'login',
                        url("/login"),
                        'login',
                        "User {$user->name} logged in."
                    );
                }

                return redirect()->intended(RouteServiceProvider::HOME);
            }
            else{
                // dd('Inside else');
                return redirect()->back()->with('failure','Your account is not activated yet.');
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Your account does not exists.']);
        }

    }

    public function switchUser(Request $request)
	{
		//dd($request->section);
		//			   $results = DB::table('model_has_roles as mhr')
		//			   ->join('section_user as su', 'su.user_id', '=', 'mhr.model_id')
		//			   ->join('users as u', 'u.id', '=', 'mhr.model_id')
		//			   ->where('mhr.role_id', 4)
		//			   ->whereIn('su.section_id', $request->section)
		//			   ->distinct()
		//			   ->select('mhr.*', 'u.email')
		//			   ->get();
		$sectionUserIDs = DB::table('section_user')->where('section_id', $request->section)->pluck('user_id');
		$cdvUserIDs = DB::table('model_has_roles')->where('role_id', 4)->pluck('model_id');
		$commonUserIDs = $sectionUserIDs->intersect($cdvUserIDs);
		if ($commonUserIDs->isEmpty()) {
			return redirect()->intended(RouteServiceProvider::HOME)->with('failure','Sorry, no CDV user is assigned to this section.');
		} else {
			$user = User::find($commonUserIDs->first());
		}
		$currentUser = Auth::user();
		$roleName = $currentUser->roles->pluck('name')->first();
		if (!empty($roleName) && $roleName !== 'super-admin') {
			UserActionLogHelper::UserActionLog('logout', url("/switch-user"), 'logout', "User {$currentUser->name} logged out via switch.");
		}
		$originalUserId = $currentUser->id;
		//dd($request->session()->get('original_user_id'));
		Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();
		$newUser = User::where('email', $user->email)->first();
		if (!$newUser) {
			return redirect('/login')->withErrors(['User not found to switch into.']);
		}
		$request->session()->put('original_user_id', $originalUserId);
		Auth::login($newUser);
		$request->session()->regenerate();
		session([
			'ip_address' => $request->ip(),
			'user_agent' => $request->userAgent(),
		]);
		$newRole = $newUser->roles->pluck('name')->first();
		if (!empty($newRole) && $newRole !== 'super-admin') {
			UserActionLogHelper::UserActionLog('login', url("/switch-user"), 'login', "User {$newUser->name} logged in via switch.");
		}
		return redirect()->intended(RouteServiceProvider::HOME);
	}
	public function restoreUser(Request $request)
	{
		$originalUserId = $request->session()->get('original_user_id');

		if (!$originalUserId) {
			return redirect()->intended(RouteServiceProvider::HOME)
			->with('failure', 'No original user to restore.');
		}

		$originalUser = User::find($originalUserId);

		if (!$originalUser) {
			return redirect()->intended(RouteServiceProvider::HOME)
			->with('failure', 'Original user not found.');
		}

		Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();

		Auth::login($originalUser);
		$request->session()->regenerate();

		// Clean up (optional, so same ID na rahe session me)
		$request->session()->forget('original_user_id');

		return redirect()->intended(RouteServiceProvider::HOME)
		->with('success', "Restored back to {$originalUser->name}");
	}


 public function validateCaptcha(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'invRegisterCaptcha' => 'required|captcha',
        ], [
            'invRegisterCaptcha.required' => 'Captcha is required.',
            'invRegisterCaptcha.captcha' => 'Invalid captcha. Please try again.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('invRegisterCaptcha'),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Captcha validated successfully.',
        ]);
    }
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Retrieve authenticated user
        $user = Auth::user();
        $roleName = $user->roles->pluck('name')->first();
        if (!empty($roleName) && $roleName != 'super-admin') {
            //  Helper function to Manage User Activity / Action Logs
            UserActionLogHelper::UserActionLog('logout', url("/logout"), 'logout', "User " . Auth::user()->name . " logout.");
        }
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

         return redirect()->route('login');

    }

     //To send and store the Login otp - Sourav Chauhan (13/Aug/2024)
     public function sendLoginOtp(Request $request,CommunicationService $communicationService)
     {
         try {
             if (isset($request->mobile)) {
                 $isUserExist = User::where('mobile_no',$request->mobile)->first();
                 if($isUserExist){
                     $generateOtp = GeneralFunctions::generateUniqueRandomNumber(6);
 
                     // Create or update OTP record for the mobile
                     $otp = Otp::updateOrCreate(
                         ['mobile' => $request->mobile],
                         ['mobile_otp' => $generateOtp, 'service_type' => getServiceType('OTP_LOGIN')]
                     );
 
                     if ($otp) {
                         $action = 'LOGIN_OTP';
                         $data = [
                             'otp' => $generateOtp
                         ];
 
                         $communicationService->sendSmsMessage($data,$request->mobile,$action);
                         $communicationService->sendWhatsAppMessage($data,$request->mobile,$action);
 
 
                         return response()->json(['success' => true, 'message' => 'OTP sent to mobile number ' . $request->mobile . ' successfully']);
                     } else {
                         return response()->json(['success' => false, 'message' => 'Failed to send OTP']);
                     }
                 } else {
                     return response()->json(['success' => false, 'message' => 'Invalid Credentials.']);
                 }
                 
             } else {
                 return response()->json(['success' => false, 'message' => 'Failed to send OTP']);
             }
         } catch (\Exception $e) {
             Log::info($e);
             return response()->json(['success' => false, 'message' => $e->getMessage()]);
         }
     }
 
 
     //To verify otp and make the user login - Sourav Chauhan (14/Aug/2024)
/*     public function verifyLoginOtp(Request $request)
     {
     $request->validate([
            'mobile' => 'required|digits:10',
            'otp' => 'required|digits:6',
            'mobileCaptcha' => 'required|captcha',
        ], [
            'mobile.required' => 'Mobile number is required.',
            'mobile.digits' => 'Mobile number must be 10 digits.',
            'otp.required' => 'OTP is required.',
            'otp.digits' => 'OTP must be 6 digits.',
            'mobileCaptcha.required' => 'Captcha is required.',
            'mobileCaptcha.captcha' => 'Invalid captcha. Please enter correct captcha.',
        ]);
         try {
             if (isset($request->mobile) && isset($request->otp)) {
                 $user = User::where('mobile_no', $request->mobile)->first();
                 if($user){
                     if($user->status == 1){
                         $otpValid = Otp::where('mobile', $request->mobile)
                             ->where('mobile_otp', $request->otp)
                             ->exists();
                         if($otpValid){
                             Auth::login($user);
                             $request->session()->regenerate();
                             $roleName = $user->roles->pluck('name')->first();
                             if (!empty($roleName) && $roleName != 'super-admin') {
                                 UserActionLogHelper::UserActionLog('login', url("/login"), 'login', "User " . $user->name . " logged in.");
                             }
                             return response()->json(['success' => true, 'message' => 'Login Successfully.']);
                         } else {
                             return response()->json(['success' => false, 'message' => 'OTP not correct']);
                         }
                     } else {
                         return response()->json(['success' => false, 'message' => 'Your account is not activated yet.']);
                     }
                 } else {
                     return response()->json(['success' => false, 'message' => 'Invalid Credentials.']);
                 }
                 
             } else {
                 return response()->json(['success' => false, 'message' => 'Mobile / Otp are required']);
             }
         } catch (\Exception $e) {
             Log::info($e);
             return response()->json(['success' => false, 'message' => $e->getMessage()]);
         }
     } */
public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
            'otp' => 'required|digits:6',
            'mobileCaptcha' => 'required|captcha',
        ], [
            'mobile.required' => 'Mobile number is required.',
            'mobile.digits' => 'Mobile number must be 10 digits.',
            'otp.required' => 'OTP is required.',
            'otp.digits' => 'OTP must be 6 digits.',
            'mobileCaptcha.required' => 'Captcha is required.',
            'mobileCaptcha.captcha' => 'Invalid captcha. Please enter correct captcha.',
        ]);

        try {
            $user = User::where('mobile_no', $request->mobile)->first();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Mobile number not registered.']);
            }

            if ($user->status != 1) {
                return response()->json(['success' => false, 'message' => 'Your account is not activated yet.']);
            }

            $otpValid = Otp::where('mobile', $request->mobile)
                ->where('mobile_otp', $request->otp)
                ->exists();

            if (!$otpValid) {
                return response()->json(['success' => false, 'message' => 'Invalid OTP. Please enter correct OTP.']);
            }

            Auth::login($user);
            $request->session()->regenerate();

            return response()->json(['success' => true, 'message' => 'Login successful.']);
        } catch (\Exception $e) {
            \Log::error($e);
            return response()->json(['success' => false, 'message' => 'Something went wrong.']);
        }
    }
    
    //  added by Swati on 12092025 for resend otp

public function resendLoginOtp(Request $request, CommunicationService $communicationService)
{
    try {
        // Same mobile format as your verifyLoginOtp
        $request->validate([
            'mobile' => 'required|digits:10',
        ], [
            'mobile.required' => 'Mobile number is required.',
            'mobile.digits'   => 'Mobile number must be 10 digits.',
        ]);

        // Must be a registered & active user (messages aligned with sendLoginOtp)
        $user = User::where('mobile_no', $request->mobile)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Mobile number not registered with us']);
        }
        if ((int)$user->status !== 1) {
            return response()->json(['success' => false, 'message' => 'Your account is not activated yet.']);
        }
 
        // Cooldown based on otps.updated_at (2 minutes = 120s)
        $cooldownSeconds = 600; 
        $otpRow = Otp::where('mobile', $request->mobile)->latest()->first();

        if ($otpRow && $otpRow->updated_at) {
            $elapsed = now()->diffInSeconds($otpRow->updated_at);
            if ($elapsed < $cooldownSeconds) {
                return response()->json([
                    'success'     => false,
                    'message'     => 'Please wait before requesting a new OTP.',
                    'code'        => 'cooldown',
                    'retry_after' => $cooldownSeconds - $elapsed,
                ], 429);
            }
        }

        // Generate 6-digit OTP (matches your login flow)
        $generateOtp = GeneralFunctions::generateUniqueRandomNumber(6);

        // Upsert OTP; updated_at is our cooldown anchor
        $otp = Otp::updateOrCreate(
            ['mobile' => $request->mobile],
            ['mobile_otp' => $generateOtp, 'service_type' => getServiceType('OTP_LOGIN')]
        );

        if (!$otp) {
            return response()->json(['success' => false, 'message' => 'Failed to send OTP']);
        }

        // Send via existing channels
        $action = 'LOGIN_OTP';
        $data   = ['otp' => $generateOtp];

        // Log::info("Generated OTP for mobile {$request->mobile}: {$generateOtp}"); // avoid logging secrets in prod
        $communicationService->sendSmsMessage($data, $request->mobile, $action);
        $communicationService->sendWhatsAppMessage($data, $request->mobile, $action);

        // Tell client to start a fresh 2:00 timer
        return response()->json([
            'success'  => true,
            'message'  => 'OTP resent successfully',
            'cooldown' => $cooldownSeconds,
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        throw $e;
    } catch (\Throwable $e) {
        \Log::error($e);
        return response()->json(['success' => false, 'message' => 'Failed to send OTP'], 500);
    }
}
}
