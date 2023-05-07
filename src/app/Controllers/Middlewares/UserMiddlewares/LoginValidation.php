<?php

namespace App\Controllers\UserMiddlewares\Middlewares;

use App\Custom\Crypter;
use App\Custom\DataBase;
use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;


class LoginValidation extends Controller
{
    public function handle(Request $req, string $method)
    {
        if (!empty(Response::getSession('id'))) Response::json(['error' => 'Вы уже авторизованы!'], 400);

        $password = $req->getParam('password');
        $login = $req->getParam('login');

        if (empty($login)) Response::json(['error' => 'Введите логин!'], 401);

        $user = DataBase::create()->quary(
            "select * from users where login = :login",
            ['login' => $login]
        );

        if (!$user['success']) Response::json(['error' => 'Что то пошло не так...'], 500);

        if (!(count($user['data']) === 1)) {
            Response::deleteSession();
            Response::json(['error' => 'Не верный логин'], 401);
        }

        if (empty($password)) Response::json(['error' => 'Введите пароль!'], 401);

        if (!Crypter::verify($password, $user['data'][0]['password'])) {
            Response::deleteSession();
            Response::json(['error' => 'Не верный пароль'], 401);
        }

        $req->setInProps('user', $user['data'][0]);
        $this->nextController($req, $method);

    }
}