<?php
ini_set('display_errors', 0); // Oculta los errores en producción
error_reporting(E_ALL);       // Pero los puedes ver en logs si los activas
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/encriptar.php';
require_once __DIR__ . '/../vendor/autoload.php'; // JWT

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

ini_set('display_errors', 0);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Clave secreta para firmar el token (guárdala en un lugar seguro)
$clave_secreta = "mi_clave_secreta_segura";

// Leer los datos JSON del cuerpo
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->nombre_usuario) || !isset($data->contrasena)) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan campos requeridos"]);
    exit;
}

$nombre_usuario = $data->nombre_usuario;
$contrasena = $data->contrasena;

try {
    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE nombre_usuario = ? AND estado = 'activo'");
    $stmt->execute([$nombre_usuario]);
    $user = $stmt->fetch();

    if ($user && verificarPassword($contrasena, $user['contrasena'])) {

        $payload = [
            "iss" => "http://localhost", // emisor
            "aud" => "http://localhost",
            "iat" => time(),             // emitido ahora
            "exp" => time() + (60 * 60), // expira en 1 hora
            "data" => [
                "id_usuario" => $user['id_usuario'],
                "nombre_usuario" => $user['nombre_usuario'],
                "rol" => $user['rol']
            ]
        ];

        $jwt = JWT::encode($payload, $clave_secreta, 'HS256');

        echo json_encode([
            "mensaje" => "Inicio de sesión exitoso",
            "token" => $jwt
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["error" => "Credenciales incorrectas"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la base de datos"]);
}
