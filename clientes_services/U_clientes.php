<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar que el usuario tiene permiso para editar
verificarPermiso('editar');

$id = $_GET['id'] ?? 0;
$cliente = null;

// Obtener los datos de la cliente
if ($id) {
    try {
        $sql = "SELECT * FROM clientes WHERE id_cliente = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cliente) {
            echo "<script>alert('Cliente no encontrado'); window.location.href='R_clientes.php';</script>";
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Procesar la actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_cliente = trim($_POST['nombre_cliente'] ?? '');
    $cedula_ruc = trim($_POST['cedula_ruc'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
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
    if (!empty($cedula_ruc)) {
        if (!preg_match('/^[0-9]+$/', $cedula_ruc)) {
            $errores[] = "El RUC/Cédula solo debe contener números.";
        } elseif (!(strlen($cedula_ruc) === 10 || strlen($cedula_ruc) === 13)) {
            $errores[] = "El RUC/Cédula debe tener exactamente 10 o 13 dígitos.";
        }
    }
    if ($nombre_cliente && $cedula_ruc && $direccion && $telefono && $correo && empty($errores)) {
        try {
            $sql = "UPDATE clientes SET nombre_cliente = ?, cedula_ruc = ?, direccion = ?, telefono = ?, correo = ? WHERE id_cliente = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre_cliente, $cedula_ruc, $direccion, $telefono, $correo, $id]);

            echo "<script>alert('Cliente actualizado exitosamente'); window.location.href='R_clientes.php';</script>";
        } catch (PDOException $e) {
            echo "Error al actualizar: " . $e->getMessage();
        }
    } else {
        if (!empty($errores)) {
            foreach ($errores as $error) {
                echo "<div style='color: red; margin: 10px 0;'>" . $error . "</div>";
            }
        } else {
            echo "<div style='color: red; margin: 10px 0;'>Por favor complete los campos obligatorios.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Cliente</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <div class="container">
        <h2>Actualizar Cliente</h2>
        <?php if ($cliente): ?>
            <form method="POST" id="clienteForm">
                <div class="form-group">
                    <label for="nombre_cliente">Nombre del Cliente:</label>
                    <input type="text" name="nombre_cliente" id="nombre_cliente" value="<?= htmlspecialchars($cliente['nombre_cliente']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="cedula_ruc">RUC/Cédula:</label>
                    <input type="text" name="cedula_ruc" id="cedula_ruc" value="<?= htmlspecialchars($cliente['cedula_ruc']) ?>" required maxlength="13">
                    <div id="cedula_ruc_error" class="error-message" style="display:none;color:red;font-size:0.9em;margin-top:5px;"></div>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" name="direccion" id="direccion" value="<?= htmlspecialchars($cliente['direccion']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" name="telefono" id="telefono" value="<?= htmlspecialchars($cliente['telefono']) ?>" required maxlength="10">
                    <div id="telefono_error" class="error-message" style="display:none;color:red;font-size:0.9em;margin-top:5px;"></div>
                </div>
                <div class="form-group">
                    <label for="correo">Correo:</label>
                    <input type="email" name="correo" id="correo" value="<?= htmlspecialchars($cliente['correo']) ?>" required>
                </div>
                <div class="form-group">
                    <button type="submit">Actualizar Cliente</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
    <script src="../public/clientes_form.js"></script>
</body>
</html>
