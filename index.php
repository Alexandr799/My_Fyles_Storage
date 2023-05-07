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

require_once(__DIR__ . '/routers/api.php');
require_once(__DIR__ . '/routers/web.php');
