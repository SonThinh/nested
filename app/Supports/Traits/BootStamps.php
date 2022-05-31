<?php

namespace App\Supports\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait BootStamps
{
    public static function boot()
    {
        parent::boot();
        $user = Auth::guard('admin')->user();
        if ($user) {
            static::creating(function ($model) use ($user) {
                if ($user && self::checkUserStampColumn($model->getTable(), 'created_by')) {
                    $model->created_by = $user->id;
                }
            });

            static::updating(function ($model) use ($user) {
                if ($user && self::checkUserStampColumn($model->getTable(), 'updated_by')) {
                    $model->updated_by = $user->id;
                }
            });

            static::deleting(function ($model) use ($user) {
                if ($user && self::checkUserStampColumn($model->getTable(), 'deleted_by')) {
                    $model->deleted_by = $user->id;
                }
            });
        }
    }

    public static function checkUserStampColumn($model, $column): bool
    {
        return Schema::hasColumn($model, $column);
    }
}
