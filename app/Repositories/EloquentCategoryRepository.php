<?php

namespace App\Repositories;

use App\Contracts\CategoryRepository;
use App\Models\Category;

class EloquentCategoryRepository extends EloquentRepository implements CategoryRepository
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function fixTree()
    {
        return Category::fixTree();
    }
}
