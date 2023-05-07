<?php

namespace App\Controllers\Middlewares\DirectoryMiddlewares;

use App\Custom\DataBase;
use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;

class DeleteDirectoryValidator extends Controller
{
    public function handle(Request $req, string $method)
    {
        $db = DataBase::create();
        $dirInfo = $db->quary(
            "SELECT id, pwd, parent_dir_id FROM `directories`  WHERE id = :id AND owner_user_id=:owner",
            ['id' => $req->getArg('id'), 'owner' => Response::getSession('id')]
        );

        if (!$dirInfo['success']) Response::json(['error' => 'Что то пошло не так...'], 500);
        if (count($dirInfo['data']) === 0) Response::json(['error' => 'Такой директории нет, или вы не имеете к нему доступа'], 404);
        $dirInfo  = $dirInfo['data'][0];

        $req->setInProps('id', $dirInfo['id']);

        $this->nextController($req, $method);
    }
}
