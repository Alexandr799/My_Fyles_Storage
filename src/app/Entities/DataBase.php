<?php

namespace App\Entities;

use Exception;
use PDO;
use PDOException;

class DataBase
{
    private PDO $connection;

    private static $db;

    private bool $transactionMode = false;

    public static function create()
    {
        if (!empty(static::$db)) {
            return static::$db;
        }

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

    public function lastRowID()
    {
        return $this->connection->lastInsertId();
    }

    public function quary(string $quary, array $params=[])
    {
        if ($this->transactionMode) new Exception('Во время транзации нужно использовать метод quaryTransaction');
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
            $message = $response['message'];
            file_put_contents(realpath('./logs/db.log'), "$message \n", FILE_APPEND);
            return $response;
        };
    }

    public function quaryTransaction(string $quary, array $params=[]){
        if (!$this->transactionMode) new Exception('Во время транзации нужно использовать метод quary');
        $state = $this->connection->prepare($quary);
        $state->execute($params);
        $data = $state->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    public function startTransaction()
    {
        $this->connection->beginTransaction();
        $this->transactionMode = true;
    }


    public function acceptTransaction()
    {
        $this->connection->commit();
        $this->transactionMode = false;
    }

    public function cancelTransaction()
    {
        $this->connection->rollBack();
        $this->transactionMode = false;
    }
}
