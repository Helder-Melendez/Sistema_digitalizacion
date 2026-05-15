<?php
session_start();
// Si no hay sesión temporal, nadie puede entrar a esta página
if (!isset($_SESSION['auth_temp_id'])) { header("Location: ../index.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificación de Identidad | DRAA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #0f172a; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card-2fa { background: white; border-radius: 15px; padding: 40px; width: 100%; max-width: 400px; text-align: center; }
    </style>
</head>
<body>
    <div class="card-2fa shadow-lg">
        <h5 class="fw-bold mb-3">Seguridad de Dos Pasos</h5>
        <p class="text-muted small">Ingresa el código de 6 dígitos de tu aplicación Google Authenticator.</p>
        
        <form action="../controllers/confirmar_2fa.php" method="POST">
            <input type="text" name="codigo_otp" class="form-control form-control-lg text-center mb-4 fw-bold" maxlength="6" placeholder="000 000" autofocus required autocomplete="off">
            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">VERIFICAR ACCESO</button>
        </form>
        
        <a href="../index.php" class="d-block mt-3 small text-decoration-none text-secondary">Regresar al inicio</a>
    </div>
</body>
</html>