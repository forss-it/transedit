<?php

namespace Dialect\TransEdit;

use Illuminate\Support\ServiceProvider;

class TransEditServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/transedit.php');
        $this->publishes([
            __DIR__.'/Controllers' => app_path('Http/Controllers'),
            __DIR__.'/../migrations' => database_path('/migrations/'),
            __DIR__.'/../config/transedit.php' => config_path('tranedit.php'),
            __DIR__.'/../assets/' => resource_path('assets'),
        ]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('transedit', TransEdit::class);
    }
}
