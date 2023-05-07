<?php

namespace App\Controllers\Middlewares\ShareMiddlewares;

use App\Custom\DataBase;
use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;

class ShareValidator extends Controller
{
    public function handle(Request $req, string $method)
    {
        $db = DataBase::create();

        $user = $db->quary('SELECT id FROM users WHERE id=:id', ['id' => $req->getArg('user_id')]);
        if (!$user['success']) Response::json(['error' => 'Что то пошло не так!'], 500);
        if (count($user['data']) === 0) Response::json(['error' => 'Пользователя с таким id нет'], 400);

        $file = $db->quary('SELECT id FROM files WHERE id=:id', ['id' => $req->getArg('id')]);
        if (!$user['success']) Response::json(['error' => 'Что то пошло не так!'], 500);
        if (count($file['data']) === 0) Response::json(['error' => 'Файла с таим id нет'], 400);

        $this->nextController($req, $method);
    }
}
