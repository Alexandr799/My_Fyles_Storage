<?php

namespace App\Custom;

use App\Custom\DataBase;
use App\Entities\Logger;
use App\Entities\Response;
use Exception;

class FileStorage
{
    public static function renameFile(string $fileName, int | string $id,  string $newname): void
    {
        $name = realpath('./storage/filestorage') . "/$id" . '_' . $fileName;
        $new = realpath('./storage/filestorage') . "/$id" . '_' . $newname;
        $result = rename($name, $new);
        if (!$result) throw new Exception("Не удалось переименовать файл!");
    }

    public static function addFile(array $file, int | string $idFile): void
    {
        $result = move_uploaded_file($file["tmp_name"], realpath('./storage/filestorage') . "/$idFile" . '_' . $file['name']);
        if (!$result) throw new Exception("Не удалось создать файл!");
    }

    public static function deleteFile(string $file, int | string $idFile): void
    {
        $result = unlink(realpath('./storage/filestorage') . "/$idFile" . '_' . $file);
        if (!$result) throw new Exception("Не удалось удалить файл!");
    }


    public static function deleteFileAll(array $filesNames): void
    {
        foreach ($filesNames as $f) {
            static::deleteFile($f['name'], $f['id']);
        }
    }

    public static function deleteFileByName(string $filename): void
    {
        $result = unlink(realpath('./storage/filestorage') . "/$filename");
        if (!$result) throw new Exception("Не удалось удалить файл!");
    }

    public static function getAllFileRecursive(int | string $id, DataBase $db,  $files = []): array
    {
        $filesCurrentDir = $db->quaryTransaction(
            "SELECT id, name FROM `files`  WHERE `directory` = :id",
            ['id' => $id]
        );

        $childrenDir = $db->quaryTransaction(
            "SELECT id FROM `directories`  WHERE `parent_dir_id` = :id",
            ['id' => $id]
        );

        if (count($childrenDir) === 0) return $filesCurrentDir;

        foreach ($childrenDir as $child) {
            $filesCurrentDir  = array_merge($filesCurrentDir, static::getAllFileRecursive($child['id'], $db));
        }

        return  $filesCurrentDir;
    }

    public static function sendFile($filename, $id){
        $true_filename = $id . '_' . $filename;
        $path = realpath("./storage/filestorage") . "/$true_filename";

        if (!file_exists($path)) {
            Logger::printLog("Файла - $filename c id - $id не существует!", 'file');
            Response::json(['error'=>'Что то пошло нет так!'], 500);
        };

        Response::upload($path, $filename);
    }
}
