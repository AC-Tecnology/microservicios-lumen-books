<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;

class Handler extends ExceptionHandler
{
    use ApiResponser;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {

        /**
        *   validar exceptiones HTTP y retornar mensaje en json
        *   probamos con un metodo y una ruta no valida, ejm DELET con ruta localhost:591/authors
        */
        if ($exception instanceof HttpException) {

            $code = $exception->getStatusCode(); //obtener codigo de exception
            $message = Response::$statusTexts[$code];
            
            if ($message == 'Method Not Allowed') {
                $message = 'Metodo no permitido';
            }

            return $this->errorResponse($message, $code);

        }

        /**
         *  validar exceptiones Models y retornar mensaje en json
         *  probamos con un eliminar una instancia que no existe, ejm DELET con ruta localhost:591/authors/234234
         */
        if ($exception instanceof ModelNotFoundException) {

            $code = Response::HTTP_NOT_FOUND;
            $model = strtolower(class_basename($exception->getModel()));

            return $this->errorResponse("No se encuentra una instancia {$model} con el id especificado", $code);

        }

        /**
         *
         */
        if ($exception instanceof AuthorizationException) {

            $code = Response::HTTP_FORBIDDEN;

            return $this->errorResponse($exception->getMessage(), $code);

        }

        /**
         * 
         */
        if ($exception instanceof AuthenticationException) {

            $code = Response::HTTP_UNAUTHORIZED;

            return $this->errorResponse($exception->getMessage(), $code);

        }

        /**
         * Validar las validaciones al actualizar y retornar mensaje en json
         * guardar un author sin nada, POST localhost:591/authors/
         */
        if ($exception instanceof ValidationException) {

            $errors = $exception->validator->errors()->getMessages();
            $code = Response::HTTP_UNPROCESSABLE_ENTITY;

            return $this->errorResponse($errors, $code);

        }

        /**
         * 
         */
        if (env('APP_DEBUG', false)) {
            return parent::render($request, $exception);
        }

        return $this->errorResponse('Unexpected Error. Try Later', Response::HTTP_INTERNAL_SERVER_ERROR);

    }
}
