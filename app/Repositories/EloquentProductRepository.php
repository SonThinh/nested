<?php

namespace App\Repositories;

use App\Contracts\ProductRepository;
use App\Models\Product;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentProductRepository extends EloquentRepository implements ProductRepository
{
    public function __construct(Product $model)
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
