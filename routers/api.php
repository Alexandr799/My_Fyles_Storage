<?php
use App\Handlers\Admin;
use App\Handlers\Auth;
use App\Controllers\User;
use App\Entities\Router;
use App\Handlers\IDValidator;

// пути важно писать в строго формате начиная со слэша /test/path а не path/test , также при создании api, любой путь важно начить со слов api
// например /api/test

// пути важно писать в строго формате начиная со слэша /test/path а не path/test


Router::get('/api/user/{id}',Auth::create()->next(IDValidator::create()->next(User::create())), 'index');

// Router::get('/api/user', User::create(), 'list'); 

Router::post('/api/user', User::create(), 'store');

Router::put('/api/user', Auth::create()->next(IDValidator::create()->next(User::create())), 'update');

Router::delete('/api/user/{id}', Auth::create()->next(IDValidator::create()->next(User::create())), 'delete');




Router::get('/api/login', User::create(), 'login');

Router::get('/api/reset_password', User::create(), 'reset_password');

Router::get('/api/logout', User::create(), 'logout');



Router::get('/api/admin/user', Admin::create()->next(User::create()), 'list');

Router::get('/api/admin/user/{id}', Admin::create()->next(User::create()), 'index');

Router::delete('/api/admin/user/{id}', Admin::create()->next(User::create()), 'delete');

Router::put('/api/admin/user', Admin::create()->next(User::create()), 'update');





Router::get('/api/admin/user', Admin::create()->next(User::create()), 'list');

Router::get('/api/admin/user/{id}', Admin::create()->next(User::create()), 'index');

Router::delete('/api/admin/user/{id}', Admin::create()->next(User::create()), 'delete');

Router::put('/api/admin/user', Admin::create()->next(User::create()), 'update');