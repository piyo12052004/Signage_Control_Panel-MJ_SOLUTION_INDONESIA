<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'device' => [
                'id' => $this->id,
                'name' => $this->name,
                'location' => $this->location,
                'status' => $this->status,
            ],

            'contents' => DashboardContentResource::collection(
                $this->whenLoaded('contents')
            ),
        ];
    }
}
