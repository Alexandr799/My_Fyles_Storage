<?php

namespace App\Custom;

class File
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function save(string $path)
    {
        // move_uploaded_file()
    }
}
