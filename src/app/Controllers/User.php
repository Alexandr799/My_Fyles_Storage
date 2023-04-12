<?php

namespace App\Controllers;

use App\Helpers\Request;
use App\Helpers\DataBase;
use App\Helpers\Response;

class User  extends Controller
{
    public function index(Request $req)
    {
        $id = $req->getArg('id');
        $users = DataBase::create()->quaryWithVars("select * from users where id = :id", ['id' => $id]);
        if ($users['success']) {
            Response::json($users['data']);
        } else {
            Response::error(['error' => 'Не удалось найти получить пользователей '], 500);
        }
    }

    public function update(Request $req)
    {
        $id = $req->getParam('id');
        echo 1;
        // $users = DataBase::create()->quaryWithVars("DELETE FROM users where id = :id", ['id' => $id]);
    }

    public function delete(Request $req)
    {
        $id = $req->getArg('id');
        $users = DataBase::create()->quaryWithVars("DELETE FROM users where id = :id", ['id' => $id]);

        if (!$users['success']) Response::error(['error' => 'Не удалось удалить пользователя'], 500);

        Response::json(['delete' => true]);
    }

    public function store(Request $req)
    {
        $login = $req->getParam('login');
        $password = $req->getParam('password');
        $role = $req->getParam('role');

        if (empty($login) || empty($password) || empty($role)) return Response::error(
            [
                'error' => 'Не указан пароль логин или роль'
            ],
            401
        );

        $updatedUser = DataBase::create()->quaryWithVars(
            'INSERT INTO users (login, password,role) VALUES (:login, :password, :role)',
            [
                'login' => $login,
                'password' => $password,
                'role' => $role,
            ]
        );

        if (!$updatedUser['success']) Response::error(['error' => 'Не удалось отредактировать пользователя!'], 500);

        Response::json(['create' => true]);
    }

    public function list(Request $req)
    {
        $users = DataBase::create()->quary('select * from users;');
        if ($users['success']) {
            Response::json($users['data']);
        } else {
            Response::error(['error' => 'Не удалось найти получить пользователей '], 500);
        }
    }

    public function login(Request $req)
    {
        $pass = $req->getParam('password');
        $log = $req->getParam('login');
        $user = DataBase::create()->quaryWithVars(
            "select * from users where login = :login and password = :password",
            [
                'password' => $pass,
                'login' => $log,
            ]
        );

        if (!$user['success']) return Response::error(['error' => 'Не удалось найти получить пользователей '], 500);


        if (count($user) > 0) {
            Response::setSesion([
                'id' => $user['data'][0]['id'],
                'role' => $user['data'][0]['role'],
            ]);
            Response::json(['auth'=>true]);
        } else {
            Response::error(['error' => 'Не верный пароль или логин'], 401);
            Response::json(['auth'=>false]);
        }
    }

    public function logout(Request $req)
    {
        Response::deleteSession();
    }

    public function reset_password(Request $req)
    {
        Response::getSession('id');
    }
}
