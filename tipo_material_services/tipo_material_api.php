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
            // Si se solicita un tipo de material específico
            if (isset($_GET['id_tipo_material'])) {
                $stmt = $pdo->prepare("SELECT * FROM tipos_material WHERE id_tipo_material = ?");
                $stmt->execute([$_GET['id_tipo_material']]);
                $tipo_material = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($tipo_material) {
                    echo json_encode(["mensaje" => "Tipo de material encontrado", "data" => $tipo_material]);
                } else {
                    http_response_code(404);
                    echo json_encode(["error" => "Tipo de material no encontrado"]);
                }
            } else {
                // Listar todos los tipos de material
                $stmt = $pdo->query("SELECT * FROM tipos_material ORDER BY id_tipo_material DESC");
                echo json_encode(["mensaje" => "Listado de tipos de material", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener tipos de material"]);
        }
        break;

    case 'POST':
        verificarPermiso('crear', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nombre_tipo'])) {
            http_response_code(400);
            echo json_encode(["error" => "Falta campo requerido: nombre_tipo"]);
            exit;
        }

        // Validaciones adicionales
        $nombre_tipo = trim($data['nombre_tipo']);
        if (empty($nombre_tipo)) {
            http_response_code(400);
            echo json_encode(["error" => "El nombre del tipo de material no puede estar vacío"]);
            exit;
        }

        if (strlen($nombre_tipo) > 100) {
            http_response_code(400);
            echo json_encode(["error" => "El nombre del tipo de material no puede exceder 100 caracteres"]);
            exit;
        }

        try {
            // Verificar duplicados
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM tipos_material WHERE LOWER(nombre_tipo) = LOWER(?)");
            $stmt_check->execute([$nombre_tipo]);
            
            if ($stmt_check->fetchColumn() > 0) {
                http_response_code(409);
                echo json_encode(["error" => "Ya existe un tipo de material con ese nombre"]);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO tipos_material (nombre_tipo) VALUES (?)");
            $stmt->execute([$nombre_tipo]);
            echo json_encode(["mensaje" => "Tipo de material creado correctamente", "id_tipo_material" => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry error
                http_response_code(409);
                echo json_encode(["error" => "El tipo de material ya está registrado"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error al crear tipo de material"]);
            }
        }
        break;

    case 'PUT':
        verificarPermiso('editar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id_tipo_material']) || !isset($data['nombre_tipo'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos: id_tipo_material y nombre_tipo"]);
            exit;
        }

        // Validaciones adicionales
        $nombre_tipo = trim($data['nombre_tipo']);
        if (empty($nombre_tipo)) {
            http_response_code(400);
            echo json_encode(["error" => "El nombre del tipo de material no puede estar vacío"]);
            exit;
        }

        if (strlen($nombre_tipo) > 100) {
            http_response_code(400);
            echo json_encode(["error" => "El nombre del tipo de material no puede exceder 100 caracteres"]);
            exit;
        }

        try {
            // Verificar duplicados (excluyendo el actual)
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM tipos_material WHERE LOWER(nombre_tipo) = LOWER(?) AND id_tipo_material != ?");
            $stmt_check->execute([$nombre_tipo, $data['id_tipo_material']]);
            
            if ($stmt_check->fetchColumn() > 0) {
                http_response_code(409);
                echo json_encode(["error" => "Ya existe otro tipo de material con ese nombre"]);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE tipos_material SET nombre_tipo = ? WHERE id_tipo_material = ?");
            $result = $stmt->execute([
                $nombre_tipo,
                $data['id_tipo_material']
            ]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(["mensaje" => "Tipo de material actualizado correctamente"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Tipo de material no encontrado"]);
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry error
                http_response_code(409);
                echo json_encode(["error" => "El tipo de material ya está registrado por otro registro"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error al actualizar tipo de material"]);
            }
        }
        break;

    case 'DELETE':
        verificarPermiso('eliminar', $rol);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id_tipo_material'])) {
            http_response_code(400);
            echo json_encode(["error" => "Falta el campo 'id_tipo_material'"]);
            exit;
        }

        try {
            // Verificar si el tipo de material tiene materiales asociados
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM materiales WHERE id_tipo_material = ?");
            $stmt->execute([$data['id_tipo_material']]);
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            if ($count > 0) {
                http_response_code(409);
                echo json_encode(["error" => "No se puede eliminar el tipo de material porque tiene materiales asociados"]);
                exit;
            }

            // Verificar si el tipo de material tiene recolecciones asociadas
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM recolecciones WHERE id_tipo_material = ?");
                $stmt->execute([$data['id_tipo_material']]);
                $count_recolecciones = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                
                if ($count_recolecciones > 0) {
                    http_response_code(409);
                    echo json_encode(["error" => "No se puede eliminar el tipo de material porque tiene recolecciones asociadas"]);
                    exit;
                }
            } catch (PDOException $e) {
                // La tabla recolecciones podría no tener esta relación, continuar
            }
            
            $stmt = $pdo->prepare("DELETE FROM tipos_material WHERE id_tipo_material = ?");
            $result = $stmt->execute([$data['id_tipo_material']]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(["mensaje" => "Tipo de material eliminado correctamente"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Tipo de material no encontrado"]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al eliminar tipo de material"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
?>
