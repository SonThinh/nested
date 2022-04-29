<?php

namespace App\Services;

use App\Contracts\UserRepository;
use App\Models\User;
use App\Transformers\UserTransformer;

class UserService extends BaseService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = $this->userRepository->index();

        return $this->httpOK($user->paginate($this->perPage), UserTransformer::class);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $data
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function store($data)
    {
        $user = $this->userRepository->store(array_merge($data, [
            'unique_code' => generateUniqueCode(),
        ]));

        return $this->httpOK($user, UserTransformer::class);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\User $user
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder
     */
    public function show(User $user)
    {
        $user = $this->userRepository->findOne($user);

        return $this->httpOK($user, UserTransformer::class);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Models\User $user
     * @param $data
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function update(User $user, $data)
    {
        $user = $this->userRepository->update($user, $data);

        return $this->httpOK($user, UserTransformer::class);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\User $user
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        $user->delete();

        return $this->httpNoContent();
    }
}
