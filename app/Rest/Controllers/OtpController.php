<?php
namespace App\Rest\Controllers;
use Illuminate\Http\Request;

use App\Rest\Controller as RestController;
use App\Mail\OtpMail;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;


use Illuminate\Support\Facades\Cache;


class OtpController extends RestController
{
    public function send(Request $request)
    {
        // Validate incoming request
        $request->validate(['email' => 'required|email']);
        Log::info('OTP send requested for email: '.$request->email);

        // Generate OTP
        $otp = rand(100000, 999999);
        Log::info('Generated OTP '.$otp.' for email '.$request->email);

        // Cache OTP for 10 minutes
        $cacheKey = 'otp_'.$request->email;
        Cache::put($cacheKey, $otp, now()->addMinutes(10));
        Log::info('Cached OTP under key '.$cacheKey.' for 10 minutes');

        // Send OTP email
        try {
            Mail::to($request->email)->send(new OtpMail($otp));
            Log::info('OTP email sent to '.$request->email);
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email to '.$request->email.' - '.$e->getMessage());
            return response()->json(['message' => 'Failed to send OTP email'], 500);
        }

        return response()->json(['message' => 'OTP sent']);
    }

    public function verify(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
        ]);
        Log::info('OTP verify requested for email: '.$request->email.' with OTP: '.$request->otp);

        // Retrieve expected OTP from cache
        $cacheKey  = 'otp_'.$request->email;
        $expectedOtp = Cache::get($cacheKey);
        Log::info('Expected OTP from cache ('.$cacheKey.'): '.var_export($expectedOtp, true));

        if ($expectedOtp && $expectedOtp == $request->otp) {
            // Clear it so it can’t be reused
            Cache::forget($cacheKey);
            Log::info('OTP verified successfully for '.$request->email);
            return response()->json(['message' => 'Verified']);
        }

        Log::warning('OTP verification failed for '.$request->email.' — submitted: '.$request->otp.' expected: '.var_export($expectedOtp, true));
        return response()->json(['message' => 'Invalid OTP'], 422);
    }
}


