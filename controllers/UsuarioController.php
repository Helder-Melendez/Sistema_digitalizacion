<?php
ob_start(); // BLINDAJE: Atrapa espacios en blanco para que la redirección no falle
session_start();

// Activamos errores temporalmente para no volver a ver pantallas blancas vacías
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';
require_once '../models/Usuario.php';

// Seguridad estricta para el rol administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'ADMINISTRADOR') {
    header("Location: ../views/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$usuarioModel = new Usuario($db);

// --- ACCIÓN: ELIMINAR REGISTRO (GET) ---
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    
    if ($id == $_SESSION['id_usuario']) {
        $ruta = "../views/configuracion.php?status=self_delete_error";
    } else {
        if ($usuarioModel->eliminar($id)) {
            $ruta = "../views/configuracion.php?status=delete_ok";
        } else {
            $ruta = "../views/configuracion.php?status=error";
        }
    }
    // Redirección segura con respaldo en JavaScript
    header("Location: " . $ruta);
    echo "<script>window.location.href='" . $ruta . "';</script>";
    exit();
}

// --- ACCIÓN: CREAR / MODIFICAR (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? 'register';

    $datos = [
        'nom'  => $_POST['nombres'],
        'ape'  => $_POST['apellidos'],
        'usu'  => $_POST['usuario'],
        'rol'  => $_POST['rol']
    ];

    try {
        if ($action == "register") {
            $datos['pass'] = $_POST['password'];
            $res = $usuarioModel->crear($datos);
            $ruta = "../views/configuracion.php?status=success";
        } else if ($action == "update") {
            $datos['id'] = $_POST['id_usuario'];
            $datos['pass'] = $_POST['password']; 
            $res = $usuarioModel->actualizar($datos);
            $ruta = "../views/configuracion.php?status=edit_ok";
        }
        
        header("Location: " . $ruta);
        echo "<script>window.location.href='" . $ruta . "';</script>";
        
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $ruta = "../views/configuracion.php?status=duplicado";
        } else {
            $ruta = "../views/configuracion.php?status=error";
        }
        header("Location: " . $ruta);
        echo "<script>window.location.href='" . $ruta . "';</script>";
    }
    exit();
}