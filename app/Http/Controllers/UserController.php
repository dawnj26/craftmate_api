<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper\ResponseHelper;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class UserController extends Controller
{
    public function unbanUser(Request $request, int $user)
    {
        $user = User::withTrashed()->find($user);
        $user->restore();

        return ResponseHelper::json(200, 'User activated successfully');
    }

    public function banUser(Request $request, User $user)
    {
        $user->delete();

        return ResponseHelper::json(200, 'User banned successfully');
    }

    public function shareUser(Request $request, User $user)
    {
        return ResponseHelper::jsonWithData(200, 'User data retrieved successfully',  [
            'share_link' => URL::to('/user/' . $user->id),
        ]);
    }

    public function shareCurrentUser(Request $request)
    {
        $user = auth()->user();

        return ResponseHelper::jsonWithData(200, 'User data retrieved successfully', [
            'share_link' => URL::to('/user/' . $user->id),
        ]);
    }

    public function getUsers()
    {
        $users = User::all();

        return ResponseHelper::jsonWithData(200, 'Users retrieved successfully', UserResource::collection($users));
    }

    public function getUserById(User $user)
    {
        if ($user->trashed()) {
            return ResponseHelper::json(410, 'User has been banned');
        }

        return ResponseHelper::jsonWithData(200, 'User data retrieved successfully', new UserResource($user));
    }

    public function updateBio(Request $request)
    {
        $request->validate([
            'bio' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        $profile = $user->profile;

        $profile->bio = $request->bio;
        $profile->save();

        return ResponseHelper::jsonWithData(200, 'Bio updated successfully', new UserResource($user));
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $user = auth()->user();

        $user->email = $request->email;
        $user->save();

        return ResponseHelper::jsonWithData(200, 'Email updated successfully', new UserResource($user));
    }

    public function updateName(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = auth()->user();

        $user->name = $request->name;
        $user->save();

        return ResponseHelper::jsonWithData(200, 'Name updated successfully', new UserResource($user));
    }

    public function user(Request $request)
    {
        $id = $request->id;

        if ($id === null) {
            $user = auth()->user();
        } else {
            $user = User::withTrashed()->find($id);
        }

        if ($user === null) {
            return ResponseHelper::json(404, 'User not found');
        }

        if ($user->trashed()) {
            return ResponseHelper::json(410, 'User has been banned');
        }

        return ResponseHelper::jsonWithData(200, 'User data retrieved successfully', new UserResource($user));
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

    public function getFollowers(Request $request, $timeframe = 'all')
    {
        $user = auth()->user();
        $query = $user->followers();

        switch ($timeframe) {
            case 'weekly':
                $query->where('followers.created_at', '>=', now()->subWeek());
                break;
            case 'monthly':
                $query->where('followers.created_at', '>=', now()->subMonth());
                break;
            case 'yearly':
                $query->where('followers.created_at', '>=', now()->subYear());
                break;
            case 'all':
            default:
                break;
        }

        $count = $query->count();

        return ResponseHelper::jsonWithData(200, 'Followers count retrieved successfully', ['count' => $count]);
    }
}
