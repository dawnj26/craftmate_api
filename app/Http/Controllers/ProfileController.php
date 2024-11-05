<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Helpers\ResponseHelper\ResponseHelper;
use App\Http\Resources\UserResource;
use App\Jobs\SendMessage;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function getUser(Request $request, User $user)
    {
        return ResponseHelper::jsonWithData(200, 'User data retrieved successfully', new UserResource($user));
    }

    public function getUserProfile(Request $request)
    {
        return ResponseHelper::jsonWithData(200, 'Profile retrieved successfully', new UserResource($request->user()));
    }

    public function createProfile(Request $request)
    {
        $validated = $request->validate([
            'bio' => 'nullable|string|max:255',
            'image' => 'nullable|string'
        ]);

        $profile = $request->user()->profile()->create($validated);
        return ResponseHelper::jsonWithData(201, 'Profile created successfully', new UserResource($request->user()));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'bio' => 'nullable|string|max:255',
            'image' => 'nullable|string'
        ]);

        $profile = $request->user()->profile;
        if (!$profile) {
            return ResponseHelper::json(404, 'Profile not found');
        }

        $profile->update($validated);
        return ResponseHelper::jsonWithData(200, 'Profile updated successfully', new UserResource($request->user()));
    }

    public function deleteProfile(Request $request)
    {
        $profile = $request->user()->profile;
        if (!$profile) {
            return ResponseHelper::json(404, 'Profile not found');
        }

        $profile->delete();
        return ResponseHelper::json(200, 'Profile deleted successfully');
    }
}
