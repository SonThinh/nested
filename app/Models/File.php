<?php

namespace App\Models;

use App\Supports\Traits\BootStamps;
use App\Supports\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory, HasUuid, SoftDeletes, BootStamps;

    protected $fillable = [
        'name',
        'mime_type',
        'is_published',
        'type',
        'size',
        'disk',
        'name_file',
        'path',
        'additional',
    ];

    protected $casts = [
        'additional'   => 'json',
        'is_published' => 'boolean',
    ];

    protected $appends = ['url'];

    public function getUrlAttribute(): string
    {
        dd(1);
        $disk = $this->attributes['disk'];
        $path = $this->attributes['path'];
        switch ($disk) {
            case 's3':
                return (string) Storage::disk('s3')->url($path);
            case 'public':
                return route_ui('file.show', ['path' => $path.'?'.now()->timestamp]);
            default:
                return '';
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function products(): MorphToMany
    {
        return $this->morphToMany(Product::class, 'model', 'model_has_files');
    }
}