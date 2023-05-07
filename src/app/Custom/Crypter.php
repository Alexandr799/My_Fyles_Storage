<?php

namespace App\Custom;

class Crypter
{
    public static function crypt(string $str)
    {
        return password_hash($str, PASSWORD_DEFAULT);
    }

    public static function verify(string $str, string $pass_hash)
    {
        return password_verify($str, $pass_hash);
    }

    public static function encodeID(string $str): string
    {
        return base64_encode($str);
    }

    public static function decodeID(string $str): string
    {
        return base64_decode($str);
    }
}
