<?php

namespace DevLoggerPackage\Exceptions;

use DevLoggerPackage\Facades\DevLogger;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class DevLoggerExceptionHandler extends ExceptionHandler
{
    /**
     * Report or log an exception.
     */
    public function report(Throwable $e): void
    {
        if ($this->shouldReport($e) && config('devlogger.auto_catch_exceptions', true)) {
            DevLogger::logException($e, [
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'input' => request()->except(['password', 'password_confirmation', '_token']),
            ]);
        }

        parent::report($e);
    }
}