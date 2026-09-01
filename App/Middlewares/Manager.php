<?php

namespace App\Middlewares;

use Core\Middleware;
use Core\Auth;

class Manager extends Middleware
{
    function __construct() {}

    function execute()
    {
        return (new Auth)->hasRole("manager");
    }
}
