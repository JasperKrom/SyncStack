<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as BaseTestCase;
use RapideSoftware\SyncStack\SyncStackServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use refreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            SyncStackServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
