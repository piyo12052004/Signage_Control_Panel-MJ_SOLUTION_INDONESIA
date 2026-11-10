<?php

namespace App\Modules\Service\Dashboard;

use App\Models\Content;
use App\Models\Device;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get dashboard summary.
     */
    public function getSummary(): array
    {
        return [
            'total_devices' => Device::count(),

            'online_devices' => Device::where('status', 'online')->count(),

            'offline_devices' => Device::where('status', 'offline')->count(),

            'total_contents' => Content::count(),

            'recent_devices' => Device::query()
                ->withCount('contents')
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Get device with assigned and available contents.
     */
    public function getDeviceContents(string $deviceId): array
    {
        $device = Device::with('contents')->findOrFail($deviceId);

        $availableContents = Content::query()
            ->whereDoesntHave('devices', function ($query) use ($deviceId) {
                $query->where('devices.id', $deviceId);
            })
            ->latest()
            ->get();

        return [
            'device' => $device,
            'assigned_contents' => $device->contents,
            'available_contents' => $availableContents,
        ];
    }

    /**
     * Attach content to device.
     */
    public function attachContent(string $deviceId, string $contentId): void
    {
        $device = Device::findOrFail($deviceId);

        // syncWithoutDetaching mencegah relasi yang sudah ada ditambahkan ulang.
        $device->contents()->syncWithoutDetaching([$contentId]);
    }

    /**
     * Detach content from device.
     */
    public function detachContent(string $deviceId, string $contentId): void
    {
        $device = Device::findOrFail($deviceId);

        $device->contents()->detach($contentId);
    }
}
