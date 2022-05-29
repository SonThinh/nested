<?php

namespace App\Supports\Actions;

use App\Exceptions\ErrorUploadException;
use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadFileAction
{
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem|\Illuminate\Filesystem\FilesystemAdapter
     */
    protected $storage;

    /**
     * @param $uploadedFile
     * @param $request
     * @param string|null $type
     * @param string|null $disk
     * @return \App\Models\File|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection|null
     */
    public function __invoke($uploadedFile, $request = null, string $type = null, string $disk = null)
    {
        if (is_array($uploadedFile)) {
            return collect($uploadedFile)
                ->filter(function ($file) {
                    return $file instanceof UploadedFile;
                })
                ->map(function ($file) use ($type, $disk, $request) {
                    return $this->upload($file, $type, $disk, $request);
                });
        }
        if ($uploadedFile instanceof UploadedFile) {
            return $this->upload($uploadedFile, $type, $disk, $request);
        }

        return null;
    }

    /**
     * @throws \App\Exceptions\ErrorUploadException
     */
    private function upload(UploadedFile $uploadedFile, string $type = null, string $disk = null, $request = null)
    {
        $attributes = [
            'name' => sprintf('%s_%s', now()->timestamp, $uploadedFile->getClientOriginalName()),
            'disk' => $disk ?? config('filesystems.default'),
        ];
        $values = [
            'mime_type' => $uploadedFile->getClientMimeType(),
            'size'      => convertUploadedFileToHumanReadable($uploadedFile->getSize()),
        ];

        $file = File::query()->firstOrNew($attributes, $values);
        $this->storage = Storage::disk($file->disk);

        if ($file->exists) {
            $file = $file->replicate();
            $timestamp = now()->timestamp;
            $pathinfo = pathinfo($uploadedFile->getClientOriginalName());
            $file->name = sprintf('%s_%s.%s', $timestamp, $pathinfo['filename'], $pathinfo['extension']);
        }
        // Handle file upload
        $path = $this->storage->putFileAs($this->getPathUpload(), $uploadedFile, $file->name);
        dd($path);
        if (! $path) {
            throw ErrorUploadException::create($uploadedFile);
        }
        $file->path = $path;
        $file->is_published = true;
        $file->type = $type;

        $file->save();

        return $file;
    }

    private function getPathUpload(): string
    {
        return 'uploads/'.now()->format('Y/m/d');
    }
}
