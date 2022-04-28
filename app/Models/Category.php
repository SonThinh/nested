<?php

namespace App\Models;

use Kalnoy\Nestedset\NodeTrait;

class Category extends BaseModel
{
    use NodeTrait;

    protected $fillable = [
        'parent_id',
        'name',
        '_lft',
        '_rgt',
    ];
}
