<?php
namespace modules\database;
use PDO;

class Database 
{
    public function __construct(
                                    string $host, 
                                    string $name, 
                                    string $user, 
                                    string $password
                                )
    {
        $this->host = $host;
        $this->name = $name;
        $this->user = $user;
        $this->password = $password;
    }

    public function getConnection(): PDO
    {
        $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8";

        $db = new PDO($dsn, $this->user, $this->password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    }
}