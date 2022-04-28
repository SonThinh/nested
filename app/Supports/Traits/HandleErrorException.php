<?php

namespace App\Supports\Traits;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

trait HandleErrorException
{
    /**
     * @param \Illuminate\Validation\ValidationException $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function renderApiResponse(ValidationException $exception): JsonResponse
    {
        return response()->json([
            'code'    => JsonResponse::HTTP_BAD_REQUEST,
            'message' => trans('status.validation'),
            'errors'  => $this->convertApiErrors($exception->errors()),
        ], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @param $errors
     *
     * @return array
     */
    private function convertApiErrors($errors)
    {
        $result = [];
        foreach ($errors as $k => $error) {
            $result[] = [
                'field'   => $k,
                'message' => $error,
            ];
        }

        return $result;
    }

    public function renderApiNotFoundResponse(NotFoundHttpException $exception): JsonResponse
    {
        return response()->json([
            'code'    => JsonResponse::HTTP_NOT_FOUND,
            'message' => trans('status.not_found'),
            'errors'  => $exception->getMessage(),
        ], JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @param \Tymon\JWTAuth\Exceptions\TokenExpiredException $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function renderExpiredException(TokenExpiredException $exception): JsonResponse
    {
        return response()->json([
            'code'    => JsonResponse::HTTP_REQUEST_TIMEOUT,
            'message' => trans('status.time_expired'),
            'errors'  => trans('auth.expired'),
        ], JsonResponse::HTTP_REQUEST_TIMEOUT);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function renderNotLoginException(): JsonResponse
    {
        return response()->json([
            'code'    => JsonResponse::HTTP_NOT_FOUND,
            'message' => trans('auth.auth'),
            'errors'  => trans('status.not_login'),
        ], JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @param \Symfony\Component\HttpKernel\Exception\BadRequestHttpException $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function renderApiBadRequestResponse(BadRequestHttpException $exception): JsonResponse
    {
        return response()->json([
            'code'    => JsonResponse::HTTP_BAD_REQUEST,
            'message' => trans('status.not_found'),
            'errors'  => $exception->getMessage(),
        ], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * Response server error exception
     *
     * @param $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function renderServerErrorException($exception): JsonResponse
    {
        return response()->json([
            'code'    => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            'message' => null,
            'errors'  => $exception->getMessage(),
        ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param $exception
     *
     * @return JsonResponse
     */
    public function renderForbidenException($exception): JsonResponse
    {
        return response()->json([
            'code'    => JsonResponse::HTTP_FORBIDDEN,
            'message' => $exception->getMessage(),
        ], JsonResponse::HTTP_FORBIDDEN);
    }

    /**
     * Response unauthenticated
     *
     * @param $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function renderUnauthenticatedException($exception): JsonResponse
    {
        return response()->json([
            'code' => JsonResponse::HTTP_UNAUTHORIZED,
            'message' =>  trans('api.msg_err_401'),
            'errors' => 'unauthenticated',
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }
}
