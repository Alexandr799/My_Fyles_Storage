<?php

use App\Entities\Router;
use App\Controllers\Pages;


// пути важно писать в строго формате начиная со слэша /test/path а не path/test

Router::get('/', Pages::create(), 'index');

Router::get('/test', Pages::create(), 'test');