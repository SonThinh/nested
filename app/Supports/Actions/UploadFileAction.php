<?php

namespace App\Supports\Actions;

use App\Exceptions\ErrorUploadException;
use App\Models\File;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadFileAction
{
    /**
     * @param $data
     * @return \App\Models\File|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection|null
     * @throws \App\Exceptions\ErrorUploadException
     */
    public function __invoke($data)
    {
        $uploadedFile = Arr::get($data, 'files');
        if (is_array($uploadedFile)) {
            return collect($uploadedFile)
                ->filter(function ($file) {
                    return $file instanceof UploadedFile;
                })
                ->map(function ($file) use ($data) {
                    return $this->upload($file, $data);
                });
        }
        if ($uploadedFile instanceof UploadedFile) {
            return $this->upload($uploadedFile, $data);
        }

        return null;
    }

    /**
     * @throws \App\Exceptions\ErrorUploadException
     */
    private function upload(UploadedFile $uploadedFile, $data)
    {
        $attributes = [
            'name' => $uploadedFile->getClientOriginalName(),
            'disk' => config('filesystems.default'),
        ];
        $values = [
            'mime_type' => $uploadedFile->getClientMimeType(),
            'size'      => convertUploadedFileToHumanReadable($uploadedFile->getSize()),
        ];

        $file = File::query()->firstOrNew($attributes, $values);

        $tmpExtension = $uploadedFile->getClientOriginalExtension();
        $path = $this->getPathUpload();
        $filePath = $path.sprintf('%s_%s.%s', now()->timestamp, Str::random(8), $tmpExtension);

        if ($file->exists) {
            $file = $file->replicate();
        }
        // Handle file upload
        $path = $this->storageDriver($file->disk)->put($filePath, file_get_contents($uploadedFile));

        if (! $path) {
            throw ErrorUploadException::create($uploadedFile);
        }
        $file->path = $filePath;
        $file->is_published = Arr::get($data, 'is_active');
        $file->type = Arr::get($data, 'type');

        $file->save();

        return $file;
    }

    private function getPathUpload(): string
    {
        return 'uploads/'.now()->format('Y/m/d').'/';
    }

    public function storageDriver($disk): FilesystemAdapter
    {
        return Storage::disk($disk);
    }
}
