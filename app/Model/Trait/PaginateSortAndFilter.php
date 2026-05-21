<?php

namespace App\Model\Trait;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait PaginateSortAndFilter
{
    /**
     * Default number of items per page for pagination.
     *
     * @var int
     */
    protected $defaultSize = 100;

    /**
     * Get the filterable fields.
     */
    public function getFilterableFields(): array
    {
        return $this->filterableFields ? $this->filterableFields : [];
    }

    /**
     * Set the filterable fields.
     */
    public function setFilterableFields(array $filterableFields): void
    {
        $this->filterableFields = $filterableFields;
    }

    /**
     * Get the default size for pagination.
     */
    public function getDefaultSize(): int
    {
        return $this->defaultSize;
    }

    /**
     * Set the default size for pagination.
     */
    public function setDefaultSize(int $defaultSize): void
    {
        $this->defaultSize = $defaultSize;
    }

    /**
     * Scope a query to paginate, sort, and filter results.
     *
     * @param  Request|null  $request
     */
    public function scopePaginateSortAndFilter(Builder $query, $paginateSortAndFilter = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $paginateSortAndFilter = $paginateSortAndFilter ?? request()->all();

        // Filtering
        if (isset($paginateSortAndFilter['filter'])) {
            foreach ($paginateSortAndFilter['filter'] as $field => $value) {

                $value = trim($value);

                if (! in_array($field, $this->getFilterableFields() ?? [])) {
                    continue; // Skip fields that are not enabled for filtering
                }
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    if (isset($paginateSortAndFilter['relation']) && $paginateSortAndFilter['relation'] === 'or') {
                        $query->orWhere($field, 'like', '%'.$value.'%');
                    } else {
                        $query->where($field, 'like', '%'.$value.'%');
                    }
                }
            }
        }

        // Sorting
        if (isset($paginateSortAndFilter['sort'])) {
            // Example: sort=name,asc
            [$column, $direction] = explode(',', $paginateSortAndFilter['sort']) + [null, 'desc'];
            if ($column) {
                $query->orderBy($column, $direction);
            }
        }

        // Pagination
        $perPage = (int) ($paginateSortAndFilter['size'] ?? $this->defaultSize);

        return $query->paginate($perPage)->appends($paginateSortAndFilter);
    }
}
