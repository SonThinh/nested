<?php

namespace App\Http\Controllers;

use App\Enums\ModelTypeEnum;
use App\Http\Requests\AttachFileToModelRequest;
use App\Services\FileService;

class AttachFilesModelController extends Controller
{
    private FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function store(AttachFileToModelRequest $request, string $modelId, string $modelType)
    {
        $modelTypeCheck = ModelTypeEnum::asArray();
        if (! in_array($modelType, $modelTypeCheck)) {
            return $this->httpNotFound([], 404, 'Model Not Found');
        }
        return $this->fileService->attachFilesModel($modelId, $modelType, $request->validated());
    }
}

