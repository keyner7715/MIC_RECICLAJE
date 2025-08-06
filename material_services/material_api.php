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
            // Consulta con JOIN para obtener el nombre del tipo de material
            $stmt = $pdo->query("SELECT m.id_material, m.nombre_material, m.descripcion, m.id_tipo_material, tm.nombre_tipo
                               FROM materiales m
                               LEFT JOIN tipos_material tm ON m.id_tipo_material = tm.id_tipo_material
                               ORDER BY m.id_material DESC");
            echo json_encode(["mensaje" => "Listado de materiales", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener materiales"]);
        }
        break;

    case 'POST':
        verificarPermiso('crear', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nombre_material']) || empty(trim($data['nombre_material']))) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: nombre_material"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO materiales (nombre_material, descripcion, id_tipo_material) VALUES (?, ?, ?)");
            $stmt->execute([
                trim($data['nombre_material']),
                $data['descripcion'] ?? null,
                $data['id_tipo_material'] ?? null
            ]);
            echo json_encode(["mensaje" => "Material creado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear material"]);
        }
        break;

    case 'PUT':
        verificarPermiso('editar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id_material']) || !isset($data['nombre_material']) || empty(trim($data['nombre_material']))) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: id_material o nombre_material"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE materiales SET nombre_material = ?, descripcion = ?, id_tipo_material = ? WHERE id_material = ?");
            $stmt->execute([
                trim($data['nombre_material']),
                $data['descripcion'] ?? null,
                $data['id_tipo_material'] ?? null,
                $data['id_material']
            ]);
            echo json_encode(["mensaje" => "Material actualizado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar material"]);
        }
        break;

    case 'DELETE':
        verificarPermiso('eliminar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id_material'])) {
            http_response_code(400);
            echo json_encode(["error" => "Falta el campo 'id_material'"]);
            exit;
        }

        try {
            // Verificar si el material existe antes de eliminar
            $stmt_check = $pdo->prepare("SELECT nombre_material FROM materiales WHERE id_material = ?");
            $stmt_check->execute([$data['id_material']]);
            $material = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if (!$material) {
                http_response_code(404);
                echo json_encode(["error" => "Material no encontrado"]);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM materiales WHERE id_material = ?");
            $stmt->execute([$data['id_material']]);
            echo json_encode(["mensaje" => "Material eliminado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al eliminar material"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
