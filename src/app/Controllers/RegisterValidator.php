<?php

namespace App\Controllers;

use App\Entities\DataBase;
use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;

class RegisterValidator extends Controller
{
    public function handle(Request $req, string $method)
    {
        $login = $req->getParam('login');
        $password = $req->getParam('password');
        $role = $req->getParam('role');

        if (empty($password) && (strlen($password)<5)) Response::json(['error' => 'Пароль должен быть длинее 5 или более символов'], 400);

        if (empty($login)) Response::json(['error' => 'Не указан логин'], 400);

        if(!filter_var($login, FILTER_VALIDATE_EMAIL)) Response::json(['Логин должен быть email! Введите правильный email'], 400);
       
        if (($role !== 'admin') && ($role !== 'user') ) Response::json(['error' => 'Не верно выбрана роль!'], 400);

        $users = DataBase::create()->quary("select * from users where login = :login", ['login' => $login]);

        if (!$users['success']) Response::json(['error' => 'Что то пошло не так...'], 500);

        if (count($users['data']) > 0) return  Response::json(['error' => 'Данный логин занят!'], 400);

        
        $this->nextController($req, $method);
    }
}