<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para crear tipos de material
verificarPermiso('crear');

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_tipo = trim($_POST['nombre_tipo']);
    
    // Validaciones
    if (empty($nombre_tipo)) {
        $error = 'El nombre del tipo de material es obligatorio.';
    } elseif (strlen($nombre_tipo) > 100) {
        $error = 'El nombre del tipo de material no puede exceder 100 caracteres.';
    } else {
        try {
            // Verificar que no exista ya este tipo de material (comparación insensible a mayúsculas)
            $sql_check = "SELECT COUNT(*) FROM tipos_material WHERE LOWER(nombre_tipo) = LOWER(?)";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->execute([$nombre_tipo]);
            
            if ($stmt_check->fetchColumn() > 0) {
                $error = 'Este tipo de material ya existe.';
            } else {
                // Insertar el nuevo tipo de material
                $sql = "INSERT INTO tipos_material (nombre_tipo) VALUES (?)";
                $stmt = $pdo->prepare($sql);
                
                if ($stmt->execute([$nombre_tipo])) {
                    $mensaje = 'Tipo de material creado exitosamente.';
                    // Limpiar el formulario
                    $_POST = array();
                } else {
                    $error = 'Error al crear el tipo de material.';
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
    <title>Crear Tipo de Material</title>
    <link rel="stylesheet" href="../public/style.css">
    <link rel="stylesheet" href="../public/forms.css">
</head>
<body>
    <div class="container">
        <h2>Crear Nuevo Tipo de Material</h2>
        
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
        
        <div class="form-container">
            <form method="POST" action="" id="tipoMaterialForm">
                <div class="form-group">
                    <label for="nombre_tipo">Tipo de Material:</label>
                    <input type="text" 
                           name="nombre_tipo" 
                           id="nombre_tipo" 
                           placeholder="Ingrese el nombre del tipo de material"
                           value="<?= isset($_POST['nombre_tipo']) ? htmlspecialchars($_POST['nombre_tipo']) : '' ?>"
                           maxlength="100"
                           required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-primary">Crear Tipo de Material</button>
                    <button type="reset" class="btn-secondary">Limpiar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/tipo_material_form.js"></script>
</body>
</html>
