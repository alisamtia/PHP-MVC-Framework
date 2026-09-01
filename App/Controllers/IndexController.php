<?php

namespace App\Controllers;

use App\Services\FileUploadService;
use Core\View;
use App\Models\Listing;

class IndexController
{
    public function index($var)
    {
        View::render("index");
    }

    public function tailwind()
    {
        // View::render("admin/tailwind");
    }
}

        // <progress id="bar" value="0" max="100"></progress>

        // <script>
        // let xhr = new XMLHttpRequest();
        // xhr.open("POST", "upload.php");

        // xhr.upload.onprogress = function (e) {
        //     let percent = (e.loaded / e.total) * 100;
        //     document.getElementById("bar").value = percent;
        // };

        // xhr.send(new FormData(document.querySelector("form")));
        // </script>
        // dd(User::create("john", "John Doe", "johndoe@gmail.com", "Sahb@679", "manager"));

        // (new Auth)->attemptLogin("123@gmail.com", "password123");
        // dd((new Auth)->hasRole("admin"));