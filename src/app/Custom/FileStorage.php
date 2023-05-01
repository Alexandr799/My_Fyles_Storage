<?php

namespace App\Custom;

use App\Entities\DataBase;
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

    public static function deleteFileByName(string $filename): void
    {
        $result = unlink(realpath('./storage/filestorage') . "/$filename");
        if (!$result) throw new Exception("Не удалось удалить файл!");
    }


    private static function concatStringElement(string $first, string $second, $sep): array
    {
        $firstExp  = explode($sep, $first);
        $secondExp  = explode($sep, $second);
        return array_map(function ($id, $name) {
            return $id . '_' . $name;
        }, $firstExp,  $secondExp);
    }

    public static function getAllFileRecursive(int | string $id, null | string | int $parent_dir_id, DataBase $db,  $files = []): array
    {
        if (empty($parent_dir_id)) return $files;

        $filesFromDb= $db->quaryTransaction(
            "SELECT id, name FROM `files`  WHERE directory = :id",
            ['id' => $id]
        );

        $files = array_merge($files, $filesFromDb);

        $dirInfo = $db->quaryTransaction(
            "SELECT id, pwd, parent_dir_id FROM `directories`  WHERE id = :id",
            ['id' => $parent_dir_id]
        )[0];

        $filesFromDb = static::getAllFileRecursive($dirInfo['id'], $dirInfo['parent_dir_id'], $db, $files);

        return $filesFromDb;
    }
}
