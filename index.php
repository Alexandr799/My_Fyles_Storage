<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '/vendor/autoload.php');

use App\Entities\Response;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

function exception_handler(Throwable $exception)
{
    Response::html('500', 500);
}

set_exception_handler('exception_handler');

require_once(__DIR__ . DIRECTORY_SEPARATOR . '/src/app/Config/ini.php');


foreach (scandir(__DIR__ . '/routers/') as $file) {
    $path = __DIR__ . '/routers/' . $file;
    if (is_file($path)) {
        require_once($path);
    }
}