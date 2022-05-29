<?php

namespace App\Repositories;

use App\Contracts\FileRepository;
use App\Models\File;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentFileRepository extends EloquentRepository implements FileRepository
{
    public function __construct(File $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $conditions
     * @return \Spatie\QueryBuilder\Concerns\SortsQuery|\Spatie\QueryBuilder\QueryBuilder
     */
    public function index(array $conditions = [])
    {
        return QueryBuilder::for($this->model->query()->where($conditions))
                           ->select($this->defaultSelect)
                           ->allowedFilters($this->allowedFilters)
                           ->allowedFields($this->allowedFields)
                           ->allowedIncludes($this->allowedIncludes)
                           ->allowedSorts($this->allowedSorts)
                           ->defaultSort($this->defaultSort);
    }
}
