<?php

namespace App\Providers;

use App\Contracts\CategoryRepository;
use App\Contracts\UserRepository;
use App\Models\Category;
use App\Models\User;
use App\Repositories\EloquentCategoryRepository;
use App\Repositories\EloquentUserRepository;
use App\Supports\Elasticsearch\CategoryElasticsearch;
use App\Supports\Elasticsearch\UserElasticsearch;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if (config('elasticsearch.enable')) {
            $this->app->singleton(CategoryRepository::class, function () {
                return new CategoryElasticsearch(new Category());
            });

            $this->app->singleton(UserRepository::class, function () {
                return new UserElasticsearch(new User());
            });
        } else {
            $this->app->singleton(CategoryRepository::class, function () {
                return new EloquentCategoryRepository(new Category());
            });

            $this->app->singleton(UserRepository::class, function () {
                return new EloquentUserRepository(new User());
            });
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
