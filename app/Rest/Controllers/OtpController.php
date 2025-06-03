<?php
namespace App\Rest\Controllers;
use Illuminate\Http\Request;

use App\Rest\Controller as RestController;
use App\Mail\OtpMail;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\Cache;

use App\Models\Client; // or User, depending on your app
use Laravel\Passport\HasApiTokens;

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


public function verify_new(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp'   => 'required|digits:6',
    ]);

    Log::info('OTP verify requested for email: '.$request->email.' with OTP: '.$request->otp);

    $cacheKey = 'otp_' . $request->email;
    $expectedOtp = Cache::get($cacheKey);

    Log::info('Expected OTP from cache ('.$cacheKey.'): '.var_export($expectedOtp, true));

    if ($expectedOtp && $expectedOtp == $request->otp) {
        Cache::forget($cacheKey);

        // ✅ Retrieve or create client
        $client = Client::where('email', $request->email)->first();

        if (!$client) {
            // You can adjust this based on your app's logic
            $client = Client::create([
                'email' => $request->email,
                'password' => bcrypt(str::random(10)), // placeholder if needed
            ]);
        }

        // ✅ Authenticate the client
        Auth::login($client); // optional: ensures the auth()->user() is set

        // ✅ Generate Passport token
        $token = $client->createToken('OTP Login')->accessToken;

        Log::info('OTP verified and token issued for '.$request->email);

        return response()->json([
            'message' => 'Verified and authenticated',
            'token' => $token,
            'client' => $client,
        ]);
    }

    Log::warning('OTP verification failed for '.$request->email.' — submitted: '.$request->otp.' expected: '.var_export($expectedOtp, true));
    return response()->json(['message' => 'Invalid OTP'], 422);
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


