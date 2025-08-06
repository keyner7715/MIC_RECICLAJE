<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para editar materiales
verificarPermiso('editar');

$id = $_GET['id'] ?? 0;
$material = null;
$mensaje = '';
$error = '';

// Obtener tipos de material para el select
try {
    $stmt_tipos = $pdo->query("SELECT id_tipo_material, nombre_tipo FROM tipos_material ORDER BY nombre_tipo ASC");
    $tipos_material = $stmt_tipos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al obtener tipos de material: " . $e->getMessage();
    $tipos_material = [];
}

// Obtener datos del material
if ($id) {
    try {
        $sql = "SELECT * FROM materiales WHERE id_material = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $material = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$material) {
            echo "<script>alert('Material no encontrado'); window.location.href='R_material.php';</script>";
            exit;
        }
    } catch(PDOException $e) {
        $error = "Error al obtener el material: " . $e->getMessage();
    }
} else {
    echo "<script>alert('ID no válido'); window.location.href='R_material.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_material = trim($_POST['nombre_material']);
    $descripcion = trim($_POST['descripcion']);
    $id_tipo_material = $_POST['id_tipo_material'] ?? null;
    
    // Validaciones
    if (empty($nombre_material)) {
        $error = 'El nombre del material es obligatorio.';
    } elseif (strlen($nombre_material) > 100) {
        $error = 'El nombre del material no puede exceder 100 caracteres.';
    } else {
        try {
            // Verificar que no exista ya este material (excluyendo el actual)
            $sql_check = "SELECT COUNT(*) FROM materiales WHERE LOWER(nombre_material) = LOWER(?) AND id_material != ?";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->execute([$nombre_material, $id]);
            
            if ($stmt_check->fetchColumn() > 0) {
                $error = 'Ya existe otro material con ese nombre.';
            } else {
                // Actualizar el material
                $sql = "UPDATE materiales SET nombre_material = ?, descripcion = ?, id_tipo_material = ? WHERE id_material = ?";
                $stmt = $pdo->prepare($sql);
                
                // Si no se selecciona tipo, pasar NULL
                $tipo_final = empty($id_tipo_material) ? null : $id_tipo_material;
                
                if ($stmt->execute([$nombre_material, $descripcion, $tipo_final, $id])) {
                    $mensaje = 'Material actualizado exitosamente.';
                    // Actualizar los datos mostrados en el formulario
                    $material['nombre_material'] = $nombre_material;
                    $material['descripcion'] = $descripcion;
                    $material['id_tipo_material'] = $tipo_final;
                } else {
                    $error = 'Error al actualizar el material.';
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
    <title>Actualizar Material</title>
    <link rel="stylesheet" href="../public/style.css">
    <link rel="stylesheet" href="../public/forms.css">
</head>
<body>
    <div class="container">
        <h2>Actualizar Material</h2>
        
        <div class="actions">
            <a href="R_material.php" class="btn-primary">Ver Materiales</a>
            <a href="../public/menu.php" class="btn-primary">Inicio</a>
        </div>
        
        <?php if ($mensaje): ?>
            <div class="mensaje-exito"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($material): ?>
            <div class="form-container">
                <form method="POST" action="" id="materialForm">
                    <div class="form-group">
                        <label for="nombre_material">Nombre del Material:</label>
                        <input type="text" 
                               name="nombre_material" 
                               id="nombre_material" 
                               placeholder="Ingrese el nombre del material"
                               value="<?= htmlspecialchars($material['nombre_material']) ?>"
                               maxlength="100"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion">Descripción:</label>
                        <textarea name="descripcion" 
                                  id="descripcion" 
                                  placeholder="Ingrese una descripción del material (opcional)"
                                  rows="4"><?= htmlspecialchars($material['descripcion'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="id_tipo_material">Tipo de Material:</label>
                        <select name="id_tipo_material" id="id_tipo_material">
                            <option value="">-- Seleccione un tipo (opcional) --</option>
                            <?php foreach ($tipos_material as $tipo): ?>
                                <option value="<?= htmlspecialchars($tipo['id_tipo_material']) ?>" 
                                    <?= ($material['id_tipo_material'] == $tipo['id_tipo_material']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tipo['nombre_tipo']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn-primary">Actualizar Material</button>
                        <a href="R_material.php" class="btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
        
        <?php if (!$material && empty($error)): ?>
            <div class="mensaje-error">
                Material no encontrado.
            </div>
        <?php endif; ?>
    </div>

    <script src="../js/material_form.js"></script>
</body>
</html>
