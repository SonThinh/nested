<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadFileRequest;
use App\Services\FileService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    private FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->fileService->index();
    }

    /**
     * @param \App\Http\Requests\UploadFileRequest $request
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ErrorUploadException
     */
    public function upload(UploadFileRequest $request)
    {
        return $this->fileService->upload($request->validated());
    }

    /**
     * @param $path
     * @return mixed
     */
    public function show($path)
    {
        return $this->fileService->show($path);
    }

    /**
     * @param $file
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($file): BinaryFileResponse
    {
        return $this->fileService->download($file);
    }

    public function destroy($file)
    {
        return $this->fileService->destroy($file);
    }
}
