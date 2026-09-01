<?php

namespace App\Services;

use App\Models\User;
use App\Validators\AuthValidator;
use Core\Auth;

class UserService
{
    static function register($username, $name, $email, $password, $confirm_password, $avatar)
    {
        $errors = (new AuthValidator())->register($username, $name, $email, $password, $confirm_password, $avatar);
        if ($errors) {
            back()->with('error', $errors)->withInput()->redirect();
        }

        if (!empty($errors)) {
            back()->with('error', $errors)->withInput()->redirect();
        }
        $file_path = FileUploadService::upload($avatar, "/Public/uploads/avatars/")['filename'];

        $usernameExists = User::usernameExists($username) ? true : false;
        if ($usernameExists) {
            $errors['username'] = "Username Already Exists";
            back()->with('error', $errors)->withInput()->redirect();
        }

        $emailExists = User::emailExists($email);
        if ($emailExists) {
            $errors['email'] = "Email Already Exists";
            back()->with('error', $errors)->withInput()->redirect();
        }
        $created_at = now();

        $hashed_pass = password_hash($password, PASSWORD_BCRYPT);
        User::create($username, $name, $email, $hashed_pass, 'user', $file_path, $created_at);

        Auth::login($email);
        redirect("/");
    }
}
