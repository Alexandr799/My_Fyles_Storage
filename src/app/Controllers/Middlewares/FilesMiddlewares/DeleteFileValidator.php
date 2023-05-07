<?php

namespace App\Controllers\Middlewares\FilesMiddlewares;

use App\Custom\DataBase;
use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;

class DeleteFileValidator extends Controller
{
    public function handle(Request $req, string $method)
    {
        $db = DataBase::create();
        $file = $db->quary(
            "SELECT * FROM files WHERE id=:id AND owner_user_id=:owner",
            ['id' => $req->getArg('id'), 'owner' => Response::getSession('id')]
        );

        if (!$file['success']) Response::json(['error'=>'Что то пошло не так!'], 500);
        if (count($file['data']) === 0) Response::json(['error'=>'Файла не существует или вы не имеете к нему доступ!'], 404);

        $req->setInProps('fileName', $file['data'][0]['name']);
        $this->nextController($req, $method);
    }
}