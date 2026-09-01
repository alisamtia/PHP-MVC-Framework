<?php

namespace App\Validators;

use App\Validators\Base;

class AuthValidator extends Base
{
    function register(string $username, string $name, string $email, string $pass, string $confirm_pass, $avatar)
    {
        $this->vd->username($username, "username");
        $this->vd->str($name, 6, 100, "name");
        $this->vd->email($email);
        $this->vd->password($pass);
        $this->vd->confirm_password($pass, $confirm_pass);
        $this->vd->fileUploaded($avatar, ['image/jpeg', "image/png"], 2000, true, "avatar");

        return $this->errors();
    }

    function update(string $name, string $bio, string $location, string $skills, $avatar)
    {
        $this->vd->str($bio, 10, 100, "bio");
        $this->vd->str($name, 6, 100, "name");
        $this->vd->str($location, 8, 70, "location");
        $this->vd->str_to_array($skills, 2, 30, 1, 8);
        $this->vd->fileUploaded($avatar, ['image/jpeg', "image/png"], 2000, true, false, 'avatar');




        return $this->errors();
    }

    function pass_change($pass, $confirm_pass)
    {
        $this->vd->password($pass);
        $this->vd->confirm_password($pass, $confirm_pass);

        return $this->errors();
    }

    function login(string $email, string $password)
    {
        $this->vd->email($email);
        $this->vd->password($password);

        return $this->errors();
    }

    function email($email)
    {
        $this->vd->email($email);
        return $this->errors();
    }
}
