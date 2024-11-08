<?php

use Illuminate\Support\Facades\Storage;

trait AttachmentValidator
{
    private $errors = [];

    public function validateAttachment(string $path): bool
    {
        // Check if the file exists at the given path
        if (isset($path)) {
            return true;
        } else {
            $errors[] = [
                'path' => $path,
                'message' => 'file tidak ditemukan'
            ];

            return false;
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }
}