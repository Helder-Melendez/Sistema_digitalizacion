<?php
session_start();
if (!isset($_SESSION['id_usuario'])) { header("Location: login.php"); exit(); }
require_once '../config/database.php';
require_once '../models/Solicitante.php';

$database = new Database();
$db = $database->getConnection();
$solicitanteModel = new Solicitante($db);
$listado = $solicitanteModel->listar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Padrón de Solicitantes - Saneamiento Legal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; font-size: 0.85rem; }
        .sidebar { width: 240px; height: 100vh; position: fixed; background: #0f172a; color: white; }
        .main-content { margin-left: 240px; padding: 30px; }
        .glass-container { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; }
        .upper-input { text-transform: uppercase; }
        .nav-link { color: #94a3b8; padding: 10px 25px; text-decoration: none; display: flex; align-items: center; font-size: 0.85rem;}
        .nav-link.active { color: white; background: rgba(255,255,255,0.05); border-left: 3px solid #10b981; }
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
        <h4 class="fw-bold mb-4">Administración de Solicitantes</h4>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="glass-container shadow-sm border-top border-primary border-4">
                    <h6 class="fw-bold mb-3 text-dark">Nuevo Registro</h6>
                    <form action="../controllers/SolicitanteController.php" method="POST">
                        <input type="hidden" name="action" value="register">
                        <div class="mb-2">
                            <label class="small fw-bold">DNI</label>
                            <input type="text" name="dni" class="form-control form-control-sm" maxlength="8" required>
                        </div>
                        <div class="mb-2">
                            <label class="small fw-bold">NOMBRES</label>
                            <input type="text" name="nombres" class="form-control form-control-sm upper-input" oninput="this.value = this.value.toUpperCase()" required>
                        </div>
                        <div class="mb-2">
                            <label class="small fw-bold">AP. PATERNO</label>
                            <input type="text" name="ap_paterno" class="form-control form-control-sm upper-input" oninput="this.value = this.value.toUpperCase()" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">AP. MATERNO</label>
                            <input type="text" name="ap_materno" class="form-control form-control-sm upper-input" oninput="this.value = this.value.toUpperCase()" required>
                        </div>
                        <button type="submit" class="btn btn-dark btn-sm w-100 py-2 fw-bold">GUARDAR SOLICITANTE</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="glass-container shadow-sm p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">DNI</th>
                                <th>NOMBRE COMPLETO</th>
                                <th class="text-end px-4">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $listado->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td class="px-4 fw-bold text-primary"><?php echo $row['dni']; ?></td>
                                <td class="text-uppercase"><?php echo $row['apellido_paterno'] . " " . $row['apellido_materno'] . ", " . $row['nombres']; ?></td>
                                <td class="text-end px-4">
                                    <button class="btn btn-sm btn-outline-primary btn-modificar me-1" 
                                            data-dni="<?php echo $row['dni']; ?>" data-nom="<?php echo $row['nombres']; ?>"
                                            data-pat="<?php echo $row['apellido_paterno']; ?>" data-mat="<?php echo $row['apellido_materno']; ?>"
                                            data-bs-toggle="modal" data-bs-target="#modalEdit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="../controllers/SolicitanteController.php?action=delete&dni=<?php echo $row['dni']; ?>" 
                                       class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Está seguro de eliminar este registro?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white">
                    <h6 class="modal-title small fw-bold text-uppercase">Modificar Ciudadano</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="../controllers/SolicitanteController.php" method="POST" class="p-4">
                    <input type="hidden" name="action" value="update">
                    <div class="mb-3">
                        <label class="small fw-bold">DNI</label>
                        <input type="text" name="dni" id="m_dni" class="form-control bg-light" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">NOMBRES</label>
                        <input type="text" name="nombres" id="m_nombres" class="form-control upper-input" oninput="this.value = this.value.toUpperCase()" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="small fw-bold">AP. PATERNO</label>
                            <input type="text" name="ap_paterno" id="m_pat" class="form-control upper-input" oninput="this.value = this.value.toUpperCase()" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="small fw-bold">AP. MATERNO</label>
                            <input type="text" name="ap_materno" id="m_mat" class="form-control upper-input" oninput="this.value = this.value.toUpperCase()" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">ACTUALIZAR DATOS</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('click', function (e) {
            if (e.target.closest('.btn-modificar')) {
                const btn = e.target.closest('.btn-modificar');
                document.getElementById('m_dni').value = btn.getAttribute('data-dni');
                document.getElementById('m_nombres').value = btn.getAttribute('data-nom');
                document.getElementById('m_pat').value = btn.getAttribute('data-pat');
                document.getElementById('m_mat').value = btn.getAttribute('data-mat');
            }
        });
    </script>
</body>
</html>