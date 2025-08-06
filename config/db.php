<?php
// Configuración de la base de datos
$host = 'localhost';
$dbname = 'maquinaria_alquiler';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Conexión exitosa a la base de datos";
} catch(PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>
