<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function ($request, Exception $exception) {
            if ($this->isHttpException($exception)) {
                if ($exception->getStatusCode() == 500) {
                    return response()->view('errors.' . '500', ['exception' => $e], 500);
                }
            }
            return parent::render($request, $exception);
        });
        $this->renderable(function ($request, Exception $exception) {
            if ($this->isHttpException($exception)) {
                if ($exception->getStatusCode() == 404) {
                    return response()->view('errors.' . '404', ['exception' => $e], 404);
                }
            }
            return parent::render($request, $exception);
        });
        $this->renderable(function ($request, Exception $exception) {
            if ($this->isHttpException($exception)) {
                if ($exception->getStatusCode() == 403) {
                    return response()->view('errors.' . '403', ['exception' => $e], 403);
                }
            }
            return parent::render($request, $exception);
        });
        $this->renderable(function ($request, Exception $exception) {
            if ($this->isHttpException($exception)) {
                if ($exception->getStatusCode() == 401) {
                    return response()->view('errors.' . '401', ['exception' => $e], 401);
                }
            }
            return parent::render($request, $exception);
        });
        $this->renderable(function ($request, Exception $exception) {
            if ($this->isHttpException($exception)) {
                if ($exception->getStatusCode() == 405) {
                    return response()->view('errors.' . '405', ['exception' => $e], 405);
                }
            }
            return parent::render($request, $exception);
        });
    }

    public function render($request, Throwable $e){
        if ($e instanceof ModelNotFoundException) {
            return redirect()->back()->with('error', 'Requested record not found / deleted!');
        }
        return parent::render($request, $e);
    }
}
