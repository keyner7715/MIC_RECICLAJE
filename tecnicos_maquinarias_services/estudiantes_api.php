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
            $stmt = $pdo->query("SELECT * FROM estudiantes ORDER BY estudiante_id DESC");
            echo json_encode(["mensaje" => "Listado de estudiantes", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener estudiantes"]);
        }
        break;

    case 'POST':
        verificarPermiso('crear', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nombre']) || !isset($data['correo']) || !isset($data['telefono']) || !isset($data['direccion'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: nombre, correo, telefono o direccion"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO estudiantes (nombre, correo, telefono, direccion) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $data['nombre'],
                $data['correo'],
                $data['telefono'],
                $data['direccion']
            ]);
            echo json_encode(["mensaje" => "Estudiante creado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear estudiante"]);
        }
        break;

    case 'PUT':
        verificarPermiso('editar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['estudiante_id']) || !isset($data['nombre']) || !isset($data['correo']) || !isset($data['telefono']) || !isset($data['direccion'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: estudiante_id, nombre, correo, telefono o direccion"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE estudiantes SET nombre = ?, correo = ?, telefono = ?, direccion = ? WHERE estudiante_id = ?");
            $stmt->execute([
                $data['nombre'],
                $data['correo'],
                $data['telefono'],
                $data['direccion'],
                $data['estudiante_id']
            ]);
            echo json_encode(["mensaje" => "Estudiante actualizado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar estudiante"]);
        }
        break;

    case 'DELETE':
        verificarPermiso('eliminar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['estudiante_id'])) {
            http_response_code(400);
            echo json_encode(["error" => "Falta el campo 'estudiante_id'"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM estudiantes WHERE estudiante_id = ?");
            $stmt->execute([$data['estudiante_id']]);
            echo json_encode(["mensaje" => "Estudiante eliminado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al eliminar estudiante"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
