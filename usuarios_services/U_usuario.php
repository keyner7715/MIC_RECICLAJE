<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar que el usuario tiene permiso para editar
verificarPermiso('editar');

$id = $_GET['id'] ?? 0;
$usuario = null;

// Obtener los datos del usuario
if ($id) {
    try {
        $sql = "SELECT * FROM usuario WHERE id_usuario = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            echo "<script>alert('Usuario no encontrado'); window.location.href='R_usuarios.php';</script>";
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Procesar la actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');
    $rol = trim($_POST['rol'] ?? '');
    $estado = trim($_POST['estado'] ?? '');

    if ($nombre_usuario && $rol && $estado) {
        try {
            $sql = "UPDATE usuario SET nombre_usuario = ?, rol = ?, estado = ?";
            $params = [$nombre_usuario, $rol, $estado];

            // Si se proporciona una nueva contraseña, se hashea y se incluye en la consulta
            if ($contrasena) {
                $hashed_password = password_hash($contrasena, PASSWORD_BCRYPT);
                $sql .= ", contrasena = ?";
                $params[] = $hashed_password;
            }

            $sql .= " WHERE id_usuario = ?";
            $params[] = $id;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            echo "<script>alert('Usuario actualizado exitosamente'); window.location.href='R_usuario.php';</script>";
        } catch (PDOException $e) {
            echo "Error al actualizar: " . $e->getMessage();
        }
    } else {
        echo "Por favor complete los campos obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Usuario</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <div class="container">
        <h2>Actualizar Usuario</h2>
        <?php if ($usuario): ?>
            <form method="POST">
                <div class="form-group">
                    <label for="nombre_usuario">Nombre de Usuario:</label>
                    <input type="text" name="nombre_usuario" id="nombre_usuario" value="<?= htmlspecialchars($usuario['nombre_usuario']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="contrasena">Contraseña (dejar en blanco para no cambiar):</label>
                    <input type="password" name="contrasena" id="contrasena">
                </div>
                <div class="form-group">
                    <label for="rol">Rol:</label>
                    <input type="text" name="rol" id="rol" value="<?= htmlspecialchars($usuario['rol']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="estado">Estado:</label>
                    <input type="text" name="estado" id="estado" value="<?= htmlspecialchars($usuario['estado']) ?>" required>
                </div>
                <div class="form-group">
                    <button type="submit">Actualizar Usuario</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>