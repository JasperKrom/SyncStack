<?php

namespace RapideSoftware\SyncStack\Database\Migrations\Commands;

use Illuminate\Support\Composer;
use RapideSoftware\SyncStack\Database\Migrations\MigrationCreator;

class MigrateMakeCommand extends \Illuminate\Database\Console\Migrations\MigrateMakeCommand
{
    protected $signature = 'sync:make:migration {name : The name of the migration}
        {--create= : The table to be created}
        {--table= : The table to migrate}
        {--path= : The location where the migration file should be created}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
        {--fullpath : Output the full path of the migration (Deprecated)}';

    protected $description = 'Create a new synchronisation migration file';

    public function __construct(MigrationCreator $creator, Composer $composer) {
        parent::__construct($creator, $composer);
    }
}
