<?php

namespace App\Models;


class LoginToken extends ModelBase
{
    const TABLE = "login_sessions";
    const COLUMNS = [
        'token',
        'expires_at',
        'user',
    ];

    public static function create($token, $expires_at, $user)
    {
        return self::store(compact('token', 'expires_at', 'user'));
    }

    public static function findAlive(string $token)
    {
        return self::where(compact('token'), "expires_at > NOW() AND token=:token")->fetch();
    }

    public static function delete(string $token)
    {
        return self::destroy(compact('token'));
    }
}
