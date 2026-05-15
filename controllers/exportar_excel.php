<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$filename = "Matriz_Detallada_Saneamiento_" . date('d-m-Y_His') . ".csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // Soporte para tildes/eñes

// Cabeceras actualizadas
fputcsv($output, array(
    'CÓDIGO EXPEDIENTE', 
    'NOMBRE', 
    'APELLIDOS', 
    'DISTRITO',
    'ESTADO REVISIÓN', 
    'ERROR NOMENCLATURA', 
    'ERROR LEGIBILIDAD', 
    'ERROR FOLIOS', 
    'OBSERVACIONES', 
    'FECHA AUDITORÍA'
));

// Consulta con JOIN para traer los datos del beneficiario
$query = "SELECT e.codigo_expediente, e.nombres, 
                 CONCAT(e.apellido_paterno, ' ', e.apellido_materno) as apellidos,
                 e.distrito, a.estado_revision, a.error_nomenclatura, 
                 a.error_legibilidad, a.error_folios, a.observaciones, a.fecha_auditoria 
          FROM auditoria_calidad a 
          JOIN expedientes_saneamiento e ON a.id_expediente = e.id_expediente";

$stmt = $db->query($query);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

fclose($output);
exit();