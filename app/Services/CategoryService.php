<?php

namespace App\Services;

use App\Contracts\CategoryRepository;
use App\Models\Category;
use App\Transformers\CategoryTransformer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CategoryService extends BaseService
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        parent::__construct();
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
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
        $data = Arr::get($data, 'categories');

        return DB::transaction(function () use ($data) {
            $insertedData = [];

            foreach ($data as $item) {
                $category = $this->categoryRepository->store($item);
                array_push($insertedData, $category);
            }

            $this->categoryRepository->fixTree();

            return $this->httpOK($insertedData, CategoryTransformer::class);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Category $category
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder
     */
    public function show(Category $category)
    {
        $category = $this->categoryRepository->findOne($category);

        return $this->httpOK($category, CategoryTransformer::class);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $data
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function update($data)
    {
        $data = Arr::get($data, 'categories');

        return DB::transaction(function () use ($data) {
            $updatedData = [];

            foreach ($data as $item) {
                $category = $this->categoryRepository->update($this->categoryRepository->findById($item['id']), $item);
                array_push($updatedData, $category);
            }

            $this->categoryRepository->fixTree();

            return $this->httpOK($updatedData, CategoryTransformer::class);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Category $category
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Category $category)
    {
        $parentId = null;
        if (! $category->isRoot()) {
            $parentId = $category->getParentId();
        }

        return DB::transaction(function () use ($parentId, $category) {
            if ($category->children->count() > 0) {
                $ids = $category->children->pluck('id')->toArray();
                $this->categoryRepository->whereIn('id', $ids)->update(['parent_id' => $parentId]);
            }
            $category->delete();
            $this->categoryRepository->fixTree();

            return $this->httpNoContent();
        });
    }
}
