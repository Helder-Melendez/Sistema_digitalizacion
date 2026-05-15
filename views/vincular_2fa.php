<?php
session_start();

/** * AJUSTE 1: Redirección de seguridad.
 * Como este archivo está en 'views/', para volver al login que está en la misma carpeta,
 * simplemente apuntamos a 'login.php'.
 */
if (!isset($_SESSION['auth_temp_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

/**
 * AJUSTE 2: Ruta de la librería.
 * Me confirmaste que GoogleAuthenticator.php está en la carpeta 'config'.
 * Desde 'views/', subimos un nivel (../) y entramos a 'config/'.
 */
require_once '../config/GoogleAuthenticator.php';
$ga = new PHPGangsta_GoogleAuthenticator();

// 1. Generamos un secreto nuevo para este usuario si no existe
if (!isset($_SESSION['nuevo_secreto_temp'])) {
    $_SESSION['nuevo_secreto_temp'] = $ga->createSecret();
}

$secret = $_SESSION['nuevo_secreto_temp'];
$user_name = $_SESSION['auth_temp_nom'];

// 2. Generamos la URL del QR
$qrCodeUrl = $ga->getQRCodeGoogleUrl("Saneamiento_DRAA_2026", $secret);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vincular Google Authenticator | DRAA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Inter', sans-serif; }
        .card-qr { background: white; border-radius: 20px; padding: 40px; width: 100%; max-width: 450px; border: none; }
        .qr-frame { background: #fff; border: 2px solid #e2e8f0; padding: 10px; border-radius: 10px; display: inline-block; }
    </style>
</head>
<body>
    <div class="card-qr shadow-lg text-center">
        <h5 class="fw-bold text-dark mb-1">Activar Seguridad 2FA</h5>
        <p class="text-muted small mb-4">Hola <b><?php echo htmlspecialchars($user_name); ?></b>, escanea el código para proteger tu cuenta.</p>
        
        <div class="qr-frame mb-4">
            <img src="<?php echo $qrCodeUrl; ?>" alt="Código QR de Seguridad">
        </div>

        <div class="alert alert-info py-2 small mb-4 text-start">
            <i class="bi bi-info-circle-fill me-2"></i>
            Abre <b>Google Authenticator</b>, pulsa el botón <b>"+"</b> y selecciona <b>"Escanear código QR"</b>.
        </div>

        <form action="../controllers/finalizar_vinculacion.php" method="POST">
            <label class="small fw-bold mb-2 text-secondary">Ingresa el código que aparece en tu celular:</label>
            <input type="text" name="codigo_verificador" maxlength="6" class="form-control form-control-lg text-center mb-3 fw-bold" placeholder="000000" required autofocus autocomplete="off">
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">VINCULAR Y FINALIZAR</button>
        </form>
    </div>
</body>
</html>