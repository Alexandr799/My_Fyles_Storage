<?php

namespace App\Controllers;

use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;



class Auth extends Controller
{
    public function handle(Request $req, string $method)
    {
        $id = Response::getSession('id');
        if (empty($id)) return Response::json(['error' => 'Вы не авторизованы!'], 403);

        if ($id == $req->getArg('id') || $id == $req->getParam('id')) {
            $this->nextController($req, $method);
        } else {
            Response::json(['error' => 'Вы не авторизованы!'], 403);
        }
    }
}