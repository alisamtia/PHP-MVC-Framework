<?php

namespace Core;

class Validator
{
    private $errors = [];
    public function str(string $str, int $min, int $max, string $error_varname, bool $trim = true)
    {
        if ($trim === true) {
            $str = trim($str);
        }
        if (strlen($str) === 0) {
            $this->errors[$error_varname] = "$error_varname is required!";
        } elseif (strlen($str) < $min) {
            $this->errors[$error_varname] = "The $error_varname must be atleast more than $min characters";
        } elseif (strlen($str) > $max) {
            $this->errors[$error_varname] = "The $error_varname must be less than $max characters";
        }
    }

    public function float(string $str, $min, $max, string $error_varname, bool $trim = true)
    {
        if ($trim === true) {
            $str = trim($str);
        }
        if (strlen($str) === 0) {
            $this->errors[$error_varname] = "$error_varname is required!";
        } elseif (!floatval($str)) {
            $this->errors[$error_varname] = "The $error_varname must be valid integer(float)";
        } elseif (floatval($str) < $min) {
            $this->errors[$error_varname] = "The $error_varname must be more than $min";
        } elseif (floatval($str) > $max) {
            $this->errors[$error_varname] = "The $error_varname must be less than $max";
        }
    }

    public function array(array $arr, $min_each, $max_each, $total_min, $total_max, $error_varname, bool $required = true)
    {
        if (count($arr) == 0 || !is_array($arr)) {
            $this->errors[$error_varname] = "The $error_varname are required!";
            return;
        }
        if (count($arr) < $total_min) {
            $this->errors[$error_varname] = "The total $error_varname must be more than $total_min";
            return;
        }
        if (count($arr) > $total_max) {
            $this->errors[$error_varname] = "The total $error_varname must be less than or equal to $total_max";
            return;
        }
        foreach ($arr as $item) {
            $this->str($item, $min_each, $max_each, $error_varname);
        }
    }

    public function in_array($needle, $haystack, $error_varname)
    {
        if (!in_array($needle, $haystack)) {
            $this->errors[$error_varname] = "Select $error_varname (entries) only!!";
        }
    }

    public function password(string $password, string $error_varname = "password")
    {
        $this->str($password, 8, 254, $error_varname);
    }
    public function confirm_password(string $password, string $confirm_password, $error_varname = "confirm_password")
    {
        if ($password !== $confirm_password) {
            $this->errors[$error_varname] = "Password and confirm password does not match!";
        }
    }

    public function fileUploaded($file, array $types, int $max_size_in_kb, bool $mustBeImage = false, $required = true, $file_name = "file")
    {
        if ($required) {
            $this->fileUploadExists($file, $file_name);
        }
        if (!isset($file) || ($file['error'] ?? false) === UPLOAD_ERR_NO_FILE || !$file) {
            return;
        }
        $this->limitFileSize($file, $max_size_in_kb, $file_name);
        $this->fileType($file, $types, $file_name);

        if ($mustBeImage) {
            $this->mustBeImage($file, $file_name);
        }
    }

    public function email(string $email, string $error_varname = "email")
    {
        $email = trim(strtolower($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$error_varname] = "Valid Email is required";
        }
    }

    public function str_to_array(string $str, int $each_min, int $each_max, int $min_entries, $max_entries, string $error_varname = "skills",  string $seprator = ",")
    {
        $str = trim($str);

        $array = explode(",", $str);
        if (count($array) < $min_entries) {
            $this->errors[$error_varname] = "Minimum of $error_varname are required!";
        }
        if (count($array) > $max_entries) {
            $this->errors[$error_varname] = "Maximum number of $error_varname is $max_entries. Your Entries Exceed minimum limit!";
        }

        foreach ($array as $item) {
            $str_len = strlen(trim($item));
            if ($str_len < $each_min) {
                $this->errors[$error_varname] = "In $error_varname, each item('$item') must be greater than $each_min characters";
            } elseif ($str_len > $each_max) {
                $this->errors[$error_varname] = "In $error_varname, each item('$item') must be less than $each_max characters";
            }
        }

        if ($min_entries > 0) {
            $this->str($str, 1, 999, 'skills');
        }
    }

    public function mustBeImage($fileToUpload, string $error_varname)
    {
        if (
            empty($fileToUpload['tmp_name']) ||
            !getimagesize($fileToUpload['tmp_name'])
        ) {
            $this->errors[$error_varname] = "The uploaded file must be an image!";
        }
    }

    // size in kilobytes comparison
    public function limitFileSize($fileToUpload, $max_size, string $error_varname)
    {
        if ($fileToUpload["size"] > 1024 * $max_size) {
            $this->errors[$error_varname] = "Sorry, your file is too large.";
        }
    }

    function fileType($fileToUpload, array $allowed_types, string $error_varname)
    {
        // $target_file = BASE_PATH . "uploads/" . basename($fileToUpload["name"]);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fileToUpload['tmp_name']);
        if (!in_array($mime, $allowed_types)) {
            $this->errors[$error_varname] = "Valid file type must be uploaded!";
        }
    }

    public function fileUploadExists($file, string $error_varname)
    {
        if (!isset($file) || ($file['error'] ?? false) === UPLOAD_ERR_NO_FILE || !$file) {
            $this->errors[$error_varname] = "Please upload a file!";
        }
    }

    public function username(string $username, string $error_varname = "username")
    {
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', trim($username))) {
            $this->errors[$error_varname] = "Valid $error_varname is required";
        }
    }

    public function get_errors()
    {
        return $this->errors;
    }
}
