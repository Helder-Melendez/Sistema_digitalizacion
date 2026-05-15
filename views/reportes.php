<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'ADMINISTRADOR') { header("Location: login.php"); exit(); }
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$total_auditados = $db->query("SELECT COUNT(*) FROM auditoria_calidad")->fetchColumn();
$aprobados = $db->query("SELECT COUNT(*) FROM auditoria_calidad WHERE estado_revision='APROBADO'")->fetchColumn();
$observados = $db->query("SELECT COUNT(*) FROM auditoria_calidad WHERE estado_revision='OBSERVADO'")->fetchColumn();

$p_aprobados = ($total_auditados > 0) ? round(($aprobados / $total_auditados) * 100, 1) : 0;
$p_observados = ($total_auditados > 0) ? round(($observados / $total_auditados) * 100, 1) : 0;

$fallas = $db->query("SELECT SUM(error_nomenclatura) as nom, SUM(error_legibilidad) as leg, SUM(error_folios) as fol FROM auditoria_calidad")->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes | Saneamiento Legal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; color: #334155; }
        .sidebar { width: 240px; height: 100vh; position: fixed; background: #0f172a; color: white; }
        .main-content { margin-left: 240px; padding: 25px 35px; }
        .nav-link { color: #94a3b8; padding: 10px 25px; text-decoration: none; display: flex; align-items: center; font-size: 0.85rem; }
        .nav-link.active { color: white; background: rgba(255,255,255,0.05); border-left: 3px solid #10b981; }
        .report-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-bottom: 25px; }
        .stat-label { font-size: 0.65rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; }
    </style>
</head>
<body>

    <nav class="sidebar shadow d-flex flex-column">
        <div>
            <div class="p-4 fw-bold border-bottom border-secondary mb-3 small text-uppercase">
                <i class="bi bi-shield-check text-success me-2"></i> Saneamiento Legal
            </div>
            
            <a href="dashboard.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>"><i class="bi bi-grid me-2"></i> Dashboard</a>
            <a href="busqueda.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'busqueda.php') ? 'active' : ''; ?>"><i class="bi bi-search me-2"></i> Búsqueda</a>
            <a href="solicitantes.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'solicitantes.php') ? 'active' : ''; ?>"><i class="bi bi-people me-2"></i> Solicitantes</a>
            <a href="expedientes.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'expedientes.php') ? 'active' : ''; ?>"><i class="bi bi-folder2 me-2"></i> Expedientes</a>

            <?php if ($_SESSION['rol'] === 'ADMINISTRADOR' || $_SESSION['rol'] === 'AUDITOR'): ?>
                <a href="auditoria.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'auditoria.php') ? 'active' : ''; ?>"><i class="bi bi-shield-check me-2"></i> Auditoría</a>
            <?php endif; ?>

            <?php if ($_SESSION['rol'] === 'ADMINISTRADOR'): ?>
                <a href="reportes.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'reportes.php') ? 'active' : ''; ?>"><i class="bi bi-file-earmark-bar-graph me-2"></i> Reportes</a>
                <a href="configuracion.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'configuracion.php') ? 'active' : ''; ?>"><i class="bi bi-gear me-2"></i> Configuración</a>
            <?php endif; ?>
        </div>

        <div class="mt-auto border-top border-secondary" style="background-color: rgba(0,0,0,0.15);">
            <div class="p-3 pb-1">
                <div class="small fw-bold text-white text-truncate">
                    <i class="bi bi-person-circle me-2 text-success"></i><?php echo htmlspecialchars($_SESSION['nombres']); ?>
                </div>
                <div class="text-muted text-uppercase" style="font-size: 0.65rem; margin-left: 26px; letter-spacing: 0.5px;">
                    <?php echo $_SESSION['rol']; ?>
                </div>
            </div>
            <a href="../controllers/logout.php" class="nav-link text-danger p-3 pt-2 mb-1 border-0">
                <i class="bi bi-box-arrow-left me-2"></i> Cerrar Sesión
            </a>
        </div>
    </nav>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold m-0">Generación de Reportes</h5>
                <p class="text-muted small m-0">Matriz de cumplimiento y calidad documental.</p>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="report-card shadow-sm">
                    <h6 class="stat-label mb-4">Tabla de Frecuencias</h6>
                    <table class="table table-bordered text-center align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start">ESTADO</th>
                                <th>CANTIDAD (n)</th>
                                <th>PORCENTAJE (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-start fw-bold text-success">Aprobados</td>
                                <td><?php echo $aprobados; ?></td>
                                <td><?php echo $p_aprobados; ?>%</td>
                            </tr>
                            <tr>
                                <td class="text-start fw-bold text-danger">Observados</td>
                                <td><?php echo $observados; ?></td>
                                <td><?php echo $p_observados; ?>%</td>
                            </tr>
                            <tr class="table-light fw-bold">
                                <td class="text-start">TOTAL GENERAL</td>
                                <td><?php echo $total_auditados; ?></td>
                                <td>100%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="report-card shadow-sm">
                    <h6 class="stat-label mb-4">Resumen de Incidencias</h6>
                    <div class="p-3 bg-light rounded-3">
                        <div class="d-flex justify-content-between mb-2 small">
                            <span>Errores de Nomenclatura</span>
                            <span class="badge bg-dark rounded-pill"><?php echo $fallas['nom'] ?? 0; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span>Baja Legibilidad</span>
                            <span class="badge bg-primary rounded-pill"><?php echo $fallas['leg'] ?? 0; ?></span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span>Folios Incompletos</span>
                            <span class="badge bg-secondary rounded-pill"><?php echo $fallas['fol'] ?? 0; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="report-card shadow-sm p-0 overflow-hidden">
            <div class="p-3 border-bottom bg-light">
                <h6 class="m-0 fw-bold small">Vista Previa de la Matriz de Datos</h6>
            </div>
            <div class="p-3 bg-white">
                 <div class="d-flex gap-2 mb-3">
                    <a href="reporte_imprimible.php" target="_blank" class="btn btn-danger fw-bold px-4 shadow-sm btn-sm">
                        <i class="bi bi-file-earmark-pdf me-2"></i> REPORTE PDF
                    </a>
                    <a href="../controllers/exportar_excel.php" class="btn btn-success fw-bold px-4 shadow-sm btn-sm">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i> EXCEL / MATRIZ
                    </a>
                </div>
                <table class="table table-hover mb-0 small align-middle">
                    <thead class="table-light">
                        <tr class="text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                            <th class="px-4 py-3">Código Expediente</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Distrito</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT a.estado_revision, e.codigo_expediente, e.nombres, e.apellido_paterno, e.apellido_materno, e.distrito 
                                  FROM auditoria_calidad a 
                                  JOIN expedientes_saneamiento e ON a.id_expediente = e.id_expediente 
                                  ORDER BY a.fecha_auditoria DESC LIMIT 8";
                        $list = $db->query($query);
                        if ($list->rowCount() > 0):
                            while($r = $list->fetch(PDO::FETCH_ASSOC)):
                        ?>
                        <tr>
                            <td class="px-4 fw-bold"><?php echo $r['codigo_expediente']; ?></td>
                            <td><?php echo $r['nombres']; ?></td>
                            <td><?php echo $r['apellido_paterno'] . " " . $r['apellido_materno']; ?></td>
                            <td><?php echo $r['distrito']; ?></td>
                            <td class="text-center">
                                <span class="badge <?php echo $r['estado_revision']=='APROBADO' ? 'text-success bg-success-subtle' : 'text-danger bg-danger-subtle'; ?> px-2 py-1">
                                    <?php echo $r['estado_revision']; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted small">No hay auditorías registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>