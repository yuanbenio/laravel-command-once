<?php
namespace Yuanben\CommandOnce;

use Illuminate\Support\ServiceProvider;
use Yuanben\CommandOnce\Commands\CommandOnce;

class CommandOnceServiceProvider extends ServiceProvider
{
    /**
     * Register extends.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Migrations' => database_path('migrations'),
        ]);

        if (app()->runningInConsole()) {
            $this->commands([
                CommandOnce::class
            ]);
        }
    }
}
