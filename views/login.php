
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Saneamiento Legal DRA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>
<body class="login-body">

    <div class="global-header text-center">
        <div class="brand-icon mx-auto mb-2">
            <i class="bi bi-shield-check"></i>
        </div>
        <h1 class="global-title">Saneamiento Legal</h1>
        <p class="global-subtitle">Dirección Regional Agraria</p>
    </div>

    <div class="login-wrapper">
        
        <div class="glass-card">
            <div class="mb-4">
                <h2 class="auth-title">Iniciar Sesión</h2>
                <p class="auth-subtitle">Ingrese sus credenciales de acceso</p>
            </div>

            <form action="../controllers/AuthController.php" method="POST">
                
                <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-custom text-center mb-4">
                        <i class="bi bi-exclamation-circle me-1"></i> Credenciales incorrectas o token inválido.
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="input-label">Usuario o Correo</label>
                    <div class="input-group glass-input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" name="usuario" placeholder="Ej: admin" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="input-label">Contraseña</label>
                    <div class="input-group glass-input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="••••••••" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="input-label">Rol de Acceso</label>
                    <div class="input-group glass-input-group">
                        <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                        <select class="form-select" name="rol" required>
                            <option value="" disabled selected>Seleccione...</option>
                            <option value="ADMINISTRADOR">Administrador del Sistema</option>
                            <option value="AUDITOR">Auditor de Calidad</option>
                            <option value="DIGITALIZADOR">Digitalizador</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="input-label">Token de Seguridad (2FA)</label>
                    <div class="input-group glass-input-group">
                        <span class="input-group-text"><i class="bi bi-key"></i></span>
                        <input type="text" class="form-control" name="token" placeholder="Código de 6 dígitos" maxlength="6">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-glass-login w-100">
                    Ingresar al Sistema
                </button>
            </form>
        </div>

        <div class="text-center mt-4">
            <a href="#" class="forgot-link">¿Problemas para acceder? Contacte a soporte</a>
        </div>
        
    </div>

</body>
</html>