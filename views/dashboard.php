<?php
session_start();
if (!isset($_SESSION['id_usuario'])) { header("Location: login.php"); exit(); }
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// --- MÉTRICAS ---
$total_fisicos = $db->query("SELECT COUNT(*) FROM expedientes_saneamiento")->fetchColumn();
$muestra_procesada = $db->query("SELECT COUNT(*) FROM auditoria_calidad")->fetchColumn();
$fallos_detectados = $db->query("SELECT COUNT(*) FROM auditoria_calidad WHERE estado_revision = 'OBSERVADO'")->fetchColumn();
$tasa_error = ($muestra_procesada > 0) ? ($fallos_detectados / $muestra_procesada) * 100 : 0;

$dist = $db->query("SELECT SUM(error_nomenclatura) as nom, SUM(error_legibilidad) as leg, SUM(error_folios) as fol FROM auditoria_calidad")->fetch(PDO::FETCH_ASSOC);
$err_nom = $dist['nom'] ?? 0; $err_leg = $dist['leg'] ?? 0; $err_fol = $dist['fol'] ?? 0;

$stmt_evol = $db->query("SELECT MONTH(fecha_ingreso_fisico) as mes, COUNT(*) as cantidad FROM expedientes_saneamiento WHERE YEAR(fecha_ingreso_fisico) = 2026 GROUP BY mes ORDER BY mes ASC");
$meses_data = array_fill(1, 12, 0);
while($row = $stmt_evol->fetch(PDO::FETCH_ASSOC)) { $meses_data[$row['mes']] = $row['cantidad']; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Saneamiento Legal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; color: #334155; }
        .sidebar { width: 240px; height: 100vh; position: fixed; background: #0f172a; color: white; z-index: 1000; }
        .main-content { margin-left: 240px; padding: 25px 35px; }
        .nav-link { color: #94a3b8; padding: 10px 25px; text-decoration: none; display: flex; align-items: center; font-size: 0.85rem; }
        .nav-link.active { color: white; background: rgba(255,255,255,0.05); border-left: 3px solid #10b981; }
        .metric-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 22px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .text-label { font-size: 0.65rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; }
        .metric-value { font-size: 1.8rem; font-weight: 800; margin: 0; }
        .color-total { color: #0f172a; } .color-audit { color: #2563eb; } .color-obs { color: #f59e0b; } .color-error { color: #dc2626; }
        .chart-container { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px; height: 320px; }
        canvas { max-height: 220px !important; }
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
            <a href="seguimiento.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'seguimiento.php') ? 'active' : ''; ?>"><i class="bi bi-clock-history me-2"></i> Seguimiento de Expedientes</a>

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
                <h5 class="fw-bold m-0">Panel de Control Operativo</h5>
                <p class="text-muted small m-0">Gestión de calidad Dirección Regional Agraria</p>
            </div>
            <div class="d-flex gap-2">
                <a href="../controllers/exportar_excel.php" class="btn btn-sm btn-outline-success fw-bold px-3">
                    <i class="bi bi-download me-1"></i> EXCEL / SPSS
                </a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="text-label">Digitalizados</div>
                    <div class="d-flex align-items-center justify-content-between mt-1">
                        <p class="metric-value color-total"><?php echo $total_fisicos; ?></p>
                        <i class="bi bi-archive-fill color-total fs-4"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="text-label">Muestra Auditada</div>
                    <div class="d-flex align-items-center justify-content-between mt-1">
                        <p class="metric-value color-audit"><?php echo $muestra_procesada; ?></p>
                        <i class="bi bi-patch-check-fill color-audit fs-4"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="text-label">Obs. Detectadas</div>
                    <div class="d-flex align-items-center justify-content-between mt-1">
                        <p class="metric-value color-obs"><?php echo $fallos_detectados; ?></p>
                        <i class="bi bi-exclamation-octagon-fill color-obs fs-4"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="text-label">Tasa de Error</div>
                    <div class="d-flex align-items-center justify-content-between mt-1">
                        <p class="metric-value color-error"><?php echo number_format($tasa_error, 1); ?>%</p>
                        <i class="bi bi-graph-down-arrow color-error fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="chart-container shadow-sm">
                    <h6 class="fw-bold mb-4 small text-uppercase text-muted">Producción Mensual 2026</h6>
                    <canvas id="chartEvolucion"></canvas>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="chart-container shadow-sm">
                    <h6 class="fw-bold mb-4 small text-uppercase text-muted">Distribución de Errores</h6>
                    <div style="height: 160px;"><canvas id="chartErrores"></canvas></div>
                    <div class="mt-4 pt-3 border-top small">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Nomenclatura</span><span class="fw-bold"><?php echo $err_nom; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Legibilidad</span><span class="fw-bold"><?php echo $err_leg; ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Folios</span><span class="fw-bold"><?php echo $err_fol; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('chartEvolucion'), {
            type: 'bar',
            data: {
                labels: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
                datasets: [{ data: [<?php echo implode(',', $meses_data); ?>], backgroundColor: '#0f172a', borderRadius: 4 }]
            },
            options: { maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });

        new Chart(document.getElementById('chartErrores'), {
            type: 'doughnut',
            data: {
                labels: ['Nom', 'Leg', 'Fol'],
                datasets: [{ data: [<?php echo "$err_nom, $err_leg, $err_fol"; ?>], backgroundColor: ['#0f172a', '#2563eb', '#94a3b8'], borderWidth: 0 }]
            },
            options: { maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false } } }
        });
    </script>
</body>
</html>