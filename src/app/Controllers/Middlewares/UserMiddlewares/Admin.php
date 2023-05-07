<?php

namespace App\Controllers\Middlewares\UserMiddlewares;

use App\Custom\DataBase;
use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;



class Admin extends Controller
{
    public function handle(Request $req, string $method)
    {
        $user = DataBase::create()->quary('SELECT * FROM users WHERE id = :id', ['id' => Response::getSession('id')]);
        if (!$user['success']) Response::json(['error'=>'Что то пошло не так!'], 500);

        $role = $user['data'][0]['role'];
        if (empty($role) || $role !== 'admin') {
            Response::json(['error' => 'нет прав доступа!'], 403);
        } else {
            $this->nextController($req, $method);
        }
    }
}
