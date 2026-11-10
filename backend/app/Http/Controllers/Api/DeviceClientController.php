<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\DeviceClientResource;
use App\Models\Device;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

class DeviceClientController extends Controller
{
    public function show(string $deviceId): JsonResponse
    {
        $device = Device::with([
            'contents' => function ($query) {
                $query->orderBy('device_contents.created_at');
            },
        ])->findOrFail($deviceId);

        $resource = (new DeviceClientResource($device))->resolve();

        return ApiResponse::success(
            data: $resource,
            message: 'Data device client berhasil diambil.'
        );
    }
}