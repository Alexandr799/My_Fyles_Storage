<?php

namespace App\Controllers\DirectoryMiddlewares\Middlewares;

use App\Custom\DataBase;
use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;

class DirectoryAddValidator extends Controller
{
    public function handle(Request $req, string $method)
    {
        if (empty($req->getParam('name'))) Response::json(['error' => 'Укажите название директории!'], 400);
        if (empty($req->getParam('parent_dir_id'))) Response::json(['error' => 'Укажите id папки для хранения'], 400);
        $slash = strpos($req->getParam('name'), '/');
        if (!empty($slash) || $slash === 0) Response::json(['error' => 'Название папки не может содержать /'], 400);

        $db = DataBase::create();
        $dir = $db->quary(
            'SELECT id, pwd  FROM `directories` WHERE `id`=:id' , ['id' => $req->getParam('parent_dir_id')]);

        if (!$dir['success']) Response::json(['error' => 'Что то пошло не так...'], 500);
        if (count($dir['data']) === 0) Response::json(['error' => 'У вас нет директории куда вы хотите положить папку!'], 404);

        $pwdNewDir = $dir['data'][0]['pwd'] . $req->getParam('name') . '/';
        $dirExists = $db->quary('SELECT id, pwd  FROM `directories` WHERE `pwd`=:pwd', ['pwd' => $pwdNewDir]);

        if (!$dirExists['success']) Response::json(['error' => 'Что то пошло не так...'], 500);
        if (count($dirExists['data']) > 0) Response::json(['error' => 'Директория с таким названием уже существует!'], 404);

        $req->setInProps('pwd', $pwdNewDir);

        $this->nextController($req, $method);
    }
}