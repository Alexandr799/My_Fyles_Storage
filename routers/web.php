<?php
use App\Controllers\File;
use App\Controllers\Middlewares\UserMiddlewares\Auth;
use App\Controllers\Pages;
use App\Entities\Response;
use App\Entities\Router;

// пути важно писать в строго формате начиная со слэша /test/path а не path/test

Router::get(
    '/',
    Pages::create(),
    'index'
);

Router::get(
    '/share_file/{id}',
    Auth::create()->next(File::create()),
    'getShareFile'
);

Router::get(
    '/download/{id}',
    Auth::create()->next(File::create()),
    'getSelfFile'
);

Response::php('404', ['message'=>'Простите, такой страницы не существует!'], 404);
