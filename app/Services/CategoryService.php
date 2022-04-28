<?php

namespace App\Services;

use App\Contracts\CategoryRepository;
use App\Models\Admin;
use App\Transformers\CategoryTransformer;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;

class CategoryService extends BaseService
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {dd(3);
        parent::__construct();
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function index()
    {dd(1);
        $category = $this->categoryRepository->all()->toTree();

        return $this->httpOK($category, CategoryTransformer::class);
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
            $admin = $this->adminRepository->store($data);
            $admin->assignRole($data['role']);

            return $this->httpOK($admin, AdminTransformer::class);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Admin $admin
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder
     */
    public function show(Admin $admin)
    {
        $admin = $this->adminRepository->findOne($admin);

        return $this->httpOK($admin, AdminTransformer::class);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Models\Admin $admin
     * @param $data
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function update(Admin $admin, $data)
    {
        return DB::transaction(function () use ($admin, $data) {
            $this->adminRepository->update($admin, $data);
            if ($data['role']) {
                $admin->syncRoles($data['role']);
                $this->adminRepository->update($admin, ['updated_at' => now()]);
            }

            return $this->httpOK($admin, AdminTransformer::class);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Admin $admin
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function destroy(Admin $admin)
    {
        // Check if delete current logged in
        $currentAdmin = auth()->user();
        if ($currentAdmin->id == $admin->id) {
            throw new AuthenticationException();
        }

        // Check if delete login_id = 'admin' (default account)
        if ($admin->name == 'admin') {
            throw new AuthenticationException();
        }
        $admin->delete();

        return $this->httpNoContent();
    }
}
