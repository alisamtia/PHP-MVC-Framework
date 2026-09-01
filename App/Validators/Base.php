<?php

namespace App\Validators;

use Core\Validator;

class Base
{
    protected Validator $vd;

    public function __construct()
    {
        $this->vd = new Validator();
    }

    protected function errors()
    {
        $errors = $this->vd->get_errors();
        return !empty($errors) ? $errors : null;
    }
}
