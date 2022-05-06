<?php

namespace App\Supports\Elasticsearch;

use App\Contracts\UserRepository;
use App\Models\User;
use Illuminate\Support\Arr;

class UserElasticsearch extends Elasticsearch implements UserRepository
{
    public $userId;

    /**
     * @param \App\Models\User $user
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function getFilter($conditions): array
    {
        if (Arr::has(Arr::get($conditions, 'filter'),'id')) {
            $this->userId = [
                'term' => [
                    '_id' => Arr::get(Arr::get($conditions, 'filter'),'id'),
                ],
            ];
        }

        return array_filter([
            'bool' => array_filter([
                'must' => array_values(array_filter([
                    $this->userId,
                ])),
            ]),
        ]);
    }
}
