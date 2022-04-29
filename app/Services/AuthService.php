<?php

namespace App\Services;

use App\Enums\GuardType;
use App\Supports\Traits\HasTransformer;
use App\Transformers\AdminTransformer;
use App\Transformers\UserTransformer;
use Flugg\Responder\Exceptions\Http\PageNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    use HasTransformer;

    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login($request): JsonResponse
    {
        if (! $token = auth($this->checkGuard($request))->attempt($request->validated())) {
            return $this->httpUnauthorized();
        }

        return $this->generateToken($token);
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function logout($request): JsonResponse
    {
        $this->checkGuard($request);
        JWTAuth::invalidate(JWTAuth::getToken());

        return $this->httpNoContent();
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function profile($request): JsonResponse
    {
        $this->checkGuard($request);

        return $this->httpOK(auth()->user(), $this->guard == GuardType::USER ? UserTransformer::class : AdminTransformer::class);
    }

    /**
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateToken($token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => config('jwt.ttl'),
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Routing\Route|object|string|null
     */
    private function checkGuard(Request $request)
    {
        $this->guard = $request->route('guard');
        if (! in_array($this->guard, GuardType::getValues())) {
            throw new PageNotFoundException();
        }

        return $this->guard;
    }
}
