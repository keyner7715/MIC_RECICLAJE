<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para editar disponibilidad
verificarPermiso('editar');

$id = $_GET['id'] ?? 0;
$disponibilidad = null;
$error = '';
$success = '';

// Obtener datos de la disponibilidad
if ($id) {
    try {
        $sql = "SELECT * FROM disponibilidad_maquinaria WHERE id_disponibilidad = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $disponibilidad = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$disponibilidad) {
            echo "<script>alert('Registro no encontrado'); window.location.href='R_disponibilidad.php';</script>";
            exit;
        }
        // Obtener maquinarias para el select
        $stmt_maquinarias = $pdo->query("SELECT id_maquinaria, nombre_maquinaria FROM maquinarias");
        $maquinarias = $stmt_maquinarias->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
} else {
    echo "<script>alert('ID no válido'); window.location.href='R_disponibilidad.php';</script>";
    exit;
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_maquinaria = $_POST['id_maquinaria'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $disponible = isset($_POST['disponible']) ? 1 : 0;

    if ($id_maquinaria && $fecha) {
        try {
            $sql = "UPDATE disponibilidad_maquinaria SET id_maquinaria = ?, fecha = ?, disponible = ? WHERE id_disponibilidad = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_maquinaria, $fecha, $disponible, $id]);
            $success = "Registro actualizado exitosamente.";
            // Refrescar datos
            $disponibilidad['id_maquinaria'] = $id_maquinaria;
            $disponibilidad['fecha'] = $fecha;
            $disponibilidad['disponible'] = $disponible;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Ya existe un registro de disponibilidad para esa maquinaria y fecha.";
            } else {
                $error = "Error al actualizar: " . $e->getMessage();
            }
        }
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Disponibilidad de Maquinaria</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <div class="container">
        <h2>Actualizar Disponibilidad de Maquinaria</h2>
        <a href="R_disponibilidad.php" class="btn-primary">Volver a la lista</a>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($disponibilidad): ?>
            <form method="POST">
                <div class="form-group">
                    <label for="id_maquinaria">Seleccionar Maquinaria:</label>
                    <select name="id_maquinaria" id="id_maquinaria" required>
                        <option value="">Seleccione una maquinaria</option>
                        <?php foreach ($maquinarias as $maquinaria): ?>
                            <option value="<?= htmlspecialchars($maquinaria['id_maquinaria']) ?>" <?= $maquinaria['id_maquinaria'] == $disponibilidad['id_maquinaria'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($maquinaria['nombre_maquinaria']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fecha">Fecha:</label>
                    <input type="date" name="fecha" id="fecha" value="<?= htmlspecialchars($disponibilidad['fecha']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="disponible">Disponible:</label>
                    <input type="checkbox" name="disponible" id="disponible" value="1" <?= $disponibilidad['disponible'] ? 'checked' : '' ?>>
                </div>
                <button type="submit" class="btn-primary">Actualizar</button>
            </form>
        <?php else: ?>
            <p>Registro no encontrado.</p>
        <?php endif; ?>
    </div>
</body>
</html>
