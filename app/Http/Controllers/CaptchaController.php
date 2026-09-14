<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CaptchaController extends Controller
{
    public function refreshCaptcha()
    {
 return response()->json([
        'captcha' => route('captcha', ['config' => 'default']) . '?' . now()->timestamp
    ]);
 //    return  response()->json(['captcha' => captcha_src()]);
     
    }
        public function default()
    {
       return response()->json(['captcha' => captcha_src()]);
    }


    public function validateCaptcha(Request $request)
    {
        $this->validate($request, [
            'captcha' => 'required|captcha',
        ], [
            'captcha.required' => 'Captcha is required.',
            'captcha.captcha' => 'Captcha is invalid. Please try again.',
        ]);

        return response()->json(['success' => true, 'message' => 'Captcha validated successfully.']);
    }
}
