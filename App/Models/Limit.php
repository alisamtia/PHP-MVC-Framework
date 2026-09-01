<?php

namespace App\Models;


class Limit extends ModelBase
{
    const TABLE = "limit_attempts";
    const COLUMNS = [
        'identifier',
        'type',
        'attempts',
        'last_attempt'
    ];
    static function create(string $identifier, string $type, string $attempts, string $last_attempt)
    {
        self::store(compact('identifier', 'type', 'attempts', 'last_attempt'));
    }

    static function find(string $identifier, string $type)
    {
        return self::where(compact('identifier', 'type'))->fetch();
    }

    static function edit(array $conditions, array $args)
    {
        return self::update($conditions, $args);
    }
}
