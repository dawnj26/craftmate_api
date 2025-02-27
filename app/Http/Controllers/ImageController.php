<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper\ResponseHelper;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use App\Models\Project;

class ImageController extends Controller
{
    public function uploadMessageImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'metadata' => [
                    'status' => 400,
                    'message' => 'Missing or invalid image file'
                ]
            ], 400);
        }

        $image = $request->file('image')->store('images');
        if (!$image) {
            return response()->json([
                'metadata' => [
                    'status' => 500,
                    'message' => 'Failed to upload image'
                ]
            ], 500);
        }

        $imageUrl = URL::to('/') . '/' . $image;

        return ResponseHelper::jsonWithData(200, 'Picture uploaded', $imageUrl);
    }
    public function uploadProfilePicture(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'metadata' => [
                    'status' => 400,
                    'message' => 'Missing or invalid image file'
                ]
            ], 400);
        }

        $image = $request->file('image')->store('images');
        if (!$image) {
            return response()->json([
                'metadata' => [
                    'status' => 500,
                    'message' => 'Failed to upload image'
                ]
            ], 500);
        }

        $user = auth()->user();
        $imageUrl = URL::to('/') . '/' . $image;
        $user->profile->image = $imageUrl;
        $user->profile->save();

        return ResponseHelper::jsonWithData(200, 'Profile picture uploaded successfully', new UserResource($user));
    }

    public function uploadProjectImage(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif'
        ]);

        $user = auth()->user();

        if (!$user || $user->id !== $project->user_id) {
            return response()->json([
                'metadata' => [
                    'status' => 403,
                    'message' => 'Unauthorized'
                ]
            ], 403);
        }

        if ($validator->fails()) {
            return response()->json([
                'metadata' => [
                    'status' => 400,
                    'message' => 'Missing or invalid image file'
                ]
            ], 400);
        }

        $image = $request->file('image')->store('images');
        if (!$image) {
            return response()->json([
                'metadata' => [
                    'status' => 500,
                    'message' => 'Failed to upload image'
                ]
            ], 500);
        }

        $imageUrl = URL::to('/') . '/' . $image;
        $project->image_path = $imageUrl;
        $project->save();

        return response()->json([
            'metadata' => [
                'status' => 201,
                'message' => 'Image uploaded successfully'
            ],
            'data' => [
                'image_url' => $imageUrl
            ]
        ], 201);
    }

    public function uploadDocumentImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'metadata' => [
                    'status' => 400,
                    'message' => 'Missing or invalid image file'
                ]
            ], 400);
        }


        $image = $request->file('image')->store('images');
        if (!$image) {
            return response()->json([
                'metadata' => [
                    'status' => 500,
                    'message' => 'Failed to upload image'
                ]
            ], 500);
        }

        $imageUrl = URL::to('/') . '/' . $image;

        return response()->json([
            'metadata' => [
                'status' => 201,
                'message' => 'Image uploaded successfully'
            ],
            'data' => [
                'image_url' => $imageUrl
            ]
        ], 201);
    }

    public function uploadMultipleImages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'metadata' => [
                    'status' => 400,
                    'message' => $validator->errors()->first()
                ]
            ], 400);
        }

        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('images');
            $uploadedImages[] = URL::to('/') . '/' . $path;
        }

        return response()->json([
            'metadata' => [
                'status' => 200,
                'message' => 'Images uploaded successfully'
            ],
            'data' => [
                'images' => $uploadedImages
            ]
        ], 200);
    }
}
