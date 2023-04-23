<?php

namespace App\Controllers;

use App\Entities\DataBase;
use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;



class Admin extends Controller
{
    public function handle(Request $req, string $method)
    {
        $user = DataBase::create()->quary('SELECT * FROM users WHERE id = :id', ['id' => Response::getSession('id')]);
        $role = $user['role'];
        if (empty($role) || $role !== 'admin') {
            Response::json(['error' => 'нет прав доступа!'], 403);
        } else {
            $this->nextController($req, $method);
        }
    }
}
