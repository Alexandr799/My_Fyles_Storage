<?php

namespace App\Controllers\Middlewares\UserMiddlewares;

use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;

class ValidID extends Controller
{
    public function handle(Request $req, string $method)
    {
        $id = Response::getSession('id');
        $idResponse =  empty($req->getArg('id')) ? $req->getParam('id') : $req->getArg('id');

        if (empty($idResponse)) Response::json(['error' => 'Не задан id в запросе!'], 400);
        if ($id != $idResponse) Response::json(['error' => 'Нет прав на выполнение действия!'], 403);

        $this->nextController($req, $method);
    }
}
