<?php

namespace Core;

use Core\Router;
use Core\Auth;
use Core\Session;
use Core\Request;
use Core\View;
use Core\RedirectResponse;


class App
{

    public function run()
    {
        self::bootstrap();

        $router = new Router();
        require BASE_PATH . "routes.php";
        $request = Request::capture();
        $response = $router->route($request);
    }
    protected function bootstrap()
    {
        require BASE_PATH . "functions.php";
        Session::start();
        Auth::boot();

        View::flashData();
        RedirectResponse::capturePreviousUrl();

        // session_set_cookie_params([
        //     'httponly' => true,
        //     'secure' => true,
        //     'samesite' => 'Lax'
        // ]);
    }
}
