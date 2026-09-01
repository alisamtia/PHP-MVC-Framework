<?php

namespace App\Validators;

use App\Validators\Base;

class ListingValidator extends Base
{
    function store(string $title, string $category)
    {
        $this->vd->str($title, 20, 80, "title");
        $this->vd->str($category, 1, 100, "category");


        return $this->errors();
    }

    function editStepTwo($effort, $description, $included)
    {
        $this->vd->float($effort, 0.5, 1000, 'effort');
        $this->vd->str($description, 200, 700, "description");
        $this->vd->array($included, 15, 60, 1, 6, 'included'); // change minimum each to 10

        return $this->errors();
    }

    function editStepThree($offerings)
    {
        $this->vd->array($offerings, 10, 60, 1, 6, 'offerings');

        return $this->errors();
    }

    function editStepFour($status)
    {
        $this->vd->str($status, 1, 10, 'status');
        $this->vd->in_array($status, ['public', 'draft', 'paused'], 'status');

        return $this->errors();
    }

    function imageUpload($img, $required)
    {
        $this->vd->fileUploaded($img, ['image/jpeg', "image/png"], 5000, true, $required, 'photos_input');

        return $this->errors();
    }
}
