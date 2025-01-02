<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper\ResponseHelper;
use Gemini\Data\GenerationConfig;
use Gemini\Enums\ModelType;
use Gemini\Data\Blob;
use Gemini\Enums\MimeType;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Http\Request;

class AIController extends Controller
{
    private function generateColor()
    {
        $chars = 'ABCDEF0123456789';
        $color = 'FF';
        for ($i = 0; $i < 6; $i++) {
            $color .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $color;
    }

    private function getImageMimeType($file)
    {
        $mimeType = $file->getMimeType();
        return match ($mimeType) {
            'image/jpeg', 'image/jpg' => MimeType::IMAGE_JPEG,
            'image/png' => MimeType::IMAGE_PNG,

            default => MimeType::IMAGE_JPEG, // fallback to JPEG
        };
    }

    public function generateSuggestions(Request $request)
    {
        $config = new GenerationConfig(
            temperature: 1.5,
        );

        $request->validate([
            'prompt' => 'required|string',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|nullable'
        ]);

        if ($request->hasFile('image')) {
            $imageContents = file_get_contents($request->file('image')->path());
            $response = Gemini::generativeModel(ModelType::GEMINI_FLASH)
                ->withGenerationConfig($config)
                ->generateContent([
                    $request->prompt,
                    new Blob(
                        mimeType: MimeType::IMAGE_JPEG,
                        data: base64_encode($imageContents)
                    )
                ]);
            return ResponseHelper::jsonWithData(201, 'Generated suggestions', $response->text());
        }

        $response = Gemini::generativeModel(ModelType::GEMINI_FLASH)
            ->withGenerationConfig($config)
            ->generateContent($request->prompt);

        return ResponseHelper::jsonWithData(201, 'Generated suggestions', $response->text());
    }

    public function generateProject(Request $request)
    {
        $config = new GenerationConfig(
            temperature: 1.3,
        );

        $request->validate([
            'prompt' => 'required|string',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|nullable'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageContents = file_get_contents($image->path());
            $mimeType = $this->getImageMimeType($image);

            $response = Gemini::generativeModel(ModelType::GEMINI_FLASH)
                ->withGenerationConfig($config)
                ->generateContent([
                    $request->prompt,
                    new Blob(
                        mimeType: $mimeType,
                        data: base64_encode($imageContents)
                    )
                ]);
            return ResponseHelper::jsonWithData(201, 'Generated suggestions', $response->text());
        }

        $response = Gemini::generativeModel(ModelType::GEMINI_FLASH)
            ->withGenerationConfig($config)
            ->generateContent($request->prompt);

        return ResponseHelper::jsonWithData(201, 'Generated suggestions', $response->text());
    }
}
