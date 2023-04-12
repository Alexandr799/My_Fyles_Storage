<?php
use App\Controllers\Root;

require_once(__DIR__ . DIRECTORY_SEPARATOR . '/vendor/autoload.php');

use App\Controllers\Admin;
use App\Controllers\User;
use App\Helpers\Router;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// пути важно писать в строго формате начиная со слэша /test/path а не path/test

Router::get('/user/{id}', User::create(), 'index');

Router::get('/user', User::create(), 'list');

Router::post('/user', User::create(), 'store');

Router::put('/user', User::create(), 'update');

Router::delete('/user/{id}', User::create(), 'delete');




Router::get('/login', User::create(), 'login');

Router::get('/reset_password', User::create(), 'reset_password');

Router::get('/logout', User::create(), 'logout');



Router::get('/admin/user', Admin::create()->next(User::create()), 'list');

Router::get('/admin/user/{id}', Admin::create()->next(User::create()), 'index');

Router::delete('/admin/user/{id}', Admin::create()->next(User::create()), 'delete');

Router::put('/admin/user', Admin::create()->next(User::create()), 'update');





Router::get('/admin/user', Admin::create()->next(User::create()), 'list');

Router::get('/admin/user/{id}', Admin::create()->next(User::create()), 'index');

Router::delete('/admin/user/{id}', Admin::create()->next(User::create()), 'delete');

Router::put('/admin/user', Admin::create()->next(User::create()), 'update');
