<?php

namespace App\Repositories;

use App\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class EloquentRepository implements BaseRepository
{
    protected Model $model;

    protected bool $withoutGlobalScopes = false;

    protected array $with = [];

    protected string $defaultSort = '-created_at';

    protected array $defaultSelect = ['*'];

    protected array $allowedFilters = [
        'id',
    ];

    protected array $allowedSorts = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected array $allowedIncludes = [];

    protected array $allowedFields = [];

    protected int $perPage = 50;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(): Builder
    {
        return $this->model->query();
    }

    /**
     * {@inheritdoc}
     */
    public function with(array $with = []): BaseRepository
    {
        $this->with = $with;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutGlobalScopes(): BaseRepository
    {
        $this->withoutGlobalScopes = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function store(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Model $model, array $data): Model
    {
        return tap($model)->update($data);
    }

    /**
     * {@inheritdoc}
     */
    public function findOne(Model $model): Model
    {
        if (! $model) {
            throw (new ModelNotFoundException());
        }

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria): Model
    {
        if (! $this->withoutGlobalScopes) {
            return $this->model->with($this->with)
                               ->where($criteria)
                               ->orderByDesc('created_at')
                               ->firstOrFail();
        }

        return $this->model->with($this->with)
                           ->withoutGlobalScopes()
                           ->where($criteria)
                           ->orderByDesc('created_at')
                           ->firstOrFail();
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model|null
     */
    public function findById($id): Model
    {
        return $this->model->query()->find($id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->query()->get();
    }

    /**
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findByAttributes(array $data)
    {
        return $this->model->query()->where($data)->get();
    }

    /**
     * @param $filters
     * @return void
     */
    public function addExtraFilters($filters)
    {
        $this->allowedFilters = array_merge($this->allowedFilters, $filters);
    }

    /**
     * @param $column
     * @param array $ids
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function whereIn($column, array $ids): Builder
    {
        return $this->model->query()->whereIn($column, $ids);
    }
}
