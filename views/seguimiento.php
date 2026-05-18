<?php
session_start();
// Control estricto de seguridad de la DRA Amazonas
if (!isset($_SESSION['id_usuario'])) { header("Location: login.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguimiento de Expedientes - Saneamiento Legal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Ajustes de pantalla completa para congelar el scroll externo */
        html, body { height: 100%; margin: 0; padding: 0; overflow: hidden; background-color: #f8fafc; font-family: 'Inter', sans-serif; }
        
        /* Barra lateral idéntica a tu módulo de búsqueda y dashboard */
        .sidebar { width: 240px; height: 100vh; position: fixed; background: #0f172a; color: white; z-index: 1000; }
        .main-content { margin-left: 240px; height: 100vh; display: flex; flex-direction: column; }
        
        /* Navegación del menú */
        .nav-link { color: #94a3b8; padding: 10px 25px; text-decoration: none; display: flex; align-items: center; border-left: 3px solid transparent; font-size: 0.85rem;}
        .nav-link.active { color: white; background: rgba(255,255,255,0.03); border-left: 3px solid #10b981; }
        .nav-link:hover { color: white; background: rgba(255,255,255,0.01); }

        /* Contenedor adaptativo para el SGD central */
        .workspace-frame { flex: 1; width: 100%; height: 100%; border: none; background: #ffffff; }
    </style>
</head>
<body>

    <nav class="sidebar shadow d-flex flex-column">
        <div>
            <div class="p-4 fw-bold border-bottom border-secondary mb-3 small text-uppercase">
                <i class="bi bi-shield-check text-success me-2"></i> Saneamiento Legal
            </div>
            
            <a href="dashboard.php" class="nav-link"><i class="bi bi-grid me-2"></i> Dashboard</a>
            <a href="busqueda.php" class="nav-link"><i class="bi bi-search me-2"></i> Búsqueda</a>
            <a href="solicitantes.php" class="nav-link"><i class="bi bi-people me-2"></i> Solicitantes</a>
            <a href="expedientes.php" class="nav-link"><i class="bi bi-folder2 me-2"></i> Expedientes</a>
            
            <a href="seguimiento.php" class="nav-link active"><i class="bi bi-clock-history me-2"></i> Seguimiento de Expedientes</a>

            <?php if ($_SESSION['rol'] === 'ADMINISTRADOR' || $_SESSION['rol'] === 'AUDITOR'): ?>
                <a href="auditoria.php" class="nav-link"><i class="bi bi-shield-check me-2"></i> Auditoría</a>
            <?php endif; ?>

            <?php if ($_SESSION['rol'] === 'ADMINISTRADOR'): ?>
                <a href="reportes.php" class="nav-link"><i class="bi bi-file-earmark-bar-graph me-2"></i> Reportes</a>
                <a href="configuracion.php" class="nav-link"><i class="bi bi-gear me-2"></i> Configuración</a>
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
            <div>
                <h6 class="fw-bold m-0 text-dark">Módulo de Consulta Externa (SGD Central)</h6>
                <small class="text-muted" style="font-size: 0.72rem;">Buscador en tiempo real interconectado con la Sede Central del Gobierno Regional de Amazonas</small>
            </div>
            <div>
                <span class="badge bg-success-subtle text-success border border-success-subtle p-2 px-3 small fw-semibold">
                    <i class="bi bi-cloud-check-fill me-1"></i> Canal Operativo Activo
                </span>
            </div>
        </div>

        <iframe class="workspace-frame" src="https://sgd.regionamazonas.gob.pe/consultav2/"></iframe>

    </main>

</body>
</html>