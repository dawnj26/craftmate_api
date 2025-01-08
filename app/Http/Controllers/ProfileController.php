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


    public function getUser(Request $request, int $user)
    {
        $user = User::withTrashed()->find($user);

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

    public function toggleFollowUser(User $user)
    {
        $currentUser = auth()->user();

        if ($user->trashed() || $currentUser->trashed()) {
            return ResponseHelper::json(410, 'Unable to follow - User has been banned');
        }

        // Prevent self-following
        if ($currentUser->id === $user->id) {
            return ResponseHelper::json(400, 'You cannot follow yourself');
        }

        $message = '';
        if ($user->isFollowedByUser($currentUser)) {
            // Unfollow
            $user->followers()->detach($currentUser->id);
            $message = 'User unfollowed successfully';
        } else {
            // Follow
            $user->followers()->attach($currentUser->id);
            $message = 'User followed successfully';
        }
        return ResponseHelper::jsonWithData(200, $message, new UserResource($user));
    }
}
