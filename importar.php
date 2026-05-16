<?php
// Jalar las variables de entorno de Railway
$host = getenv('MYSQLHOST');
$db_name = getenv('MYSQLDATABASE');
$username = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');
$port = getenv('MYSQLPORT') ?: "3306";

if (!$host) {
    die("Por seguridad, este script solo se puede ejecutar desde la URL de Railway.");
}

try {
    // Conectar usando el entorno nativo de PHP en la nube
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$db_name;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Leer tu archivo SQL
    $sql = file_get_contents('saneamiento_db.sql');
    
    // Ejecutar todo el contenido del SQL
    $conn->exec($sql);
    
    echo "<h1>¡Felicidades! Base de datos importada con éxito en Railway.</h1>";
    echo "<p>Por seguridad, elimina el archivo 'importar.php' de tu proyecto y vuelve a hacer un git push.</p>";

} catch(PDOException $e) {
    echo "<h3>Error al importar:</h3> " . $e->getMessage();
}
?>