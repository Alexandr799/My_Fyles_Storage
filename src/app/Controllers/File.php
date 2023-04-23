<?php

namespace App\Controllers;

use App\Entities\DataBase;
use App\Entities\Logger;
use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;
use Exception;

class File extends Controller
{
    public function fileAll(Request $req)
    {
        $userId = Response::getSession('id');
        $files = DataBase::create()->quary(
            'SELECT * FROM `files` WHERE `owner_user_id`=:id',
            ['id' => $userId]
        );
        if (!$files['success']) Response::json(['error' => 'Что то пошло не так...'], 500);

        Response::json($files['data']);
    }
    public function file(Request $req)
    {
        $files = DataBase::create()->quary(
            'SELECT * FROM `files` WHERE `owner_user_id`=:owner_id AND `id`=:id',
            [
                'owner_id' => Response::getSession('id'),
                'id' => $req->getArg('id')
            ]
        );

        if (!$files['success']) Response::json(['error' => 'Что то пошло не так...'], 500);

        if (count($files['data']) === 0) Response::json(['error' => 'Такого файла нет, или вы не имеете к нему доступа'], 404);

        Response::json($files['data']);
    }

    public function addFile(Request $req)
    {
        $file = $req->getFile('file');
        $db = DataBase::create();
        $db->startTransaction();
        try {
            $db->quary(
                'INSERT INTO files (`name`, `directory`, `owner_user_id`) VALUES (:name, :dir_id, :user_id);',
                ['name'=>$file['name'], 'dir_id'=>$req->getProps('directory_id'), 'user_id'=>Response::getSession('id')]
            );
            $idFile = $db->lastRowID();
            move_uploaded_file($file["tmp_name"], realpath('./storage/filestorage') . "/$idFile" . '_' . $file['name']);
            $db->acceptTransaction();
        } catch (Exception $e) {
            $db->cancelTransaction();
            Logger::printLog($e->getMessage(), 'db');
            Response::json(['error' => 'Не удалось создать пользователя!'], 500);
        }

        Response::json([
            'created_file' => 'файл успешно создан',
            'id'=>$idFile
        ]);
    }

    public function deleteFile(Request $req)
    {
    }

    public function renameFile(Request $req)
    {
    }

    public function addDirectory(Request $req)
    {
    }

    public function renameDirectory(Request $req)
    {
    }

    public function infoDirectory(Request $req)
    {
    }

    public function deleteDirectory(Request $req)
    {
    }
}
