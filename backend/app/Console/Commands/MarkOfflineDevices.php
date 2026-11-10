<?php

namespace App\Console\Commands;

use App\Events\DeviceStatusUpdated;
use App\Models\Device;
use Illuminate\Console\Command;

class MarkOfflineDevices extends Command
{
    protected $signature = 'devices:mark-offline';

    protected $description = 'Mark inactive devices as offline';

    public function handle(): int
    {
        $devices = Device::query()
            ->where('status', 'online')
            ->where(function ($query) {
                $query
                    ->whereNull('last_seen')
                    ->orWhere('last_seen', '<', now()->subSeconds(10));
            })
            ->get();

        foreach ($devices as $device) {
            $device->update([
                'status' => 'offline',
            ]);

            event(new DeviceStatusUpdated(
                deviceId: $device->id,
                deviceName: $device->name,
                status: 'offline',
                lastSeen: $device->last_seen?->toISOString(),
            ));
        }

        $this->info("{$devices->count()} device diperbarui menjadi offline.");

        return self::SUCCESS;
    }
}
