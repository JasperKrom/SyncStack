<?php

use RapideSoftware\SyncStack\Commands\MakeSynchronisationCommand;

use function PHPUnit\Framework\assertTrue;

it('can create sync succesfully', function() {
    $this->artisan(MakeSynchronisationCommand::class, ['--name' => 'test_sync'])
        ->assertExitCode(0);
});

it('can run sync commands', function() {

});

it('can rollback sync commands', function() {

});