<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AttachDeviceContentRequest;
use App\Http\Resources\Dashboard\DashboardDeviceCollection;
use App\Http\Resources\Dashboard\DashboardDeviceContentsResource;
use App\Http\Resources\Dashboard\DashboardDeviceResource;
use App\Modules\Service\Dashboard\DashboardService as DashboardDashboardService;
use App\Support\Http\ApiResponse;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardDashboardService $dashboardService
    ) {}

    /**
     * Get dashboard summary.
     */
    public function index()
    {
        $summary = $this->dashboardService->getSummary();

        $recentDevices = DashboardDeviceResource::collection(
            $summary['recent_devices']
        )->resolve();

        return ApiResponse::success(
            data: [
                'total_devices' => $summary['total_devices'],
                'online_devices' => $summary['online_devices'],
                'offline_devices' => $summary['offline_devices'],
                'total_contents' => $summary['total_contents'],
                'recent_devices' => $recentDevices,
            ],
            message: 'Data dashboard berhasil diambil.'
        );
    }

    /**
     * Get device contents.
     */
    public function deviceContents(string $deviceId)
    {
        $data = $this->dashboardService->getDeviceContents($deviceId);

        $resource = (new DashboardDeviceContentsResource($data))->resolve();

        return ApiResponse::success(
            data: $resource,
            message: 'Data content device berhasil diambil.'
        );
    }

    /**
     * Attach content to device.
     */
    public function attachContent(
        AttachDeviceContentRequest $request,
        string $deviceId
    ) {
        $this->dashboardService->attachContent(
            $deviceId,
            $request->validated('content_id')
        );

        return ApiResponse::success(
            message: 'Content berhasil ditambahkan ke device.'
        );
    }

    /**
     * Detach content from device.
     */
    public function detachContent(string $deviceId, string $contentId)
    {
        $this->dashboardService->detachContent($deviceId, $contentId);

        return ApiResponse::success(
            message: 'Content berhasil dilepas dari device.'
        );
    }
}
