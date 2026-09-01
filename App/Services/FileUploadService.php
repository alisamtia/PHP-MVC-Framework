<?php

namespace App\Services;


class FileUploadService
{
    public static function upload(array $file, $path = "")
    {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

        $file_name = uniqid('', true) . '.' . $extension;

        $target_file = BASE_PATH . $path . $file_name;

        if (!move_uploaded_file($file['tmp_name'], $target_file)) {
            return [
                'success' => false,
                'error' => "File not Uploaded... Internal Server Error!!",
            ];
        }

        return [
            'success' => true,
            'filename' => $file_name,
            'path' => $target_file
        ];
    }
}
