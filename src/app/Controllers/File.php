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
            'SELECT f.id as file_id, d.pwd as pwd, f.name as file_name, d.id as directory_id
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
            'SELECT f.id as file_id, d.pwd as pwd, f.name as file_name, d.id as directory_id
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
                ['name' => $file['name'], 'dir_id' => $req->getParam('dir_id'), 'user_id' => Response::getSession('id')]
            );
            $idFile = $db->lastRowID();
            FileStorage::addFile($file, $idFile);
            $db->acceptTransaction();
            Response::json([
                'created_file' => 'файл успешно создан',
                'id' => $idFile
            ]);
        } catch (Exception $e) {
            $db->cancelTransaction();
            Logger::printLog($e->getMessage(), 'db');
            Response::json(['error' => 'Не удалось создать файл!'], 500);
        }
    }

    public function deleteFile(Request $req)
    {
        $db = DataBase::create();
        $db->startTransaction();
        try {
            $db->quaryTransaction(
                'DELETE FROM files WHERE id=:id AND owner_user_id=:owner',
                ['id' => $req->getArg('id'), 'owner' => Response::getSession('id')]
            );
            FileStorage::deleteFile($req->getProps('fileName'), $req->getArg('id'));
            $db->acceptTransaction();
            Response::json(['delete' => true], 500);
        } catch (Exception $e) {
            $db->cancelTransaction();
            Logger::printLog($e->getMessage(), 'db');
            Response::json(['error' => 'Не удалось удалить файл!'], 500);
        }
    }

    public function updateFile(Request $req)
    {
        $db = DataBase::create();
        $db->startTransaction();
        try {
            $updateParams = [];
            $updatePath = null;
            if (!empty($req->getParam('dir_id'))) {
                $updatePath = 'directory=:dir_id';
                $updateParams['dir_id'] = $req->getParam('dir_id');
            }
            $updateName = null;
            if (!empty($req->getParam('name'))) {
                $updateName = 'name=:name';
                $updateParams['name'] = $req->getParam('name');
            }
            $sep  = isset($updateName) && isset($updatePath) ? ',' : '';
            $db->quaryTransaction(
                "UPDATE files SET $updatePath $sep $updateName WHERE id=:id",
                array_merge(['id' => $req->getParam('id')], $updateParams)
            );
            if ($req->getParam('name')) {
                FileStorage::renameFile($req->getProps('fileName'), $req->getParam('id'), $req->getParam('name'));
            }
            $db->acceptTransaction();
            Response::json(['edit_file' => true]);
        } catch (Exception $e) {
            $db->cancelTransaction();
            Logger::printLog($e->getMessage(), 'db');
            Response::json(['error' => 'Не удалось обновить файл'], 500);
        }
    }

    public function addDirectory(Request $req)
    {
        $db = DataBase::create();
        $newDir = $db->quary(
            'INSERT INTO `directories` (pwd, owner_user_id, parent_dir_id) VALUES (:pwd, :owner, :parent)',
            [
                'pwd' => $req->getProps('pwd'),
                'owner' => Response::getSession('id'),
                'parent' => $req->getParam('parent_dir_id')
            ]
        );

        if (!$newDir['success']) Response::json(['error' => 'Что то пошло не так...'], 500);

        Response::json(['created' => true, 'pwd' => $req->getProps('pwd'), 'id' => $db->lastRowID()]);
    }

    public function renameDirectory(Request $req)
    {
        $db = DataBase::create();

        $update = $db->quary(
            'UPDATE `directories` SET pwd = :pwd WHERE id=:id',
            ['pwd' => $req->getProps('pwd'), 'id' => $req->getParam('id')]
        );

        if (!$update['success']) Response::json(['error' => 'Что пошло не так!'], 500);

        Response::json(['edit_dir' => true], 200);
    }

    public function infoDirectory(Request $req)
    {
        $db = DataBase::create();

        $dirInfo = $db->quary(
            "SELECT  t1.directory_id as id , t1.directory_pwd as pwd, 
            t1.children_dirs as dirs, t2.children_files as files, t1.owner as owner
            FROM 
            (SELECT d.id as directory_id, d.pwd as directory_pwd, d.owner_user_id as owner, 
            GROUP_CONCAT(c.pwd SEPARATOR '===') as children_dirs
            FROM `directories` as d 
            LEFT JOIN directories as c on c.parent_dir_id = d.id 
            GROUP BY d.id) as t1
            INNER JOIN 
            (SELECT d.id as directory_id, d.pwd as directory_pwd, 
            GROUP_CONCAT(f.name SEPARATOR '===') as children_files
            FROM `directories` as d 
            LEFT JOIN files as f on f.directory = d.id 
            GROUP BY d.id) as t2 
            ON t1.directory_id = t2.directory_id
            HAVING id = :id AND owner = :owner",
            ['owner' => Response::getSession('id'), 'id' => $req->getArg('id')]
        );

        if (!$dirInfo['success']) Response::json(['error' => 'Что то пошло не так...'], 500);
        if (count($dirInfo['data']) === 0) Response::json(['error' => 'Такой директории нет, или вы не имеете к нему доступа'], 404);

        $data = $dirInfo['data'][0];


        Response::json([
            'id' => $data['id'],
            'pwd' => $data['pwd'],
            'owner' => $data['owner'],
            'children_directories' => isset($data['dirs']) ? explode('===', $data['dirs']) : [],
            'children_files' => isset($data['files']) ? explode('===', $data['files']) : [],
        ]);
    }

    public function deleteDirectory(Request $req)
    {
        $db = DataBase::create();
        $dirInfo = $db->quary(
            "SELECT id, pwd, parent_dir_id FROM `directories`  WHERE id = :id AND owner_user_id=:owner",
            ['id' => $req->getArg('id'), 'owner' => Response::getSession('id')]
        );

        if (!$dirInfo['success']) Response::json(['error' => 'Что то пошло не так...'], 500);
        if (count($dirInfo['data']) === 0) Response::json(['error' => 'Такой директории нет, или вы не имеете к нему доступа'], 404);
        $dirInfo  = $dirInfo['data'];

        $files = FileStorage::getAllFileRecursive($dirInfo['id'], $dirInfo['parent_dir_id']);
        
    }
}
