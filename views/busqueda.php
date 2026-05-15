<?php
session_start();
if (!isset($_SESSION['id_usuario'])) { header("Location: login.php"); exit(); }
require_once '../config/database.php';
require_once '../models/Expediente.php';

$database = new Database();
$db = $database->getConnection();
$expedienteModel = new Expediente($db);

$resultados = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $resultados = $expedienteModel->buscarAvanzado($_POST);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Búsqueda Avanzada - Saneamiento Legal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; font-size: 0.8rem; color: #334155; }
        .sidebar { width: 240px; height: 100vh; position: fixed; background: #0f172a; color: white; z-index: 100; }
        .main-content { margin-left: 240px; padding: 30px; }
        .glass-card { background: white; border-radius: 12px; padding: 25px; margin-bottom: 25px; border: none; }
        .form-label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 600; color: #64748b; margin-bottom: 4px; }
        .nav-link { color: #94a3b8; padding: 10px 25px; text-decoration: none; display: flex; align-items: center; border-left: 3px solid transparent; font-size: 0.85rem;}
        .nav-link.active { color: white; background: rgba(255,255,255,0.03); border-left: 3px solid #10b981; }
        .btn-primary-dark { background: #0f172a; border: none; font-weight: 600; font-size: 0.75rem; padding: 10px; color: white; }
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
        <div class="mb-4">
            <h5 class="fw-bold m-0">Filtros de Búsqueda Especializada</h5>
        </div>

        <div class="glass-card shadow-sm">
            <form action="busqueda.php" method="POST">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Año</label>
                        <select name="anio" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="2026">2026</option>
                            <option value="2025">2025</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">DNI</label>
                        <input type="text" name="dni" class="form-control form-control-sm" maxlength="8">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nombres</label>
                        <input type="text" name="nombres" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Apellidos</label>
                        <input type="text" name="apellidos" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" name="f_inicio" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" name="f_fin" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary-dark btn-sm w-100 rounded-3">
                            <i class="bi bi-search me-2"></i> BUSCAR EXPEDIENTE
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-3 border shadow-sm overflow-hidden">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr class="table-light">
                        <th class="px-4 py-3" style="font-size: 0.65rem; text-transform: uppercase; color: #64748b;">Código</th>
                        <th style="font-size: 0.65rem; text-transform: uppercase; color: #64748b;">Beneficiario</th>
                        <th class="text-center" style="font-size: 0.65rem; text-transform: uppercase; color: #64748b;">Fecha Registro</th>
                        <th class="text-end px-4" style="font-size: 0.65rem; text-transform: uppercase; color: #64748b;">Archivo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultados && $resultados->rowCount() > 0): ?>
                        <?php while($row = $resultados->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td class="px-4 fw-bold"><?php echo $row['codigo_expediente']; ?></td>
                            <td>
                                <div class="fw-bold"><?php echo $row['apellido_paterno'] . " " . $row['apellido_materno'] . ", " . $row['nombres']; ?></div>
                                <div class="text-muted small">DNI: <?php echo $row['dni']; ?></div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border"><?php echo date('d/m/Y', strtotime($row['fecha_ingreso_fisico'])); ?></span>
                            </td>
                            <td class="text-end px-4">
                                <?php if($row['ruta_archivo']): ?>
                                    <a href="../uploads/<?php echo $row['ruta_archivo']; ?>" target="_blank" class="btn btn-sm btn-outline-primary border-0">
                                        <i class="bi bi-file-earmark-pdf fs-5"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted small">
                                <i class="bi bi-info-circle me-2"></i> Use los filtros superiores para iniciar la búsqueda.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>