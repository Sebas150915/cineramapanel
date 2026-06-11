<?php

/**
 * Configuración de Base de Datos - Cinerama
 * Conexión PDO a MySQL
 */

class Database
{
    private $host = "37.60.241.157";
    private $db_name = "cinerama";
    private $username = "cinerama";
    private $password = "yJxHkdK5xiMrRkrT";
    private $charset = "utf8mb4";
    public $conn;

    /**
     * Obtener conexión PDO
     */
    public function getConnection()
    {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
