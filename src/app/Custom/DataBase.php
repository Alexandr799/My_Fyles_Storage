<?php

namespace App\Custom;

use App\Entities\Logger;
use App\Errors\DataBaseException;
use Exception;
use PDO;
use PDOException;

class DataBase
{
    private PDO $connection;

    private static $db;

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
            Logger::printLog($e->getMessage(), 'db');
        };
    }

    public function lastRowID()
    {
        return $this->connection->lastInsertId();
    }

    public function quary(string $quary, array $params=[])
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
            Logger::printLog($e->getMessage(), 'db');
            return $response;
        };
    }

    public function quaryTransaction(string $quary, array $params=[]){
        try {
            $state = $this->connection->prepare($quary);
            $state->execute($params);
            $data = $state->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (Exception $e) {
            $message = $e->getMessage();
            throw new DataBaseException("Error on quaryTransaction - $quary, message = $message");
        }
    }

    public function startTransaction()
    {
        $this->connection->beginTransaction();
    }


    public function acceptTransaction()
    {
        $this->connection->commit();
    }

    public function cancelTransaction()
    {
        $this->connection->rollBack();
    }
}
