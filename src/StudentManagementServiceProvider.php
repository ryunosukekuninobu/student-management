<?php

namespace Calema\StudentManagement;

use Illuminate\Support\ServiceProvider;

class StudentManagementServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/student-management.php',
            'student-management'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'student-management');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        // Publish config
        $this->publishes([
            __DIR__.'/../config/student-management.php' => config_path('student-management.php'),
        ], 'student-management-config');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/student-management'),
        ], 'student-management-views');
    }
}
