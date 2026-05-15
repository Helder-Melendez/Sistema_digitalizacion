<?php
session_start();
if (!isset($_SESSION['id_usuario'])) { header("Location: login.php"); exit(); }
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Estadísticas para los cuadros resumen
$auditados = $db->query("SELECT COUNT(*) FROM auditoria_calidad")->fetchColumn();
$aprobados = $db->query("SELECT COUNT(*) FROM auditoria_calidad WHERE estado_revision='APROBADO'")->fetchColumn();
$observados = $db->query("SELECT COUNT(*) FROM auditoria_calidad WHERE estado_revision='OBSERVADO'")->fetchColumn();
$p_aprobados = ($auditados > 0) ? round(($aprobados / $auditados) * 100, 1) : 0;
$p_observados = ($auditados > 0) ? round(($observados / $auditados) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte_Tecnico_Final_<?php echo date('d_m_Y'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Times New Roman', serif; padding: 20px; color: #000; }
        .header-line { border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 25px; }
        h4, h5 { font-weight: bold; text-transform: uppercase; margin-top: 25px; }
        table { border: 1px solid #000 !important; font-size: 10pt; }
        th { background-color: #f2f2f2 !important; border: 1px solid #000 !important; text-align: center; }
        td { border: 1px solid #000 !important; }
        .page-break { page-break-before: always; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">

    <div class="header-line d-flex justify-content-between">
        <div class="small">
            <strong>DIRECCIÓN REGIONAL AGRARIA AMAZONAS</strong><br>
            Saneamiento Físico Legal de la Propiedad Agraria
        </div>
        <div class="text-end small">
            Fecha: <?php echo date('d/m/Y'); ?><br>
            Sistema de Gestión Documental V1.0
        </div>
    </div>

    <div class="text-center mb-4">
        <h4>Informe Técnico de Control de Calidad</h4>
        <p>Proyecto: Optimización del Proceso de Digitalización de Archivos</p>
    </div>

    <h5>1. Cuadro Estadístico de Cumplimiento</h5>
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th class="text-start">ESTADO DE DIGITALIZACIÓN</th>
                <th>FRECUENCIA (n)</th>
                <th>PORCENTAJE (%)</th>
            </tr>
        </thead>
        <tbody>
            <tr><td class="text-start">Expedientes Aprobados</td><td><?php echo $aprobados; ?></td><td><?php echo $p_aprobados; ?>%</td></tr>
            <tr><td class="text-start">Expedientes Observados</td><td><?php echo $observados; ?></td><td><?php echo $p_observados; ?>%</td></tr>
            <tr class="fw-bold"><td class="text-start">TOTAL AUDITADO</td><td><?php echo $auditados; ?></td><td>100%</td></tr>
        </tbody>
    </table>

    <h5>2. Relación Detallada de Expedientes Auditados</h5>
    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>CÓDIGO</th>
                <th>NOMBRE</th>
                <th>APELLIDOS</th>
                <th>DISTRITO</th>
                <th>ESTADO</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT e.codigo_expediente, e.nombres, e.apellido_paterno, e.apellido_materno, e.distrito, a.estado_revision 
                      FROM auditoria_calidad a 
                      JOIN expedientes_saneamiento e ON a.id_expediente = e.id_expediente 
                      ORDER BY e.codigo_expediente ASC";
            $res = $db->query($query);
            while($row = $res->fetch(PDO::FETCH_ASSOC)):
            ?>
            <tr>
                <td class="text-center fw-bold"><?php echo $row['codigo_expediente']; ?></td>
                <td><?php echo $row['nombres']; ?></td>
                <td><?php echo $row['apellido_paterno'] . " " . $row['apellido_materno']; ?></td>
                <td class="text-center"><?php echo $row['distrito']; ?></td>
                <td class="text-center">
                    <?php echo $row['estado_revision']; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="mt-5 pt-4">
        <div class="row">
            <div class="col-6 text-center">
                <br>__________________________<br>
                <span class="small">Responsable de Digitalización</span>
            </div>
            <div class="col-6 text-center">
                <br>__________________________<br>
                <span class="small">Auditor de Calidad</span>
            </div>
        </div>
    </div>

    <div class="no-print text-center mt-5">
        <button onclick="window.close()" class="btn btn-secondary btn-sm">Cerrar Reporte</button>
    </div>
</body>
</html>