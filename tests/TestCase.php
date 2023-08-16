<?php

namespace Zaidysf\EloquentDatabaseEncryption\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Zaidysf\EloquentDatabaseEncryption\EloquentDatabaseEncryptionServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            EloquentDatabaseEncryptionServiceProvider::class,
        ];
    }
}
