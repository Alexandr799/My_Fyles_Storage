<?php

namespace App\Handlers;

use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;



class Auth extends Controller
{
    public function handle(Request $req, string $method)
    {
        $id = Response::getSession('id');

        if (empty($id)) return Response::json(['error' => 'Вы не авторизованы!'], 403);

   
        $this->nextController($req, $method);
    }
}