<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\VerifyOTPRequest;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['auth', 'verifyOTP']]);
    }

    public function auth(AuthRequest $request)
    {
        $user = User::firstOrCreate(['phone' => $request->phone]);
        $otp = $this->generateOtp();
        $user->otp = $otp;
        $user->otp_verified = false;
        $user->save();
        $this->sendOTP($user->phone);
        return response()->json([
            'message' => 'please verify OTP'
        ]);
    }

    private function generateOtp()
    {
        return rand(111111, 99999);
    }

    private function sendOTP($phone)
    {
        //SEND OTP SMS
    }

    public function generateToken()
    {

    }

    public function verifyOTP(VerifyOTPRequest $request)
    {
        $user = User::where('phone', $request->phone)->first();

        if ($request->code == $user->otp) {
            $user->otp = null;
            $user->otp_verified=true;
            $user->save();
            return response()->json([
                'token' => auth()->fromUser($user),
            ]);
        }

        return response()->json(['message' => 'OTP is invalid.']);
    }

}
