<?php

namespace App\Modules\Service\Content;

use App\Models\Content;

class ContentService
{
    public function getAll(array $filters = [])
    {
        $query = Content::query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where('title', 'ILIKE', "%{$search}%");
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        $allowedSorts = ['title', 'type', 'created_at'];

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'created_at';
        }

        $sortDirection = $sortDirection === 'asc' ? 'asc' : 'desc';

        return $query
            ->orderBy($sortBy, $sortDirection)
            ->paginate($filters['per_page'] ?? 10);
    }

    public function create(array $data): Content
    {
        return Content::create($data);
    }

    public function update(Content $content, array $data): Content
    {
        $content->update($data);

        return $content->fresh();
    }

    public function delete(Content $content): bool
    {
        return $content->delete();
    }
}
