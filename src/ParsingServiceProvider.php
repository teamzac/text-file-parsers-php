<?php

namespace TeamZac\Parsing;

use Illuminate\Support\ServiceProvider;

class ParsingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            //
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Register the main class to use with the facade
        $this->app->singleton('text-file-parsers', function () {
            return new TextFileParsers;
        });
    }
}
