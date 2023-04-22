<?php

namespace App\Controllers;

use App\Custom\Filefacade;
use App\Entities\DataBase;
use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;


class File extends Controller
{
    public function fileAll(Request $req)
    {
        $userId = Response::getSession('id');
        $files = DataBase::create()->quary(
            'SELECT * FROM `files` WHERE `owner_user_id`=:id',
            ['id' =>$userId]
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

        if (count($files['data']) === 0 ) Response::json(['error' => 'Такого файла нет, или вы не имеете к нему доступа'], 404);

        Response::json($files['data']);
    }

    public function addFile(Request $req)
    {
        $path = $req->getParam('path');
        $title = $req->getParam('name');

        $userId = Response::getSession('id');
        $path = realpath("./storage/filestorage") . "/user_storage_$userId";
        
        if (file_exists("$path" . "$title")) Response::json(['error'=>'Файл уже существует!'], 400);

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
