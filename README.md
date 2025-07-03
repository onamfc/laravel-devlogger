# DevLogger Package
[![Packagist License](https://img.shields.io/badge/Licence-MIT-blue)](http://choosealicense.com/licenses/mit/)

`DevLogger` is a comprehensive logging package for Laravel that stores logs in a database with automatic error catching, advanced filtering, and management capabilities.

## Features

- 📊 **Database Logging**: Store logs in database with rich metadata
- 🚨 **Automatic Error Catching**: Automatically capture and log all application exceptions
- 🏷️ **Tagging System**: Organize logs with custom tags
- 🔍 **Advanced Filtering**: Filter logs by level, date, queue, status, and more
- 🧹 **Automatic Cleanup**: Configurable log retention with automatic cleanup
- 🔄 **Queue Support**: Associate logs with specific queues
- 📱 **Request Context**: Capture HTTP request information automatically
- 👤 **User Tracking**: Track which user triggered each log entry
- 🎯 **Flexible Configuration**: Extensive configuration options via environment variables

## Installation

### 1. Install via Composer

```bash
composer require onamfc/devlogger-laravel
```

### 2. Publish Configuration and Migrations

```bash
# Publish configuration file
php artisan vendor:publish --tag=devlogger-config

# Publish migrations (optional - they auto-load by default)
php artisan vendor:publish --tag=devlogger-migrations
```

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Configure Environment Variables (Optional)

Add these to your `.env` file to customize behavior:

```env
# Enable/disable database logging
DEVLOGGER_ENABLE_DB=true

# Default log level
DEVLOGGER_LOG_LEVEL=debug

# Database connection (null = default)
DEVLOGGER_DB_CONNECTION=null

# Table name
DEVLOGGER_TABLE_NAME=developer_logs

# Auto-catch exceptions
DEVLOGGER_AUTO_CATCH=true

# Log retention (days)
DEVLOGGER_RETENTION_DAYS=30

# Fallback Laravel log channels
DEVLOGGER_FALLBACK_CHANNELS=null
```

## Basic Usage

### Manual Logging

```php
use DevLogger;

// Basic logging
DevLogger::info('User logged in', ['user_id' => 123]);
DevLogger::error('Payment failed', ['order_id' => 456, 'error' => 'Card declined']);
DevLogger::debug('Debug information', ['data' => $debugData]);

// With queue context
DevLogger::onQueue('email-queue')->info('Email sent', ['recipient' => 'user@example.com']);

// With tags
DevLogger::withTags(['payment', 'critical'])->error('Payment gateway timeout');

// Method chaining
DevLogger::onQueue('reports')
    ->withTags(['report', 'daily'])
    ->info('Daily report generated');
```

### Exception Logging

```php
try {
    // Some risky operation
    $result = $this->riskyOperation();
} catch (Exception $e) {
    DevLogger::logException($e, ['context' => 'additional info']);
    throw $e; // Re-throw if needed
}
```

### Automatic Exception Catching

The package automatically catches and logs all unhandled exceptions when `DEVLOGGER_AUTO_CATCH=true` (default). To set this up:

#### Option 1: Extend the DevLogger Exception Handler

In your `app/Exceptions/Handler.php`:

```php
<?php

namespace App\Exceptions;

use DevLoggerPackage\Exceptions\DevLoggerExceptionHandler;

class Handler extends DevLoggerExceptionHandler
{
    // Your existing exception handling code
}
```

#### Option 2: Manual Integration

If you prefer to keep your existing handler, add this to your `report` method:

```php
use DevLoggerPackage\Facades\DevLogger;

public function report(Throwable $exception)
{
    if ($this->shouldReport($exception) && config('devlogger.auto_catch_exceptions', true)) {
        DevLogger::logException($exception, [
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'input' => request()->except(['password', 'password_confirmation', '_token']),
        ]);
    }

    parent::report($exception);
}
```

## Advanced Usage

### Working with Log Models

```php
use DevLoggerPackage\Models\DeveloperLog;

// Query logs
$errorLogs = DeveloperLog::level('error')->open()->get();
$recentLogs = DeveloperLog::dateRange(now()->subDays(7), now())->get();
$queueLogs = DeveloperLog::queue('email-queue')->get();

// Manage log status
$log = DeveloperLog::find(1);
$log->markAsClosed(auth()->id());
$log->markAsOpen();

// Work with tags
$log->addTags(['reviewed', 'fixed']);
$log->removeTags(['critical']);
```

### Log Cleanup

The package includes automatic cleanup based on your retention policy:

```bash
# Manual cleanup
php artisan devlogger:cleanup

# Cleanup with custom retention
php artisan devlogger:cleanup --days=7

# Dry run to see what would be deleted
php artisan devlogger:cleanup --dry-run
```

Automatic cleanup runs daily when `DEVLOGGER_RETENTION_DAYS` is configured.

## Database Schema

The `developer_logs` table includes these fields:

| Field | Type | Description |
|-------|------|-------------|
| `id` | bigint | Primary key |
| `level` | string | Log level (debug, info, error, etc.) |
| `log` | longtext | The log message |
| `context` | json | Additional context data |
| `file_path` | string | File where log originated |
| `line_number` | integer | Line number where log originated |
| `exception_class` | string | Exception class name (for exceptions) |
| `stack_trace` | longtext | Full stack trace (for exceptions) |
| `queue` | string | Associated queue name |
| `request_url` | string | HTTP request URL |
| `request_method` | string | HTTP request method |
| `user_id` | bigint | ID of authenticated user |
| `ip_address` | string | Client IP address |
| `user_agent` | text | Client user agent |
| `status` | string | Log status (open/closed) |
| `tags` | json | Array of tags |
| `updated_by` | bigint | User who last updated the log |
| `created_at` | timestamp | When log was created |
| `updated_at` | timestamp | When log was last updated |
| `deleted_at` | timestamp | Soft delete timestamp |

## Configuration Options

The `config/devlogger.php` file provides extensive configuration:

```php
return [
    // Default log level
    'log_level' => env('DEVLOGGER_LOG_LEVEL', 'debug'),
    
    // Database connection
    'database_connection' => env('DEVLOGGER_DB_CONNECTION', null),
    
    // Table name
    'table_name' => env('DEVLOGGER_TABLE_NAME', 'developer_logs'),
    
    // Enable database logging
    'enable_database_logging' => env('DEVLOGGER_ENABLE_DB', true),
    
    // Auto-catch exceptions
    'auto_catch_exceptions' => env('DEVLOGGER_AUTO_CATCH', true),
    
    // Fallback Laravel channels
    'fallback_channels' => env('DEVLOGGER_FALLBACK_CHANNELS', null),
    
    // Log retention in days
    'retention_days' => env('DEVLOGGER_RETENTION_DAYS', 30),
    
    // Paths to exclude from auto-logging
    'excluded_paths' => [
        'vendor/',
        'storage/framework/',
    ],
];
```

## Performance Considerations

- The package uses database indexes for optimal query performance
- Automatic cleanup prevents database bloat
- Failed database writes fall back to Laravel's default logging
- Excluded paths prevent logging of framework internals

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## Support

If you encounter any issues or have questions, please open an issue on the GitHub repository.