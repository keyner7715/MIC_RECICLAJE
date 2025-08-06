<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Content-Type: application/json; charset=UTF-8");

// Clave con la que se firmó el token
$clave_secreta = "mi_clave_secreta_segura";

// Obtener el token del encabezado Authorization: Bearer xxx
$headers = apache_request_headers();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["error" => "Token no proporcionado"]);
    exit;
}

list($tipo, $token) = explode(" ", $headers['Authorization'], 2);

if (strtolower($tipo) !== 'bearer' || !$token) {
    http_response_code(401);
    echo json_encode(["error" => "Formato de token inválido"]);
    exit;
}

try {
    $decoded = JWT::decode($token, new Key($clave_secreta, 'HS256'));

    // Puedes guardar los datos decodificados del usuario si los necesitas luego
    $usuario = (array) $decoded->data;

    // Para poder usar en otros archivos
    $GLOBALS['usuario_autenticado'] = $usuario;

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["error" => "Token inválido o expirado", "detalles" => $e->getMessage()]);
    exit;
}
