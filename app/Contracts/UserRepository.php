<?php

namespace App\Contracts;

interface UserRepository extends BaseRepository
{
    public function register();
}
