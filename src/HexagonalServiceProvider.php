<?php

namespace Gascencio\Hexagonal;

use Illuminate\Support\ServiceProvider;
use Gascencio\Hexagonal\Console\InstallCommand;

class HexagonalServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}