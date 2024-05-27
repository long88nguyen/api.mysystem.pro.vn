<?php
namespace App\Services\_Response;

use App\Services\_Response\ApiResponse;
use Illuminate\Http\JsonResponse;

abstract class ApiResponseProvider implements ApiResponse
{
    public function sendErrorResponse($message, $code = 400, $status=false, $errors = null): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors,
            'status' => $status
        ], $code);
    }

    public function sendSuccessResponse($data, $code = 200, $status=true, $message = ''): JsonResponse
    {   
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
    }
}
