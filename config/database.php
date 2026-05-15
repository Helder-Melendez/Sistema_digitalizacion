<?php
class Database {
    private $host = "localhost";
    private $db_name = "saneamiento_db";
    private $username = "root"; // Usuario por defecto de XAMPP
    private $password = "";     // Contraseña por defecto (vacía)
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>