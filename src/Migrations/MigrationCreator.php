<?php

namespace RapideSoftware\SyncStack\Migrations;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class MigrationCreator extends \Illuminate\Database\Migrations\MigrationCreator
{
    public function __construct(Filesystem $files, $customStubPath = null) {
        parent::__construct($files, $customStubPath);
    }

    /**
     * Overrides getStub method, but does not use the table or create params
     * @param $table
     * @param $create
     * @return string
     * @throws FileNotFoundException
     */
    protected function getStub($table = null, $create = null): string {
        return $this->files->get($this->stubPath().'/migration.synchronisation.stub');
    }

    public function stubPath(): string {
        return __DIR__.'/Stubs';
    }
}
