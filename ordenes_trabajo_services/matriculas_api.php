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
            $stmt = $pdo->query("SELECT * FROM matriculas ORDER BY matricula_id DESC");
            echo json_encode(["mensaje" => "Listado de matrículas", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener matrículas"]);
        }
        break;

    case 'POST':
        verificarPermiso('crear', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['estudiante_id']) || !isset($data['curso_id']) || !isset($data['fecha_matricula'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: estudiante_id, curso_id o fecha_matricula"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO matriculas (estudiante_id, curso_id, fecha_matricula) VALUES (?, ?, ?)");
            $stmt->execute([
                $data['estudiante_id'],
                $data['curso_id'],
                $data['fecha_matricula']
            ]);
            echo json_encode(["mensaje" => "Matrícula creada correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear matrícula"]);
        }
        break;

    case 'PUT':
        verificarPermiso('editar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['matricula_id']) || !isset($data['estudiante_id']) || !isset($data['curso_id']) || !isset($data['fecha_matricula'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: matricula_id, estudiante_id, curso_id o fecha_matricula"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE matriculas SET estudiante_id = ?, curso_id = ?, fecha_matricula = ? WHERE matricula_id = ?");
            $stmt->execute([
                $data['estudiante_id'],
                $data['curso_id'],
                $data['fecha_matricula'],
                $data['matricula_id']
            ]);
            echo json_encode(["mensaje" => "Matrícula actualizada correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar matrícula"]);
        }
        break;

    case 'DELETE':
        verificarPermiso('eliminar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['matricula_id'])) {
            http_response_code(400);
            echo json_encode(["error" => "Falta el campo 'matricula_id'"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM matriculas WHERE matricula_id = ?");
            $stmt->execute([$data['matricula_id']]);
            echo json_encode(["mensaje" => "Matrícula eliminada correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al eliminar matrícula"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
