<?php

namespace App\Repositories;

use App\Contracts\FileRepository;
use App\Models\File;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EloquentFileRepository extends EloquentRepository implements FileRepository
{
    public function __construct(File $model)
    {
        parent::__construct($model);

        $this->addExtraFilters([
            'id',
            'name',
            AllowedFilter::exact('type'),
            AllowedFilter::exact('is_published'),
        ]);
    }

    /**
     * @param array $conditions
     * @return \Spatie\QueryBuilder\Concerns\SortsQuery|\Spatie\QueryBuilder\QueryBuilder
     */
    public function index(array $conditions = [])
    {
        return QueryBuilder::for($this->model->query()->where($conditions))
                           ->select($this->defaultSelect)
                           ->allowedFilters($this->allowedFilters)
                           ->allowedFields($this->allowedFields)
                           ->allowedIncludes($this->allowedIncludes)
                           ->allowedSorts($this->allowedSorts)
                           ->defaultSort($this->defaultSort);
    }

    /**
     * @param $path
     * @return mixed
     */
    public function show($path)
    {
        if (strpos($path, 'uploads/images') === false) {
            $file = File::wherePath($path)->firstOrFail();
            $path = Storage::disk($file->disk)->path($file->path);
        } else {
            $path = $this->fetchContentFile($path);
        }

        header('Cache-Control: max-age='.(60 * 60 * 24 * 365));
        header('Expires: '.gmdate(DATE_RFC1123, time() + 60 * 60 * 24 * 365));
        header('Last-Modified: '.gmdate(DATE_RFC1123, filemtime($path)));

        return Image::make($path)->response();
    }

    /**
     * @param $path
     * @return string
     */
    public function fetchContentFile($path): string
    {
        $isCorrectFile = Storage::disk('public')->exists($path);

        if (! $isCorrectFile) {
            throw (new ModelNotFoundException);
        }

        return Storage::disk('public')->path($path);
    }

    public function download($file): BinaryFileResponse
    {
        $path = storage_path('app/public/'.$file->path);

        $fileName = $file->name;

        return response()->download($path, $fileName);
    }
}
