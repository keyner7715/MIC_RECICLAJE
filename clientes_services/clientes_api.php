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
            // Si se solicita un cliente específico
            if (isset($_GET['id_cliente'])) {
                $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
                $stmt->execute([$_GET['id_cliente']]);
                $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($cliente) {
                    echo json_encode(["mensaje" => "Cliente encontrado", "data" => $cliente]);
                } else {
                    http_response_code(404);
                    echo json_encode(["error" => "Cliente no encontrado"]);
                }
            } else {
                // Listar todos los clientes
                $stmt = $pdo->query("SELECT * FROM clientes ORDER BY id_cliente DESC");
                echo json_encode(["mensaje" => "Listado de clientes", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener clientes"]);
        }
        break;

    case 'POST':
        verificarPermiso('crear', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nombre_cliente']) || !isset($data['cedula_ruc'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: nombre_cliente y cedula_ruc"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO clientes (nombre_cliente, cedula_ruc, direccion, telefono, correo) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['nombre_cliente'],
                $data['cedula_ruc'],
                $data['direccion'] ?? null,
                $data['telefono'] ?? null,
                $data['correo'] ?? null
            ]);
            echo json_encode(["mensaje" => "Cliente creado correctamente", "id_cliente" => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry error
                http_response_code(409);
                echo json_encode(["error" => "La cédula/RUC ya está registrada"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error al crear cliente"]);
            }
        }
        break;

    case 'PUT':
        verificarPermiso('editar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id_cliente']) || !isset($data['nombre_cliente']) || !isset($data['cedula_ruc'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: id_cliente, nombre_cliente y cedula_ruc"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE clientes SET nombre_cliente = ?, cedula_ruc = ?, direccion = ?, telefono = ?, correo = ? WHERE id_cliente = ?");
            $result = $stmt->execute([
                $data['nombre_cliente'],
                $data['cedula_ruc'],
                $data['direccion'] ?? null,
                $data['telefono'] ?? null,
                $data['correo'] ?? null,
                $data['id_cliente']
            ]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(["mensaje" => "Cliente actualizado correctamente"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Cliente no encontrado"]);
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry error
                http_response_code(409);
                echo json_encode(["error" => "La cédula/RUC ya está registrada por otro cliente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error al actualizar cliente"]);
            }
        }
        break;

    case 'DELETE':
        verificarPermiso('eliminar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id_cliente'])) {
            http_response_code(400);
            echo json_encode(["error" => "Falta el campo 'id_cliente'"]);
            exit;
        }

        try {
            // Verificar si el cliente tiene recolecciones asociadas
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM recolecciones WHERE id_cliente = ?");
            $stmt->execute([$data['id_cliente']]);
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            if ($count > 0) {
                http_response_code(409);
                echo json_encode(["error" => "No se puede eliminar el cliente porque tiene recolecciones asociadas"]);
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM clientes WHERE id_cliente = ?");
            $result = $stmt->execute([$data['id_cliente']]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(["mensaje" => "Cliente eliminado correctamente"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Cliente no encontrado"]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al eliminar cliente"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
