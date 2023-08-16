<?php

namespace Zaidysf\EloquentDatabaseEncryption\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Zaidysf\EloquentDatabaseEncryption\Commands\DecryptModel;
use Zaidysf\EloquentDatabaseEncryption\Commands\EncryptModel;

class EloquentDatabaseEncryptionProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * This method is called after all other service providers have
     * been registered, meaning you have access to all other services
     * that have been registered by the framework.
     */
    public function boot(): void
    {
        $this->bootValidators();

        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__.'/../../config/eloquent-database-encryption.php' => config_path('eloquent-database-encryption.php'),
            ], 'config');

            $this->commands([
                EncryptModel::class,
                DecryptModel::class,
            ]);
        }
    }

    private function bootValidators(): void
    {

        Validator::extend('unique_encrypted', function ($attribute, $value, $parameters, $validator) {

            // Initialize
            $salt = substr(hash('sha256', config('eloquent-database-encryption.encrypt_key')), 0, 16);

            $withFilter = count($parameters) > 3;

            $ignore_id = $parameters[2] ?? '';

            // Check using normal checker
            $data = DB::table($parameters[0])->whereRaw("CONVERT(AES_DECRYPT(FROM_BASE64(`{$parameters[1]}`), '{$salt}') USING utf8mb4) = '{$value}' ");
            $data = $ignore_id != '' ? $data->where('id', '!=', $ignore_id) : $data;

            if ($withFilter) {
                $data->where($parameters[3], $parameters[4]);
            }

            if ($data->first()) {
                return false;
            }

            return true;
        });

        Validator::extend('exists_encrypted', function ($attribute, $value, $parameters, $validator) {

            // Initialize
            $salt = substr(hash('sha256', config('eloquent-database-encryption.encrypt_key')), 0, 16);

            $withFilter = count($parameters) > 3;
            if (! $withFilter) {
                $ignore_id = $parameters[2] ?? '';
            } else {
                $ignore_id = $parameters[4] ?? '';
            }

            // Check using normal checker
            $data = DB::table($parameters[0])->whereRaw("CONVERT(AES_DECRYPT(FROM_BASE64(`{$parameters[1]}`), '{$salt}') USING utf8mb4) = '{$value}' ");
            $data = $ignore_id != '' ? $data->where('id', '!=', $ignore_id) : $data;

            if ($withFilter) {
                $data->where($parameters[2], $parameters[3]);
            }

            if ($data->first()) {
                return true;
            }

            return false;
        });
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/eloquent-database-encryption.php', 'eloquent-database-encryption');
    }
}
