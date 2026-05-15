<?php
class Auditoria {
    private $conn;
    private $table_name = "auditoria_calidad";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function registrarAuditoria($datos) {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET id_expediente=:id_exp, estado_revision=:estado, 
                      error_nomenclatura=:nom, error_legibilidad=:leg, 
                      error_folios=:fol, observaciones=:obs, auditor_id=:auditor";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute($datos);
    }

    public function obtenerPendientes() {
        // Selecciona expedientes que NO tienen registro en la tabla auditoria_calidad
        $query = "SELECT e.* FROM expedientes_saneamiento e 
                  LEFT JOIN auditoria_calidad a ON e.id_expediente = a.id_expediente 
                  WHERE a.id_auditoria IS NULL ORDER BY e.id_expediente ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}