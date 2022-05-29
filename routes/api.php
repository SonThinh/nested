<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RandomNumberRegexController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth/{guard}')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::get('refresh', [AuthController::class, 'refresh']);
    Route::middleware(['auth:admin,user'])->group(function () {
        Route::delete('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'me']);
    });
});

Route::middleware(['auth:admin'])->group(function () {
    Route::apiResource('categories', CategoryController::class)->except(['update']);
    Route::put('categories/nested', [CategoryController::class, 'nestedCategory']);
    Route::apiResource('users',UserController::class);

    //file
    Route::match(['PUT', 'PATCH', 'POST'], 'files/upload', [FileController::class, 'upload']);
    Route::get('files', [FileController::class, 'index']);
    Route::delete('files/{file}', [FileController::class, 'destroy']);

    Route::match(['POST', 'PUT', 'PATCH'], 'attach-file/{modelId}/{modelType}',
        [AttachFilesModelController::class, 'store']);

    Route::apiResource('products',ProductController::class);
});

