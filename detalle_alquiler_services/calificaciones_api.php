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
            $stmt = $pdo->query("SELECT * FROM calificaciones ORDER BY calificacion_id DESC");
            echo json_encode(["mensaje" => "Listado de calificaciones", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener calificaciones"]);
        }
        break;

    case 'POST':
        verificarPermiso('crear', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['estudiante_id']) || !isset($data['asignatura_id']) || !isset($data['nota'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: estudiante_id, asignatura_id o nota"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO calificaciones (estudiante_id, asignatura_id, nota, observaciones) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $data['estudiante_id'],
                $data['asignatura_id'],
                $data['nota'],
                $data['observaciones'] ?? null
            ]);
            echo json_encode(["mensaje" => "Calificación creada correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear calificación"]);
        }
        break;

    case 'PUT':
        verificarPermiso('editar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['calificacion_id']) || !isset($data['estudiante_id']) || !isset($data['asignatura_id']) || !isset($data['nota'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: calificacion_id, estudiante_id, asignatura_id o nota"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE calificaciones SET estudiante_id = ?, asignatura_id = ?, nota = ?, observaciones = ? WHERE calificacion_id = ?");
            $stmt->execute([
                $data['estudiante_id'],
                $data['asignatura_id'],
                $data['nota'],
                $data['observaciones'] ?? null,
                $data['calificacion_id']
            ]);
            echo json_encode(["mensaje" => "Calificación actualizada correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar calificación"]);
        }
        break;

    case 'DELETE':
        verificarPermiso('eliminar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['calificacion_id'])) {
            http_response_code(400);
            echo json_encode(["error" => "Falta el campo 'calificacion_id'"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM calificaciones WHERE calificacion_id = ?");
            $stmt->execute([$data['calificacion_id']]);
            echo json_encode(["mensaje" => "Calificación eliminada correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al eliminar calificación"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
