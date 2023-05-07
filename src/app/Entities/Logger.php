<?php

namespace App\Entities;


class Logger
{
    static public function printLog(string $message,string $file): void
    {
        $p = $_ENV['LOGS_DIR'];
        $path = realpath(".$p") . "/$file" . ".log";
        if (file_exists($path)) {
            file_put_contents($path, "$message \n", FILE_APPEND);
        } else {
            file_put_contents($path, "$message \n");
        };
    }
}
