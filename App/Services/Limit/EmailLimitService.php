<?php

namespace App\Services\Limit;

use Core\LimitService;


class EmailLimitService
{
    public static function verify($email, $identifier = 'login', $attempts = 5, $time = 300)
    {
        return LimitService::verify_attempts($email, "{$identifier}_email", $attempts, $time);
    }
    public static function increase($email, $identifier = 'login', $attempts = 5)
    {
        return LimitService::increamentAttempts($email, "{$identifier}_email", $attempts);
    }
}
