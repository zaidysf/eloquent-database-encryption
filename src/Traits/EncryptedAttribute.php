<?php

namespace Zaidysf\EloquentDatabaseEncryption\Traits;

use Exception;
use Zaidysf\EloquentDatabaseEncryption\Builders\EloquentDatabaseEncryptionBuilder;
use Zaidysf\EloquentDatabaseEncryption\EloquentDatabaseEncryption;

trait EncryptedAttribute
{

    /**
     * @var bool
     */
    public static bool $enableEncryption = true;

    /**
     *
     */
    function __construct()
    {
        self::$enableEncryption = config('eloquent-database-encryption.enable_encryption');
    }

    /**
     * @return array
     */
    public function getEncryptableAttributes(): array
    {
        return $this->encryptable;
    }

    /**
     * @param $key
     * @return string
     */
    public function getAttribute($key): string
    {
        $value = parent::getAttribute($key);
        if ($this->isEncryptable($key) && (!is_null($value) && $value != '')) {
            try {
                $value = EloquentDatabaseEncryption::decrypt($value);
            } catch (Exception $th) {
            }
        }
        return $value;
    }

    /**
     * @param $key
     * @return bool
     */
    public function isEncryptable($key): bool
    {
        if (self::$enableEncryption) {
            return in_array($key, $this->encryptable);
        }

        return false;
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function setAttribute($key, $value): mixed
    {
        if ($this->isEncryptable($key) && (!is_null($value) && $value != '')) {
            try {
                $value = EloquentDatabaseEncryption::encrypt($value);
            } catch (Exception $th) {
            }
        }
        return parent::setAttribute($key, $value);
    }

    /**
     * @return mixed
     */
    public function attributesToArray(): mixed
    {
        $attributes = parent::attributesToArray();
        if ($attributes) {
            foreach ($attributes as $key => $value) {
                if ($this->isEncryptable($key) && (!is_null($value)) && $value != '') {
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
     *
     * @param $query
     * @return EloquentDatabaseEncryptionBuilder
     */
    public function newEloquentBuilder($query): EloquentDatabaseEncryptionBuilder
    {
        return new EloquentDatabaseEncryptionBuilder($query);
    }

    /**
     * Decrypt Attribute
     *
     * @param string $value
     *
     * @return string|null
     */
    public function decryptAttribute(string $value): ?string
    {
        return ($value != '') ? EloquentDatabaseEncryption::decrypt($value) : $value;
    }

    /**
     * Encrypt Attribute
     *
     * @param string $value
     *
     * @return string|null
     */
    public function encryptAttribute(string $value): ?string
    {
        return ($value != '') ? EloquentDatabaseEncryption::encrypt($value) : $value;
    }
}
