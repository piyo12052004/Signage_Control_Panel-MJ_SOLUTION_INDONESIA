<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Content extends Model
{
    use HasUuids;

    protected $table = 'contents';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'type',
        'payload',
        'url',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(
            Device::class,
            'device_contents',
            'content_id',
            'device_id'
        )
            ->using(DeviceContent::class)
            ->withPivot('id')
            ->withTimestamps();
    }
}
