<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar que el usuario tiene permiso para editar
verificarPermiso('editar');

// Tipos de proveedor permitidos
$tipos_permitidos = ['Industrial', 'Institucional', 'Comercial', 'Gubernamental'];

$id = $_GET['id'] ?? 0;
$proveedor = null;

// Obtener los datos del proveedor
if ($id) {
    try {
        $sql = "SELECT * FROM proveedores WHERE id_proveedor = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$proveedor) {
            echo "<script>alert('Proveedor no encontrado'); window.location.href='R_proveedores.php';</script>";
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Procesar la actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_proveedor = trim($_POST['nombre_proveedor'] ?? '');
    $tipo_proveedor = $_POST['tipo_proveedor'] ?? '';
    $direccion = trim($_POST['direccion'] ?? '');
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
    
    // Validar tipo de proveedor
    if (!empty($tipo_proveedor) && !in_array($tipo_proveedor, $tipos_permitidos)) {
        $errores[] = "Tipo de proveedor no válido.";
    }

    // Validar correo si se proporciona
    if (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del correo electrónico no es válido.";
    }

    if ($nombre_proveedor && empty($errores)) {
        try {
            $sql = "UPDATE proveedores SET nombre_proveedor = ?, tipo_proveedor = ?, direccion = ?, telefono = ?, correo = ? WHERE id_proveedor = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $nombre_proveedor,
                !empty($tipo_proveedor) ? $tipo_proveedor : null,
                !empty($direccion) ? $direccion : null,
                !empty($telefono) ? $telefono : null,
                !empty($correo) ? $correo : null,
                $id
            ]);

            echo "<script>alert('Proveedor actualizado exitosamente'); window.location.href='R_proveedores.php';</script>";
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
    <title>Actualizar Proveedor</title>
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
        <h2>Actualizar Proveedor</h2>
        <?php if ($proveedor): ?>
            <form method="POST" id="proveedorForm">
                <div class="form-group">
                    <label for="nombre_proveedor">Nombre del Proveedor:</label>
                    <input type="text" name="nombre_proveedor" id="nombre_proveedor" 
                           value="<?= htmlspecialchars($proveedor['nombre_proveedor']) ?>" 
                           required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="tipo_proveedor">Tipo de Proveedor:</label>
                    <select name="tipo_proveedor" id="tipo_proveedor">
                        <option value="">Seleccione un tipo</option>
                        <?php foreach ($tipos_permitidos as $tipo): ?>
                            <option value="<?= $tipo ?>" <?= $proveedor['tipo_proveedor'] == $tipo ? 'selected' : '' ?>>
                                <?= $tipo ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" name="direccion" id="direccion" 
                           value="<?= htmlspecialchars($proveedor['direccion'] ?? '') ?>" 
                           maxlength="150">
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" name="telefono" id="telefono" 
                           value="<?= htmlspecialchars($proveedor['telefono'] ?? '') ?>" 
                           maxlength="10" placeholder="10 dígitos numéricos">
                    <div id="telefono_error" class="error-message"></div>
                </div>
                <div class="form-group">
                    <label for="correo">Correo:</label>
                    <input type="email" name="correo" id="correo" 
                           value="<?= htmlspecialchars($proveedor['correo'] ?? '') ?>" 
                           maxlength="100" placeholder="ejemplo@empresa.com">
                </div>
                <div class="form-group">
                    <button type="submit">Actualizar Proveedor</button>
                    <a href="R_proveedores.php" class="btn-secondary" style="margin-left: 10px; text-decoration: none; padding: 8px 15px; background-color: #6c757d; color: white; border-radius: 4px;">Cancelar</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
    
    <script src="../js/proveedores_form.js"></script>
</body>
</html>
