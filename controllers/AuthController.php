<?php
session_start();
require_once '../config/database.php';
require_once '../models/Usuario.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();
    $usuarioModel = new Usuario($db);

    // Capturamos solo las credenciales básicas
    $userInput = $_POST['usuario'] ?? '';
    $passInput = $_POST['password'] ?? '';
    $rolInput  = $_POST['rol'] ?? '';

    $usuario = $usuarioModel->autenticar($userInput, $passInput);

    if ($usuario) {
        if ($usuario['rol'] === $rolInput) {
            
            // Creamos las variables de sesión temporales
            $_SESSION['auth_temp_id'] = $usuario['id_usuario'];
            $_SESSION['auth_temp_nom'] = $usuario['nombres'];
            $_SESSION['auth_temp_rol'] = $usuario['rol'];

            // Evaluamos el secreto 2FA (Asegurándonos de que no sea un espacio en blanco)
            if (empty($usuario['google_secret']) || strlen(trim($usuario['google_secret'])) < 10) {
                // Si NO tiene secreto, lo mandamos a VINCULAR (Código QR)
                header("Location: ../views/vincular_2fa.php");
            } else {
                // Si YA tiene secreto, lo mandamos a VERIFICAR (Poner los 6 dígitos)
                $_SESSION['auth_secret'] = $usuario['google_secret'];
                header("Location: ../views/verificar_2fa.php");
            }
            exit();
        } else {
            header("Location: ../views/login.php?error=rol");
            exit();
        }
    } else {
        header("Location: ../views/login.php?error=credenciales");
        exit();
    }
}
