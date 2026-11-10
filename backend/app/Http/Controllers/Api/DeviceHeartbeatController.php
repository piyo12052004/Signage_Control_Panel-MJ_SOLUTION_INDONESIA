<?php

namespace App\Http\Controllers\Api;

use App\Events\DeviceStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

class DeviceHeartbeatController extends Controller
{
    public function __invoke(string $deviceId): JsonResponse
    {
        $device = Device::findOrFail($deviceId);

        $wasOffline = $device->status !== 'online';

        $device->update([
            'status' => 'online',
            'last_seen' => now(),
        ]);

        // Broadcast hanya ketika status berubah menjadi online.
        if ($wasOffline) {
            event(new DeviceStatusUpdated(
                deviceId: $device->id,
                deviceName: $device->name,
                status: 'online',
                lastSeen: $device->last_seen?->toISOString(),
            ));
        }

        return ApiResponse::success(
            data: [
                'device_id' => $device->id,
                'status' => $device->status,
                'last_seen' => $device->last_seen,
            ],
            message: 'Heartbeat berhasil diterima.'
        );
    }
}
