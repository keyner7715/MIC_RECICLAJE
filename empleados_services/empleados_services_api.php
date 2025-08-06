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

// Cargos permitidos
$cargos_permitidos = ['Supervisor', 'Recolector', 'Clasificadora'];

switch ($method) {
    case 'GET':
        verificarPermiso('listar', $rol);
        try {
            // Si se solicita un empleado específico
            if (isset($_GET['id_empleado'])) {
                $stmt = $pdo->prepare("SELECT * FROM empleados WHERE id_empleado = ?");
                $stmt->execute([$_GET['id_empleado']]);
                $empleado = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($empleado) {
                    echo json_encode(["mensaje" => "Empleado encontrado", "data" => $empleado]);
                } else {
                    http_response_code(404);
                    echo json_encode(["error" => "Empleado no encontrado"]);
                }
            } else {
                // Listar todos los empleados
                $stmt = $pdo->query("SELECT * FROM empleados ORDER BY id_empleado DESC");
                echo json_encode(["mensaje" => "Listado de empleados", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener empleados"]);
        }
        break;

    case 'POST':
        verificarPermiso('crear', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nombre_empleado']) || !isset($data['cargo'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: nombre_empleado y cargo"]);
            exit;
        }

        // Validar cargo
        if (!in_array($data['cargo'], $cargos_permitidos)) {
            http_response_code(400);
            echo json_encode(["error" => "Cargo no válido. Debe ser: " . implode(', ', $cargos_permitidos)]);
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
            $stmt = $pdo->prepare("INSERT INTO empleados (nombre_empleado, cargo, telefono, correo) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $data['nombre_empleado'],
                $data['cargo'],
                !empty($data['telefono']) ? $data['telefono'] : null,
                !empty($data['correo']) ? $data['correo'] : null
            ]);
            echo json_encode(["mensaje" => "Empleado creado correctamente", "id_empleado" => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear empleado"]);
        }
        break;

    case 'PUT':
        verificarPermiso('editar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id_empleado']) || !isset($data['nombre_empleado']) || !isset($data['cargo'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: id_empleado, nombre_empleado y cargo"]);
            exit;
        }

        // Validar cargo
        if (!in_array($data['cargo'], $cargos_permitidos)) {
            http_response_code(400);
            echo json_encode(["error" => "Cargo no válido. Debe ser: " . implode(', ', $cargos_permitidos)]);
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
            $stmt = $pdo->prepare("UPDATE empleados SET nombre_empleado = ?, cargo = ?, telefono = ?, correo = ? WHERE id_empleado = ?");
            $result = $stmt->execute([
                $data['nombre_empleado'],
                $data['cargo'],
                !empty($data['telefono']) ? $data['telefono'] : null,
                !empty($data['correo']) ? $data['correo'] : null,
                $data['id_empleado']
            ]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(["mensaje" => "Empleado actualizado correctamente"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Empleado no encontrado"]);
            }
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
            // Verificar si el empleado tiene recolecciones asociadas
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM recolecciones WHERE id_empleado = ?");
            $stmt->execute([$data['id_empleado']]);
            $recolecciones_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Verificar si el empleado tiene recolecciones de proveedores asociadas
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM recoleccion_proveedor WHERE id_empleado = ?");
            $stmt->execute([$data['id_empleado']]);
            $recolecciones_prov_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Verificar si el empleado es responsable de algún centro de acopio
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM centros_acopio WHERE id_responsable = ?");
            $stmt->execute([$data['id_empleado']]);
            $centros_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            if ($recolecciones_count > 0 || $recolecciones_prov_count > 0 || $centros_count > 0) {
                http_response_code(409);
                echo json_encode(["error" => "No se puede eliminar el empleado porque tiene registros asociados"]);
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM empleados WHERE id_empleado = ?");
            $result = $stmt->execute([$data['id_empleado']]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(["mensaje" => "Empleado eliminado correctamente"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Empleado no encontrado"]);
            }
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
