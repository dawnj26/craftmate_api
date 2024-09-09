<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    public function upload(Request $request)
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

        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();

        $img = Image::read($image->getRealPath());

        $img->resize(800, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $img->save(public_path('images/') . $imageName, 60);

        $imageUrl = URL::to('/') . '/images/' . $imageName;

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
}
