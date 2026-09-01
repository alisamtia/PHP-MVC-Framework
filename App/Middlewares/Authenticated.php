<?php

namespace App\Middlewares;

use Core\Auth;
use Core\Middleware;

class Authenticated extends Middleware
{
    function __construct() {}

    function execute()
    {
        return (new Auth)->user();
    }
}
