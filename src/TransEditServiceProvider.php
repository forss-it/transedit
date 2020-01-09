<?php

namespace Dialect\TransEdit;

use Dialect\TransEdit\Console\Commands\AddLangFilesToDatabase;
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
            __DIR__.'/../config/transedit.php' => config_path('transedit.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../assets/' => resource_path('assets'),
        ], 'assets');

        $this->registerCommands();
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

    /**
     * Register package commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                AddLangFilesToDatabase::class,
            ]);
        }
    }
}
