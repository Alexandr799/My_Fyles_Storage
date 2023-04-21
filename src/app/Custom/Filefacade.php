<?php

namespace App\Custom;

use App\Entities\Crypter;
use Exception;

/**
 * Summary of Filefacade
 */
class Filefacade
{

    static public function getAllFileRecursiveById(string $id): array
    {
        $path = realpath("./storage/filestorage") . "/user_storage_$id";
        if (!is_dir($path)) {
            mkdir($path);
            return [];
        }
        return static::fileRecursion($path);
    }
    

    static private function fileRecursion(string $path, $filesList = [],  string $dir = '/'): array
    {

        if (!is_dir($path)) new Exception('Directory not found!');
        $files = $filesList;
        foreach (scandir($path) as $f) {
            if ($f === '..') continue;

            if ($f === '.') continue;
            $pathToFile = $path . DIRECTORY_SEPARATOR . $f;

            if (is_file($pathToFile)) {
                $files[] = [
                    'path' => $dir . $f,
                    'name' => $f, 
                    'id'=>Crypter::encodeID($dir . $f),
                ];
            }

            if (is_dir($pathToFile)) {
                $files = static::fileRecursion($pathToFile, $files, $dir . $f . '/');
            };
        }
        return $files;
    }
}
