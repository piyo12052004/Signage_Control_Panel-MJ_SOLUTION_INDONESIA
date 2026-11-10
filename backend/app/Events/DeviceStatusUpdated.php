<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $deviceId,
        public string $deviceName,
        public string $status,
        public ?string $lastSeen,
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('devices')];
    }

    public function broadcastAs(): string
    {
        return 'device.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'device_id' => $this->deviceId,
            'name' => $this->deviceName,
            'status' => $this->status,
            'last_seen' => $this->lastSeen,
        ];
    }
}
