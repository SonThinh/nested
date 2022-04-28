<?php

namespace App\Http\Controllers;

use App\Supports\Traits\HasTransformer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, HasTransformer;

    protected $perPage;

    public function __construct()
    {
        $this->perPage = (int) request('perPage',10);
    }
}
