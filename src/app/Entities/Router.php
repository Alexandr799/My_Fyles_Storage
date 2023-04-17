<?php

namespace App\Entities;

use App\Controllers\Controller;
use Exception;

class Router
{
    private static $isRelolve = false;
    private static function getArgs(string $path): array
    {
        $args = [];
        foreach (explode('/', substr($path, 1)) as $pos => $partPath) {
            if (str_starts_with($partPath, '{') && str_ends_with($partPath, '}')) {
                $args[$pos] = substr($partPath, 1, strlen($partPath) - 2);
            }
        };
        return $args;
    }

    private static function matchPath(array $args, $path): bool
    {
        $cleanUrl = parse_url($_SERVER["REQUEST_URI"])['path'];
        $cleanUrl = str_ends_with($cleanUrl, '/') ? substr($cleanUrl, 0, strlen($cleanUrl) - 1) : $cleanUrl;
        $path = str_ends_with($path, '/') ? substr($cleanUrl, 0, strlen($cleanUrl) - 1) : $path;


        if (count($args) === 0) return $cleanUrl === $path;

        $pathArray = explode('/', substr($path, 1));
        $serverPathArray = explode('/', substr($cleanUrl, 1));

        if (count($pathArray) !== count($serverPathArray)) return false;

        for ($i = 0; count($pathArray) > $i; $i++) {
            if (array_key_exists($i, $args)) continue;
            if ($pathArray[$i] !== $serverPathArray[$i]) return false;
        }

        return true;
    }

    private static function parseArgs(array $args)
    {
        $cleanUrl = parse_url($_SERVER["REQUEST_URI"])['path'];
        $serverPathArray = explode('/', substr($cleanUrl, 1));
        $argsArray = [];
        foreach ($args as $key => $val) {
            if (isset($serverPathArray[$key]) && $serverPathArray[$key] === "") {
                throw new Exception("not found argument $val");
            }
            $argsArray[$val] = $serverPathArray[$key];
        }
        return $argsArray;
    }

    public static function __callStatic($name, $arguments)
    {

        if (static::$isRelolve) exit();
        if (strtolower($_SERVER["REQUEST_METHOD"]) !== strtolower($name)) return;

        $path = $arguments[0];
        $controller = $arguments[1];
        $method = $arguments[2];

        $args = static::getArgs($path);

        if (static::matchPath($args, $path)) {
            $body = file_get_contents("php://input");
            $_METHOD = [];
            if (($_SERVER)["CONTENT_TYPE"] === 'application/json') {
                $_METHOD = json_decode($body, true);
            } else {
                parse_str(file_get_contents("php://input"), $_METHOD);
            }
            $pathArgs = static::parseArgs($args);
            static::$isRelolve = true;
            return $controller->handle(new Request(array_merge($_GET, $_METHOD), $pathArgs), $method);
        };
    }
}
