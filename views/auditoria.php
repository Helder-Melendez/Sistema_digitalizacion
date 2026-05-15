<?php
session_start();
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] !== 'AUDITOR' && $_SESSION['rol'] !== 'ADMINISTRADOR')) { header("Location: login.php"); exit(); }
require_once '../config/database.php';
require_once '../models/Auditoria.php';

$database = new Database();
$db = $database->getConnection();
$auditoriaModel = new Auditoria($db);
$pendientes = $auditoriaModel->obtenerPendientes();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Auditoría de Calidad - Saneamiento Legal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f1f5f9; font-family: 'Inter', sans-serif; font-size: 0.8rem; overflow: hidden; }
        .sidebar { width: 240px; height: 100vh; position: fixed; background: #0f172a; color: white; z-index: 1000; }
        .main-content { margin-left: 240px; height: 100vh; display: flex; flex-direction: column; }
        .work-area { display: flex; flex: 1; overflow: hidden; background: #334155; }
        .pdf-panel { flex: 1; position: relative; overflow: hidden; background: #334155; }
        #pdf-viewer { width: 100%; height: 100%; border: none; position: absolute; top: 0; left: 0; z-index: 5; }
        #no-file { width: 100%; height: 100%; display: flex; z-index: 1; }
        .data-panel { width: 420px; background: white; overflow-y: auto; padding: 30px; border-left: 1px solid #e2e8f0; z-index: 10; }
        .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; margin-bottom: 20px; }
        .label-mini { font-size: 0.6rem; text-transform: uppercase; font-weight: 800; color: #94a3b8; display: block; }
        .val-mini { font-size: 0.85rem; font-weight: 600; color: #1e293b; display: block; }
        .nav-link { color: #94a3b8; padding: 10px 25px; text-decoration: none; display: flex; align-items: center; font-size: 0.85rem;}
        .nav-link.active { color: white; background: rgba(255,255,255,0.03); border-left: 4px solid #10b981; }
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
        <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center shadow-sm">
            <h6 class="fw-bold m-0 text-dark">Control de Calidad Documental</h6>
            <select id="exp-selector" class="form-select form-select-sm" style="width: 380px;">
                <option value="">-- Seleccionar Expediente para Visualizar --</option>
                <?php while($exp = $pendientes->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?php echo $exp['id_expediente']; ?>">
                        <?php echo $exp['codigo_expediente']; ?> | <?php echo $exp['apellido_paterno']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="work-area">
            <div class="pdf-panel">
                <div id="no-file" class="flex-column align-items-center justify-content-center text-white-50">
                    <i class="bi bi-file-earmark-pdf" style="font-size: 5rem; opacity: 0.2;"></i>
                    <p class="mt-3">Seleccione un expediente para visualizar</p>
                </div>
                <iframe id="pdf-viewer" src="" style="display:none;"></iframe>
            </div>

            <div class="data-panel">
                <div id="data-content" style="display: none;">
                    <h6 class="fw-bold mb-3 text-primary">Datos del Registro Original</h6>
                    <div class="info-box">
                        <div class="row g-3">
                            <div class="col-12 border-bottom pb-2">
                                <span class="label-mini">Titular / Beneficiario</span>
                                <span class="val-mini text-uppercase" id="view_nombre">--</span>
                            </div>
                            <div class="col-6"><span class="label-mini">DNI</span><span class="val-mini" id="view_dni">--</span></div>
                            <div class="col-6"><span class="label-mini">N° Título</span><span class="val-mini" id="view_titulo">--</span></div>
                            <div class="col-6"><span class="label-mini">Sector</span><span class="val-mini" id="view_sector">--</span></div>
                            <div class="col-6"><span class="label-mini">Distrito</span><span class="val-mini" id="view_distrito">--</span></div>
                        </div>
                    </div>

                    <form action="../controllers/AuditoriaController.php" method="POST">
                        <input type="hidden" name="id_expediente" id="form_id_exp">
                        <input type="hidden" name="auditor" value="<?php echo $_SESSION['id_usuario']; ?>">

                        <label class="label-mini mb-2">Veredicto de Calidad</label>
                        <div class="btn-group w-100 mb-4 shadow-sm">
                            <input type="radio" class="btn-check" name="estado" id="aprobado" value="APROBADO" checked>
                            <label class="btn btn-outline-success btn-sm fw-bold py-2" for="aprobado">APROBADO</label>
                            <input type="radio" class="btn-check" name="estado" id="observado" value="OBSERVADO">
                            <label class="btn btn-outline-danger btn-sm fw-bold py-2" for="observado">OBSERVADO</label>
                        </div>

                        <div class="card p-3 mb-4 bg-light border-0">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="nom" value="1" id="e1">
                                <label class="form-check-label small" for="e1">Error de Nomenclatura</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="leg" value="1" id="e2">
                                <label class="form-check-label small" for="e2">Baja Legibilidad</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="fol" value="1" id="e3">
                                <label class="form-check-label small" for="e3">Folios Saltados / Incompletos</label>
                            </div>
                        </div>

                        <textarea name="obs" class="form-control form-control-sm mb-4" rows="2" placeholder="Observaciones técnicas..."></textarea>
                        <button type="submit" class="btn btn-dark w-100 fw-bold py-3">FINALIZAR AUDITORÍA</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('exp-selector').addEventListener('change', function() {
            const id = this.value;
            const pdfViewer = document.getElementById('pdf-viewer');
            const placeholder = document.getElementById('no-file');
            const dataContent = document.getElementById('data-content');

            if (!id) {
                pdfViewer.style.display = 'none';
                placeholder.style.display = 'flex';
                dataContent.style.display = 'none';
                return;
            }

            fetch(`../controllers/obtener_expediente.php?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.ruta_archivo) {
                        const ruta = `../uploads/${data.ruta_archivo}`;
                        placeholder.setAttribute("style", "display: none !important");
                        pdfViewer.style.display = 'block';
                        pdfViewer.src = ruta;

                        document.getElementById('view_nombre').innerText = `${data.nombres} ${data.apellido_paterno} ${data.apellido_materno}`;
                        document.getElementById('view_dni').innerText = data.dni;
                        document.getElementById('view_titulo').innerText = data.n_titulo;
                        document.getElementById('view_sector').innerText = data.sector;
                        document.getElementById('view_distrito').innerText = data.distrito;
                        document.getElementById('form_id_exp').value = data.id_expediente;
                        dataContent.style.display = 'block';
                    }
                });
        });
    </script>
</body>
</html>