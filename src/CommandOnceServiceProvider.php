<?php
namespace Larastarscn\CommandOnce;

use Illuminate\Support\ServiceProvider;
use Larastarscn\CommandOnce\Commands\CommandOnce;

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
            __DIR__.'/command.php' => config_path('command.php'),
        ]);

        if (app()->runningInConsole()) {
            $this->commands([
                CommandOnce::class
            ]);
        }
    }
}
