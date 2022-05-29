<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadFileRequest;
use App\Services\FileService;

class FileController extends Controller
{
    private FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function index()
    {
        return $this->fileService->index();
    }

    public function upload(UploadFileRequest $request)
    {
        return $this->fileService->upload($request->validated());
    }
}
