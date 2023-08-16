<?php

namespace Zaidysf\EloquentDatabaseEncryption;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Zaidysf\EloquentDatabaseEncryption\Commands\DecryptModel;
use Zaidysf\EloquentDatabaseEncryption\Commands\EncryptModel;

class EloquentDatabaseEncryptionServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('eloquent-database-encryption')
            ->hasCommands(DecryptModel::class, EncryptModel::class)
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->copyAndRegisterServiceProviderInApp()
                    ->askToStarRepoOnGitHub('zaidysf/eloquent-database-encryption');
            });
    }
}
