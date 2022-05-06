<?php

namespace App\Supports\Elasticsearch;

use App\Contracts\CategoryRepository;
use App\Models\Category;
use App\Supports\Traits\ElasticsearchSearchable;

class CategoryElasticsearch extends Elasticsearch implements CategoryRepository
{
    use ElasticsearchSearchable;

}
