<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '/vendor/autoload.php');

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once(__DIR__ . '/routers/api.php');
require_once(__DIR__ . '/routers/web.php');
require_once(__DIR__ . '/routers/error.php');
?>