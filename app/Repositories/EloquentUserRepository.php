<?php

namespace App\Repositories;

use App\Contracts\UserRepository;
use App\Models\User;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentUserRepository extends EloquentRepository implements UserRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);

        $this->defaultSelect = [
            'id',
            'name',
            'furigana_name',
            'login_id',
            'email',
            'unique_code',
        ];
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
