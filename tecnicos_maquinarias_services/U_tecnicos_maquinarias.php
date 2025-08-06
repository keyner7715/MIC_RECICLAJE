<?php
require_once '../config/db.php';

$id_tecnico = $_GET['id_tecnico'] ?? '';
$id_maquinaria = $_GET['id_maquinaria'] ?? '';
$fecha_asignacion = $_GET['fecha_asignacion'] ?? '';
$error = '';
$success = '';

// Verificar que los IDs sean válidos
if ($id_tecnico && $id_maquinaria && $fecha_asignacion) {
    try {
        // Obtener los datos de la asignación
        $stmt_asignacion = $pdo->prepare("SELECT * FROM tecnico_maquinaria WHERE id_tecnico = ? AND id_maquinaria = ? AND fecha_asignacion = ?");
        $stmt_asignacion->execute([$id_tecnico, $id_maquinaria, $fecha_asignacion]);
        $asignacion = $stmt_asignacion->fetch(PDO::FETCH_ASSOC);

        if (!$asignacion) {
            echo "<script>alert('Asignación no encontrada'); window.location.href='R_tecnicos_maquinarias.php';</script>";
            exit;
        }

        // Obtener técnicos y maquinarias para los select
        $stmt_tecnicos = $pdo->query("SELECT id_tecnico, nombre_tecnico FROM tecnicos");
        $tecnicos = $stmt_tecnicos->fetchAll(PDO::FETCH_ASSOC);

        $stmt_maquinarias = $pdo->query("SELECT id_maquinaria, nombre_maquinaria FROM maquinarias");
        $maquinarias = $stmt_maquinarias->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error al obtener datos: " . $e->getMessage();
    }
} else {
    echo "<script>alert('Datos de asignación no válidos'); window.location.href='R_tecnicos_maquinarias.php';</script>";
    exit;
}

// Procesar la actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_id_tecnico = $_POST['id_tecnico'] ?? '';
    $nuevo_id_maquinaria = $_POST['id_maquinaria'] ?? '';
    $nueva_fecha_asignacion = $_POST['fecha_asignacion'] ?? '';

    if ($nuevo_id_tecnico && $nuevo_id_maquinaria && $nueva_fecha_asignacion) {
        try {
            $stmt_update = $pdo->prepare("UPDATE tecnico_maquinaria SET id_tecnico = ?, id_maquinaria = ?, fecha_asignacion = ? WHERE id_tecnico = ? AND id_maquinaria = ? AND fecha_asignacion = ?");
            $stmt_update->execute([
                $nuevo_id_tecnico,
                $nuevo_id_maquinaria,
                $nueva_fecha_asignacion,
                $id_tecnico,
                $id_maquinaria,
                $fecha_asignacion
            ]);
            $success = "Asignación actualizada exitosamente.";
            // Actualizar los valores para el formulario
            $id_tecnico = $nuevo_id_tecnico;
            $id_maquinaria = $nuevo_id_maquinaria;
            $fecha_asignacion = $nueva_fecha_asignacion;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Ya existe una asignación con esos datos.";
            } else {
                $error = "Error al actualizar la asignación: " . $e->getMessage();
            }
        }
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Actualizar Asignación Técnico-Maquinaria</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <div class="container">
        <h2>Actualizar Asignación Técnico-Maquinaria</h2>
        <a href="R_tecnicos_maquinarias.php" class="btn-primary">Volver a la lista</a>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="id_tecnico">Seleccionar Técnico:</label>
                <select name="id_tecnico" id="id_tecnico" required>
                    <option value="">Seleccione un técnico</option>
                    <?php foreach ($tecnicos as $tecnico): ?>
                        <option value="<?= htmlspecialchars($tecnico['id_tecnico']) ?>" <?= $tecnico['id_tecnico'] == $id_tecnico ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tecnico['nombre_tecnico']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_maquinaria">Seleccionar Maquinaria:</label>
                <select name="id_maquinaria" id="id_maquinaria" required>
                    <option value="">Seleccione una maquinaria</option>
                    <?php foreach ($maquinarias as $maquinaria): ?>
                        <option value="<?= htmlspecialchars($maquinaria['id_maquinaria']) ?>" <?= $maquinaria['id_maquinaria'] == $id_maquinaria ? 'selected' : '' ?>>
                            <?= htmlspecialchars($maquinaria['nombre_maquinaria']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha_asignacion">Fecha de Asignación:</label>
                <input type="date" name="fecha_asignacion" id="fecha_asignacion" value="<?= htmlspecialchars($fecha_asignacion) ?>" required>
            </div>
            <button type="submit" class="btn-primary">Actualizar Asignación</button>
        </form>
    </div>
</body>
</html>
