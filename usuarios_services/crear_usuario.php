<?php
session_start();
require_once '../config/db.php';
require_once '../auth_services/encriptar.php';

// Verificar si el usuario tiene rol de Administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
    die("No tienes permiso para crear usuarios.");
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');
    $rol = $_POST['rol'] ?? '';
    $estado = $_POST['estado'] ?? 'activo';

    if (!empty($nombre_usuario) && !empty($contrasena)) {
        $contrasena = encriptarPassword($contrasena);
        
        try {
            $sql = "INSERT INTO usuario (nombre_usuario, contrasena, rol, estado) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre_usuario, $contrasena, $rol, $estado]);
            
            $mensaje = "Usuario creado exitosamente";
            $tipo_mensaje = "exito";
        } catch(PDOException $e) {
            $mensaje = "Error al crear el usuario: " . $e->getMessage();
            $tipo_mensaje = "error";
        }
    } else {
        $mensaje = "El nombre de usuario y contraseña son obligatorios";
        $tipo_mensaje = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="../public/style.css">
    <link rel="stylesheet" href="../public/forms.css">
</head>
<body>
    <div class="form-wrapper">
        <div class="form-container">
            <div class="form-header">
            <h1>Crear Nuevo Usuario</h1>
            
            <?php if (isset($mensaje)): ?>
                <div class="alert <?= $tipo_mensaje == 'exito' ? 'alert-success' : 'alert-error'; ?>">
                    <?= htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>
            
            </div>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nombre_usuario" class="required">Nombre de Usuario:</label>
                    <input type="text" id="nombre_usuario" name="nombre_usuario" required placeholder="Ingresa el nombre de usuario">
                </div>
                
                <div class="form-group">
                    <label for="contrasena" class="required">Contraseña:</label>
                    <input type="password" id="contrasena" name="contrasena" required placeholder="Ingresa la contraseña">
                </div>
                
                <div class="form-group">
                    <label for="rol">Rol:</label>
                    <div class="select-wrapper">
                        <select id="rol" name="rol" required>
                            <option value="Desarrollador">Desarrollador</option>
                            <option value="Administrador">Administrador</option>
                            <option value="Supervisor">Supervisor</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn">Crear Usuario</button>
                </div>
            </form>
            
            <div class="form-footer">
                <a href="../usuarios_services/R_usuario.php">Volver a la Lista</a> |
                <a href="../public/menu.php">Volver al Menu</a>
            </div>
        </div>
    </div>
</body>
</html>
