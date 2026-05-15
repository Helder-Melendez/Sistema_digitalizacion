<?php
session_start();
require_once '../config/database.php';
require_once '../models/Auditoria.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();
    $auditoria = new Auditoria($db);

    // Captura y limpieza de datos del formulario
    $datos = [
        'id_exp'   => $_POST['id_expediente'],
        'estado'   => $_POST['estado'],
        // Convertimos los checkbox (on) a valores numéricos (1 o 0)
        'nom'      => isset($_POST['nom']) ? 1 : 0,
        'leg'      => isset($_POST['leg']) ? 1 : 0,
        'fol'      => isset($_POST['fol']) ? 1 : 0,
        'obs'      => htmlspecialchars(strip_tags($_POST['obs'])),
        'auditor'  => $_POST['auditor']
    ];

    // Ejecutar el registro en la base de datos
    if ($auditoria->registrarAuditoria($datos)) {
        // Redireccionar con mensaje de éxito
        header("Location: ../views/auditoria.php?status=success");
    } else {
        // Redireccionar con mensaje de error
        header("Location: ../views/auditoria.php?status=error");
    }
} else {
    header("Location: ../views/dashboard.php");
}