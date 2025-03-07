<?php

namespace RapideSoftware\SyncStack\Commands;

use Illuminate\Console\Command;

class InitializeSynchronisationCommand extends Command
{
    protected $signature = 'sync:migrate {--path= : The location where the migration file should be created}
    {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}';

    protected $description = 'Create migration table for synchronisation with correct table and name usage using sync:make:migration command.';

    public function handle(): void {
        $this->call('sync:make:migration', ['name' => 'create_synchronisations_table', '--table' => 'synchronisations', '--path' => $this->option('path'), '--realpath' => $this->option('realpath')]);
    }
}
