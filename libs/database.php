<?php

class Database
{

    private $host;
    private $port;
    private $db;
    private $user;
    private $password;
    private $charset;

    public function __construct()
    {
        $this->host = constant('DB_HOST');
        $this->port = constant('DB_PORT');
        $this->db = constant('DB_NAME');
        $this->user = constant('DB_USER');
        $this->password = constant('DB_PASS');
        $this->charset = constant('DB_CHARSET');
    }

    function connect()
    {
        try {
            $connection = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $pdo = new PDO($connection, $this->user, $this->password, $options);
            error_log('Database::Connect::Conexión a BD exitosa');
            return $pdo;
        } catch (PDOException $e) {
            return null;
            error_log('Database::Connect::Error connection::' . $e->getMessage());
        }
    }
}
