<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Content\StoreContentRequest;
use App\Http\Resources\Dashboard\DashboardContentResource;
use App\Models\Content;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ContentController extends Controller
{
    /**
     * Menampilkan daftar content dengan pagination,
     * search, filter, dan sorting.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'in:image,video,html,url'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['nullable', 'in:title,type,created_at'],
            'sort_direction' => ['nullable', 'in:asc,desc'],
        ]);

        $query = Content::query();

        if (! empty($validated['search'])) {
            $query->where(
                'title',
                'ilike',
                '%' . $validated['search'] . '%'
            );
        }

        if (! empty($validated['type'])) {
            $query->where('type', $validated['type']);
        }

        $sortBy = $validated['sort_by'] ?? 'created_at';
        $sortDirection = $validated['sort_direction'] ?? 'desc';
        $perPage = $validated['per_page'] ?? 10;

        $contents = $query
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);

        return ApiResponse::success(
            data: [
                'items' => DashboardContentResource::collection(
                    $contents->getCollection()
                )->resolve(),
            ],
            message: 'Data content berhasil diambil.',
            meta: [
                'current_page' => $contents->currentPage(),
                'per_page' => $contents->perPage(),
                'total' => $contents->total(),
                'last_page' => $contents->lastPage(),
            ],
        );
    }

    /**
     * Menambahkan content baru.
     */
    public function store(StoreContentRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $filePath = null;

        DB::beginTransaction();

        try {
            if (in_array($validated['type'], ['image'], true)) {
                $directory = $validated['type'] === 'image'
                    ? 'contents/images'
                    : 'contents/videos';

                $filePath = $request->file('file')->store(
                    $directory,
                    'public'
                );
            }

            $content = Content::create([
                'title' => $validated['title'],
                'type' => $validated['type'],

                'payload' => $validated['type'] === 'html'
                    ? ($validated['payload'] ?? null)
                    : null,

                'url' => match ($validated['type']) {
                    'image' => $filePath,
                    'url' => $validated['url'],
                    default => null,
                },
            ]);

            DB::commit();

            return ApiResponse::created(
                data: (new DashboardContentResource($content))->resolve(),
                message: 'Content berhasil ditambahkan.'
            );
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }

            throw $e;
        }
    }

    /**
     * Menampilkan detail content.
     */
    public function show(string $id): JsonResponse
    {
        $content = Content::findOrFail($id);

        return ApiResponse::success(
            data: (new DashboardContentResource($content))->resolve(),
            message: 'Detail content berhasil diambil.'
        );
    }

    /**
     * Memperbarui content.
     */
    public function update(
        StoreContentRequest $request,
        string $id
    ): JsonResponse {
        $content = Content::findOrFail($id);

        $validated = $request->validated();

        $oldType = $content->type;
        $oldPath = $content->url;

        $newPath = null;
        $newFilePath = null;

        $isFileType = in_array(
            $validated['type'],
            ['image', 'video'],
            true
        );

        if ($isFileType) {
            if ($request->hasFile('file')) {
                $directory = $validated['type'] === 'image'
                    ? 'contents/images'
                    : 'contents/videos';

                $newFilePath = $request->file('file')->store(
                    $directory,
                    'public'
                );

                $newPath = $newFilePath;
            } else {
                // Pertahankan file lama hanya jika tipe tidak berubah.
                if (
                    $oldType === $validated['type'] &&
                    $oldPath
                ) {
                    $newPath = $oldPath;
                } else {
                    throw ValidationException::withMessages([
                        'file' => [
                            'Silakan upload file baru untuk tipe content ini.',
                        ],
                    ]);
                }
            }
        } elseif ($validated['type'] === 'url') {
            $newPath = $validated['url'];
        }

        try {
            $content->update([
                'title' => $validated['title'],
                'type' => $validated['type'],

                'payload' => $validated['type'] === 'html'
                    ? ($validated['payload'] ?? null)
                    : null,

                'url' => $newPath,
            ]);

            // Hapus file lama setelah update database berhasil.
            if (
                $oldPath &&
                in_array($oldType, ['image', 'video'], true) &&
                $oldPath !== $newPath
            ) {
                Storage::disk('public')->delete($oldPath);
            }

            $content->refresh();

            return ApiResponse::success(
                data: (new DashboardContentResource($content))->resolve(),
                message: 'Content berhasil diperbarui.'
            );
        } catch (\Throwable $e) {
            // Jika proses update gagal, hapus file baru.
            if ($newFilePath) {
                Storage::disk('public')->delete($newFilePath);
            }

            throw $e;
        }
    }

    /**
     * Menghapus content beserta file.
     */
    public function destroy(string $id): JsonResponse
    {
        $content = Content::findOrFail($id);

        $oldType = $content->type;
        $oldPath = $content->url;

        $content->delete();

        if (
            $oldPath &&
            in_array($oldType, ['image', 'video'], true)
        ) {
            Storage::disk('public')->delete($oldPath);
        }

        return ApiResponse::success(
            message: 'Content berhasil dihapus.'
        );
    }
}