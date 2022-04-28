<?php

namespace App\Exceptions;

use App\Supports\Traits\HandleErrorException;
use Flugg\Responder\Exceptions\ConvertsExceptions;
use Flugg\Responder\Exceptions\Http\HttpException;
use Flugg\Responder\Exceptions\Http\UnauthorizedException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use HandleErrorException, ConvertsExceptions;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        switch (true) {
            case $e instanceof HttpException:
                return $this->renderResponse($e);
            case $e instanceof ValidationException:
                return $this->renderApiResponse($e);
            case $e instanceof NotFoundHttpException:
                return $this->renderApiNotFoundResponse($e);
            case $e instanceof BadRequestHttpException:
                return $this->renderApiBadRequestResponse($e);
            case $e instanceof AuthorizationException:
            case $e instanceof AuthenticationException:
            case $e instanceof UnauthorizedException:
            case $e instanceof ModelNotFoundException:
                return $this->renderUnauthenticatedException($e);
            default:
                return $this->renderServerErrorException($e);
        }
    }
}
