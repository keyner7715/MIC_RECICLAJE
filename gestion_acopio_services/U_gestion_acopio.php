
<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para editar centros de acopio
verificarPermiso('editar');

$id = $_GET['id'] ?? 0;
$centro = null;

// Obtener lista de empleados para el select
$empleados = $pdo->query("SELECT id_empleado, nombre_empleado, cargo FROM empleados ORDER BY nombre_empleado ASC")->fetchAll(PDO::FETCH_ASSOC);

// Obtener datos del centro actual
if ($id) {
    try {
        $sql = "SELECT * FROM centros_acopio WHERE id_centro = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $centro = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$centro) {
            echo "<script>alert('Centro de acopio no encontrado'); window.location.href='R_gestion_acopio.php';</script>";
            exit;
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_centro = $_POST['nombre_centro'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $id_responsable = $_POST['id_responsable'] ?? null;

    try {
        $sql = "UPDATE centros_acopio SET nombre_centro = ?, direccion = ?, id_responsable = ? WHERE id_centro = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nombre_centro, 
            !empty($direccion) ? $direccion : null, 
            !empty($id_responsable) ? $id_responsable : null, 
            $id
        ]);

        echo "<script>alert('Centro de acopio actualizado exitosamente'); window.location.href='R_gestion_acopio.php';</script>";
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Centro de Acopio</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
<div class="container">
    <h2>Actualizar Centro de Acopio</h2>

    <?php if ($centro): ?>
        <form method="POST">
            <div class="form-group">
                <label for="nombre_centro">Nombre del Centro:</label>
                <input type="text" name="nombre_centro" id="nombre_centro" value="<?= htmlspecialchars($centro['nombre_centro']) ?>" maxlength="100" required>
            </div>
            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <textarea name="direccion" id="direccion" maxlength="150" rows="3"><?= htmlspecialchars($centro['direccion'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label for="id_responsable">Responsable:</label>
                <select name="id_responsable" id="id_responsable">
                    <option value="">-- Seleccionar responsable (opcional) --</option>
                    <?php foreach ($empleados as $empleado): ?>
                        <option value="<?= $empleado['id_empleado'] ?>" <?= $centro['id_responsable'] == $empleado['id_empleado'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($empleado['nombre_empleado']) ?><?= $empleado['cargo'] ? ' - ' . htmlspecialchars($empleado['cargo']) : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <button type="submit">Actualizar</button>
            </div>
        </form>
    <?php endif; ?>
    <a href="R_gestion_acopio.php" class="btn-volver">Volver</a>
</div>
</body>
</html>
