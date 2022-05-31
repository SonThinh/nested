<?php

namespace App\Services;

use App\Contracts\FileRepository;
use App\Supports\Actions\UploadFileAction;
use App\Transformers\FileTransformer;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileService extends BaseService
{
    private FileRepository $fileRepository;

    private UploadFileAction $action;

    public function __construct(FileRepository $fileRepository, UploadFileAction $action)
    {
        parent::__construct();
        $this->fileRepository = $fileRepository;
        $this->action = $action;
    }

    /**
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $file = $this->fileRepository->index()->paginate($this->perPage);

        return $this->httpOK($file, FileTransformer::class);
    }

    /**
     * @param $data
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ErrorUploadException
     */
    public function upload($data)
    {
        $files = ($this->action)($data);

        return $this->httpOK($files, FileTransformer::class);
    }

    /**
     * @param $path
     * @return mixed
     */
    public function show($path)
    {
        return $this->fileRepository->show($path);
    }

    /**
     * @param $file
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($file): BinaryFileResponse
    {
        $file = $this->fileRepository->findById($file);

        return $this->fileRepository->download($file);
    }

    /**
     * @param $file
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function destroy($file)
    {
        $file = $this->fileRepository->findById($file);
        $file->delete();

        return $this->httpNoContent();
    }

    public function attachFilesModel(string $modelId, string $modelType, array $data){
        switch ($modelType) {
            case 'posttranslation':
                $locale = Arr::get($data, 'locale');
                $fileIds = Arr::get($data, 'file_ids');

                $translation = PostTranslation::query()->where('locale', $locale)->findOrFail($modelId);
                $files = File::query()->whereIn('id', $fileIds)->get();
                $translation->files()->attach($files);
                break;
        }
    }
}
