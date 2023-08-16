<?php

namespace Zaidysf\EloquentDatabaseEncryption\Builders;

use Illuminate\Database\Eloquent\Builder;
use stdClass;

class EloquentDatabaseEncryptionBuilder extends Builder
{
    /**
     * @param $param1
     * @param $param2
     * @param $param3
     * @return mixed
     */
    public function whereEncrypted($param1, $param2, $param3 = null): mixed
    {
        $filter = new stdClass();
        $filter->field = $param1;
        $filter->operation = isset($param3) ? $param2 : '=';
        $filter->value = $param3 ?? $param2;

        $salt = substr(hash('sha256', config('laravelDatabaseEncryption.encrypt_key')), 0, 16);

        return self::whereRaw("CONVERT(AES_DECRYPT(FROM_BASE64(`{$filter->field}`), '{$salt}') USING utf8mb4) {$filter->operation} ? ", [$filter->value]);
    }

    /**
     * @param $param1
     * @param $param2
     * @param $param3
     * @return mixed
     */
    public function orWhereEncrypted($param1, $param2, $param3 = null): mixed
    {
        $filter = new stdClass();
        $filter->field = $param1;
        $filter->operation = isset($param3) ? $param2 : '=';
        $filter->value = $param3 ?? $param2;

        $salt = substr(hash('sha256', config('laravelDatabaseEncryption.encrypt_key')), 0, 16);

        return self::orWhereRaw("CONVERT(AES_DECRYPT(FROM_BASE64(`{$filter->field}`), '{$salt}') USING utf8mb4) {$filter->operation} ? ", [$filter->value]);
    }
}
