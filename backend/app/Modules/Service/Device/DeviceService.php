<?php

namespace App\Modules\Service\Device;

use App\Models\Device;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DeviceService
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = Device::query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('location', 'ilike', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate(
            perPage: (int) ($filters['per_page'] ?? 10),
            page: (int) ($filters['page'] ?? 1)
        );
    }

    public function getById(string $id): ?Device
    {
        return Device::query()->find($id);
    }

    public function create(array $data): Device
    {
        return Device::create($data);
    }

    public function update(Device $device, array $data): Device
    {
        $device->update($data);

        return $device->refresh();
    }

    public function delete(Device $device): void
    {
        $device->delete();
    }
}
