<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video' => 'required|file|mimes:mp4'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'metadata' => [
                    'status' => 400,
                    'message' => 'Missing or invalid video file'
                ]
            ], 400);
        }

        $video = $request->file('video')->store('videos');

        if (!$video) {
            return response()->json([
                'metadata' => [
                    'status' => 500,
                    'message' => 'Failed to upload video'
                ]
            ], 500);
        }

        $videoUrl = URL::to('/') . '/' . $video;

        return response()->json([
            'metadata' => [
                'status' => 201,
                'message' => 'Video uploaded successfully'
            ],
            'data' => [
                'video_url' => $videoUrl
            ]
        ], 201);
    }
}
