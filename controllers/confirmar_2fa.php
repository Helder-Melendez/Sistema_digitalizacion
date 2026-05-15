<?php
session_start();
// AQUÍ ESTABA EL ERROR: Cambiamos 'libs' por 'config'
require_once '../config/GoogleAuthenticator.php';

if (!isset($_SESSION['auth_temp_id'])) { header("Location: ../views/login.php"); exit(); }

$ga = new PHPGangsta_GoogleAuthenticator();
$codigo_ingresado = $_POST['codigo_otp'];
$secreto_usuario = $_SESSION['auth_secret'];

// Verificamos el código con una tolerancia de 1 (30 segundos de desfase)
$es_valido = $ga->verifyCode($secreto_usuario, $codigo_ingresado, 1);

if ($es_valido) {
    // ÉXITO: Creamos la sesión real del sistema
    $_SESSION['id_usuario'] = $_SESSION['auth_temp_id'];
    $_SESSION['nombres'] = $_SESSION['auth_temp_nom']; // Variable corregida para el Dashboard
    $_SESSION['rol'] = $_SESSION['auth_temp_rol'];

    // Borramos los datos temporales
    unset($_SESSION['auth_temp_id'], $_SESSION['auth_temp_nom'], $_SESSION['auth_temp_rol'], $_SESSION['auth_secret']);

    header("Location: ../views/dashboard.php");
} else {
    // ERROR: Código incorrecto
    header("Location: ../views/verificar_2fa.php?error=codigo_incorrecto");
}
exit();