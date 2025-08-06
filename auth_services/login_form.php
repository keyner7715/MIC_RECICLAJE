<?php
session_start();
require_once '../config/db.php'; // Asegúrate de que este archivo define correctamente $pdo
require_once 'encriptar.php'; // Asegúrate de que este archivo define la función verificarPassword

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que los campos no estén vacíos
    $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');

    if (empty($nombre_usuario) || empty($contrasena)) {
        $error = 'Por favor, complete todos los campos.';
    } else {
        try {
            // Preparar la consulta para buscar al usuario
            $stmt = $pdo->prepare("SELECT * FROM usuario WHERE nombre_usuario = ? AND estado = 'activo'");
            $stmt->execute([$nombre_usuario]);
            $user = $stmt->fetch();

            if ($user) {
                // Verificar la contraseña
                if (verificarPassword($contrasena, $user['contrasena'])) {
                    // Iniciar sesión y redirigir al menú
                    $_SESSION['id_usuario'] = $user['id_usuario'];
                    $_SESSION['nombre_usuario'] = $user['nombre_usuario'];
                    $_SESSION['rol'] = $user['rol'];
                    $_SESSION['estado'] = $user['estado'];
                    header('Location: ../public/menu.php');
                    exit;
                } else {
                    $error = 'Contraseña incorrecta.';
                }
            } else {
                $error = 'Usuario no encontrado o inactivo.';
            }
        } catch (PDOException $e) {
            $error = 'Error de conexión: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Maquinaria De Alquiler</title>
    <link rel="stylesheet" href="../public/login.css">
    <link rel="stylesheet" href="../public/forms.css">
</head>
<body>
    <!-- Fondo con imagen y formulario centrado -->
    <div class="login-bg-split">
        <div class="login-bg-image">
            <img src="../public/maquinaria.jpg" alt="Fondo de Login">
        </div>

        <div class="login-bg-form">
            <div class="form-container">
                
                <div class="form-header">
                    <h1>Iniciar Sesión</h1>
                    <?php if ($error): ?>
                        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                </div>
                <form method="POST" action="login_form.php">
                    <div class="form-group">
                        <label for="nombre_usuario" class="required">Usuario:</label>
                        <input type="text" id="nombre_usuario" name="nombre_usuario" required>
                    </div>
                    <div class="form-group">
                        <label for="contrasena" class="required">Contraseña:</label>
                        <input type="password" id="contrasena" name="contrasena" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Ingresar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>