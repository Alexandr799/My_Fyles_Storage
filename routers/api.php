<?php

use App\Controllers\File;
use App\Controllers\Middlewares\Admin;
use App\Controllers\Middlewares\Auth;
use App\Controllers\Middlewares\FileAddValidator;
use App\Controllers\Middlewares\LoginValidation;
use App\Controllers\Middlewares\RegisterValidator;
use App\Controllers\Middlewares\UpdateValidator;
use App\Controllers\Middlewares\ValidID;
use App\Controllers\User;
use App\Entities\Router;

// пути важно писать в строго формате начиная со слэша /test/path а не path/test , также при создании api, любой путь важно начить со слов api
// например /api/test

// пути важно писать в строго формате начиная со слэша /test/path а не path/test

Router::get('/api/user/{id}', Auth::create()->next(ValidID::create()->next(User::create())), 'index');

Router::post('/api/user', RegisterValidator::create()->next(User::create()), 'store');

Router::put(
    '/api/user',
    Auth::create()
        ->next(
            ValidID::create()
                ->next(UpdateValidator::create()
                    ->next(
                        User::create()
                    ))
        ),
    'update'
);

Router::delete('/api/user/{id}', Auth::create()->next(ValidID::create()->next(User::create())), 'delete');



Router::get('/api/login', LoginValidation::create()->next(User::create()), 'login');

Router::get('/api/reset_password', User::create(), 'reset_password');

Router::get('/api/logout',  Auth::create()->next(User::create()), 'logout');;



Router::get('/api/admin/user', Admin::create()->next(User::create()), 'list');

Router::get('/api/admin/user/{id}', Admin::create()->next(User::create()), 'index');

Router::delete('/api/admin/user/{id}', Admin::create()->next(User::create()), 'delete');

Router::put('/api/admin/user', Admin::create()->next(UpdateValidator::create()->next(User::create())), 'update');



Router::get('/api/file', Auth::create()->next(File::create()), 'fileAll');

Router::get('/api/file/{id}', Auth::create()->next(File::create()), 'file');

Router::post(
    '/api/file',
    Auth::create()
        ->next(FileAddValidator::create()
            ->next(
                File::create()
            )),
    'addFile'
);

Router::get(
    '/api/file/{id}',
    Auth::create()
        ->next(File::create()),
    'file'
);

Router::get(
    '/api/file',
    Auth::create()
        ->next(File::create()),
    'fileAll'
);

Router::put(
    '/api/file',
    Auth::create()
        ->next(File::create()),
    'updateFile'
);

Router::post('/api/test', User::create(), 'list');
