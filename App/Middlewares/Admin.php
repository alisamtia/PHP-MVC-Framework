<?php

namespace App\Middlewares;

use Core\Middleware;
use Core\Auth;

class Admin extends Middleware
{
    function __construct() {}

    function execute()
    {
        return (new Auth)->hasRole("admin");
    }
}
