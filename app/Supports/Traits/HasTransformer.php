<?php

namespace App\Supports\Traits;

use Flugg\Responder\Http\MakesResponses;
use Flugg\Responder\Http\Responses\SuccessResponseBuilder;
use Flugg\Responder\Serializers\SuccessSerializer;
use Flugg\Responder\Transformers\Transformer;
use Illuminate\Http\JsonResponse;

trait HasTransformer
{
    use MakesResponses;

    /**
     * @var mixed
     */
    protected $serializer = SuccessSerializer::class;

    /**
     * Build a HTTP_OK response.
     *
     * @param mixed                            $data
     * @param callable|string|Transformer|null $transformer
     * @param                                  $resourceKey
     *
     * @return SuccessResponseBuilder|JsonResponse
     */
    public function httpOK($data = null, $transformer = null, string $resourceKey = null)
    {
        return $this->success($data, $transformer, $resourceKey)
                    ->serializer($this->getSerializer())
                    ->respond(JsonResponse::HTTP_OK);
    }

    /**
     * @return mixed
     */
    protected function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @param mixed $serializer
     *
     * @return $this
     */
    protected function setSerializer($serializer)
    {
        $this->serializer = $serializer;

        return $this;
    }

    /**
     * Build a HTTP_CREATED response.
     *
     * @param mixed                            $data
     * @param callable|string|Transformer|null $transformer
     * @param                                  $resourceKey
     *
     * @return SuccessResponseBuilder|JsonResponse
     */
    public function httpCreated($data = null, $transformer = null, string $resourceKey = null)
    {
        return $this->success($data, $transformer, $resourceKey)
                    ->serializer($this->getSerializer())
                    ->respond(JsonResponse::HTTP_CREATED);
    }

    /**
     * Build a HTTP_NO_CONTENT response.
     *
     * @return SuccessResponseBuilder|JsonResponse
     */
    public function httpNoContent()
    {
        return $this->success()
                    ->serializer($this->getSerializer())
                    ->respond(JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Build a HTTP_BAD_REQUEST response.
     *
     * @param array $errors
     *
     * @param       $message
     *
     * @return JsonResponse
     */
    public function httpBadRequest(string $message = null, array $errors = [])
    {
        return $this->error(JsonResponse::HTTP_BAD_REQUEST, $message)
                    ->data($errors)
                    ->respond(JsonResponse::HTTP_BAD_REQUEST);
    }

    public function httpNotFound(array $errors = [], $errorCode = null, string $message = null)
    {
        return $this->error($errorCode, $message)
                    ->data($errors)
                    ->respond(JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * Build a HTTP_Unauthorized response.
     *
     * @return JsonResponse
     */
    public function httpUnauthorized()
    {
        return $this->error('unauthenticated')->respond(JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * Build a HTTP_Conflict response.
     *
     * @return JsonResponse
     */
    public function httpConflict()
    {
        return $this->error('conflict')->respond(JsonResponse::HTTP_CONFLICT);
    }

    /**
     * @return JsonResponse
     */
    public function httpForbidden()
    {
        return $this->error('unauthorized')->respond(JsonResponse::HTTP_FORBIDDEN);
    }

    /**
     * @param string|null $message
     * @param array       $errors
     *
     * @return JsonResponse
     */
    public function httpUnprocessable(string $message = null, array $errors = [])
    {
        return $this->error(null, $message)
                    ->data($errors)
                    ->respond(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }
}
