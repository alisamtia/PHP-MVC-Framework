<?php

namespace Core;

use App\Models\Limit;

class LimitService
{
    public function __construct() {}

    protected static function create(string $identifier, string $type, string $attempts, string $last_attempt)
    {
        $fetched_record = Limit::find($identifier, $type);
        if ($fetched_record) {
            return;
        }
        Limit::create($identifier, $type, $attempts, $last_attempt);
    }

    public static function verify_attempts($identifier, $type, $max_attempts, int $time_interval)
    {
        $fetched_record = Limit::find($identifier, $type);
        if (!$fetched_record) {
            return true;
        }
        $last_attempt = strtotime($fetched_record['last_attempt']);
        $time_now = strtotime(now());

        if ($fetched_record['attempts'] >= $max_attempts &&  ($time_now - $last_attempt) < $time_interval) {
            return false;
        }
        if ($fetched_record['attempts'] >= $max_attempts) {
            Limit::edit(
                compact('identifier', 'type'),
                [
                    'attempts' => 0,
                    'last_attempt' => now()
                ]
            );
        }
        return true;
    }

    public static function increamentAttempts(string $identifier, string $type, $total_attempts)
    {
        $fetched_record = Limit::find($identifier, $type);
        if (!$fetched_record) {
            self::create($identifier, $type, $total_attempts, now());
            return true;
        }

        $last_attempt = now();
        $attempts = $fetched_record['attempts'] + 1;
        Limit::edit(
            compact('identifier', 'type'),
            compact('attempts', 'last_attempt'),
        );
        return true;
    }
}
