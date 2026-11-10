<?php

namespace App\Support\Http;

use Illuminate\Http\JsonResponse;

final class ApiResponse
{
    /**
     * Return a successful API response.
     */
    public static function success(
        mixed $data = null,
        ?string $message = null,
        ?array $meta = null,
        int $status = 200,
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ], $status);
    }

    /**
     * Return a created resource response.
     */
    public static function created(
        mixed $data = null,
        ?string $message = null,
        ?array $meta = null,
    ): JsonResponse {
        return self::success(
            data: $data,
            meta: $meta,
            message: $message,
            status: 201,
        );
    }

    /**
     * Return a successful response with no content.
     */
    public static function noContent(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => null,
            'meta' => null,
        ], 204);
    }

    /**
     * Return a client/server error API response.
     */
    public static function error(
        string $code,
        string $message,
        mixed $details = null,
        int $status = 400,
        ?string $requestId = null,
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => $details,
            ],
            'meta' => [
                'request_id' => $requestId,
            ],
        ], $status);
    }

    /**
     * 400 Bad Request.
     */
    public static function badRequest(
        string $code,
        string $message,
        mixed $details = null,
        ?string $requestId = null,
    ): JsonResponse {
        return self::error(
            code: $code,
            message: $message,
            details: $details,
            status: 400,
            requestId: $requestId,
        );
    }

    /**
     * 401 Unauthorized.
     */
    public static function unauthorized(
        string $code = 'UNAUTHENTICATED',
        string $message = 'Authentication is required.',
        mixed $details = null,
        ?string $requestId = null,
    ): JsonResponse {
        return self::error(
            code: $code,
            message: $message,
            details: $details,
            status: 401,
            requestId: $requestId,
        );
    }

    /**
     * 403 Forbidden.
     */
    public static function forbidden(
        string $code = 'FORBIDDEN',
        string $message = 'You do not have permission to perform this action.',
        mixed $details = null,
        ?string $requestId = null,
    ): JsonResponse {
        return self::error(
            code: $code,
            message: $message,
            details: $details,
            status: 403,
            requestId: $requestId,
        );
    }

    /**
     * 404 Not Found.
     */
    public static function notFound(
        string $code = 'NOT_FOUND',
        string $message = 'The requested resource was not found.',
        mixed $details = null,
        ?string $requestId = null,
    ): JsonResponse {
        return self::error(
            code: $code,
            message: $message,
            details: $details,
            status: 404,
            requestId: $requestId,
        );
    }

    /**
     * 409 Conflict.
     */
    public static function conflict(
        string $code,
        string $message,
        mixed $details = null,
        ?string $requestId = null,
    ): JsonResponse {
        return self::error(
            code: $code,
            message: $message,
            details: $details,
            status: 409,
            requestId: $requestId,
        );
    }

    /**
     * 422 Unprocessable Entity.
     */
    public static function validationError(
        mixed $details = null,
        string $message = 'The given data was invalid.',
        ?string $requestId = null,
    ): JsonResponse {
        return self::error(
            code: 'VALIDATION_ERROR',
            message: $message,
            details: $details,
            status: 422,
            requestId: $requestId,
        );
    }

    /**
     * 429 Too Many Requests.
     */
    public static function tooManyRequests(
        string $message = 'Too many requests.',
        mixed $details = null,
        ?string $requestId = null,
    ): JsonResponse {
        return self::error(
            code: 'RATE_LIMIT_EXCEEDED',
            message: $message,
            details: $details,
            status: 429,
            requestId: $requestId,
        );
    }

    /**
     * 500 Internal Server Error.
     */
    public static function serverError(
        string $code = 'INTERNAL_SERVER_ERROR',
        string $message = 'An unexpected error occurred.',
        mixed $details = null,
        ?string $requestId = null,
    ): JsonResponse {
        return self::error(
            code: $code,
            message: $message,
            details: $details,
            status: 500,
            requestId: $requestId,
        );
    }

    /**
     * 503 Service Unavailable.
     */
    public static function serviceUnavailable(
        string $code = 'SERVICE_UNAVAILABLE',
        string $message = 'The service is temporarily unavailable.',
        mixed $details = null,
        ?string $requestId = null,
    ): JsonResponse {
        return self::error(
            code: $code,
            message: $message,
            details: $details,
            status: 503,
            requestId: $requestId,
        );
    }
}
