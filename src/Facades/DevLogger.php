<?php

namespace DevLoggerPackage\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \DevLoggerPackage\Services\DeveloperLogService log(string $level, string $message, array $context = [])
 * @method static \DevLoggerPackage\Services\DeveloperLogService debug(string $message, array $context = [])
 * @method static \DevLoggerPackage\Services\DeveloperLogService info(string $message, array $context = [])
 * @method static \DevLoggerPackage\Services\DeveloperLogService notice(string $message, array $context = [])
 * @method static \DevLoggerPackage\Services\DeveloperLogService warning(string $message, array $context = [])
 * @method static \DevLoggerPackage\Services\DeveloperLogService error(string $message, array $context = [])
 * @method static \DevLoggerPackage\Services\DeveloperLogService critical(string $message, array $context = [])
 * @method static \DevLoggerPackage\Services\DeveloperLogService alert(string $message, array $context = [])
 * @method static \DevLoggerPackage\Services\DeveloperLogService emergency(string $message, array $context = [])
 * @method static \DevLoggerPackage\Services\DeveloperLogService logException(\Throwable $exception, array $context = [])
 * @method static \DevLoggerPackage\Services\DeveloperLogService onQueue(string $queue)
 * @method static \DevLoggerPackage\Services\DeveloperLogService withTags(array $tags)
 * @method static int cleanup()
 *
 * @see \DevLoggerPackage\Services\DeveloperLogService
 */
class DevLogger extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'devlogger';
    }
}