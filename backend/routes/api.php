<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DeviceClientController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\DeviceHeartbeatController;

Route::prefix('v1')->group(function () {
    Route::get('/device-client/{deviceId}', [DeviceClientController::class, 'show'])->whereUuid('deviceId');
    Route::post('/device-client/{deviceId}/heartbeat', DeviceHeartbeatController::class);
    Route::prefix('auth')->group(function () {

        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:5,1');

        Route::post('/refresh', [AuthController::class, 'refresh']);

        Route::middleware('auth:api')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);

            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    Route::middleware('auth:api')->group(function () {
        Route::prefix('setting')->group(function () {
            Route::apiResource('devices', DeviceController::class);
            Route::apiResource('contents', ContentController::class);
        });

        Route::prefix('dashboard')->group(function () {
            Route::get('/', [DashboardController::class, 'index']);
            Route::get('/devices/{deviceId}/contents', [DashboardController::class, 'deviceContents']);
            Route::post('/devices/{deviceId}/contents', [DashboardController::class, 'attachContent']);
            Route::delete('/devices/{deviceId}/contents/{contentId}', [DashboardController::class, 'detachContent']);
        });
    });
});
