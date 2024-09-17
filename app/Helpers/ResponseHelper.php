<?php

namespace App\Helpers\ResponseHelper;

use function Pest\Laravel\json;

class ResponseHelper
{
    static public function errInput()
    {
        return ResponseHelper::json(422, 'Missing required fields or validation error');
    }

    static public function updated()
    {
        return ResponseHelper::json(200, 'Updated successfully');
    }

    static public function json(int $statusCode, string $message)
    {
        return response()->json(
            [
                'metadata' => [
                    'status' => $statusCode,
                    'message' => $message,
                ]
            ],
            $statusCode
        );
    }

    static public function jsonWithData(int $statusCode, string $message,  $data)
    {
        return response()->json(
            [
                'metadata' => [
                    'status' => $statusCode,
                    'message' => $message,
                ],
                'data' => $data,
            ],
            $statusCode
        );
    }
}
