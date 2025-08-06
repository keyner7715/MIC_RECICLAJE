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
            $stmt = $pdo->query("SELECT * FROM cursos ORDER BY curso_id DESC");
            echo json_encode(["mensaje" => "Listado de cursos", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener cursos"]);
        }
        break;

    case 'POST':
        verificarPermiso('crear', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['asignatura_id']) || !isset($data['docente_id']) || !isset($data['periodo'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: asignatura_id, docente_id o periodo"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO cursos (asignatura_id, docente_id, periodo) VALUES (?, ?, ?)");
            $stmt->execute([
                $data['asignatura_id'],
                $data['docente_id'],
                $data['periodo']
            ]);
            echo json_encode(["mensaje" => "Curso creado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear curso"]);
        }
        break;

    case 'PUT':
        verificarPermiso('editar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['curso_id']) || !isset($data['asignatura_id']) || !isset($data['docente_id']) || !isset($data['periodo'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: curso_id, asignatura_id, docente_id o periodo"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE cursos SET asignatura_id = ?, docente_id = ?, periodo = ? WHERE curso_id = ?");
            $stmt->execute([
                $data['asignatura_id'],
                $data['docente_id'],
                $data['periodo'],
                $data['curso_id']
            ]);
            echo json_encode(["mensaje" => "Curso actualizado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar curso"]);
        }
        break;

    case 'DELETE':
        verificarPermiso('eliminar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['curso_id'])) {
            http_response_code(400);
            echo json_encode(["error" => "Falta el campo 'curso_id'"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM cursos WHERE curso_id = ?");
            $stmt->execute([$data['curso_id']]);
            echo json_encode(["mensaje" => "Curso eliminado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al eliminar curso"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
