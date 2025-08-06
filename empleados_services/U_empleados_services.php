<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar que el usuario tiene permiso para editar
verificarPermiso('editar');

// Cargos permitidos
$cargos_permitidos = ['Supervisor', 'Recolector', 'Clasificadora'];

$id = $_GET['id'] ?? 0;
$empleado = null;

// Obtener los datos del empleado
if ($id) {
    try {
        $sql = "SELECT * FROM empleados WHERE id_empleado = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$empleado) {
            echo "<script>alert('Empleado no encontrado'); window.location.href='R_empleados_services.php';</script>";
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Procesar la actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_empleado = trim($_POST['nombre_empleado'] ?? '');
    $cargo = $_POST['cargo'] ?? '';
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');

    // Validaciones adicionales
    $errores = [];
    
    // Validar teléfono: solo números y exactamente 10 dígitos
    if (!empty($telefono)) {
        if (!preg_match('/^[0-9]+$/', $telefono)) {
            $errores[] = "El teléfono solo debe contener números.";
        } elseif (strlen($telefono) !== 10) {
            $errores[] = "El teléfono debe tener exactamente 10 dígitos.";
        }
    }
    
    // Validar cargo
    if (!in_array($cargo, $cargos_permitidos)) {
        $errores[] = "Debe seleccionar un cargo válido.";
    }

    if ($nombre_empleado && $cargo && empty($errores)) {
        try {
            $sql = "UPDATE empleados SET nombre_empleado = ?, cargo = ?, telefono = ?, correo = ? WHERE id_empleado = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $nombre_empleado, 
                $cargo, 
                !empty($telefono) ? $telefono : null, 
                !empty($correo) ? $correo : null, 
                $id
            ]);

            echo "<script>alert('Empleado actualizado exitosamente'); window.location.href='R_empleados_services.php';</script>";
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
    <title>Actualizar Empleado</title>
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
        <h2>Actualizar Empleado</h2>
        <?php if ($empleado): ?>
            <form method="POST" id="empleadoForm">
                <div class="form-group">
                    <label for="nombre_empleado">Nombre del Empleado:</label>
                    <input type="text" name="nombre_empleado" id="nombre_empleado" value="<?= htmlspecialchars($empleado['nombre_empleado']) ?>" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="cargo">Cargo:</label>
                    <select name="cargo" id="cargo" required>
                        <option value="">Seleccione un cargo</option>
                        <?php foreach ($cargos_permitidos as $cargo_opcion): ?>
                            <option value="<?= $cargo_opcion ?>" <?= $empleado['cargo'] == $cargo_opcion ? 'selected' : '' ?>>
                                <?= $cargo_opcion ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" name="telefono" id="telefono" value="<?= htmlspecialchars($empleado['telefono'] ?? '') ?>" maxlength="10" placeholder="10 dígitos numéricos">
                    <div id="telefono_error" class="error-message"></div>
                </div>
                <div class="form-group">
                    <label for="correo">Correo:</label>
                    <input type="email" name="correo" id="correo" value="<?= htmlspecialchars($empleado['correo'] ?? '') ?>" maxlength="100" placeholder="ejemplo@empresa.com">
                </div>
                <div class="form-group">
                    <button type="submit">Actualizar Empleado</button>
                    <a href="R_empleados_services.php" class="btn-secondary" style="margin-left: 10px; text-decoration: none; padding: 8px 15px; background-color: #6c757d; color: white; border-radius: 4px;">Cancelar</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
    
    <script src="../js/empleados_form.js"></script>
</body>
</html>
