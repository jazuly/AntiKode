<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Throwable;

class Handler extends ExceptionHandler
{
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

    public function render($request, Throwable $th)
    {
        if ($th instanceof ValidationException) {
            return response()->error(
                422,
                $th->validator->messages()->messages()
            );

        } else if ($th instanceof ModelNotFoundException) {
            return response()->error(
                422,
                "Data not found!"
            );

        } else if ($th instanceof NotFoundHttpException) {
            return response()->error(
                404,
                "The requested URL was not found this server!"
            );

        } else {
            if ($th->getMessage()) {
                return response()->error(
                    $th->getCode(),
                    $th->getMessage()
                );
            } else {
                return parent::render($request, $th);
            }
        }
    }
}
