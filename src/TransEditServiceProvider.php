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
        $this->loadRoutesFrom(__DIR__.'/../routes/transedit.php');
        $this->publishes([
            __DIR__.'/../migrations' => database_path('migrations'),
        ], 'migrations');

        $this->publishes([
            __DIR__.'/../config/transedit.php' => config_path('tranedit.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../assets/' => resource_path('assets'),
        ], 'assets');
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
