<?php

namespace DevLoggerPackage\Providers;

use DevLoggerPackage\Services\DeveloperLogService;
use Illuminate\Support\ServiceProvider;

class DevLoggerServiceProvider extends ServiceProvider {
    public function register() {
        // Register DeveloperLogService as a singleton
        $this->app->singleton( 'devlogger', function ( $app ) {
            return new DeveloperLogService();
        } );
    }

    public function boot() {
        // Any bootstrapping for your package (optional)
        $this->publishes( [
            __DIR__ . '/../config/devlogger.php' => config_path( 'devlogger.php' ),
        ] );

        // Publishing the migrations
        if ( $this->app->runningInConsole() ) {
            $this->publishes( [
                __DIR__ . '/../../migrations/2024_01_01_000000_create_developer_logs_table.php' => database_path( 'migrations/' . date( 'Y_m_d_His' ) . '_create_developer_logs_table.php' ),
            ], 'migrations' );
        }
    }
}
