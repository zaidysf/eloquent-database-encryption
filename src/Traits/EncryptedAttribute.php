<?php

namespace Zaidysf\EloquentDatabaseEncryption\Traits;

use Exception;
use Zaidysf\EloquentDatabaseEncryption\Builders\EloquentDatabaseEncryptionBuilder;
use Zaidysf\EloquentDatabaseEncryption\EloquentDatabaseEncryption;

trait EncryptedAttribute
{
    public static bool $enableEncryption = true;

    public function __construct()
    {
        self::$enableEncryption = config('eloquent-database-encryption.enable_encryption');
    }

    public function getEncryptableAttributes(): array
    {
        return $this->encryptable;
    }

    public function getAttribute($key): string
    {
        $value = parent::getAttribute($key);
        if ($this->isEncryptable($key) && (! is_null($value) && $value != '')) {
            try {
                $value = EloquentDatabaseEncryption::decrypt($value);
            } catch (Exception $th) {
            }
        }

        return $value;
    }

    public function isEncryptable($key): bool
    {
        if (self::$enableEncryption) {
            return in_array($key, $this->encryptable);
        }

        return false;
    }

    public function setAttribute($key, $value): mixed
    {
        if ($this->isEncryptable($key) && (! is_null($value) && $value != '')) {
            try {
                $value = EloquentDatabaseEncryption::encrypt($value);
            } catch (Exception $th) {
            }
        }

        return parent::setAttribute($key, $value);
    }

    public function attributesToArray(): mixed
    {
        $attributes = parent::attributesToArray();
        if ($attributes) {
            foreach ($attributes as $key => $value) {
                if ($this->isEncryptable($key) && (! is_null($value)) && $value != '') {
                    $attributes[$key] = $value;
                    try {
                        $attributes[$key] = EloquentDatabaseEncryption::decrypt($value);
                    } catch (Exception $th) {
                    }
                }
            }
        }

        return $attributes;
    }

    /**
     * Extend EloquentDatabaseEncryptionBuilder
     */
    public function newEloquentBuilder($query): EloquentDatabaseEncryptionBuilder
    {
        return new EloquentDatabaseEncryptionBuilder($query);
    }

    /**
     * Decrypt Attribute
     */
    public function decryptAttribute(string $value): ?string
    {
        return ($value != '') ? EloquentDatabaseEncryption::decrypt($value) : $value;
    }

    /**
     * Encrypt Attribute
     */
    public function encryptAttribute(string $value): ?string
    {
        return ($value != '') ? EloquentDatabaseEncryption::encrypt($value) : $value;
    }
}
