<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\UserResource;
use App\Modules\Service\Auth\AuthService;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->validated()
        );

        if (!$result) {
            return ApiResponse::unauthorized(
                code: 'INVALID_CREDENTIALS',
                message: 'Email atau password salah.'
            );
        }

        return ApiResponse::success(
            data: [
                ...$result,
            ],
            message: 'Login berhasil.'
        );
    }

    public function me(): JsonResponse
    {
        return ApiResponse::success(
            data: UserResource::make(
                $this->authService->me()
            )->resolve(),
            message: 'Data user berhasil diambil.'
        );
    }

    public function refresh(): JsonResponse
    {
        try {
            $result = $this->authService->refresh();

            return ApiResponse::success(
                data: $result,
                message: 'Token berhasil diperbarui.'
            );
        } catch (\Throwable $e) {

            report($e);

            return ApiResponse::unauthorized(
                code: 'TOKEN_REFRESH_FAILED',
                message: 'Token tidak valid, sudah digunakan, atau masa refresh telah berakhir.'
            );
        }
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return ApiResponse::success(
            message: 'Logout berhasil.'
        );
    }
}
