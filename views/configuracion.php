<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'ADMINISTRADOR') { header("Location: login.php"); exit(); }
require_once '../config/database.php';
require_once '../models/Usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuarioModel = new Usuario($db);
$usuarios = $usuarioModel->listar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración de Usuarios | DRA Amazonas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; font-size: 0.85rem; color: #334155; }
        .sidebar { width: 240px; height: 100vh; position: fixed; background: #0f172a; color: white; z-index: 1000; }
        .main-content { margin-left: 240px; padding: 30px; }
        .nav-link { color: #94a3b8; padding: 10px 25px; text-decoration: none; display: flex; align-items: center; font-size: 0.85rem;}
        .nav-link.active { color: white; background: rgba(255,255,255,0.05); border-left: 3px solid #10b981; }
        .glass-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; }
        .table-custom th { font-size: 0.65rem; text-transform: uppercase; color: #64748b; background: #f8fafc; padding: 12px; border: none; }
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
            <h4 class="fw-bold m-0">Gestión de Usuarios</h4>
            <?php if(isset($_GET['status'])): ?>
                <?php if($_GET['status'] == 'success' || $_GET['status'] == 'edit_ok' || $_GET['status'] == 'delete_ok'): ?>
                    <span class="badge bg-success p-2 px-3"><i class="bi bi-check-circle me-1"></i> Operación Exitosa</span>
                <?php elseif($_GET['status'] == 'duplicado'): ?>
                    <span class="badge bg-danger p-2 px-3"><i class="bi bi-x-octagon-fill me-1"></i> Error: El usuario ya existe</span>
                <?php else: ?>
                    <span class="badge bg-secondary p-2 px-3"><i class="bi bi-exclamation-triangle me-1"></i> Error de sistema</span>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="glass-card shadow-sm border-top border-dark border-4">
                    <h6 class="fw-bold mb-3 text-dark">Registrar Operador</h6>
                    <form action="../controllers/UsuarioController.php" method="POST">
                        <input type="hidden" name="action" value="register">
                        <div class="mb-2">
                            <label class="small fw-bold">Nombres</label>
                            <input type="text" name="nombres" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-2">
                            <label class="small fw-bold">Apellidos</label>
                            <input type="text" name="apellidos" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-2">
                            <label class="small fw-bold">Usuario</label>
                            <input type="text" name="usuario" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-2">
                            <label class="small fw-bold">Contraseña</label>
                            <input type="password" name="password" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Rol</label>
                            <select name="rol" class="form-select form-select-sm" required>
                                <option value="DIGITALIZADOR">Digitalizador</option>
                                <option value="AUDITOR">Auditor de Calidad</option>
                                <option value="ADMINISTRADOR">Administrador</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-dark btn-sm w-100 fw-bold py-2">CREAR USUARIO</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="glass-card shadow-sm p-0 overflow-hidden">
                    <table class="table table-hover align-middle mb-0 table-custom">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Personal</th>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th class="text-end px-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($u = $usuarios->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td class="px-4 fw-bold"><?php echo htmlspecialchars($u['nombres'] . " " . $u['apellidos']); ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($u['usuario']); ?></span></td>
                                <td><small class="fw-bold text-secondary"><?php echo $u['rol']; ?></small></td>
                                <td class="text-end px-4">
                                    <button class="btn btn-sm btn-outline-primary btn-modificar border-0" 
                                            data-id="<?php echo $u['id_usuario']; ?>"
                                            data-nom="<?php echo htmlspecialchars($u['nombres']); ?>"
                                            data-ape="<?php echo htmlspecialchars($u['apellidos']); ?>"
                                            data-usu="<?php echo htmlspecialchars($u['usuario']); ?>"
                                            data-rol="<?php echo $u['rol']; ?>"
                                            data-bs-toggle="modal" data-bs-target="#modalEdit">
                                        <i class="bi bi-pencil-square fs-5"></i>
                                    </button>
                                    
                                    <a href="../controllers/UsuarioController.php?action=delete&id=<?php echo $u['id_usuario']; ?>" 
                                       class="btn btn-sm btn-outline-danger border-0" 
                                       onclick="return confirm('¿Eliminar acceso de este usuario?')">
                                        <i class="bi bi-trash3-fill fs-5"></i>
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
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white border-0 py-3">
                    <h6 class="modal-title fw-bold small text-uppercase">Modificar Usuario</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="../controllers/UsuarioController.php" method="POST" class="p-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id_usuario" id="m_id">
                    
                    <div class="mb-2">
                        <label class="small fw-bold">Nombres</label>
                        <input type="text" name="nombres" id="m_nombres" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="small fw-bold">Apellidos</label>
                        <input type="text" name="apellidos" id="m_apellidos" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="small fw-bold">Usuario</label>
                        <input type="text" name="usuario" id="m_usuario" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="small fw-bold">Contraseña (Vacío para mantener)</label>
                        <input type="password" name="password" class="form-control form-control-sm" placeholder="••••••••">
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Rol</label>
                        <select name="rol" id="m_rol" class="form-select form-select-sm" required>
                            <option value="DIGITALIZADOR">Digitalizador</option>
                            <option value="AUDITOR">Auditor de Calidad</option>
                            <option value="ADMINISTRADOR">Administrador</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary bg-dark border-0 w-100 fw-bold">GUARDAR CAMBIOS</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('click', function (e) {
            if (e.target.closest('.btn-modificar')) {
                const btn = e.target.closest('.btn-modificar');
                document.getElementById('m_id').value = btn.getAttribute('data-id');
                document.getElementById('m_nombres').value = btn.getAttribute('data-nom');
                document.getElementById('m_apellidos').value = btn.getAttribute('data-ape');
                document.getElementById('m_usuario').value = btn.getAttribute('data-usu');
                document.getElementById('m_rol').value = btn.getAttribute('data-rol');
            }
        });
    </script>
</body>
</html>