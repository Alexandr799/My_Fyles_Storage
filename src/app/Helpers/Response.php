<?php

namespace App\Helpers;

class Response
{
    public static function json(array $arrayToJson)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($arrayToJson);
    }

    public static function setSesion($sessionValues)
    {
        session_start();
        $_SESSION = array_merge( $_SESSION, $sessionValues);
    }

    public static function getSession($key)
    {
        session_start();
        return $_SESSION[$key];
    }

    public static function deleteSession()
    {
        session_start();
        session_destroy();
    }

    public static function error(array $arrayToJson, int $code)
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($code);
        echo json_encode($arrayToJson);
    }
}
