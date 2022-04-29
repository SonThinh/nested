<?php

namespace App\Repositories;

use App\Contracts\UserRepository;
use App\Models\User;

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
}
