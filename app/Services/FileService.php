<?php

namespace App\Services;

use App\Contracts\CategoryRepository;
use App\Contracts\FileRepository;
use App\Enums\ModelTypeEnum;
use App\Supports\Actions\UploadFileAction;
use App\Transformers\CategoryTransformer;
use App\Transformers\FileTransformer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileService extends BaseService
{
    private FileRepository $fileRepository;

    private CategoryRepository $categoryRepository;

    private UploadFileAction $action;

    public function __construct(
        FileRepository $fileRepository,
        CategoryRepository $categoryRepository,
        UploadFileAction $action
    ) {
        parent::__construct();
        $this->fileRepository = $fileRepository;
        $this->categoryRepository = $categoryRepository;
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

    public function attachFilesModel(string $modelId, string $modelType, array $data)
    {
        return DB::transaction(function () use ($modelId, $modelType, $data) {
            switch ($modelType) {
                case ModelTypeEnum::CATEGORY:
                    $fileIds = Arr::get($data, 'file_ids');
                    $category = $this->categoryRepository->findById($modelId);
                    $files = $this->fileRepository->whereIn('id', $fileIds)->get();
                    $category->files()->attach($files);

                    return $this->httpOK($category, CategoryTransformer::class);
                default:
                    return $this->httpNotFound();
            }
        });
    }
}
