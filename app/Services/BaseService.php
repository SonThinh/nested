<?php

namespace App\Services;

use App\Supports\Traits\HasTransformer;

class BaseService
{
    use HasTransformer;

    /**
     * @var int $perPage
     */
    protected int $perPage;

    /**
     * Controller constructor.
     *
     */
    public function __construct()
    {
        $this->perPage = (int) request('per_page', 50);
    }
}
