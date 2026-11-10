<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardDeviceContentsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'device' => [
                'id' => $this->resource['device']->id,
                'name' => $this->resource['device']->name,
                'location' => $this->resource['device']->location,
                'status' => $this->resource['device']->status,
            ],

            'assigned_contents' => DashboardContentResource::collection(
                $this->resource['assigned_contents']
            ),

            'available_contents' => DashboardContentResource::collection(
                $this->resource['available_contents']
            ),
        ];
    }
}
