<?php

namespace App\Controllers\Middlewares;

use App\Entities\DataBase;
use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;

class UpdateFileValidator extends Controller
{
    public function handle(Request $req, string $method)
    {
        if (empty($req->getParam('id'))) Response::json(['error' => 'Укажите id файла'], 400);

        if (empty($req->getParam('name')) && empty($req->getParam('path'))) {
            Response::json(['error' => 'Не указано ни новое имя файла, ни папка для перемещения'], 400);
        }

        if (!empty($req->getParam('path'))) {
            $dirs = DataBase::create()->quary(
                'SELECT id
                FROM  `directories` 
                WHERE path=:path AND owner_user_id=:id',
                [
                    'owner_id' => Response::getSession('id'),
                    'id' => $req->getParam('id')
                ]
            );
            if (!$dirs['success']) Response::json(['error' => 'Что то пошло не так...'], 500);
            if (count($dirs['data'])) Response::json(['error' => 'Такой директори не существует!'], 400);
        }

        $files = DataBase::create()->quary(
            'SELECT id, name
            FROM files 
            WHERE owner_user_id=:owner_id AND id=:id',
            [
                'owner_id' => Response::getSession('id'),
                'id' => $req->getParam('id')
            ]
        );

        if (!$files['success']) Response::json(['error' => 'Что то пошло не так...'], 500);

        if (count($files['data']) === 0) Response::json(['error' => 'У вас нет такого файла!'], 404);

        $req->setInProps('fileName', $files['data']['name']);

        $this->nextController($req, $method);
    }
}
