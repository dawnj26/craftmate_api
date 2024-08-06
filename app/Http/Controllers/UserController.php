<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function user(Request $request)
    {
        $id = $request->id;

        if ($id === null) {
            $user = auth()->user();
        } else {
            $user = User::find($id);
        }

        if ($user === null) {
            return response()->json([
                'metadata' => [
                    'status' => 404,
                    'message' => 'User not found'
                ],
            ], 404);
        }

        return response()->json([
            'metadata' => [
                'status' => 200,
                'message' => 'User found'
            ],
            'data' => $user
        ], 200);
    }

    public function verifyUser(Request $request)
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

        return response()->json([
            'metadata' => [
                'status' => 200,
                'message' => 'User found',
            ],
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        // Get the bearer token from the request
        $tokenString = $request->bearerToken();

        // If no token is provided, return a 401 Unauthorized response
        if ($tokenString === null) {
            return response()->json([
                'metadata' => [
                    'status' => 401,
                    'message' => 'Unauthorized'
                ],
            ], 401);
        }

        $validate = Validator::make($request->all(), [
            'password' => 'required|min:6',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'metadata' => [
                    'status' => 422,
                    'message' => 'Missing required fields or validation error',
                ],
            ], 422);
        }

        $tokenModel = PersonalAccessToken::findToken($tokenString);
        $user = auth()->user();

        if ($tokenModel->cant('password-reset')) {
            return response()->json([
                'metadata' => [
                    'status' => 403,
                    'message' => 'Token does not have the required abilities',
                ]
            ], 403);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $tokenModel->delete();

        return response()->json([
            'metadata' => [
                'status' => 200,
                'message' => 'Password changed successfully',
            ]
        ], 200);
    }
}
