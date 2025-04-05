<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ResponseHelper
{
    public static function success(string $msg, mixed $data): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'msg' => $msg,
        ], Response::HTTP_OK);
    }
    public static function error(string $msg, array $data, int $status_code): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'msg' => $msg,
        ], $status_code);
    }

    public static function returnValidationError($validator)
    {
        return ResponseHelper::error(__('validation.errorValidation'), [$validator->errors()->first()], '422');
    }
}
