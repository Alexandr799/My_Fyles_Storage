<?php

namespace App\Entities;


class Logger
{
    static public function printLog(string $message, string $file, bool $currentTimePrint = true): void
    {
        $p = $_ENV['LOGS_DIR'];
        $path = realpath(".$p") . "/$file" . ".log";
        $time = $currentTimePrint ? date(DATE_RFC822) : '';
        if (file_exists($path)) {
            file_put_contents($path, "$time $message \n", FILE_APPEND);
        } else {
            file_put_contents($path, "$time $message \n");
        };
    }
}
