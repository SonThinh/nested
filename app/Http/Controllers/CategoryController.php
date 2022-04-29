<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Flugg\Responder\Http\Responses\SuccessResponseBuilder;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->categoryService->index();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        return $this->categoryService->store($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Category $category
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder
     */
    public function show(Category $category): SuccessResponseBuilder
    {
        return $this->categoryService->show($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function nestedCategory(Request $request)
    {
        return $this->categoryService->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Category $category
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\AuthenticationException|\Exception
     */
    public function destroy(Category $category)
    {
        return $this->categoryService->destroy($category);
    }
}
