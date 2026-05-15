<?php
require_once '../config/database.php';

if (isset($_GET['dni'])) {
    $database = new Database();
    $db = $database->getConnection();
    
    $dni = $_GET['dni'];
    $query = "SELECT nombres, apellido_paterno, apellido_materno FROM solicitantes WHERE dni = :dni LIMIT 1";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':dni', $dni);
    $stmt->execute();
    
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode($row); // Enviamos los datos como JSON
    } else {
        echo json_encode(null);
    }
}
?>