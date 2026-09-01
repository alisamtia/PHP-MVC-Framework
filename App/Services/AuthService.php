<?php

namespace App\Services;

use Core\Session;

class AuthService
{
    static function createCookie($token)
    {
        $production = get_config('production');
        setcookie("remember_token", $token, [
            'expires' => time() + (86400 * 30), // 30 days
            'path' => '/',
            'secure' => $production,
            'httponly' => $production,
            'samesite' => 'Lax'
        ]);
    }

    static function createSession($email, $role, $avatar)
    {
        Session::start();
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'email' => $email,
            'role' => $role,
            'avatar' => $avatar
        ];
    }
}
