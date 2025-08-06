<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar que el usuario tiene permiso para crear
verificarPermiso('crear');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_cliente = trim($_POST['nombre_cliente'] ?? '');
    $ruc_cedula = trim($_POST['ruc_cedula'] ?? '');
    $direccion = $_POST['direccion'] ?? '';
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');

    // Validaciones adicionales
    $errores = [];
    
    // Validar teléfono: solo números y máximo 10 dígitos
    if (!empty($telefono)) {
        if (!preg_match('/^[0-9]+$/', $telefono)) {
            $errores[] = "El teléfono solo debe contener números.";
        } elseif (strlen($telefono) > 10) {
            $errores[] = "El teléfono no puede tener más de 10 dígitos.";
        }
    }
    
    // Validar RUC/Cédula: solo números, exactamente 10 o 13 dígitos
    if (!empty($ruc_cedula)) {
        if (!preg_match('/^[0-9]+$/', $ruc_cedula)) {
            $errores[] = "El RUC/Cédula solo debe contener números.";
        } elseif (!(strlen($ruc_cedula) === 10 || strlen($ruc_cedula) === 13)) {
            $errores[] = "El RUC/Cédula debe tener exactamente 10 o 13 dígitos.";
        }
    }

    if ($nombre_cliente && $ruc_cedula && $direccion && $telefono && $correo && empty($errores)) {
        try {
            $sql = "INSERT INTO clientes (nombre_cliente, ruc_cedula, direccion, telefono, correo) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre_cliente, $ruc_cedula, $direccion, $telefono, $correo]);

            echo "<script>alert('Cliente registrado exitosamente'); window.location.href='R_clientes.php';</script>";
        } catch (PDOException $e) {
            echo "Error al registrar el cliente: " . $e->getMessage();
        }
    } else {
        if (!empty($errores)) {
            foreach ($errores as $error) {
                echo "<div style='color: red; margin: 10px 0;'>" . $error . "</div>";
            }
        } else {
            echo "<div style='color: red; margin: 10px 0;'>Por favor complete todos los campos obligatorios.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Cliente</title>
    <link rel="stylesheet" href="../public/style.css">
    <style>
        .error-message {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
            display: none;
        }
        .input-error {
            border: 2px solid red !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registrar Nuevo Cliente</h2>
        <form method="POST" id="clienteForm">
            <div class="form-group">
                <label for="nombre_cliente">Nombre del Cliente:</label>
                <input type="text" name="nombre_cliente" id="nombre_cliente" required>
            </div>
            <div class="form-group">
                <label for="ruc_cedula">RUC/Cédula:</label>
                <input type="text" name="ruc_cedula" id="ruc_cedula" required maxlength="13">
                <div id="ruc_error" class="error-message"></div>
            </div>
            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <input type="text" name="direccion" id="direccion" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" name="telefono" id="telefono" required maxlength="10">
                <div id="telefono_error" class="error-message"></div>
            </div>
            <div class="form-group">
                <label for="correo">Correo:</label>
                <input type="email" name="correo" id="correo" required>
            </div>
            <button type="submit">Crear Cliente</button>
        </form>
    </div>

    <script src="../js/clientes_form.js"></script>
</body>
</html>
