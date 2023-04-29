<?php

namespace App\Controllers\Middlewares;

use App\Entities\DataBase;
use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;

class UpdateDirValidator extends Controller
{
    public function handle(Request $req, string $method)
    {
        if (empty($req->getParam('id'))) Response::json(['error' => 'Задайте id директории!']);
        if (empty($req->getParam('name'))) Response::json(['error' => 'Задайте название директории!']);

        $db = DataBase::create();
        $dirs = $db->quary(
            'SELECT id, pwd
            FROM  `directories` 
            WHERE id=:id AND owner_user_id=:owner_id',
            [
                'owner_id' => Response::getSession('id'),
                'id' => $req->getParam('id')
            ]
        );

        if (!$dirs['success']) Response::json(['error' => 'Что пошло не так!'], 500);
        if (count($dirs['data']) === 0) Response::json(['error' => 'Такой директории не существует!'], 404);

        $oldPwd = $dirs['data'][0]['pwd'];

        $newPwd = explode('/', $oldPwd);
        $newPwd[count($newPwd) - 2] = $req->getParam('name');
        $newPwd = implode('/', $newPwd);

        if ($oldPwd === $newPwd) Response::json(['error' => 'Файл уже так называется!'], 400);

        $dirs = $db->quary(
            'SELECT id
            FROM  `directories` 
            WHERE owner_user_id=:owner AND pwd=:pwd',
            ['owner' => Response::getSession('id'), 'pwd' => $newPwd]
        );

        if (!$dirs['success']) Response::json(['error' => 'Что пошло не так!'], 500);
        if (count($dirs['data']) > 0) Response::json(['error' => 'Такая директория существует!'], 404);
        
        $req->setInProps('pwd', $newPwd);
        
        $this->nextController($req, $method);
    }
}