<?php

namespace Core;

use App\Models\LoginToken;
use App\Services\AuthService;
use App\Models\User;
use Core\Session;

class Auth
{
    public $user = false;

    // login without any verification
    static function login($email)
    {
        $user = User::find($email);
        AuthService::createSession($email, $user['role'], $user['avatar']);
    }

    static function attemptLogin(string $email, string $password, $remember_me): array
    {
        $user = User::find($email);

        if ($user) {
            if (!password_verify($password, $user['password'])) {
                return ['success' => false, 'user_exist' => true];
            }
        } else {
            return ['success' => false, 'user_exist' => false];
        }

        $token = bin2hex(random_bytes(32));
        if ($remember_me) {
            AuthService::createCookie($token);

            $tokenHash = hash('sha256', $token);
            $expires_at = now(strtotime('+30 days', time()));
            LoginToken::create($tokenHash, $expires_at, $user['id']);
        }
        AuthService::createSession($email, $user['role'], $user['avatar']);
        return ['success' => true, 'user_exist' => true];
    }

    static function boot()
    {
        $user = $_SESSION['user'] ?? false;
        if (!$user) {
            $token = $_COOKIE['remember_token'] ?? false;
            if ($token) {
                $match = LoginToken::findAlive(hash('sha256', $token));
                if ($match) {
                    $user = User::findById($match['user']);
                    AuthService::createSession($user['email'], $user['role'], $user['avatar']);
                    $user = $_SESSION['user'];
                }
            }
        }
    }

    static function user()
    {
        if (Session::has("user")) {
            return Session::get("user");
        }
        return false;
    }

    static function logout()
    {
        Session::destroy();
        if (isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
            $token_match = LoginToken::findAlive(hash('sha256', $token));
            if ($token_match) {
                $token_match = LoginToken::delete(hash('sha256', $token));
            }
            setcookie("remember_token", '', time() - 3600);
        }
    }

    // Check current logged_in user role matches the input value
    static function hasRole(string $role): bool
    {
        $user = Session::get('user', false);
        if ($user) {
            return $role == $user['role'];
        }
        return false;
    }
}
