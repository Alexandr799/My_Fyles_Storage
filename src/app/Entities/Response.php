<?php

namespace App\Entities;

class Response
{
    public static function json(array $arrayToJson, $code=200)
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($code);
        echo json_encode($arrayToJson);
    }

    public static function setSession($sessionValues)
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



    public static function html(string $title, $code=200){
        $path = realpath("./public/html/$title.html");
        if (file_exists($path)) {
            http_response_code($code);
            echo file_get_contents($path);
        } else {
            http_response_code(500);
            echo 'Шаблона не найдено!';
        }
    }
}
