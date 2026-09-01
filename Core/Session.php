<?php

namespace Core;

class Session
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function flash($key, array $array)
    {
        static::set($key, $array);
    }

    public static function pull($key)
    {
        $errors = static::get($key);
        static::unset($key);
        return $errors;
    }

    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public static function unset($key)
    {
        if (static::get($key)) {
            unset($_SESSION[$key]);
        }
    }

    public static function all()
    {
        return $_SESSION;
    }

    public static function destroy()
    {
        $_SESSION = [];

        session_unset();
        session_destroy();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
    }
}
