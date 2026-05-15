<?php
session_start();
// Se retiró el bloqueo: Ahora el Digitalizador sí puede acceder
if (!isset($_SESSION['id_usuario'])) { header("Location: login.php"); exit(); }
require_once '../config/database.php';
require_once '../models/Expediente.php';

$database = new Database();
$db = $database->getConnection();
$expedienteModel = new Expediente($db);

$nuevoCodigo = $expedienteModel->generarSiguienteCodigo();
$listado = $expedienteModel->listar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Expedientes - Saneamiento Legal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; font-size: 0.8rem; color: #334155; }
        .sidebar { width: 240px; height: 100vh; position: fixed; background: #0f172a; color: white; z-index: 100; }
        .main-content { margin-left: 240px; padding: 30px; }
        .glass-container { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; height: calc(100vh - 120px); overflow-y: auto; }
        .form-label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 600; color: #64748b; margin-bottom: 4px; }
        .form-control-sm, .form-select-sm { border-radius: 6px; border: 1px solid #cbd5e1; padding: 8px; font-size: 0.8rem; }
        .btn-primary-dark { background: #0f172a; border: none; font-weight: 600; font-size: 0.75rem; padding: 10px; transition: all 0.3s; }
        .btn-primary-dark:hover { background: #1e293b; transform: translateY(-1px); }
        .nav-link { color: #94a3b8; padding: 10px 25px; text-decoration: none; display: flex; align-items: center; font-size: 0.85rem;}
        .nav-link.active { color: white; background: rgba(255,255,255,0.05); border-left: 3px solid #10b981; }
        .table-custom th { font-size: 0.65rem; text-transform: uppercase; color: #64748b; background: #f8fafc; padding: 12px; border: none; }
        .is-valid-dni { border-color: #10b981 !important; background-color: #f0fdf4 !important; }
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
            <h5 class="fw-bold m-0">Registro y Control de Expedientes</h5>
        </div>

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="glass-container shadow-sm">
                    <button type="button" class="btn btn-sm btn-outline-primary mb-4 w-100 fw-bold" data-bs-toggle="modal" data-bs-target="#modalSolicitante">
                        <i class="bi bi-person-plus me-2"></i>AÑADIR NUEVO SOLICITANTE
                    </button>

                    <form action="../controllers/ExpedienteController.php" method="POST" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label text-primary">Cód. Expediente</label>
                                <input type="text" name="codigo" class="form-control form-control-sm bg-light fw-bold" value="<?php echo $nuevoCodigo; ?>" readonly>
                            </div>
                            <div class="col-6">
                                <label class="form-label">DNI Beneficiario</label>
                                <input type="text" name="dni" id="inputDNI" class="form-control form-control-sm" maxlength="8" required placeholder="Buscar DNI...">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Nombres Completos</label>
                                <input type="text" name="nombres" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Apellido Paterno</label>
                                <input type="text" name="ap_pat" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Apellido Materno</label>
                                <input type="text" name="ap_mat" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">N° Título</label>
                                <input type="text" name="titulo" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Procedimiento</label>
                                <select name="procedimiento" class="form-select form-select-sm">
                                    <option value="Titulación">Titulación</option>
                                    <option value="Deslinde">Deslinde</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Distrito</label>
                                <input type="text" name="distrito" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Sector / Predio</label>
                                <input type="text" name="sector" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Subir Documento (PDF)</label>
                                <input type="file" name="archivo" class="form-control form-control-sm" accept=".pdf" required>
                            </div>
                            <input type="hidden" name="provincia" value="Chachapoyas">
                            <input type="hidden" name="tipo_doc" value="Expediente Digital">

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary-dark w-100 rounded-3">
                                    <i class="bi bi-cloud-arrow-up me-2"></i> FINALIZAR REGISTRO
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="glass-container shadow-sm p-0">
                    <div class="p-3 border-bottom bg-light rounded-top">
                        <h6 class="fw-bold m-0 small">EXPEDIENTES REGISTRADOS (2026)</h6>
                    </div>
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Beneficiario</th>
                                <th>Sector</th>
                                <th class="text-end">PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $listado->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr class="align-middle">
                                <td class="fw-bold px-3"><?php echo $row['codigo_expediente']; ?></td>
                                <td>
                                    <div class="fw-bold"><?php echo $row['nombres']; ?></div>
                                    <div class="text-muted" style="font-size: 0.7rem;"><?php echo $row['apellido_paterno']; ?></div>
                                </td>
                                <td class="small"><?php echo $row['sector']; ?></td>
                                <td class="text-end px-3">
                                    <a href="../uploads/<?php echo $row['ruta_archivo']; ?>" target="_blank" class="btn btn-sm btn-light border text-primary"><i class="bi bi-file-pdf"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalSolicitante" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white border-0 py-3">
                    <h6 class="modal-title fw-bold small">NUEVO SOLICITANTE</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="../controllers/SolicitanteController.php" method="POST" class="p-4">
                    <input type="hidden" name="action" value="register">
                    <div class="mb-3">
                        <label class="form-label">DNI</label>
                        <input type="text" name="dni" class="form-control form-control-sm" maxlength="8" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombres</label>
                        <input type="text" name="nombres" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellido Paterno</label>
                        <input type="text" name="ap_paterno" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellido Materno</label>
                        <input type="text" name="ap_materno" class="form-control form-control-sm" required>
                    </div>
                    <button type="submit" class="btn btn-primary-dark w-100 mt-2">GUARDAR EN PADRÓN</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('inputDNI').addEventListener('input', function(e) {
        let dni = e.target.value;
        if (dni.length === 8) {
            fetch(`../controllers/buscar_solicitante.php?dni=${dni}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        document.getElementsByName('nombres')[0].value = data.nombres;
                        document.getElementsByName('ap_pat')[0].value = data.apellido_paterno;
                        document.getElementsByName('ap_mat')[0].value = data.apellido_materno;
                        e.target.classList.add('is-valid-dni');
                    } else {
                        e.target.classList.remove('is-valid-dni');
                        alert("DNI no encontrado en el padrón.");
                    }
                });
        }
    });
    </script>
</body>
</html>