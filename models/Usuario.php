<?php
class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. Iniciar sesión
    public function autenticar($usuario, $password) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE usuario = :u LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':u', $usuario);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password, $row['contraseña'])) {
            return $row;
        }
        return false;
    }

    // 2. Listar todos los usuarios para la tabla
    public function listar() {
        $query = "SELECT id_usuario, nombres, apellidos, usuario, rol, google_secret FROM " . $this->table_name . " ORDER BY id_usuario DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // 3. Crear un nuevo usuario
    public function crear($datos) {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nombres=:nom, apellidos=:ape, usuario=:usu, contraseña=:pass, rol=:rol, estado=1";
        $stmt = $this->conn->prepare($query);
        
        // Encriptar la contraseña de forma segura
        $password_hash = password_hash($datos['pass'], PASSWORD_DEFAULT);
        
        $stmt->bindParam(':nom', $datos['nom']);
        $stmt->bindParam(':ape', $datos['ape']);
        $stmt->bindParam(':usu', $datos['usu']);
        $stmt->bindParam(':pass', $password_hash);
        $stmt->bindParam(':rol', $datos['rol']);
        return $stmt->execute();
    }

    // 4. Actualizar datos de un usuario existente
    public function actualizar($datos) {
        if (!empty($datos['pass'])) {
            // Si escribió una nueva contraseña, la actualizamos
            $query = "UPDATE " . $this->table_name . " 
                      SET nombres=:nom, apellidos=:ape, usuario=:usu, contraseña=:pass, rol=:rol 
                      WHERE id_usuario=:id";
            $stmt = $this->conn->prepare($query);
            $password_hash = password_hash($datos['pass'], PASSWORD_DEFAULT);
            $stmt->bindParam(':pass', $password_hash);
        } else {
            // Si la dejó en blanco, conservamos la que ya tenía
            $query = "UPDATE " . $this->table_name . " 
                      SET nombres=:nom, apellidos=:ape, usuario=:usu, rol=:rol 
                      WHERE id_usuario=:id";
            $stmt = $this->conn->prepare($query);
        }
        
        $stmt->bindParam(':nom', $datos['nom']);
        $stmt->bindParam(':ape', $datos['ape']);
        $stmt->bindParam(':usu', $datos['usu']);
        $stmt->bindParam(':rol', $datos['rol']);
        $stmt->bindParam(':id', $datos['id']);
        return $stmt->execute();
    }

    // 5. Eliminar un usuario
    public function eliminar($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_usuario = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>