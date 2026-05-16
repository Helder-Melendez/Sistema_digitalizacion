<?php
class Database {
    // Si está en Railway, jala las variables de la nube. Si está en XAMPP, usa "localhost" y "root".
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    public $conn;

    public function __construct() {
        $this->host = getenv('MYSQLHOST') ?: "localhost";
        $this->db_name = getenv('MYSQLDATABASE') ?: "saneamiento_db";
        $this->username = getenv('MYSQLUSER') ?: "root";
        $this->password = getenv('MYSQLPASSWORD') ?: "";
        $this->port = getenv('MYSQLPORT') ?: "3306";
    }

    public function getConnection() {
        $this->conn = null;
        try {
            // Conexión incluyendo el puerto dinámico de la nube
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>