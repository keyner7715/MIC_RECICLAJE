<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . '/vendor/autoload.php'; // Ajusta si tu ruta cambia

// Clave secreta para validar JWT (debe ser la misma que usas en login_auth.php)
$clave_secreta = "mi_clave_secreta_segura";

$method = $_SERVER['REQUEST_METHOD'];
$entity = $_GET['entity'] ?? null;
$action = $_GET['action'] ?? null;

if (!$entity || !$action) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan parámetros en la URL']);
    exit;
}

// Validar token (excepto para el login, que no requiere token)
if ($entity !== 'auth_services' || $action !== 'login_auth') {
    // Leer el header Authorization
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (!$authHeader) {
        http_response_code(401);
        echo json_encode(['error' => 'No se encontró el token de autorización']);
        exit;
    }

    // El header debe tener el formato "Bearer <token>"
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(['error' => 'Formato de token inválido']);
        exit;
    }

    $jwt = $matches[1];

    try {
        // Decodificar el token
        $decoded = JWT::decode($jwt, new Key($clave_secreta, 'HS256'));
        // Puedes guardar info útil para usar en el microservicio:
        // Por ejemplo, pasar $decoded a un global o variable de entorno
        $_REQUEST['user_data'] = $decoded->data;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Token inválido o expirado']);
        exit;
    }
}

// Permitir PUT/DELETE con datos en crudo
if ($method === 'PUT' || $method === 'DELETE') {
    parse_str(file_get_contents("php://input"), $_POST);
}

$targetFile = __DIR__ . '/' . $entity . '/' . $action . '.php';

if (!file_exists($targetFile)) {
    http_response_code(404);
    echo json_encode(['error' => 'Microservicio no encontrado.']);
    exit;
}

include $targetFile;
