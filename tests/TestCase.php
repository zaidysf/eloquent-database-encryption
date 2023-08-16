<?php

namespace Zaidysf\EloquentDatabaseEncryption\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Zaidysf\EloquentDatabaseEncryption\EloquentDatabaseEncryptionServiceProvider;

class TestCase extends Orchestra
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @param $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            EloquentDatabaseEncryptionServiceProvider::class,
        ];
    }
}
