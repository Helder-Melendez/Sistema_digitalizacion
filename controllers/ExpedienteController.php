<?php
session_start();
require_once '../config/database.php';
require_once '../models/Expediente.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();
    $expediente = new Expediente($db);

    // Manejo del Archivo
    $ruta_final = "";
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {
        $nombre_archivo = time() . "_" . $_FILES['archivo']['name'];
        $ruta_destino = "../uploads/" . $nombre_archivo;
        
        if (move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta_destino)) {
            $ruta_final = $nombre_archivo;
        }
    }

    // Preparar array de datos
    $datos = [
        'codigo' => $_POST['codigo'],
        'dni' => $_POST['dni'],
        'nombres' => $_POST['nombres'],
        'ap_pat' => $_POST['ap_pat'],
        'ap_mat' => $_POST['ap_mat'],
        'titulo' => $_POST['titulo'],
        'provincia' => $_POST['provincia'],
        'distrito' => $_POST['distrito'],
        'sector' => $_POST['sector'],
        'tipo_doc' => $_POST['tipo_doc'],
        'ruta' => $ruta_final,
        'procedimiento' => $_POST['procedimiento'],
        'predio' => $_POST['sector'], // Usamos sector como predio para simplificar
        'fecha' => date('Y-m-d')
    ];

    if ($expediente->crear($datos)) {
        header("Location: ../views/expedientes.php?msg=success");
    } else {
        header("Location: ../views/expedientes.php?msg=error");
    }
}