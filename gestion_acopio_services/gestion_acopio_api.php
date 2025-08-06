<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../auth_services/permisos.php';

header("Content-Type: application/json; charset=UTF-8");

$userData = $_REQUEST['user_data'] ?? null;

if (!$userData) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$rol = $userData->rol ?? null;
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        verificarPermiso('listar', $rol);
        try {
            // Consulta con JOIN para obtener información del responsable
            $stmt = $pdo->query("SELECT c.id_centro, c.nombre_centro, c.direccion, c.id_responsable, 
                                       e.nombre_empleado as nombre_responsable, e.cargo
                                FROM centros_acopio c
                                LEFT JOIN empleados e ON c.id_responsable = e.id_empleado
                                ORDER BY c.id_centro DESC");
            echo json_encode(["mensaje" => "Listado de centros de acopio", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener centros de acopio"]);
        }
        break;

    case 'POST':
        verificarPermiso('crear', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nombre_centro']) || empty(trim($data['nombre_centro']))) {
            http_response_code(400);
            echo json_encode(["error" => "Falta el campo requerido: nombre_centro"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO centros_acopio (nombre_centro, direccion, id_responsable) VALUES (?, ?, ?)");
            $stmt->execute([
                trim($data['nombre_centro']),
                !empty($data['direccion']) ? $data['direccion'] : null,
                !empty($data['id_responsable']) ? $data['id_responsable'] : null
            ]);
            echo json_encode(["mensaje" => "Centro de acopio creado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear centro de acopio"]);
        }
        break;

    case 'PUT':
        verificarPermiso('editar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id_centro']) || !isset($data['nombre_centro']) || empty(trim($data['nombre_centro']))) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: id_centro o nombre_centro"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE centros_acopio SET nombre_centro = ?, direccion = ?, id_responsable = ? WHERE id_centro = ?");
            $stmt->execute([
                trim($data['nombre_centro']),
                !empty($data['direccion']) ? $data['direccion'] : null,
                !empty($data['id_responsable']) ? $data['id_responsable'] : null,
                $data['id_centro']
            ]);
            echo json_encode(["mensaje" => "Centro de acopio actualizado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar centro de acopio"]);
        }
        break;

    case 'DELETE':
        verificarPermiso('eliminar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id_centro'])) {
            http_response_code(400);
            echo json_encode(["error" => "Falta el campo 'id_centro'"]);
            exit;
        }

        try {
            // Verificar si el centro existe antes de eliminar
            $stmt_check = $pdo->prepare("SELECT nombre_centro FROM centros_acopio WHERE id_centro = ?");
            $stmt_check->execute([$data['id_centro']]);
            $centro = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if (!$centro) {
                http_response_code(404);
                echo json_encode(["error" => "Centro de acopio no encontrado"]);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM centros_acopio WHERE id_centro = ?");
            $stmt->execute([$data['id_centro']]);
            echo json_encode(["mensaje" => "Centro de acopio eliminado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al eliminar centro de acopio"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
