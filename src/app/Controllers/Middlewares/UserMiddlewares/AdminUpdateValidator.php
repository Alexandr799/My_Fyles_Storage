<?php

namespace App\Controllers\Middlewares\UserMiddlewares;

use App\Custom\DataBase;
use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;

class AdminUpdateValidator extends Controller
{
    public function handle(Request $req, string $method)
    {
        $login = $req->getParam('login');
        $role = $req->getParam('role');
        $id = $req->getParam('id');

        if (empty($id)) Response::json(['error' => 'Укажите айди пользователя для изменения'], 400);

        if ((!empty($login)) && (!filter_var($login, FILTER_VALIDATE_EMAIL))) {
            Response::json(['Логин должен быть email! Введите правильный email'], 400);
        }

        if ((!empty($role)) && (($role !== 'admin') && ($role !== 'user'))) {
            Response::json(['error' => 'Не верно выбрана роль!'], 400);
        }

        $data = DataBase::create()->quary('SELECT * FROM `users` WHERE id=:id',  ['id' => $id]);

        if (!$data['success']) Response::json(['error' => 'Что то пошло не так...'], 500);
        if (count($data['data']) === 0) Response::json(['error' => 'Пользователь не найден!'], 400);

        $currentLogin = $data['data'][0]['login'];

        if (!empty($login) && $login !== $currentLogin) {
            $users = DataBase::create()->quary("select * from users where login = :login", ['login' => $login]);
            if (!$users['success']) Response::json(['error' => 'Что то пошло не так...'], 500);
            if (count($users['data']) > 0) return  Response::json(['error' => 'Данный логин занят!'], 400);
        }

        $this->nextController($req, $method);
    }
}
