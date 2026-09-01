<?php

namespace App\Models;


class PasswordReset extends ModelBase
{
    const TABLE = "password_resets";
    const COLUMNS = [
        'hash',
        'created_at',
        'user'
    ];
    static function create(string $hash, string $created_at, $user)
    {
        self::store(compact('hash', 'created_at', 'user'));
    }

    static function find(string $hash)
    {
        return self::where(compact('hash'))->fetch();
    }

    static function expire($hash)
    {
        $created_at = now(strtotime('-10 days'));
        return self::update(compact('hash'), compact('created_at'));
        // return self::destroy(compact('hash'));
    }
}
