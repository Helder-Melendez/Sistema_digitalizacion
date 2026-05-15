<?php
session_start();
require_once '../config/database.php';
require_once '../models/Solicitante.php';

$database = new Database();
$db = $database->getConnection();
$solicitante = new Solicitante($db);

// --- LÓGICA PARA ELIMINAR (vía GET) ---
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $dni = $_GET['dni'];
    if ($solicitante->eliminar($dni)) {
        header("Location: ../views/solicitantes.php?status=delete_ok");
    } else {
        header("Location: ../views/solicitantes.php?status=error");
    }
    exit();
}

// --- LÓGICA PARA GUARDAR / EDITAR (vía POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action']; 
    $dni = $_POST['dni'];
    $nombres = mb_strtoupper(trim($_POST['nombres']), 'UTF-8');
    $ap_pat = mb_strtoupper(trim($_POST['ap_paterno']), 'UTF-8');
    $ap_mat = mb_strtoupper(trim($_POST['ap_materno']), 'UTF-8');

    if ($action == "register") {
        $res = $solicitante->crear($dni, $nombres, $ap_pat, $ap_mat);
        $status = "solicitante_ok";
    } else {
        $res = $solicitante->actualizar($dni, $nombres, $ap_pat, $ap_mat);
        $status = "edit_ok";
    }

    header("Location: ../views/solicitantes.php?status=" . ($res ? $status : "error"));
}