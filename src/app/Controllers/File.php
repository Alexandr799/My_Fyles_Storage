<?php

namespace App\Controllers;

use App\Custom\FileStorage;
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
            'SELECT f.id as file_id, d.path as path, f.name as file_name, d.id as directory_id
            FROM files as f
            INNER JOIN directories as d on d.id = f.directory
            WHERE f.owner_user_id=:id',
            ['id' => Response::getSession('id')]
        );
        if (!$files['success']) Response::json(['error' => 'Что то пошло не так...'], 500);

        Response::json($files['data']);
    }
    public function file(Request $req)
    {
        $files = DataBase::create()->quary(
            'SELECT f.id as file_id, d.path as path, f.name as file_name, d.id as directory_id
            FROM files as f
            INNER JOIN directories as d on d.id = f.directory
            WHERE f.owner_user_id=:owner_id AND f.id=:id',
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
                ['name' => $file['name'], 'dir_id' => $req->getProps('directory_id'), 'user_id' => Response::getSession('id')]
            );
            $idFile = $db->lastRowID();
            FileStorage::addFile($file, $idFile);
            $db->acceptTransaction();
        } catch (Exception $e) {
            $db->cancelTransaction();
            Logger::printLog($e->getMessage(), 'db');
            Response::json(['error' => 'Не удалось создать пользователя!'], 500);
        }

        Response::json([
            'created_file' => 'файл успешно создан',
            'id' => $idFile
        ]);
    }

    public function deleteFile(Request $req)
    {
    }

    public function updateFile(Request $req)
    {
        $db = DataBase::create();
        $db->startTransaction();
        try {
            $updateParams = [];
            $updatePath = '';
            if (!empty($req->getParam('path'))) {
                $updatePath = 'path=:path'; 
                $updateParams['path'] = $req->getParam('path');
            }
            $updateName = '';
            if (!empty($req->getParam('name'))) {
                $updateName = 'name=:name'; 
                $updateParams['path'] = $req->getParam('path');
            }
            $db->quaryTransaction(
                "UPDATE files SET $updatePath, $updateName WHERE id=:id",
                array_merge(['id'=>$req->getParam('id')], $updateParams)
            );
            if ($req->getParam('name')) {
                FileStorage::renameFile($req->getProps('fileName'), $req->getParam('id'), $req->getParam('name'));
            }
            $db->acceptTransaction();
        } catch (Exception $e) {
            $db->cancelTransaction();
            Logger::printLog($e->getMessage(), 'db');
            Response::json(['error' => 'Не удалось создать пользователя!'], 500);
        }

        Response::json(['edit_file' => true]);
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
