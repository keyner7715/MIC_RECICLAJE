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

// Tipos de proveedor permitidos
$tipos_permitidos = ['Industrial', 'Institucional', 'Comercial', 'Gubernamental'];

switch ($method) {
    case 'GET':
        verificarPermiso('listar', $rol);
        try {
            // Si se solicita un proveedor específico
            if (isset($_GET['id_proveedor'])) {
                $stmt = $pdo->prepare("SELECT * FROM proveedores WHERE id_proveedor = ?");
                $stmt->execute([$_GET['id_proveedor']]);
                $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($proveedor) {
                    echo json_encode(["mensaje" => "Proveedor encontrado", "data" => $proveedor]);
                } else {
                    http_response_code(404);
                    echo json_encode(["error" => "Proveedor no encontrado"]);
                }
            } else {
                // Listar todos los proveedores
                $stmt = $pdo->query("SELECT * FROM proveedores ORDER BY id_proveedor DESC");
                echo json_encode(["mensaje" => "Listado de proveedores", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener proveedores"]);
        }
        break;

    case 'POST':
        verificarPermiso('crear', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nombre_proveedor'])) {
            http_response_code(400);
            echo json_encode(["error" => "Falta el campo requerido: nombre_proveedor"]);
            exit;
        }

        // Validar tipo de proveedor si se proporciona
        if (!empty($data['tipo_proveedor']) && !in_array($data['tipo_proveedor'], $tipos_permitidos)) {
            http_response_code(400);
            echo json_encode(["error" => "Tipo de proveedor no válido. Debe ser: " . implode(', ', $tipos_permitidos)]);
            exit;
        }

        // Validar teléfono si se proporciona
        if (!empty($data['telefono']) && !preg_match('/^\d{10}$/', $data['telefono'])) {
            http_response_code(400);
            echo json_encode(["error" => "El teléfono debe contener exactamente 10 dígitos numéricos"]);
            exit;
        }

        // Validar correo si se proporciona
        if (!empty($data['correo']) && !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(["error" => "Formato de correo electrónico no válido"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO proveedores (nombre_proveedor, tipo_proveedor, direccion, telefono, correo) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['nombre_proveedor'],
                !empty($data['tipo_proveedor']) ? $data['tipo_proveedor'] : null,
                !empty($data['direccion']) ? $data['direccion'] : null,
                !empty($data['telefono']) ? $data['telefono'] : null,
                !empty($data['correo']) ? $data['correo'] : null
            ]);
            echo json_encode(["mensaje" => "Proveedor creado correctamente", "id_proveedor" => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear proveedor"]);
        }
        break;

    case 'PUT':
        verificarPermiso('editar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id_proveedor']) || !isset($data['nombre_proveedor'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: id_proveedor y nombre_proveedor"]);
            exit;
        }

        // Validar tipo de proveedor si se proporciona
        if (!empty($data['tipo_proveedor']) && !in_array($data['tipo_proveedor'], $tipos_permitidos)) {
            http_response_code(400);
            echo json_encode(["error" => "Tipo de proveedor no válido. Debe ser: " . implode(', ', $tipos_permitidos)]);
            exit;
        }

        // Validar teléfono si se proporciona
        if (!empty($data['telefono']) && !preg_match('/^\d{10}$/', $data['telefono'])) {
            http_response_code(400);
            echo json_encode(["error" => "El teléfono debe contener exactamente 10 dígitos numéricos"]);
            exit;
        }

        // Validar correo si se proporciona
        if (!empty($data['correo']) && !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(["error" => "Formato de correo electrónico no válido"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE proveedores SET nombre_proveedor = ?, tipo_proveedor = ?, direccion = ?, telefono = ?, correo = ? WHERE id_proveedor = ?");
            $result = $stmt->execute([
                $data['nombre_proveedor'],
                !empty($data['tipo_proveedor']) ? $data['tipo_proveedor'] : null,
                !empty($data['direccion']) ? $data['direccion'] : null,
                !empty($data['telefono']) ? $data['telefono'] : null,
                !empty($data['correo']) ? $data['correo'] : null,
                $data['id_proveedor']
            ]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(["mensaje" => "Proveedor actualizado correctamente"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Proveedor no encontrado"]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar proveedor"]);
        }
        break;

    case 'DELETE':
        verificarPermiso('eliminar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id_proveedor'])) {
            http_response_code(400);
            echo json_encode(["error" => "Falta el campo 'id_proveedor'"]);
            exit;
        }

        try {
            // Verificar si el proveedor tiene recolecciones asociadas
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM recoleccion_proveedor WHERE id_proveedor = ?");
            $stmt->execute([$data['id_proveedor']]);
            $recolecciones_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            if ($recolecciones_count > 0) {
                http_response_code(409);
                echo json_encode(["error" => "No se puede eliminar el proveedor porque tiene recolecciones asociadas"]);
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM proveedores WHERE id_proveedor = ?");
            $result = $stmt->execute([$data['id_proveedor']]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(["mensaje" => "Proveedor eliminado correctamente"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Proveedor no encontrado"]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al eliminar proveedor"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
