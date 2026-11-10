<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DashboardContentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $contentUrl = $this->url;

        if (
            in_array($this->type, ['image', 'video'], true) &&
            $contentUrl
        ) {
            $contentUrl = Storage::disk('public')->url($contentUrl);
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'payload' => $this->payload,
            'url' => $contentUrl
                ? url($contentUrl)
                : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
