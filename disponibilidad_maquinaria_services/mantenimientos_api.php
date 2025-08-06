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
            $stmt = $pdo->query("SELECT * FROM mantenimientos ORDER BY id_mantenimiento DESC");
            echo json_encode(["mensaje" => "Listado de mantenimientos", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener mantenimientos"]);
        }
        break;

    case 'POST':
        verificarPermiso('crear', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id_atracciones']) || !isset($data['fecha'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: id_atracciones o fecha"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO mantenimientos (id_atracciones, fecha, descripcion_mant) VALUES (?, ?, ?)");
            $stmt->execute([
                $data['id_atracciones'],
                $data['fecha'],
                $data['descripcion_mant'] ?? null
            ]);
            echo json_encode(["mensaje" => "Mantenimiento creado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear mantenimiento"]);
        }
        break;

    case 'PUT':
        verificarPermiso('editar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id_mantenimiento']) || !isset($data['id_atracciones']) || !isset($data['fecha'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: id_mantenimiento, id_atracciones o fecha"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE mantenimientos SET id_atracciones = ?, fecha = ?, descripcion_mant = ? WHERE id_mantenimiento = ?");
            $stmt->execute([
                $data['id_atracciones'],
                $data['fecha'],
                $data['descripcion_mant'] ?? null,
                $data['id_mantenimiento']
            ]);
            echo json_encode(["mensaje" => "Mantenimiento actualizado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar mantenimiento"]);
        }
        break;

    case 'DELETE':
        verificarPermiso('eliminar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id_mantenimiento'])) {
            http_response_code(400);
            echo json_encode(["error" => "Falta el campo 'id_mantenimiento'"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM mantenimientos WHERE id_mantenimiento = ?");
            $stmt->execute([$data['id_mantenimiento']]);
            echo json_encode(["mensaje" => "Mantenimiento eliminado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al eliminar mantenimiento"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
