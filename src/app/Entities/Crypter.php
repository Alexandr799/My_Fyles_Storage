<?php
namespace App\Entities;

class Crypter
{
    public static function crypt(string $str)
    {
        return password_hash($str, PASSWORD_DEFAULT);
    }

    public static function verify(string $str,string $pass_hash)
    {
        return password_verify($str, $pass_hash);
    }
}
