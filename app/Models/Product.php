<?php

namespace App\Models;

use App\Supports\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'public_duration_start',
        'public_duration_end',
        'unit_price',
        'tax_classification',
        'tax_rate',
        'tax_calculation_classification',
        'stock_quantity',
        'max_order',
        'min_order',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function files(): MorphToMany
    {
        return $this->morphToMany(File::class, 'model', 'model_has_files');
    }
}
