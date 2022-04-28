<?php

namespace App\Providers;

use App\Contracts\CategoryRepository;
use App\Contracts\UserRepository;
use App\Models\Category;
use App\Models\User;
use App\Repositories\EloquentCategoryRepository;
use App\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {dd(1);
        $this->app->singleton(CategoryRepository::class, function () {
            return new EloquentCategoryRepository(new Category());
        });

        $this->app->singleton(UserRepository::class, function () {
            return new EloquentUserRepository(new User());
        });
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
