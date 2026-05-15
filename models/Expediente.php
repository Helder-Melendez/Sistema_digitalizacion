<?php
class Expediente {
    private $conn;
    private $table_name = "expedientes_saneamiento";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Función para generar el código correlativo automático
    public function generarSiguienteCodigo() {
        $año = "2026";
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $siguiente_numero = $row['total'] + 1;
        // Formatea a 5 dígitos: 2026-00001
        return $año . "-" . str_pad($siguiente_numero, 5, "0", STR_PAD_LEFT);
    }

    public function crear($datos) {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET codigo_expediente=:codigo, dni=:dni, nombres=:nombres, 
                      apellido_paterno=:ap_pat, apellido_materno=:ap_mat, n_titulo=:titulo,
                      provincia=:provincia, distrito=:distrito, sector=:sector, 
                      tipo_documento=:tipo_doc, ruta_archivo=:ruta, tipo_procedimiento=:procedimiento,
                      nombre_predio_comunidad=:predio, fecha_ingreso_fisico=:fecha";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($datos);
        return true;
    }

    public function listar() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id_expediente DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    public function buscarAvanzado($filtros) {
    $sql = "SELECT * FROM expedientes_saneamiento WHERE 1=1";
    $params = [];

    // Filtro por Año
    if (!empty($filtros['anio'])) {
        $sql .= " AND YEAR(fecha_ingreso_fisico) = :anio";
        $params[':anio'] = $filtros['anio'];
    }

    // Filtro por DNI
    if (!empty($filtros['dni'])) {
        $sql .= " AND dni = :dni";
        $params[':dni'] = $filtros['dni'];
    }

    // Filtro por Nombres
    if (!empty($filtros['nombres'])) {
        $sql .= " AND nombres LIKE :nom";
        $params[':nom'] = "%" . $filtros['nombres'] . "%";
    }

    // Filtro por Apellidos (busca en Paterno o Materno)
    if (!empty($filtros['apellidos'])) {
        $sql .= " AND (apellido_paterno LIKE :ape OR apellido_materno LIKE :ape)";
        $params[':ape'] = "%" . $filtros['apellidos'] . "%";
    }

    // Filtro por Rango de Fechas
    if (!empty($filtros['f_inicio'])) {
        $sql .= " AND fecha_ingreso_fisico >= :f_inicio";
        $params[':f_inicio'] = $filtros['f_inicio'];
    }
    if (!empty($filtros['f_fin'])) {
        $sql .= " AND fecha_ingreso_fisico <= :f_fin";
        $params[':f_fin'] = $filtros['f_fin'];
    }

    $sql .= " ORDER BY id_expediente DESC";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}
    }
?>
