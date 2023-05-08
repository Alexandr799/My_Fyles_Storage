<?php

namespace App\Controllers;

use App\Custom\Crypter;
use App\Custom\DataBase;
use App\Custom\Email;
use App\Custom\FileStorage;
use App\Entities\Logger;
use App\Entities\Request;
use App\Entities\Response;
use App\Errors\DataBaseException;
use App\Errors\EmailException;
use App\Errors\FileStorageException;
use App\Interfaces\Controller;
use Exception;

class User  extends Controller
{
    public function index(Request $req)
    {
        $id = $req->getArg('id');
        $users = DataBase::create()->quary(
            "select u.login as login, u.id as id, u.role as role , d.id as root_directory_id
            from users  as u
            left join directories as d on u.id = d.owner_user_id
            where d.pwd = '/' AND u.id = :id",
            ['id' => $id]
        );

        if (!$users['success']) Response::json(['error' => 'Что то пошло не так...'], 500);

        if (count($users['data']) === 0) return  Response::json(['error' => 'Такого пользователя не существует!'], 404);

        Response::json($users['data']);
    }

    public function update(Request $req)
    {
        $id = intval(Response::getSession('id'));

        $pass = $req->getParam('password');

        $params = [
            'role' => $req->getParam('role'),
            'login' => $req->getParam('login'),
            'password' => empty($pass) ? $pass : Crypter::crypt($pass),
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

    public function updateAdmin(Request $req)
    {
        $id = intval($req->getParam('id'));

        $params = [
            'role' => $req->getParam('role'),
            'login' => $req->getParam('login'),
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
        // $users = DataBase::create()->quary("DELETE FROM users where id = :id", ['id' => $id]);

        // if (!$users['success']) Response::json(['error' => 'Не удалось удалить пользователя'], 500);

        // if (Response::getSession('id') == $id) Response::deleteSession();
        $db =  DataBase::create();
        $user = $db->quary(
            "select u.login as login, u.id as id, u.role as role , d.id as root_directory_id
            from users  as u
            left join directories as d on u.id = d.owner_user_id
            where d.pwd = '/' AND u.id = :id",
            ['id' => $id]
        );
        if (!$user['success']) Response::json(['error' => 'Что то пошло не так!'], 500);
        if (count($user['data']) === 0) Response::json(['error' => 'Пользователь для удаления не найден!'], 404);
        
        $db->startTransaction();
        try {
            $files = FileStorage::getAllFileRecursive($user['data'][0]["root_directory_id"], $db);
            $db->quaryTransaction("DELETE FROM users where id = :id", ['id' => $id]);
            FileStorage::deleteFileAll($files);
            if ($id == Response::getSession('id')) Response::deleteSession();
            $db->acceptTransaction();
            Response::json(['delete'=>true]);
        } catch (DataBaseException $e) {
            $db->cancelTransaction();
            Logger::printLog($e->getMessage(), 'db');
            Response::json(['error' => 'Что то пошло не так!'], 500);
        } catch (FileStorageException $e) {
            $db->cancelTransaction();
            Logger::printLog($e->getMessage(), 'file');
            Response::json(['error' => 'Что то пошло не так!'], 500);
        }
        

        Response::json($user);
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
        } catch (DataBaseException $e) {
            $db->cancelTransaction();
            Logger::printLog($e->getMessage(), 'db');
            Response::json(['error' => 'Не удалось создать пользователя!'], 500);
        }
    }

    public function list(Request $req)
    {
        $users = DataBase::create()->quary(
            "select u.login as login, u.id as id, u.role as role , d.id as root_directory_id
            from users  as u
            left join directories as d on u.id = d.owner_user_id
            where d.pwd = '/'"
        );
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

    function getByEmail(Request $req)
    {
        $user =  DataBase::create()->quary('SELECT id, login FROM users WHERE login=:email', ['email' => $req->getArg('email')]);
        if (!$user['success']) Response::json(['error' => 'Не удалось удалить пользователя'], 500);
        Response::json($user['data']);
    }

    public function reset_password(Request $req)
    {
        $db = DataBase::create();

        $email = $req->getParam('email');
        if (empty($email)) Response::json(['error' => 'Не задан email'], 400);

        $user = $db->quary('SELECT id FROM users WHERE login=:email', ['email' => $email]);
        if (!$user['success']) Response::json(['error' => 'Что то пошло не так!'], 500);
        if (count($user['data']) === 0) Response::json(['error' => 'Пользователя не существует с таким логином!'], 404);

        $user = $user['data'][0];

        $newPass = Crypter::encodeID(rand(0, 100000000));
        $hashNewPassword = Crypter::crypt($newPass);
        $db->startTransaction();
        try {
            $db->quaryTransaction(
                'UPDATE users SET password=:pass WHERE id=:id',
                ['pass' => $hashNewPassword, 'id' => $user['id']]
            );
            Email::send(
                'my_fyles',
                $email,
                'Запрос на смену пароля',
                "<h1>Cброс пароля</h1>
                <p>Ваш новый пароль - <b>$newPass</b></p>
                <p>Поменять данный пароль вы сможете в личном кабинете после входа!</p>
                <p>Если вы не делали запрос, проигнорируйте это письмо!</p>"
            );

            $db->acceptTransaction();
            Response::json(['send' => true, 'message' => 'Если не увидели письма проверьте спам!']);
        } catch (DataBaseException $e) {
            $db->cancelTransaction();
            Logger::printLog($e->getMessage(), 'db');
            Response::json(['error' => 'Не удалось сбросить пароль!'], 500);
        } catch (EmailException $e) {
            $db->cancelTransaction();
            Logger::printLog($e->getMessage(), 'email');
            Response::json(['error' => 'Не удалось сбросить пароль!'], 500);
        }
    }
}
