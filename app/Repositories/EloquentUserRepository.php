<?php

namespace App\Repositories;

use App\Contracts\UserRepository;
use App\Models\User;
use Spatie\QueryBuilder\AllowedFilter;
use Tymon\JWTAuth\Facades\JWTAuth;

class EloquentUserRepository extends EloquentRepository implements UserRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);

        $this->defaultSelect = [
            'id',
            'first_apply_at',
            'last_apply_at',
            'age',
            'gender',
            'impression_on_wonda',
            'created_at',
        ];

        $this->allowedIncludes = [
            'applies',
            'serials',
        ];

        $this->addExtraFilters([
            AllowedFilter::scope('created_at'),
        ]);
    }

    public function register()
    {
        $user = User::query()->create([
            'expired_at' => now(),
        ]);

        $expiredTime = now()->addMonths(3);
        $token = JWTAuth::customClaims(['exp' => $expiredTime->timestamp])->fromUser($user);

        $user->update([
            'token'      => $token,
            'expired_at' => $expiredTime,
        ]);

        return $user;
    }
}
