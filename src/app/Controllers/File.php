<?php

namespace App\Controllers;

use App\Custom\FileStorage;
use App\Custom\DataBase;
use App\Entities\Logger;
use App\Entities\Request;
use App\Entities\Response;
use App\Errors\DataBaseException;
use App\Errors\FileStorageException;
use App\Interfaces\Controller;

class File extends Controller
{
    public function fileAll(Request $req)
    {
        $files = DataBase::create()->quary(
            "SELECT f.id as file_id, d.pwd as pwd, f.name as file_name, d.id as directory_id, 
            CONCAT('/download/', f.id) as download_link 
            FROM files as f
            INNER JOIN directories as d on d.id = f.directory
            WHERE f.owner_user_id=:id",
            ['id' => Response::getSession('id')]
        );
        if (!$files['success']) Response::json(['error' => 'Что то пошло не так...'], 500);

        Response::json($files['data']);
    }
    public function file(Request $req)
    {
        $files = DataBase::create()->quary(
            "SELECT f.id as file_id, d.pwd as pwd, f.name as file_name, d.id as directory_id,
            CONCAT('/download/', f.id) as download_link 
            FROM files as f
            INNER JOIN directories as d on d.id = f.directory
            WHERE f.owner_user_id=:owner_id AND f.id=:id",
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
        } catch (DataBaseException $e) {
            $db->cancelTransaction();
            Logger::printLog($e->getMessage(), 'db');
            Response::json(['error' => 'Не удалось создать файл!'], 500);
        } catch (FileStorageException $e) {
            $db->cancelTransaction();
            Logger::printLog($e->getMessage(), 'file');
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
            Response::json(['delete' => true], 200);
        } catch (DataBaseException $e) {
            $db->cancelTransaction();
            Logger::printLog($e->getMessage(), 'db');
            Response::json(['error' => 'Не удалось удалить файл!'], 500);
        } catch (FileStorageException $e) {
            $db->cancelTransaction();
            Logger::printLog($e->getMessage(), 'file');
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
        } catch (DataBaseException $e) {
            $db->cancelTransaction();
            Logger::printLog($e->getMessage(), 'db');
            Response::json(['error' => 'Не удалось обновить файл'], 500);
        } catch (FileStorageException $e) {
            $db->cancelTransaction();
            Logger::printLog($e->getMessage(), 'file');
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

        $db->startTransaction();
        try {
            $files = FileStorage::getAllFileRecursive($req->getProps('id'), $db);

            $db->quaryTransaction(
                'DELETE FROM `directories` WHERE id = :id AND owner_user_id=:owner',
                ['id' => $req->getArg('id'), 'owner' => Response::getSession('id')]
            );

            FileStorage::deleteFileAll($files);
            $db->acceptTransaction();
            Response::json(['delete' => true]);
        } catch (DataBaseException $e) {
            $db->cancelTransaction();
            Logger::printLog($e->getMessage(), 'db');
            Response::json(['error' => 'Не удалось удалить файл!'], 500);
        } catch (FileStorageException $e) {
            $db->cancelTransaction();
            Logger::printLog($e->getMessage(), 'file');
            Response::json(['error' => 'Не удалось удалить файл!'], 500);
        }
    }


    public function shareFile(Request $req)
    {
        $db = DataBase::create();
        $db->quary(
            '
            INSERT INTO `share_files` (`reader_user_id`,`file_id`) 
            SELECT :reader, :file FROM DUAL WHERE NOT EXISTS 
            (SELECT * FROM `share_files` WHERE `reader_user_id`=:reader AND `file_id`=:file LIMIT 1);
            SELECT id from `share_files` WHERE `reader_user_id`=:reader AND `file_id`=:file;
            ',
            ['reader' => $req->getArg('user_id'), 'file' => $req->getArg('id')]
        );

        $share = $db->quary(
            'SELECT id FROM `share_files` WHERE `reader_user_id`=:reader AND `file_id`=:file',
            ['reader' => $req->getArg('user_id'), 'file' => $req->getArg('id')]
        );

        if (!$share['success']) Response::json(['error' => 'Что то пошло не так!'], 500);

        $id = $share['data'][0]['id'];

        Response::json(['share' => true, 'link_to_download' => "/share_file/$id", "id_share"=>$id]);
    }

    public function shareFileInfo(Request $req)
    {
        $db = DataBase::create();
        $usersAvaible = $db->quary(
            "
            SELECT u.login as user_login,u.id as user_id,  CONCAT('/share_file/', s.id) as share_path
            FROM `share_files` as s
            LEFT JOIN users as u on u.id = s.reader_user_id
            WHERE s.file_id = :id
            ",
            ['id' => $req->getArg('id')]
        );

        if (!$usersAvaible['success']) Response::json(['error' => 'Что то пошло не так!'], 500);

        Response::json($usersAvaible['data']);
    }


    public function deleteShareFile(Request $req)
    {
        $delete = DataBase::create()->quary(
            'DELETE FROM `share_files` WHERE `reader_user_id`=:user_id AND `file_id`=:file_id;',
            ['user_id' => $req->getArg('user_id'), 'file_id' => $req->getArg('id')]
        );

        if (!$delete['success']) Response::json(['error' => 'Что то пошло не так!'], 500);

        Response::json(['delete' => true]);
    }

    public function getShareFile(Request $req)
    {
        $db = DataBase::create();
        $avaibleList = $db->quary(
            "
            SELECT s.id as id, s.reader_user_id as reader , f.owner_user_id as owner , f.name as file_name, f.id as file_id
            FROM `share_files` as s
            LEFT JOIN files as f on f.id = s.file_id
            HAVING id = :id AND (reader = :user_id OR owner = :user_id)
            ",
            ['user_id' => Response::getSession('id'), 'id' => $req->getArg('id')]
        );

        if (!$avaibleList['success']) Response::json(['error' => 'Что то пошло не так!'], 500);

        if (count($avaibleList['data']) === 0) Response::json(['error' => 'Файла не существует или вы не имеете к нему доступ!'], 403);

        $id = $avaibleList['data'][0]["file_id"];
        $fileName = $avaibleList['data'][0]["file_name"];
        FileStorage::sendFile($fileName, $id);
    }

    public function getSelfFile(Request $req)
    {
        $db = DataBase::create();
        $avaibleList = $db->quary(
            "SELECT * FROM `files` 
            WHERE id = :id AND owner_user_id =:user_id",
            ['user_id' => Response::getSession('id'), 'id' => $req->getArg('id')]
        );

        if (!$avaibleList['success']) Response::json(['error' => 'Что то пошло не так!'], 500);

        if (count($avaibleList['data']) === 0) Response::json(['error' => 'Файла не существует или вы не имеете к нему доступ!'], 403);

        $id = $avaibleList['data'][0]["id"];
        $fileName = $avaibleList['data'][0]["name"];
        FileStorage::sendFile($fileName, $id);
    }
}
