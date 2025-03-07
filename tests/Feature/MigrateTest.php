<?php

use RapideSoftware\SyncStack\Commands\MakeSynchronisationMigrationCommand;

use function PHPUnit\Framework\assertTrue;

it('can create migrate succesfully', function() {
    $this->artisan(MakeSynchronisationMigrationCommand::class)
        ->assertExitCode(0);
});