<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ResetPasswordOTPSent;
use Ichtrojan\Otp\Otp;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class OTPController extends Controller
{
    public function sendOtp(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'metadata' => [
                    'status' => 422,
                    'message' => 'Missing required fields',
                ],
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'metadata' => [
                    'status' => 404,
                    'message' => 'User not found'
                ],
            ], 404);
        }

        $otp = (new Otp)->generate($user->email, 'numeric', 6, 5);

        Mail::to($user->email)->send(new ResetPasswordOTPSent($otp->token, $user->name));
        return response()->json([
            'metadata' => [
                'status' => 200,
                'message' => 'Email sent',
            ],
        ], 200);
    }

    public function verifyOtp(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'otp' => 'required|numeric'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'metadata' => [
                    'status' => 422,
                    'message' => 'Missing required fields',
                ],
            ], 422);
        }

        $otp = (new Otp)->validate($request->email, $request->otp);
        $user = User::where('email', $request->email)->first();

        if (!$otp->status) {
            return response()->json([
                'metadata' => [
                    'status' => 400,
                    'message' => $otp->message,
                ],
            ], 400);
        }

        $token = $user->createToken('password_reset', ['password-reset'], now()->addHour())->plainTextToken;

        return response()->json([
            'metadata' => [
                'status' => 200,
                'message' => 'OTP verified',
            ],
            'data' => [
                'reset_token' => $token,
            ]
        ], 200);
    }

}
