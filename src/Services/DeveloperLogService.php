<?php

namespace DevLoggerPackage\Services;

use DevLoggerPackage\Models\DeveloperLog;
use Illuminate\Support\Facades\Log;

class DeveloperLogService {
    protected ?string $queue = null;

    public function log( $level, $message, array $context = [] ): static
    {
        // Use debug_backtrace to get the correct calling file path
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $filePath = $this->findCallerFilePath($backtrace);

        $formattedMessage = $this->interpolate( $message, $context );
        // if production, create a new log entry otherwise use the default Laravel log
        if ( config( 'app.env' ) === 'production' ) {
            DeveloperLog::create( [
                'level'     => $level,
                'log'       => $formattedMessage,
                'context'   => json_encode( $context ),
                'file_path' => $filePath,
                'queue' => $this->queue ?? null,
            ] );
        } else {
            Log::log( $level, $formattedMessage, $context );
        }

        // Return $this to allow method chaining
        return $this;
    }

    public function debug( $message, array $context = [] ): static
    {
        return $this->log( 'debug', $message, $context );
    }

    public function info( $message, array $context = [] ): static
    {
        return $this->log( 'info', $message, $context );
    }

    public function warning( $message, array $context = [] ): static
    {
        return $this->log( 'warning', $message, $context );
    }

    public function error( $message, array $context = [] ): static
    {
        return $this->log( 'error', $message, $context );
    }

    public function critical( $message, array $context = [] ): static
    {
        return $this->log( 'error', $message, $context );
    }

    public function notice( $message, array $context = [] ): static
    {
        return $this->log( 'error', $message, $context );
    }

    public function emergency( $message, array $context = [] ): static
    {
        return $this->log( 'record', $message, $context );
    }

    public function alert( $message, array $context = [] ): static
    {
        return $this->log( 'job', $message, $context );
    }

    public function onQueue( $queue ) {
        $this->queue = $queue;

        return $this;
    }

    private function interpolate( $message, array $context = [] ) {
        $replace = [];
        foreach ( $context as $key => $val ) {
            if ( ! is_array( $val ) && ( ! is_object( $val ) || method_exists( $val, '__toString' ) ) ) {
                $replace[ '{' . $key . '}' ] = $val;
            }
        }

        return strtr( $message, $replace );
    }

    private function findCallerFilePath($backtrace) {
        // Iterate through the backtrace to find the first file that is outside the facade and service context
        foreach ($backtrace as $trace) {
            if (isset($trace['file']) && !str_contains($trace['file'], 'vendor/laravel/framework') && !str_contains($trace['file'], 'DeveloperLogService')) {
                return $trace['file'];
            }
        }
        return 'unknown'; // Fallback in case no suitable file is found
    }
}
