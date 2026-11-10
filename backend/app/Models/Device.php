<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Device extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'location',
        'status',
        'last_seen',
    ];

    protected function casts(): array
    {
        return [
            'last_seen' => 'datetime',
        ];
    }

    // relasi
    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(
            Content::class,
            'device_contents',
            'device_id',
            'content_id'
        )
            ->using(DeviceContent::class)
            ->withPivot('id')
            ->withTimestamps();
    }
}
