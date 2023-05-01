<?php

namespace App\Controllers\Middlewares;

use App\Entities\DataBase;
use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;

class FileAddValidator extends Controller
{
    public function handle(Request $req, string $method)
    {
        if (empty($req->getFile('file'))) Response::json(['error' => 'Вы не добавили файл'], 400);

        $file = $req->getFile('file');

        if ($file['error'] !== 0) Response::json(['error' => 'Ошибка при загрузке файла'], 400);


        if ($file['size'] > 2000000000) Response::json(['error' => 'Ограничение по загрузке файла 2 гигабайта!'], 400);

        if (empty($req->getParam('dir_id'))) Response::json(['error' => 'Укажите id директории для хранения!'], 400);

        $directory = DataBase::create()->quary(
            "SELECT u.id as user_id, d.id as directory_id, d.pwd as pwd
            FROM `directories` as d
            INNER JOIN users as u
            on d.owner_user_id = u.id
            HAVING user_id = :user_id AND directory_id = :dir_id;",
            ['dir_id' => $req->getParam('dir_id'), 'user_id' => Response::getSession('id')]
        );

        if (!$directory['success']) Response::json(['error' => 'Что то пошло не так!'], 500);

        if (count($directory['data']) === 0) Response::json(['error' => 'Такой папки нет у вас в хранилище!'], 400);

        $fileData = DataBase::create()->quary(
            "SELECT f.id as files_id, d.id as directory_id, f.name as file_name
            FROM `files` as f
            INNER JOIN `directories` as d
            on f.directory = d.id
            HAVING file_name = :file_name AND directory_id = :directory_id;",
            ['directory_id' => $req->getParam('dir_id'), 'file_name' => $file['name']]
        );

        if (count($fileData['data']) > 0) Response::json(['error' => 'Файл с таким названием уже существует!'], 400);

        $this->nextController($req, $method);
    }
}
