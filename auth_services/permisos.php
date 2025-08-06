<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$clave_secreta = "mi_clave_secreta_segura";

$permisos = [
    'Administrador' => ['crear', 'editar', 'eliminar', 'listar'],
    'Desarrollador' => ['listar', 'editar', 'crear'],
    'Supervisor'    => ['listar'],
];

function verificarPermiso($accion, $rol = null) {
    global $permisos;

    if ($rol === null) {
        if (!isset($_SESSION['rol'])) {
            header("Location: ../auth_services/login_auth.php");
            exit();
        }
        $rol = $_SESSION['rol'];
    }

    if (!isset($permisos[$rol])) {
        die("Rol no válido.");
    }

    if (!in_array($accion, $permisos[$rol])) {
        http_response_code(403);
        die("No tienes permiso para realizar esta acción.");
    }
}

function tienePermiso($accion, $rol = null) {
    global $permisos;

    if ($rol === null) {
        if (!isset($_SESSION['rol'])) return false;
        $rol = $_SESSION['rol'];
    }

    return isset($permisos[$rol]) && in_array($accion, $permisos[$rol]);
}

function verificarToken() {
    if (!isset($_REQUEST['user_data'])) {
        http_response_code(403);
        echo json_encode(["mensaje" => "Token no válido"]);
        exit;
    }

    $_SESSION['rol'] = $_REQUEST['user_data']->rol ?? null;
}
