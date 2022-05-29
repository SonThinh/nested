<?php

namespace App\Services;

use App\Contracts\FileRepository;
use App\Supports\Actions\UploadFileAction;
use App\Transformers\FileTransformer;

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

    public function upload($data)
    {
        $uploadedFile = $data['files'];
        $fileType = $data['type'];
        $files = ($this->action)($uploadedFile, $data, $fileType);
dd($files);
        return $this->httpOK($files, FileTransformer::class);
    }
}
