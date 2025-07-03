DevLogger Package
=================
[![Packagist License](https://img.shields.io/badge/Licence-MIT-blue)](http://choosealicense.com/licenses/mit/)

`DevLogger` is a custom logging package for Laravel that allows you to log messages to a database in production environments while still using the default Laravel logging for development environments. It also supports custom log levels, queues, and more.

Features
--------

*   Logs messages to the database in production.
*   Supports multiple log levels (`debug`, `info`, `error`, etc.).
*   Can be configured to log messages to specific queues.
*   Easily integrates with Laravel using a Facade.
*   Includes migration and configuration options.

Installation
------------

1.  **Install via Composer**

    You can install the package via Composer. If the package is on Packagist, you can require it directly:

        composer require your-vendor-name/devlogger

    Or, if you are testing locally, add the path repository to your `composer.json` file:

        "repositories": [
            {
                "type": "path",
                "url": "../devlogger"
            }
        ]

    Then require the package:

        composer require your-vendor-name/devlogger

2.  **Publish Migrations and Configuration**

    After installing the package, you need to publish the migration and configuration files:

        php artisan vendor:publish --tag=migrations
        php artisan vendor:publish --tag=config

    This will publish the migration file to `database/migrations/` and the configuration file to `config/devlogger.php`.

3.  **Run the Migration**

    Once the migration is published, you can run it to create the necessary `developer_logs` table:

        php artisan migrate


Configuration
-------------

The package comes with a default configuration file, `config/devlogger.php`. You can modify this file to suit your needs after publishing it.

### Configuration Options

*   **log\_level**: The default log level (e.g., `debug`, `info`, `error`, etc.).

Example `config/devlogger.php`:

    return [
        'log_level' => 'debug', // Default log level
    ];

Usage
-----

Once the package is installed and configured, you can start using it by calling the `DevLogger` facade.

### Basic Logging

You can log messages using various log levels (e.g., `debug`, `info`, `error`, etc.):

    use DevLogger;
    
    DevLogger::debug('This is a debug message.');
    DevLogger::info('This is an info message.', ['context' => 'example']);
    DevLogger::error('An error occurred.', ['error_code' => 500]);

### Logging to a Specific Queue

If you want to log messages and associate them with a specific queue:

    DevLogger::onQueue('my_queue')->error('A queued error log.');

### Custom Log Levels

You can define custom log levels using the `log()` method:

    DevLogger::log('custom_level', 'This is a custom log level message.');

Database Table
--------------

The `developer_logs` table stores all log entries in production environments. The table includes the following fields:

*   `id`: Primary key.
*   `level`: The log level (`debug`, `info`, `error`, etc.).
*   `log`: The log message.
*   `context`: Any additional context (stored as JSON).
*   `file_path`: The file path where the log was triggered.
*   `queue`: The queue name (optional).
*   `created_at` and `updated_at`: Timestamps.

Testing the Package Locally
---------------------------

If you're testing the package locally before publishing, add the path to your local package in the `composer.json` file of your Laravel project:

    "repositories": [
        {
            "type": "path",
            "url": "../devlogger"
        }
    ]

Then, require the package:

    composer require your-vendor-name/devlogger

Contribution
------------

If you'd like to contribute to the package, feel free to fork the repository and submit a pull request. We welcome contributions from the community!

License
-------

This package is open-sourced software licensed under the [MIT license](LICENSE.md).
