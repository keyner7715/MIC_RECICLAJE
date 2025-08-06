<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para crear centros de acopio
verificarPermiso('crear');

$mensaje = '';
$tipo_mensaje = '';

// Obtener lista de empleados para el select
try {
    $sql_empleados = "SELECT id_empleado, nombre_empleado, cargo FROM empleados ORDER BY nombre_empleado ASC";
    $stmt_empleados = $pdo->query($sql_empleados);
    $empleados = $stmt_empleados->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $empleados = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_centro = trim($_POST['nombre_centro'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $id_responsable = $_POST['id_responsable'] ?? null;
    
    // Validaciones
    $errores = [];
    
    if (empty($nombre_centro)) {
        $errores[] = 'El nombre del centro es requerido.';
    } elseif (strlen($nombre_centro) > 100) {
        $errores[] = 'El nombre del centro no puede exceder 100 caracteres.';
    }
    
    if (!empty($direccion) && strlen($direccion) > 150) {
        $errores[] = 'La dirección no puede exceder 150 caracteres.';
    }
    
    // Validar que el responsable existe si se proporciona
    if (!empty($id_responsable)) {
        try {
            $sql_check = "SELECT id_empleado FROM empleados WHERE id_empleado = ?";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->execute([$id_responsable]);
            if (!$stmt_check->fetch()) {
                $errores[] = 'El empleado seleccionado no existe.';
            }
        } catch (PDOException $e) {
            $errores[] = 'Error al verificar el empleado.';
        }
    }
    
    if (empty($errores)) {
        try {
            // Insertar nuevo centro de acopio
            $sql = "INSERT INTO centros_acopio (nombre_centro, direccion, id_responsable) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $nombre_centro,
                !empty($direccion) ? $direccion : null,
                !empty($id_responsable) ? $id_responsable : null
            ]);
            
            $mensaje = 'Centro de acopio "' . htmlspecialchars($nombre_centro) . '" creado exitosamente.';
            $tipo_mensaje = 'success';
            
            // Limpiar formulario después del éxito
            $nombre_centro = '';
            $direccion = '';
            $id_responsable = '';
            
            // Redirigir después de 2 segundos
            header("refresh:2;url=R_gestion_acopio.php");
            
        } catch (PDOException $e) {
            $mensaje = 'Error al crear el centro de acopio: ' . $e->getMessage();
            $tipo_mensaje = 'error';
        }
    } else {
        $mensaje = implode('<br>', $errores);
        $tipo_mensaje = 'error';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Crear Centro de Acopio</title>
    <link rel="stylesheet" href="../public/style.css">
    <link rel="stylesheet" href="../public/forms.css">
</head>
<body>
    <div class="container">
        <h2>Crear Centro de Acopio</h2>
        
        <?php if ($mensaje): ?>
            <div class="mensaje <?= $tipo_mensaje ?>">
                <?= $mensaje ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST" class="form" id="formCentroAcopio">
                <div class="form-group">
                    <label for="nombre_centro">Nombre del Centro *</label>
                    <input type="text" 
                           id="nombre_centro" 
                           name="nombre_centro" 
                           value="<?= htmlspecialchars($nombre_centro ?? '') ?>"
                           maxlength="100" 
                           required>
                    <small class="char-counter">
                        <span id="nombre_centro_count">0</span>/100 caracteres
                    </small>
                    <div class="error-message" id="error_nombre_centro"></div>
                </div>
                
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <textarea id="direccion" 
                              name="direccion" 
                              rows="3" 
                              maxlength="150"
                              placeholder="Ingresa la dirección del centro de acopio"><?= htmlspecialchars($direccion ?? '') ?></textarea>
                    <small class="char-counter">
                        <span id="direccion_count">0</span>/150 caracteres
                    </small>
                    <div class="error-message" id="error_direccion"></div>
                </div>
                
                <div class="form-group">
                    <label for="id_responsable">Responsable</label>
                    <select id="id_responsable" name="id_responsable">
                        <option value="">-- Seleccionar responsable (opcional) --</option>
                        <?php foreach ($empleados as $empleado): ?>
                            <option value="<?= $empleado['id_empleado'] ?>" 
                                    <?= (isset($id_responsable) && $id_responsable == $empleado['id_empleado']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($empleado['nombre_empleado']) ?> 
                                <?= $empleado['cargo'] ? '- ' . htmlspecialchars($empleado['cargo']) : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="error-message" id="error_id_responsable"></div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Crear Centro</button>
                    <a href="R_gestion_acopio.php" class="btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="../js/gestion_acopio_forms.js"></script>
</body>
</html>
