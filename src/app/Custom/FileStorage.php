<?php

namespace App\Custom;

use Exception;

class FileStorage
{
    public static function renameFile(string $fileName, int | string $id,  string $newname)
    {
        $name = realpath('./storage/filestorage') . "/$id" . '_' . $fileName;
        $new = realpath('./storage/filestorage') . "/$id" . '_' . $newname;
        $result = rename($name, $new);
        if (!$result) throw new Exception("Не удалось переименовать файл!");
    }

    public static function addFile(array $file,int | string $idFile)
    {
        $result = move_uploaded_file($file["tmp_name"], realpath('./storage/filestorage') . "/$idFile" . '_' . $file['name']);
        if (!$result) throw new Exception("Не удалось переименовать файл!");
    }
}
