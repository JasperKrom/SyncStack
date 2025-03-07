<?php

namespace RapideSoftware\SyncStack;

use Illuminate\Support\ServiceProvider;
use RapideSoftware\SyncStack\Commands\MakeSynchronisationMigrationCommand;
use RapideSoftware\SyncStack\Commands\MakeSynchronisationCommand;
use RapideSoftware\SyncStack\Commands\RollbackSynchronisationCommand;
use RapideSoftware\SyncStack\Commands\RunSynchronisationsCommand;
use RapideSoftware\SyncStack\Database\Migrations\Commands\MigrateMakeCommand;

class SyncStackServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeSynchronisationCommand::class,
                RunSynchronisationsCommand::class,
                RollbackSynchronisationCommand::class,
                MakeSynchronisationMigrationCommand::class,
                MigrateMakeCommand::class
            ]);
        }
    }
}