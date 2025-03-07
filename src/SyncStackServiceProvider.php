<?php

namespace RapideSoftware\SyncStack;

use Illuminate\Support\ServiceProvider;
use RapideSoftware\SyncStack\Migrations\MigrateMakeCommand;
use RapideSoftware\SyncStack\Commands\InitializeSynchronisationCommand;
use RapideSoftware\SyncStack\Commands\MakeSynchronisationCommand;
use RapideSoftware\SyncStack\Commands\RollbackSynchronisationCommand;
use RapideSoftware\SyncStack\Commands\RunSynchronisationsCommand;

class SyncStackServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeSynchronisationCommand::class,
                RunSynchronisationsCommand::class,
                RollbackSynchronisationCommand::class,
                InitializeSynchronisationCommand::class,
                MigrateMakeCommand::class
            ]);
        }
    }
}