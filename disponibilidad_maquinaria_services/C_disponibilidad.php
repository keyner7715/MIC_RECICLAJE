<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para crear disponibilidad
verificarPermiso('crear');

$error = '';
$success = '';

// Obtener maquinarias para el select
try {
    $stmt_maquinarias = $pdo->query("SELECT id_maquinaria, nombre_maquinaria FROM maquinarias");
    $maquinarias = $stmt_maquinarias->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al obtener maquinarias: " . $e->getMessage();
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_maquinaria = $_POST['id_maquinaria'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $disponible = isset($_POST['disponible']) ? 1 : 0;

    if ($id_maquinaria && $fecha) {
        try {
            $stmt = $pdo->prepare("INSERT INTO disponibilidad_maquinaria (id_maquinaria, fecha, disponible) VALUES (?, ?, ?)");
            $stmt->execute([$id_maquinaria, $fecha, $disponible]);
            $success = "Disponibilidad registrada exitosamente.";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Ya existe un registro de disponibilidad para esa maquinaria y fecha.";
            } else {
                $error = "Error al registrar la disponibilidad: " . $e->getMessage();
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
    <title>Registrar Disponibilidad de Maquinaria</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <div class="container">
        <h2>Registrar Disponibilidad de Maquinaria</h2>
        <a href="R_disponibilidad.php" class="btn-primary">Volver a la lista</a>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="id_maquinaria">Seleccionar Maquinaria:</label>
                <select name="id_maquinaria" id="id_maquinaria" required>
                    <option value="">Seleccione una maquinaria</option>
                    <?php foreach ($maquinarias as $maquinaria): ?>
                        <option value="<?= htmlspecialchars($maquinaria['id_maquinaria']) ?>">
                            <?= htmlspecialchars($maquinaria['nombre_maquinaria']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha">Fecha:</label>
                <input type="date" name="fecha" id="fecha" required>
            </div>
            <div class="form-group">
                <label for="disponible">Disponible:</label>
                <input type="checkbox" name="disponible" id="disponible" value="1" checked>
            </div>
            <button type="submit" class="btn-primary">Registrar</button>
        </form>
    </div>
</body>
</html>
