<?php

namespace App\Handlers;

use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;



class Admin extends Controller
{
    public function handle(Request $req, string $method)
    {
        $role = Response::getSession('role');
        if (empty($role) || $role !== 'admin') {
            Response::json(['error' => 'нет прав доступа!'], 403);
        } else {
            $this->nextController($req, $method);
        }
    }
}
