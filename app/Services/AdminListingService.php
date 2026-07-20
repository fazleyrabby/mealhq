<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminListingService
{
    protected Request $request;

    protected int $defaultPerPage = 20;

    protected int $maxPerPage = 100;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply search, filters, sorting, and pagination to a query.
     *
     * @param  array  $searchable  Columns to search against (e.g., ['name', 'email'])
     * @param  array  $filters  Map of field => allowed values (e.g., ['status' => ['active','inactive']])
     * @param  string  $defaultSort  Default sort column
     * @param  string  $defaultDir  Default sort direction ('asc'|'desc')
     * @return array{items: LengthAwarePaginator, sortField: string, sortDir: string, search: string|null, appliedFilters: array, perPage: int}
     */
    public function process(Builder $query, array $searchable = [], array $filters = [], string $defaultSort = 'created_at', string $defaultDir = 'desc'): array
    {
        $search = $this->request->get('search');
        $sortField = $this->request->get('sort', $defaultSort);
        $sortDir = $this->request->get('direction', $defaultDir);
        $perPage = min((int) $this->request->get('per_page', $this->defaultPerPage), $this->maxPerPage);

        // Search
        if ($search && ! empty($searchable)) {
            $query->where(function (Builder $q) use ($search, $searchable) {
                foreach ($searchable as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        // Filters
        $appliedFilters = [];
        foreach ($filters as $field => $allowed) {
            $value = $this->request->get("filter_{$field}");
            if ($value !== null && $value !== '' && (empty($allowed) || in_array($value, $allowed, true))) {
                $query->where($field, $value);
                $appliedFilters[$field] = $value;
            }
        }

        // Sort – validate sort field to prevent SQL injection
        $allowedSorts = array_merge($searchable, array_keys($filters), ['id', 'created_at', 'updated_at', 'name', 'title', 'sort_order', 'price', 'total_amount', 'status', 'email', 'phone', 'is_active']);
        if (! in_array($sortField, $allowedSorts, true)) {
            $sortField = $defaultSort;
        }
        $sortDir = in_array(strtolower($sortDir), ['asc', 'desc']) ? strtolower($sortDir) : $defaultDir;

        $query->orderBy($sortField, $sortDir);

        $items = $query->paginate($perPage)->appends($this->request->query());

        return [
            'items' => $items,
            'sortField' => $sortField,
            'sortDir' => $sortDir,
            'search' => $search,
            'appliedFilters' => $appliedFilters,
            'perPage' => $perPage,
        ];
    }
}
