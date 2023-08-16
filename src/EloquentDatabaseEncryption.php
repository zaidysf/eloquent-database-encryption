<?php

namespace Zaidysf\EloquentDatabaseEncryption;

class EloquentDatabaseEncryption
{
    public static function encrypt(string $value): string
    {
        return openssl_encrypt($value, config('eloquent-database-encryption.encrypt_method'), self::getKey(), 0, $iv = '');
    }

    /**
     * Get app key for encryption key
     */
    protected static function getKey(): string
    {
        return substr(hash('sha256', config('eloquent-database-encryption.encrypt_key')), 0, 16);
    }

    public static function decrypt(string $value): string
    {
        return openssl_decrypt($value, config('eloquent-database-encryption.encrypt_method'), self::getKey(), 0, $iv = '');
    }
}
