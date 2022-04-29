<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckLoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected string $guard;

    private AuthService $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    /**
     * @param \App\Http\Requests\CheckLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(CheckLoginRequest $request): JsonResponse
    {
        return $this->service->login($request);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        return $this->service->logout($request);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        return $this->service->profile($request);
    }
}
