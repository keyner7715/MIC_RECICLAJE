<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para editar
verificarPermiso('editar');

$id = $_GET['id'] ?? 0;
$mantenimiento = null;

// Obtener maquinarias
try {
    $stmt = $pdo->query("SELECT id_maquinaria, nombre_maquinaria FROM maquinarias ORDER BY nombre_maquinaria");
    $maquinarias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error al obtener maquinarias: " . $e->getMessage();
    $maquinarias = [];
}

// Obtener técnicos
try {
    $stmt = $pdo->query("SELECT id_tecnico, nombre_tecnico FROM tecnicos ORDER BY nombre_tecnico");
    $tecnicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error al obtener técnicos: " . $e->getMessage();
    $tecnicos = [];
}

// Obtener datos del mantenimiento
if ($id) {
    try {
        $sql = "SELECT * FROM mantenimiento WHERE id_mantenimiento = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $mantenimiento = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$mantenimiento) {
            echo "<script>alert('Mantenimiento no encontrado'); window.location.href='R_mantenimiento.php';</script>";
            exit;
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_maquinaria = $_POST['id_maquinaria'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $costo_mantenimiento = $_POST['costo_mantenimiento'] ?? '';
    $id_tecnico = $_POST['id_tecnico'] ?? '';

    // Validación básica
    if (!$id_maquinaria || !$fecha || !$id_tecnico || $costo_mantenimiento === '' || !is_numeric($costo_mantenimiento) || $costo_mantenimiento <= 0) {
        echo "<script>alert('Todos los campos son obligatorios y el costo debe ser un número mayor a 0.'); window.history.back();</script>";
        exit;
    }

    try {
        $sql = "UPDATE mantenimiento SET id_maquinaria = ?, fecha = ?, descripcion = ?, costo_mantenimiento = ?, id_tecnico = ? WHERE id_mantenimiento = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_maquinaria, $fecha, $descripcion, $costo_mantenimiento, $id_tecnico, $id]);
        echo "<script>alert('Mantenimiento actualizado exitosamente'); window.location.href='R_mantenimiento.php';</script>";
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Mantenimiento</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <div class="container">
        <h2>Actualizar Mantenimiento</h2>
        <?php if ($mantenimiento): ?>
        <form method="POST">
            <div class="form-group">
                <label for="id_maquinaria">Maquinaria:</label>
                <select name="id_maquinaria" id="id_maquinaria" required>
                    <option value="">Seleccione una maquinaria</option>
                    <?php foreach ($maquinarias as $m): ?>
                        <option value="<?= $m['id_maquinaria'] ?>" <?= $mantenimiento['id_maquinaria'] == $m['id_maquinaria'] ? 'selected' : '' ?>><?= htmlspecialchars($m['nombre_maquinaria']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha">Fecha:</label>
                <input type="date" name="fecha" id="fecha" value="<?= htmlspecialchars($mantenimiento['fecha']) ?>" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea name="descripcion" id="descripcion" rows="3" required><?= htmlspecialchars($mantenimiento['descripcion']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="costo_mantenimiento">Costo:</label>
                <input type="text" name="costo_mantenimiento" id="costo_mantenimiento" value="<?= htmlspecialchars($mantenimiento['costo_mantenimiento']) ?>" required>
                <div id="costo_error" class="error-message" style="display:none;color:red;font-size:0.9em;margin-top:5px;"></div>
            </div>
            <div class="form-group">
                <label for="id_tecnico">Técnico:</label>
                <select name="id_tecnico" id="id_tecnico" required>
                    <option value="">Seleccione un técnico</option>
                    <?php foreach ($tecnicos as $t): ?>
                        <option value="<?= $t['id_tecnico'] ?>" <?= $mantenimiento['id_tecnico'] == $t['id_tecnico'] ? 'selected' : '' ?>><?= htmlspecialchars($t['nombre_tecnico']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <button type="submit">Actualizar Mantenimiento</button>
            </div>
        </form>
        <?php endif; ?>
        <a href="R_mantenimiento.php" class="btn-primary">Volver</a>
    </div>
    <script src="../js/mantenimiento_forms.js"></script>
</body>
</html>
