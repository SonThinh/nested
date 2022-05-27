<?php

namespace App\Services;

use App\Contracts\ProductRepository;
use App\Models\User;
use App\Transformers\ProductTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Support\Facades\DB;

class ProductService extends BaseService
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        parent::__construct();
        $this->productRepository = $productRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $product = $this->productRepository->index();

        return $this->httpOK($product->paginate($this->perPage), ProductTransformer::class);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $data
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function store($data)
    {
        return DB::transaction(function () use ($data) {
            $product = $this->productRepository->store($data);
            $product->files()->sync();

            return $this->httpOK($product, ProductTransformer::class);
        });
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
