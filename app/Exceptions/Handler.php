<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponser;

    protected $dontReport = [];

    protected $dontFlash = [];

    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof HttpException) {
            $code = $exception->getStatusCode();
            $message = Response::$statusTexts[$code] ?? 'Http error';
            return $this->errorResponse($message, $code);
        }

        if ($exception instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($exception->getModel()));

            return $this->errorResponse(
                "Does not exist any instance of {$model} with the given id",
                Response::HTTP_NOT_FOUND
            );
        }

        if ($exception instanceof ValidationException) {
            return $this->errorResponse(
                $exception->validator->errors()->getMessages(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if (env('APP_DEBUG', false)) {
            return parent::render($request, $exception);
        }

        return $this->errorResponse(
            'Unexpected error. Try later',
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}