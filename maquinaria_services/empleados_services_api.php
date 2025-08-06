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
            $stmt = $pdo->query("SELECT * FROM empleados ORDER BY id_empleado DESC");
            echo json_encode(["mensaje" => "Listado de empleados", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener empleados"]);
        }
        break;

    case 'POST':
        verificarPermiso('crear', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nombre']) || !isset($data['correo'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: nombre o correo"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO empleados (nombre, direccion, correo, telefono) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $data['nombre'],
                $data['direccion'] ?? null,
                $data['correo'],
                $data['telefono'] ?? null
            ]);
            echo json_encode(["mensaje" => "Empleado creado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear empleado"]);
        }
        break;

    case 'PUT':
        verificarPermiso('editar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id_empleado']) || !isset($data['nombre']) || !isset($data['correo'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: id_empleado, nombre o correo"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE empleados SET nombre = ?, direccion = ?, correo = ?, telefono = ? WHERE id_empleado = ?");
            $stmt->execute([
                $data['nombre'],
                $data['direccion'] ?? null,
                $data['correo'],
                $data['telefono'] ?? null,
                $data['id_empleado']
            ]);
            echo json_encode(["mensaje" => "Empleado actualizado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar empleado"]);
        }
        break;

    case 'DELETE':
        verificarPermiso('eliminar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id_empleado'])) {
            http_response_code(400);
            echo json_encode(["error" => "Falta el campo 'id_empleado'"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM empleados WHERE id_empleado = ?");
            $stmt->execute([$data['id_empleado']]);
            echo json_encode(["mensaje" => "Empleado eliminado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al eliminar empleado"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
