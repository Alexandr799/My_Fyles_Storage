<?php 

ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] .$_ENV['SESSION_SAVE_PATH']);
ini_set('session.gc_maxlifetime', intval($_ENV['SESSION_TIME']));
ini_set('session.cookie_lifetime', intval($_ENV['COOCKIE_TIME']));

error_reporting(E_ALL);
ini_set("log_errors", intval($_ENV['PHP_ERROR_LOG']));
ini_set("error_log", $_SERVER['DOCUMENT_ROOT'] . $_ENV['ERROR_LOG_PATH']);
ini_set('display_errors', false);
