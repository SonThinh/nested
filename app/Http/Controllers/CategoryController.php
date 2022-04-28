<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Transformers\CategoryTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        $category = Category::all()->toTree();

        return $this->httpOK($category, CategoryTransformer::class);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = Arr::get($request->all(), 'categories');

        return DB::transaction(function () use ($data) {
            $insertedData = [];

            foreach ($data as $item) {
                $category = new Category();
                $category->fill($item);
                $category->save();
                array_push($insertedData, $category);
            }

            Category::fixTree();

            return $this->httpOK($insertedData, CategoryTransformer::class);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Category $category
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function show(Category $category)
    {
        return $this->httpOK($category, CategoryTransformer::class);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return Response
     */
    public function nestedCategory(Request $request)
    {
        $data = Arr::get($request->all(), 'categories');

        return DB::transaction(function () use ($data) {
            $updatedData = [];

            foreach ($data as $item) {
                $category = Category::find($item['id']);
                $category->fill($item);
                $category->save();
                array_push($updatedData, $category);
            }

            Category::fixTree();

            return $this->httpOK($updatedData, CategoryTransformer::class);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Category $category
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function destroy(Category $category)
    {

        $parentId = null;
        if (! $category->isRoot()) {
            $parentId = $category->getParentId();
        }

        DB::beginTransaction();

        try {
            if ($category->children->count() > 0) {
                $ids = $category->children->pluck('id')->toArray();
                Category::whereIn('id', $ids)->update(['parent_id' => $parentId]);
            }
            $category->delete();
            Category::fixTree();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }


        return $this->httpNoContent();
    }
}
