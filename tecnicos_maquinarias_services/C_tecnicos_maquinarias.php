<?php
require_once '../config/db.php';

$error = '';
$success = '';

// Obtener técnicos y maquinarias para los select
try {
    $stmt_tecnicos = $pdo->query("SELECT id_tecnico, nombre_tecnico FROM tecnicos");
    $tecnicos = $stmt_tecnicos->fetchAll(PDO::FETCH_ASSOC);

    $stmt_maquinarias = $pdo->query("SELECT id_maquinaria, nombre_maquinaria FROM maquinarias");
    $maquinarias = $stmt_maquinarias->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al obtener datos: " . $e->getMessage();
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tecnico = $_POST['id_tecnico'] ?? '';
    $id_maquinaria = $_POST['id_maquinaria'] ?? '';
    $fecha_asignacion = $_POST['fecha_asignacion'] ?? '';

    if ($id_tecnico && $id_maquinaria && $fecha_asignacion) {
        try {
            $stmt = $pdo->prepare("INSERT INTO tecnico_maquinaria (id_tecnico, id_maquinaria, fecha_asignacion) VALUES (?, ?, ?)");
            $stmt->execute([$id_tecnico, $id_maquinaria, $fecha_asignacion]);
            $success = "Asignación creada exitosamente.";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Esta asignación ya existe.";
            } else {
                $error = "Error al crear la asignación: " . $e->getMessage();
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
    <title>Asignar Técnico a Maquinaria</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <div class="container">
        <h2>Asignar Técnico a Maquinaria</h2>
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
                        <option value="<?= htmlspecialchars($tecnico['id_tecnico']) ?>">
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
                        <option value="<?= htmlspecialchars($maquinaria['id_maquinaria']) ?>">
                            <?= htmlspecialchars($maquinaria['nombre_maquinaria']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha_asignacion">Fecha de Asignación:</label>
                <input type="date" name="fecha_asignacion" id="fecha_asignacion" required>
            </div>
            <button type="submit" class="btn-primary">Asignar</button>
        </form>
    </div>
</body>
</html>
