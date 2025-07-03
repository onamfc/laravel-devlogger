<?php

namespace DevLoggerPackage\Services;

use DevLoggerPackage\Models\DeveloperLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class DeveloperLogService
{
    protected ?string $queue = null;
    protected array $tags = [];

    /**
     * Log a message with the given level
     */
    public function log($level, $message, array $context = []): static
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $callerInfo = $this->findCallerInfo($backtrace);

        $logData = $this->prepareLogData($level, $message, $context, $callerInfo);

        if (config('devlogger.enable_database_logging', true)) {
            $this->logToDatabase($logData);
        }

        $this->logToLaravel($level, $message, $context);

        // Reset instance state
        $this->queue = null;
        $this->tags = [];

        return $this;
    }

    /**
     * Log an exception
     */
    public function logException(Throwable $exception, array $context = []): static
    {
        $logData = $this->prepareExceptionLogData($exception, $context);

        if (config('devlogger.enable_database_logging', true)) {
            $this->logToDatabase($logData);
        }

        $this->logToLaravel('error', $exception->getMessage(), array_merge($context, [
            'exception' => $exception,
        ]));

        return $this;
    }

    /**
     * Set the queue for the next log entry
     */
    public function onQueue(string $queue): static
    {
        $this->queue = $queue;
        return $this;
    }

    /**
     * Add tags to the next log entry
     */
    public function withTags(array $tags): static
    {
        $this->tags = array_merge($this->tags, $tags);
        return $this;
    }

    /**
     * Debug level log
     */
    public function debug($message, array $context = []): static
    {
        return $this->log('debug', $message, $context);
    }

    /**
     * Info level log
     */
    public function info($message, array $context = []): static
    {
        return $this->log('info', $message, $context);
    }

    /**
     * Notice level log
     */
    public function notice($message, array $context = []): static
    {
        return $this->log('notice', $message, $context);
    }

    /**
     * Warning level log
     */
    public function warning($message, array $context = []): static
    {
        return $this->log('warning', $message, $context);
    }

    /**
     * Error level log
     */
    public function error($message, array $context = []): static
    {
        return $this->log('error', $message, $context);
    }

    /**
     * Critical level log
     */
    public function critical($message, array $context = []): static
    {
        return $this->log('critical', $message, $context);
    }

    /**
     * Alert level log
     */
    public function alert($message, array $context = []): static
    {
        return $this->log('alert', $message, $context);
    }

    /**
     * Emergency level log
     */
    public function emergency($message, array $context = []): static
    {
        return $this->log('emergency', $message, $context);
    }

    /**
     * Clean up old logs based on retention policy
     */
    public function cleanup(): int
    {
        $retentionDays = config('devlogger.retention_days');
        
        if (!$retentionDays) {
            return 0;
        }

        $cutoffDate = now()->subDays($retentionDays);
        
        return DeveloperLog::where('created_at', '<', $cutoffDate)->delete();
    }

    /**
     * Prepare log data for database storage
     */
    protected function prepareLogData(string $level, string $message, array $context, array $callerInfo): array
    {
        $formattedMessage = $this->interpolate($message, $context);

        return [
            'level' => $level,
            'log' => $formattedMessage,
            'context' => $context,
            'file_path' => $callerInfo['file'],
            'line_number' => $callerInfo['line'],
            'queue' => $this->queue,
            'tags' => !empty($this->tags) ? $this->tags : null,
            'request_url' => Request::fullUrl(),
            'request_method' => Request::method(),
            'user_id' => Auth::id(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'status' => 'open',
        ];
    }

    /**
     * Prepare exception data for database storage
     */
    protected function prepareExceptionLogData(Throwable $exception, array $context): array
    {
        return [
            'level' => 'error',
            'log' => $exception->getMessage(),
            'context' => array_merge($context, [
                'code' => $exception->getCode(),
                'previous' => $exception->getPrevious() ? $exception->getPrevious()->getMessage() : null,
            ]),
            'file_path' => $exception->getFile(),
            'line_number' => $exception->getLine(),
            'exception_class' => get_class($exception),
            'stack_trace' => $exception->getTraceAsString(),
            'queue' => $this->queue,
            'tags' => !empty($this->tags) ? array_merge($this->tags, ['exception']) : ['exception'],
            'request_url' => Request::fullUrl(),
            'request_method' => Request::method(),
            'user_id' => Auth::id(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'status' => 'open',
        ];
    }

    /**
     * Log to database
     */
    protected function logToDatabase(array $logData): void
    {
        try {
            DeveloperLog::create($logData);
        } catch (Throwable $e) {
            // Fallback to Laravel logging if database logging fails
            Log::error('DevLogger: Failed to log to database', [
                'error' => $e->getMessage(),
                'original_log' => $logData,
            ]);
        }
    }

    /**
     * Log to Laravel's logging system
     */
    protected function logToLaravel(string $level, string $message, array $context): void
    {
        $channels = config('devlogger.fallback_channels');
        
        if ($channels) {
            $channels = is_array($channels) ? $channels : [$channels];
            
            foreach ($channels as $channel) {
                Log::channel($channel)->log($level, $message, $context);
            }
        } else {
            Log::log($level, $message, $context);
        }
    }

    /**
     * Interpolate context values into the message placeholders
     */
    protected function interpolate(string $message, array $context = []): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        return strtr($message, $replace);
    }

    /**
     * Find caller information from backtrace
     */
    protected function findCallerInfo(array $backtrace): array
    {
        $excludedPaths = config('devlogger.excluded_paths', []);
        
        foreach ($backtrace as $trace) {
            if (!isset($trace['file'])) {
                continue;
            }

            $file = $trace['file'];
            
            // Skip vendor files and framework files
            $shouldSkip = false;
            foreach ($excludedPaths as $excludedPath) {
                if (str_contains($file, $excludedPath)) {
                    $shouldSkip = true;
                    break;
                }
            }
            
            if ($shouldSkip || str_contains($file, 'DeveloperLogService')) {
                continue;
            }

            return [
                'file' => $file,
                'line' => $trace['line'] ?? 0,
            ];
        }

        return [
            'file' => 'unknown',
            'line' => 0,
        ];
    }
}