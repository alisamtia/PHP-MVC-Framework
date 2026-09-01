<?php

namespace App\Services\Limit;

use Core\LimitService;


class IpLimitService
{
    public static function getUserIP()
    {
        $ip = get_config('cloudflare')
            ? ($_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? null)
            : ($_SERVER['REMOTE_ADDR'] ?? null);

        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : null;
    }

    public static function verify($identifier = "login", $attempts = 5, $time = 300, $ip = null, $automatic_redirect = true)
    {
        $bool = LimitService::verify_attempts(($ip ?? self::getUserIP()), "{$identifier}_ip", $attempts, $time);

        if ($automatic_redirect) {
            if (!$bool) {
                back()->notify(
                    "error",
                    "Too many requests! Slow down!",
                    "Too many attempts from same Ip... Please slow down a bit"
                )->withInput()->redirect();
            }
        }

        return $bool;
    }
    public static function increase($identifier = "login", $attempts = 5, $ip = null, $automatic_redirect = true)
    {
        $bool = LimitService::increamentAttempts(($ip ?? self::getUserIP()), "{$identifier}_ip", $attempts);

        if ($automatic_redirect) {
            if (!$bool) {
                back()->withInput()->notify(
                    "error",
                    "Too many requests! Slow down!",
                    "Too many attempts on same Email... Please slow down a bit"
                )->redirect();
            }
        }
        return $bool;
    }
}
