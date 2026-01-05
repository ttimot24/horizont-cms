<?php

namespace App\Services;

class SearchEngine
{

    private array $searchModels = [];
    private $searchKey = null;

    public function registerModel($model): void
    {
        $this->searchModels[$model] = [];
    }

    public function getRegisteredModels(): array
    {
        return $this->searchModels;
    }

    public function getSearchKey()
    {
        return $this->searchKey;
    }

    public function executeSearch($search_key): void
    {

        $this->clearResults();
        $this->searchKey = $search_key;

        foreach ($this->searchModels as $model => $values) {
       
            if (method_exists($model, 'scopePaginateSortAndFilter')) {

                $filter['relation'] = 'or';
                $filter['filter'] = collect((new $model)->getFilterableFields())->mapWithKeys(fn($item) => [$item => $this->searchKey])->toArray();

                $this->searchModels[$model] = $model::paginateSortAndFilter($filter);
            }
        }
    }

    public function getResultsFor($model)
    {

        if (!array_key_exists($model, $this->searchModels)) {
            return [];
        }

        return $this->searchModels[$model];
    }

    public function clearResults(): void
    {
        $this->searchKey = null;
        foreach ($this->searchModels as $key => $values) {
            if (array_key_exists((string) $key, $this->searchModels)) {
                $this->searchModels[$key] = [];
            }
        }
    }

    public function getAllResults()
    {
        return $this->searchModels;
    }

    public function getTotalCount(): int
    {

        $total_count = 0;

        foreach ($this->getAllResults() as $key => $values) {
            $total_count += count($values);
        }

        return $total_count;
    }
}
