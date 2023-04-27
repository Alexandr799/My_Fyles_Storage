<?php

namespace App\Controllers\Middlewares;

use App\Entities\DataBase;
use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;

class UpdateValidator extends Controller
{
    public function handle(Request $req, string $method)
    {
        $login = $req->getParam('login');
        $password = $req->getParam('password');
        $role = $req->getParam('role');

        if ((!empty($password)) && (strlen($password) < 5)) {
            Response::json(['error' => 'Пароль должен быть длинее 5 или более символов'], 400);
        }

        if ((!empty($login)) && (!filter_var($login, FILTER_VALIDATE_EMAIL))) {
            Response::json(['Логин должен быть email! Введите правильный email'], 400);
        }

        if ((!empty($role)) && (($role !== 'admin') && ($role !== 'user'))) {
            Response::json(['error' => 'Не верно выбрана роль!'], 400);
        }
        $data = DataBase::create()->quary('SELECT  FROM `users` WHERE id=:id',  ['id' => Response::getSession('id')]);

        if (!$data['success']) Response::json(['error' => 'Что то пошло не так...'], 500);

        $currentLogin = $data['data']['login'];

        if (!empty($login) && $login !== $currentLogin) {
            $users = DataBase::create()->quary("select * from users where login = :login", ['login' => $login]);
            if (!$users['success']) Response::json(['error' => 'Что то пошло не так...'], 500);
            if (count($users['data']) > 0) return  Response::json(['error' => 'Данный логин занят!'], 400);
        }

        $this->nextController($req, $method);
    }
}
