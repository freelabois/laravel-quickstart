<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Run;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception $exception
     * @return JsonResponse|Response
     * @throws Unauthenticated
     */
    public function render($request, Exception $exception)
    {

        if (app()->environment('testing')) {
            throw $exception;
        }

        if ($exception instanceof AuthenticationException) {
            throw new Unauthenticated(Lang::get('exceptions.users.not_authenticated'), 401);
        }

        if ($exception instanceof ValidationException) {
            $errors = $exception->errors();
            return response()->json([
                'error' => ['message' => $exception->getMessage(), 'errors' => $errors]
            ], 422);
        }

        if ($exception instanceof QueryException) {
            return response()->json([
                'error' => ['message' => $exception->getMessage()]
            ], 500);
        }

        if ($exception instanceof BadRequest) {
            return response()->json([
                'error' => ['message' => $exception->getMessage()],
            ], 400);
        }


        return self::renderJson($exception);
    }


    /**
     * Render an exception into a JSON HTTP response.
     *
     * @param Exception $e
     * @return Response
     */
    public static function renderJson(Exception $e)
    {
        $whoops = new Run;
        $whoops->pushHandler(new JsonResponseHandler);
        $whoops->sendHttpCode($e->getCode() ? $e->getCode() : 500);

        return new Response(
            $whoops->handleException($e)
        );
    }
}
