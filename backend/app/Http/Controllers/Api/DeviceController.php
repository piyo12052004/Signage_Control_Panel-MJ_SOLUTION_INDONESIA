<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Device\DevicePaginationRequest;
use App\Http\Requests\Device\StoreDeviceRequest;
use App\Http\Requests\Device\UpdateDeviceRequest;
use App\Http\Resources\Device\DeviceCollection;
use App\Http\Resources\Device\DeviceResource;
use App\Modules\Service\device\DeviceService;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

class DeviceController extends Controller
{
    public function __construct(
        protected DeviceService $deviceService
    ) {}

    /**
     * Get all devices.
     */
    public function index(DevicePaginationRequest $request): JsonResponse
    {
        $devices = $this->deviceService->getAll(
            $request->validated()
        );

        return ApiResponse::success(
            data: (new DeviceCollection($devices))->resolve(),
            message: 'Data device berhasil diambil.',
            meta: [
                'current_page' => $devices->currentPage(),
                'per_page' => $devices->perPage(),
                'total' => $devices->total(),
                'last_page' => $devices->lastPage(),
            ]
        );
    }

    /**
     * Create device.
     */
    public function store(StoreDeviceRequest $request): JsonResponse
    {
        $device = $this->deviceService->create(
            $request->validated()
        );

        return ApiResponse::created(
            data: DeviceResource::make($device)->resolve(),
            message: 'Device berhasil ditambahkan.'
        );
    }

    /**
     * Get device detail.
     */
    public function show(string $id): JsonResponse
    {
        $device = $this->deviceService->getById($id);

        if (!$device) {
            return ApiResponse::notFound(
                code: 'DEVICE_NOT_FOUND',
                message: 'Device tidak ditemukan.'
            );
        }

        return ApiResponse::success(
            data: DeviceResource::make($device)->resolve(),
            message: 'Detail device berhasil diambil.'
        );
    }

    /**
     * Update device.
     */
    public function update(UpdateDeviceRequest $request, string $id): JsonResponse
    {
        $device = $this->deviceService->getById($id);

        if (!$device) {
            return ApiResponse::notFound(
                code: 'DEVICE_NOT_FOUND',
                message: 'Device tidak ditemukan.'
            );
        }

        $device = $this->deviceService->update(
            $device,
            $request->validated()
        );

        return ApiResponse::success(
            data: DeviceResource::make($device)->resolve(),
            message: 'Device berhasil diperbarui.'
        );
    }

    /**
     * Delete device.
     */
    public function destroy(string $id): JsonResponse
    {
        $device = $this->deviceService->getById($id);

        if (!$device) {
            return ApiResponse::notFound(
                code: 'DEVICE_NOT_FOUND',
                message: 'Device tidak ditemukan.'
            );
        }

        $this->deviceService->delete($device);

        return ApiResponse::noContent();
    }
}
