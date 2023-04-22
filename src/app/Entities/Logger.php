<?php

namespace App\Entities;


class Logger
{
    static public function printLog(string $message,string $file): void
    {
        file_put_contents(realpath("./logs/$file.log"), "$message \n", FILE_APPEND);
    }
}
