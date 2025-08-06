<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para editar tipos de material
verificarPermiso('editar');

$id = $_GET['id'] ?? 0;
$tipo_material = null;
$mensaje = '';
$error = '';

// Obtener datos del tipo de material
if ($id) {
    try {
        $sql = "SELECT * FROM tipos_material WHERE id_tipo_material = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $tipo_material = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$tipo_material) {
            echo "<script>alert('Tipo de material no encontrado'); window.location.href='R_tipo_material.php';</script>";
            exit;
        }
    } catch(PDOException $e) {
        $error = "Error al obtener el tipo de material: " . $e->getMessage();
    }
} else {
    echo "<script>alert('ID no válido'); window.location.href='R_tipo_material.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_tipo = trim($_POST['nombre_tipo']);
    
    // Validaciones
    if (empty($nombre_tipo)) {
        $error = 'El nombre del tipo de material es obligatorio.';
    } elseif (strlen($nombre_tipo) > 100) {
        $error = 'El nombre del tipo de material no puede exceder 100 caracteres.';
    } else {
        try {
            // Verificar que no exista ya este tipo de material (excluyendo el actual)
            $sql_check = "SELECT COUNT(*) FROM tipos_material WHERE LOWER(nombre_tipo) = LOWER(?) AND id_tipo_material != ?";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->execute([$nombre_tipo, $id]);
            
            if ($stmt_check->fetchColumn() > 0) {
                $error = 'Ya existe otro tipo de material con ese nombre.';
            } else {
                // Actualizar el tipo de material
                $sql = "UPDATE tipos_material SET nombre_tipo = ? WHERE id_tipo_material = ?";
                $stmt = $pdo->prepare($sql);
                
                if ($stmt->execute([$nombre_tipo, $id])) {
                    $mensaje = 'Tipo de material actualizado exitosamente.';
                    // Actualizar los datos mostrados en el formulario
                    $tipo_material['nombre_tipo'] = $nombre_tipo;
                } else {
                    $error = 'Error al actualizar el tipo de material.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Error de base de datos: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Actualizar Tipo de Material</title>
    <link rel="stylesheet" href="../public/style.css">
    <link rel="stylesheet" href="../public/forms.css">
</head>
<body>
    <div class="container">
        <h2>Actualizar Tipo de Material</h2>
        
        <div class="actions">
            <a href="R_tipo_material.php" class="btn-primary">Ver Tipos de Material</a>
            <a href="../public/menu.php" class="btn-primary">Inicio</a>
        </div>
        
        <?php if ($mensaje): ?>
            <div class="mensaje-exito"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($tipo_material): ?>
            <div class="form-container">
                <form method="POST" action="" id="tipoMaterialForm">
                    <div class="form-group">
                        <label for="nombre_tipo">Tipo de Material:</label>
                        <input type="text" 
                               name="nombre_tipo" 
                               id="nombre_tipo" 
                               placeholder="Ingrese el nombre del tipo de material"
                               value="<?= htmlspecialchars($tipo_material['nombre_tipo']) ?>"
                               maxlength="100"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn-primary">Actualizar Tipo de Material</button>
                        <a href="R_tipo_material.php" class="btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script src="../js/tipo_material_form.js"></script>
</body>
</html>
