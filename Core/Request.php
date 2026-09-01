<?php

namespace Core;

class Request
{
    static function all($key = null)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        $data = [];

        if ($method == 'GET') {
            $data = $_GET;
        } elseif (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true) ?? [];
        } elseif ($method == 'POST') {
            $data = $_POST;
        } else {
            // PATCH/PUT/DELETE with urlencoded body
            $raw = file_get_contents('php://input');
            parse_str($raw, $data);
        }
        if ($key && array_key_exists($key, $data)) {
            return $data[$key];
        }

        return $data;
    }

    // Trimmed post request if exists otherwise returns default
    public static function tPost($key, $default = "")
    {
        return trim($_POST[$key] ?? $default);
    }

    public static function tAll($key, $default = "")
    {
        return trim(self::all()[$key] ?? $default);
    }

    public static function capture()
    {
        $method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
        $uri = trim($_SERVER['REQUEST_URI']);

        return compact("method", "uri");
    }


    public static function except($array)
    {
        $all = self::all();
        foreach ($array as $key) {
            unset($all[$key]);
        }

        return $all;
    }

    public static function has($key)
    {
        return isset($_POST[$key]) || isset($_GET[$key]);
    }

    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public static function isPost()
    {
        return self::method() === 'POST';
    }

    public static function isGet()
    {
        return self::method() === 'GET';
    }
}
