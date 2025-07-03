<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register services
        $this->app->singleton(\App\Services\ExternalApiService::class, function ($app) {
            return new \App\Services\ExternalApiService();
        });

        $this->app->singleton(\App\Services\ReportService::class, function ($app) {
            return new \App\Services\ReportService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for older databases
        Schema::defaultStringLength(191);

        // Use Bootstrap for pagination
        Paginator::useBootstrapFive();

        // Set locale
        setlocale(LC_TIME, 'uk_UA.UTF-8');
        \Carbon\Carbon::setLocale('uk');
    }
}