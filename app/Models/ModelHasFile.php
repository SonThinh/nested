<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelHasFile extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'file_id',
        'model_type',
        'model_id',
    ];
}
