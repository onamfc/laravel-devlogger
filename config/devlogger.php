<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Log Level
    |--------------------------------------------------------------------------
    |
    | This option defines the default log level for the DevLogger package.
    | Supported levels: emergency, alert, critical, error, warning, notice, info, debug
    |
    */
    'log_level' => env('DEVLOGGER_LOG_LEVEL', 'debug'),

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | The database connection to use for storing logs. If null, the default
    | connection will be used.
    |
    */
    'database_connection' => env('DEVLOGGER_DB_CONNECTION', null),

    /*
    |--------------------------------------------------------------------------
    | Table Name
    |--------------------------------------------------------------------------
    |
    | The name of the table where logs will be stored.
    |
    */
    'table_name' => env('DEVLOGGER_TABLE_NAME', 'developer_logs'),

    /*
    |--------------------------------------------------------------------------
    | Enable Database Logging
    |--------------------------------------------------------------------------
    |
    | Whether to enable database logging. When false, logs will only go to
    | Laravel's default logging system.
    |
    */
    'enable_database_logging' => env('DEVLOGGER_ENABLE_DB', true),

    /*
    |--------------------------------------------------------------------------
    | Auto-catch Exceptions
    |--------------------------------------------------------------------------
    |
    | Whether to automatically catch and log all exceptions that occur in the
    | application.
    |
    */
    'auto_catch_exceptions' => env('DEVLOGGER_AUTO_CATCH', true),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Specify which Laravel log channels should also receive the logs.
    | Set to null to disable, or provide an array of channel names.
    |
    */
    'fallback_channels' => env('DEVLOGGER_FALLBACK_CHANNELS', null),

    /*
    |--------------------------------------------------------------------------
    | Maximum Log Retention Days
    |--------------------------------------------------------------------------
    |
    | Number of days to keep logs in the database. Older logs will be
    | automatically cleaned up. Set to null to disable cleanup.
    |
    */
    'retention_days' => env('DEVLOGGER_RETENTION_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Excluded Paths
    |--------------------------------------------------------------------------
    |
    | File paths that should be excluded from automatic error logging.
    | Useful for excluding vendor files or specific directories.
    |
    */
    'excluded_paths' => [
        'vendor/',
        'storage/framework/',
    ],
];