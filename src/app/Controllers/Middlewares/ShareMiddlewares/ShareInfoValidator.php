<?php

namespace App\Controllers\Middlewares\ShareMiddlewares;

use App\Custom\DataBase;
use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;

class ShareInfoValidator extends Controller
{
    public function handle(Request $req, string $method)
    {
        $db = DataBase::create();

        $userFile = $db->quary(
            'SELECT id FROM `files` WHERE `id`=:id AND `owner_user_id`=:user_id',
            ['id' => $req->getArg('id'), 'user_id' => Response::getSession('id')]
        );
        if (!$userFile['success']) Response::json(['error' => 'Что то пошло не так!'], 500);
        if (count($userFile['data']) === 0) Response::json(
            [
                'error' => 'Вы не можете просматривать информацию об этом файле или его не существует!'
            ],
            400
        );

        $this->nextController($req, $method);
    }
}
