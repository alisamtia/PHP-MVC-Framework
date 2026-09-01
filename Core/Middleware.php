<?php

namespace Core;

use Core\Request;
use App\Middlewares\Admin;
use App\Middlewares\Guest;
use App\Middlewares\Manager;
use App\Middlewares\Authenticated;

class Middleware
{
    static private $middlewares = [
        "admin" => Admin::class,
        "guest" => Guest::class,
        "manager" => Manager::class,
        "auth" => Authenticated::class
    ];

    static public function verify($middlewares, $method)
    {
        self::verifyCsrf($method);

        $middlewares_passed = [];
        foreach ($middlewares as $middleware) {
            if (array_key_exists($middleware, self::$middlewares)) {
                $class = self::$middlewares[$middleware];
                $initialized_middleware = new $class();
                $middlewares_passed[] = $initialized_middleware->execute($middleware);
            } else {
                return throw_expcetion("The middleware you used can't be found here");
            }
        }
        if (!empty($middlewares_passed)) {
            if (!in_array(true, $middlewares_passed)) {
                abort(401);
            }
        }
    }

    static private function verifyCsrf($method)
    {
        if (get_config("production") == false) {
            if ((Request::all("csrf") ?? "") == "skip") {
                return;
            }
        }
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            if (Session::has("csrf")) {
                if ($_SESSION['csrf'] !== Request::all('csrf')) {
                    abort(403);
                }
            } else {
                abort(403);
            }
        }
    }

    static public function verify_middleware_exists($str)
    {
        if (!array_key_exists($str, self::$middlewares)) {
            return throw_expcetion("The middleware you used can't be found in the Middleware Class. Add a new middleware in Core\Middleware");
        }
        return true;
    }
}
