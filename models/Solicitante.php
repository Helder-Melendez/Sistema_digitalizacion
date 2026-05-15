<?php
class Solicitante {
    private $conn;
    private $table_name = "solicitantes";

    // Propiedades de la clase
    public $id_solicitante;
    public $dni;
    public $nombres;
    public $apellido_paterno;
    public $apellido_materno;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Registrar un nuevo solicitante en el padrón
     */
    public function crear($dni, $nombres, $ap_pat, $ap_mat) {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET dni = :dni, 
                      nombres = :nombres, 
                      apellido_paterno = :ap_pat, 
                      apellido_materno = :ap_mat";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos para seguridad
        $dni = htmlspecialchars(strip_tags($dni));
        $nombres = htmlspecialchars(strip_tags($nombres));
        $ap_pat = htmlspecialchars(strip_tags($ap_pat));
        $ap_mat = htmlspecialchars(strip_tags($ap_mat));

        // Vincular parámetros
        $stmt->bindParam(':dni', $dni);
        $stmt->bindParam(':nombres', $nombres);
        $stmt->bindParam(':ap_pat', $ap_pat);
        $stmt->bindParam(':ap_mat', $ap_mat);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Actualizar los datos de un solicitante existente
     */
    public function actualizar($dni, $nombres, $ap_pat, $ap_mat) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombres = :nombres, 
                      apellido_paterno = :ap_pat, 
                      apellido_materno = :ap_mat 
                  WHERE dni = :dni";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $dni = htmlspecialchars(strip_tags($dni));
        $nombres = htmlspecialchars(strip_tags($nombres));
        $ap_pat = htmlspecialchars(strip_tags($ap_pat));
        $ap_mat = htmlspecialchars(strip_tags($ap_mat));

        // Vincular parámetros
        $stmt->bindParam(':nombres', $nombres);
        $stmt->bindParam(':ap_pat', $ap_pat);
        $stmt->bindParam(':ap_mat', $ap_mat);
        $stmt->bindParam(':dni', $dni);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Obtener el listado completo de solicitantes
     */
    public function listar() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id_solicitante DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Buscar un solicitante específico por DNI
     * Utilizado para el autocompletado en el registro de expedientes
     */
    public function buscarPorDni($dni) {
        $query = "SELECT nombres, apellido_paterno, apellido_materno 
                  FROM " . $this->table_name . " 
                  WHERE dni = :dni 
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':dni', $dni);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            return $row;
        }
        return null;
    }
    public function eliminar($dni) {
        $query = "DELETE FROM " . $this->table_name . " WHERE dni = :dni";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':dni', $dni);
        return $stmt->execute();
    }
}
?>