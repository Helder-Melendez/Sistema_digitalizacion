<?php
session_start();
require_once '../config/database.php';
// AQUÍ ESTABA EL ERROR: Cambiamos 'libs' por 'config'
require_once '../config/GoogleAuthenticator.php'; 

if (!isset($_SESSION['auth_temp_id'])) { header("Location: ../views/login.php"); exit(); }

$ga = new PHPGangsta_GoogleAuthenticator();
$secret = $_SESSION['nuevo_secreto_temp'];
$codigo_usuario = $_POST['codigo_verificador'];

if ($ga->verifyCode($secret, $codigo_usuario, 1)) {
    // Si el código es correcto, guardamos en base de datos
    $db = (new Database())->getConnection();
    $query = "UPDATE usuarios SET google_secret = :sec WHERE id_usuario = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':sec', $secret);
    $stmt->bindParam(':id', $_SESSION['auth_temp_id']);
    
    if ($stmt->execute()) {
        // Iniciamos la sesión oficial
        $_SESSION['id_usuario'] = $_SESSION['auth_temp_id'];
        $_SESSION['nombres'] = $_SESSION['auth_temp_nom']; // Variable corregida para el Dashboard
        $_SESSION['rol'] = $_SESSION['auth_temp_rol'];

        // Limpiamos la basura temporal
        unset($_SESSION['auth_temp_id'], $_SESSION['auth_temp_nom'], $_SESSION['auth_temp_rol'], $_SESSION['nuevo_secreto_temp']);
        
        header("Location: ../views/dashboard.php");
    }
} else {
    // Si escribió mal el código al vincular
    header("Location: ../views/vincular_2fa.php?error=codigo_incorrecto");
}