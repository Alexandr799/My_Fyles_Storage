<?php

use App\Controllers\File;
use App\Controllers\UserMiddlewares\Middlewares\Auth;
use App\Entities\Router;
use App\Controllers\Pages;


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
