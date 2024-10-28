<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper\ResponseHelper;
use Gemini\Data\GenerationConfig;
use Gemini\Enums\ModelType;
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


    public function generateSuggestions(Request $request)
    {
        $config = new GenerationConfig(
            temperature: 1.5,
        );

        $request->validate([
            'prompt' => 'required|string'
        ]);


        $response = Gemini::generativeModel(ModelType::GEMINI_FLASH)
                    ->withGenerationConfig($config)
                    ->generateContent($request->prompt);

        return ResponseHelper::jsonWithData(201, 'Generated suggestions', $response->text());
    }

    public function generateProject(Request $request) {
        $config = new GenerationConfig(
            temperature: 1.3,
        );

        $request->validate([
            'prompt' => 'required|string'
        ]);

        $response = Gemini::generativeModel(ModelType::GEMINI_FLASH)
                    ->withGenerationConfig($config)
                    ->generateContent($request->prompt);

        return ResponseHelper::jsonWithData(201, 'Generated project', $response->text());
    }
}
