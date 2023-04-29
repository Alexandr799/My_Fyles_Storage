<?php

namespace App\Controllers;


use App\Entities\Crypter;
use App\Entities\DataBase;
use App\Entities\Logger;
use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;
use Exception;

class User  extends Controller
{
    public function index(Request $req)
    {
        $id = $req->getArg('id');
        $users = DataBase::create()->quary("select login, id, role from users where id = :id", ['id' => $id]);

        if (!$users['success']) Response::json(['error' => 'Что то пошло не так...'], 500);

        if (count($users['data']) === 0) return  Response::json(['error' => 'Такого пользователя не существует!'], 404);

        Response::json($users['data']);
    }

    public function update(Request $req)
    {
        $id = intval($req->getParam('id'));

        $params = [
            'role' => $req->getParam('role'),
            'login' => $req->getParam('login'),
            'password' => $req->getParam('password'),
        ];
        $cleanParams = [];
        $quary = '';
        foreach ($params as $key => $val) {
            if (!empty($val)) {
                $cleanParams[$key] = $val;
                $quary .= " $key = :$key,";
            }
        }
        $quary = substr($quary, 0, strlen($quary) - 1);
        $cleanParams['id'] = $id;


        $dbres = DataBase::create()->quary("UPDATE users SET $quary WHERE id=:id", $cleanParams);

        if (!$dbres['success']) {
            return  Response::json(['error' => 'Не удалось обновить пользователя'], 500);
        }

        Response::json(['update' => true]);
    }

    public function delete(Request $req)
    {
        $id = $req->getArg('id');
        $users = DataBase::create()->quary("DELETE FROM users where id = :id", ['id' => $id]);

        if (!$users['success']) Response::json(['error' => 'Не удалось удалить пользователя'], 500);

        if (Response::getSession('id') == $id) Response::deleteSession();

        Response::json(['delete' => true]);
    }

    public function store(Request $req)
    {
        $login = $req->getParam('login');
        $password = $req->getParam('password');
        $role = $req->getParam('role');

        $db = DataBase::create();
        $db->startTransaction();
        try {
            $db->quaryTransaction(
                'INSERT INTO users (login, password,role) VALUES (:login, :password, :role)',
                ['login' => $login, 'password' => Crypter::crypt($password), 'role' => $role,]
            );
            $newUserId = $db->lastRowID();
            $db->quaryTransaction(
                'INSERT INTO `directories` (pwd, owner_user_id) VALUES (:pwd, :owner_user_id)',
                ['pwd' => '/', 'owner_user_id' => $newUserId]
            );
            $db->acceptTransaction();
            Response::setSession([
                'id' => $newUserId,
            ]);
    
            Response::json(['create' => true, 'id' => $newUserId, 'logged' => true]);
        } catch (Exception $e) {
            $db->cancelTransaction();
            Logger::printLog($e->getMessage(), 'db');
            Response::json(['error' => 'Не удалось создать пользователя!'], 500);
        }
    }

    public function list(Request $req)
    {
        $users = DataBase::create()->quary('select login, id, role  from users;');
        if ($users['success']) {
            Response::json($users['data']);
        } else {
            Response::json(['error' => 'Не удалось найти получить пользователей '], 500);
        }
    }

    public function login(Request $req)
    {
        $user = $req->getProps('user');

        Response::setSession([
            'id' => $user['id'],
        ]);

        Response::json([
            'logged' => true,
            'id' => $user['id'],
        ]);
    }

    public function logout(Request $req)
    {
        Response::deleteSession();
        Response::json(['exit' => true]);
    }

    public function reset_password(Request $req)
    {
        Response::getSession('id');
    }
}
