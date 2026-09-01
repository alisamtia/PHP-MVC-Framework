<?php

namespace Core;

use Core\Session;

class View
{
    static $shared = [];

    static public function flashData()
    {
        self::share("old", Session::pull("old"));
        self::share("error", Session::pull("error"));
        self::share("success", Session::pull("success"));
        self::share("notify", Session::pull("notify"));
    }

    static protected function get(string $key)
    {
        return static::$shared[$key];
    }

    static public function old($key)
    {
        return static::get('old')[$key];
    }

    static public function error($key)
    {
        return static::get('errror')[$key];
    }

    static public function success($key)
    {
        return static::get('success')[$key];
    }

    static protected function exists($var, $key)
    {
        return isset((static::get($var) ?? [])[$key]);
    }

    static public function old_exists($key)
    {
        return static::exists("old", $key);
    }

    static public function error_exists($key)
    {
        return static::exists("error", $key);
    }

    static public function success_exists($key)
    {
        return static::exists("success", $key);
    }


    static public function share(string $key, $value)
    {
        static::$shared[$key] = $value;
    }

    static public function render($path, $data = [])
    {
        $data = array_merge(static::$shared, $data);
        extract($data);
        require_file("views/$path.view.php", $data);
    }
}
