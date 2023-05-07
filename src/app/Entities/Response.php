<?php

namespace App\Entities;

use Exception;

class Response
{
    public static function json(array $arrayToJson, $code = 200)
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($code);
        echo json_encode($arrayToJson);
        exit();
    }

    public static function end()
    {
        exit();
    }

    public static function setSession($sessionValues)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        };
        $_SESSION = array_merge($_SESSION, $sessionValues);
    }

    public static function getSession($key)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        };
        return empty($_SESSION[$key]) ? null : $_SESSION[$key];
    }

    public static function deleteSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        };
        session_destroy();
    }

    public static function html(string $title, $code = 200)
    {
        $path = realpath("./public/html/$title.html");
        if (file_exists($path)) {
            http_response_code($code);
            echo file_get_contents($path);
        } else {
            throw new Exception("html file $title.html not found");
        }
        exit();
    }


    public static function php(string $title, $vars = [], $code = 200)
    {
        $path = realpath("./public/php/$title.php");
        if (file_exists($path)) {
            http_response_code($code);
            $_VARS = $vars;
            require_once($path);
        } else {
            throw new Exception("php file $title.php not found");
        }
        exit();
    }

    public static function upload(string $path, $name = null)
    {
        if (!file_exists($path)) {
            Logger::printLog("Файла - $path не существует!", 'file');
            throw new Exception('Файл не существует!');
        };
        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        $fileName = empty($name) ? basename($path) : $name;
        header('Content-Disposition: attachment; filename=' . $fileName);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit();
    }
}
