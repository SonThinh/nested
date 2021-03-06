<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

interface BaseRepository
{
    /**
     * Set the relationships of the query.
     *
     * @param array $with
     *
     * @return BaseRepository
     */
    public function with(array $with = []): BaseRepository;

    /**
     * Set withoutGlobalScopes attribute to true and apply it to the query.
     *
     * @return BaseRepository
     */
    public function withoutGlobalScopes(): BaseRepository;

    /**
     * Find a resource by id.
     *
     * @param Model $model
     *
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findOne(Model $model): Model;

    /**
     * Find a resource by key value criteria.
     *
     * @param array $criteria
     *
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findOneBy(array $criteria): Model;

    /**
     * Save a resource.
     *
     * @param array $data
     *
     * @return Model
     */
    public function store(array $data): Model;

    /**
     * Update a resource.
     *
     * @param Model $model
     * @param array $data
     *
     * @return Model
     */
    public function update(Model $model, array $data): Model;

    /**
     * @param $id
     * @return Model
     */
    public function findById($id): Model;

    /**
     * @return mixed
     */
    public function all();

    /**
     * @param array $data
     * @return mixed
     */
    public function findByAttributes(array $data);

    /**
     * @return mixed
     */
    public function query();

    /**
     * @return mixed
     */
    public function whereIn($column, array $ids);
}
