<?php

namespace App\Middlewares;

use Core\Auth;
use Core\Middleware;

class Guest extends Middleware
{
    function __construct() {}

    function execute()
    {
        if ((new Auth)->user()) {
            return false;
        }
        return true;
    }
}
