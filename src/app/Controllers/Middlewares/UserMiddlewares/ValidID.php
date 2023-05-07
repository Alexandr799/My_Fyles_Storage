<?php

namespace App\Controllers\UserMiddlewares\Middlewares;

use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;

class ValidID extends Controller
{
    public function handle(Request $req, string $method)
    {
        $id = Response::getSession('id');
        $idResponse =  $req->getParam('id') ?? $req->getArg('id');

        if (empty($idResponse)) Response::json(['error' => 'Не задан id в запросе!'], 400);
        if ($id != $req->getArg('id') && $id != $req->getParam('id')) Response::json(['error' => 'Нет прав на выполнение действия!'], 403);
        
        $this->nextController($req, $method);
    }
}