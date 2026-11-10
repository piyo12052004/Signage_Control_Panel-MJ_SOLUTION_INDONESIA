<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;

use App\Support\Http\ApiResponse;

use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistedException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__.'/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // API tidak perlu redirect ke halaman login.
        $middleware->redirectGuestsTo(
            fn (Request $request) => $request->is('api/*')
                ? null
                : '/login'
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Paksa semua exception dari API dirender sebagai JSON.
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) =>
                $request->is('api/*') || $request->expectsJson()
        );

        $exceptions->render(function (
            TokenExpiredException $e,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::unauthorized(
                    code: 'TOKEN_EXPIRED',
                    message: 'Access token telah kedaluwarsa.'
                );
            }
        });

        $exceptions->render(function (
            TokenBlacklistedException $e,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::unauthorized(
                    code: 'TOKEN_REVOKED',
                    message: 'Token sudah tidak berlaku.'
                );
            }
        });

        $exceptions->render(function (
            TokenInvalidException $e,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::unauthorized(
                    code: 'TOKEN_INVALID',
                    message: 'Token tidak valid.'
                );
            }
        });

        $exceptions->render(function (
            AuthenticationException $e,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::unauthorized(
                    code: 'UNAUTHENTICATED',
                    message: 'Silakan login terlebih dahulu.'
                );
            }
        });

        $exceptions->render(function (
            JWTException $e,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::unauthorized(
                    code: 'JWT_ERROR',
                    message: 'Token autentikasi tidak dapat diproses.'
                );
            }
        });
    })
    ->create();