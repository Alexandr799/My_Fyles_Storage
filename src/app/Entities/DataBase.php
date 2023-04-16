<?php

namespace App\Entities;

use Exception;
use PDO;
use PDOException;

class DataBase
{
    private PDO $connection;

    public static function create()
    {
        return new static();
    }

    private function __construct()
    {
        try {
            $password = $_ENV['PASSWORD'];
            $user = $_ENV['USER'];
            $charset = $_ENV['CHARSET'];
            $dbname = $_ENV['DB_NAME'];
            $host =  $_ENV['HOST'];
            $db =  $_ENV['DB'];
            $this->connection = new PDO("$db:host=$host;dbname=$dbname;charset=$charset", $user, $password);
        } catch (PDOException $e) {
            print_r($e);
        };
    }

    public function quary(string $quaryString)
    {
        try {
            $state = $this->connection->prepare($quaryString);
            $state->execute();

            $response['success'] = true;
            $data = $state->fetchAll(PDO::FETCH_ASSOC);
            $response['data'] =  $data;
            return $response;
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            return $response;
        };
    }

    public function quaryWithVars(string $quary, array $params)
    {
        try {
            $state = $this->connection->prepare($quary);
            $state->execute($params);

            $response['success'] = true;
            $data = $state->fetchAll(PDO::FETCH_ASSOC);
            $response['data'] =  $data;
            return $response;
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            return $response;
        };
    }
}
