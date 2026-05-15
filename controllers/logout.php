<?php
// Iniciar la sesión para poder destruirla
session_start();

// Eliminar todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

// Redirigir de vuelta a la pantalla de login
header("Location: ../views/login.php");
exit();
?>