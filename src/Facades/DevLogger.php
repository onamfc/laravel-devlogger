<?php

namespace DevLoggerPackage\Facades;

/**
 * @method static log( $level, $message, array $context = [] )
 * @method static debug( $message, array $context = [] )
 * @method static info( $message, array $context = [] )
 * @method static warning( $message, array $context = [] )
 * @method static error( $message, array $context = [] )
 * @method static critical( $message, array $context = [] )
 * @method static alert( $message, array $context = [] )
 * @method static emergency( $message, array $context = [] )
 * @method static notice( $message, array $context = [] )
 *
 * @see \DevLoggerPackage\Services\DeveloperLogService
 */
class DevLogger extends \Illuminate\Support\Facades\Facade {
    protected static function getFacadeAccessor(): string {
        return 'devlogger';
    }
}
