<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->userService->index();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CreateUserRequest $request
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function store(CreateUserRequest $request)
    {
        return $this->userService->store($request->validated());
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\User $user
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder
     */
    public function show(User $user)
    {
        return $this->userService->show($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user)
    {
        return $this->userService->update($user, $request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\User $user
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        return $this->userService->destroy($user);
    }
}
