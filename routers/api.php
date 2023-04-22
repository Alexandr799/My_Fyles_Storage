<?php

use App\Controllers\Admin;
use App\Controllers\Auth;
use App\Controllers\File;
use App\Controllers\RegisterValidator;
use App\Controllers\UpdateValidator;
use App\Controllers\User;
use App\Controllers\ValidID;
use App\Entities\Router;

// пути важно писать в строго формате начиная со слэша /test/path а не path/test , также при создании api, любой путь важно начить со слов api
// например /api/test

// пути важно писать в строго формате начиная со слэша /test/path а не path/test


Router::get('/api/user/{id}',Auth::create()->next(ValidID::create()->next(User::create())), 'index');

Router::post('/api/user', RegisterValidator::create()->next(User::create()), 'store');

Router::put(
    '/api/user',
     Auth::create()
            ->next(ValidID::create()
            ->next(UpdateValidator::create()
            ->next(User::create()
            ))
    ),
    'update'
);

Router::delete('/api/user/{id}', Auth::create()->next(ValidID::create()->next(User::create())), 'delete');



Router::get('/api/login', User::create(), 'login');

Router::get('/api/reset_password', User::create(), 'reset_password');

Router::get('/api/logout',  Auth::create()->next(User::create()), 'logout');;



Router::get('/api/admin/user', Admin::create()->next(User::create()), 'list');

Router::get('/api/admin/user/{id}', Admin::create()->next(User::create()), 'index');

Router::delete('/api/admin/user/{id}', Admin::create()->next(User::create()), 'delete');

Router::put('/api/admin/user', Admin::create()->next(UpdateValidator::create()->next(User::create())), 'update');



Router::get('/api/file', Auth::create()->next(File::create()), 'fileAll');

Router::get('/api/file/{id}', Auth::create()->next(File::create()), 'file');

Router::post('/api/file', Auth::create()->next(File::create()), 'addFile');