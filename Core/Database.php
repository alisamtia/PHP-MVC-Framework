<?php

namespace Core;

class Database
{
    private $pdo;
    private $statement;
    function __construct($charset = 'utf8mb4')
    {
        $db_info = get_config('database');
        $host = $db_info['host'];
        $db = $db_info['dbname'];
        $user = $db_info['user'];
        $pass = $db_info['pass'];


        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        try {
            $this->pdo = new \PDO($dsn, $user, $pass);
        } catch (\PDOException $e) {
            // If connection fails, catch the error
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    function query($query, $placeholders = [])
    {
        $query = $this->pdo->prepare($query);
        $query->execute($placeholders);
        $this->statement = $query;
        return $this;
    }

    function fetch()
    {
        return $this->statement->fetch();
    }

    function fetchAll()
    {
        return $this->statement->fetchAll();
    }

    function fetchColumn()
    {
        return $this->statement->fetchColumn();
    }
}
