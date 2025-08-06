<?php
require_once '../config/db.php';

$id = $_GET['id'] ?? 0;
$maquinaria = null;

// Obtener datos del maquinaria
if ($id) {
    try {
        $sql = "SELECT * FROM maquinarias WHERE id_maquinaria = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $maquinaria = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$maquinaria) {
            echo "<script>alert('Maquinaria no encontrada'); window.location.href='R_maquinaria_services.php';</script>";
            exit;
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_maquinaria = $_POST['nombre_maquinaria'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $marca = $_POST['marca'] ?? '';
    $modelo = $_POST['modelo'] ?? '';
    $año = $_POST['año'] ?? '';
    $estado_maquinaria = $_POST['estado_maquinaria'] ?? '';
    $precio_diario = $_POST['precio_diario'] ?? '';

    // Validación para año y precio_diario
    if (!is_numeric($año) || intval($año) <= 0) {
        echo "<script>alert('El año debe ser un número mayor a 0'); window.history.back();</script>";
        exit;
    }
    if (!is_numeric($precio_diario) || floatval($precio_diario) <= 0) {
        echo "<script>alert('El precio diario debe ser un número mayor a 0'); window.history.back();</script>";
        exit;
    }

    try {
        $sql = "UPDATE maquinarias SET nombre_maquinaria = ?, tipo = ?, marca = ?, modelo = ?, año = ?, estado_maquinaria = ?, precio_diario = ? WHERE id_maquinaria = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre_maquinaria, $tipo, $marca, $modelo, $año, $estado_maquinaria, $precio_diario, $id]);

        echo "<script>alert('Maquinaria actualizado exitosamente'); window.location.href='R_maquinaria_services.php';</script>";
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Actualizar Maquinaria</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <div class="container">
        <h2>Actualizar Maquinaria</h2>

        <?php if ($maquinaria): ?>
            <form method="POST">
                <div class="form-group">
                    <label>Nombre de la Maquinaria:</label>
                    <input type="text" name="nombre_maquinaria" value="<?= htmlspecialchars($maquinaria['nombre_maquinaria']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Tipo:</label>
                    <input type="text" name="tipo" value="<?= htmlspecialchars($maquinaria['tipo']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Marca:</label>
                    <input type="text" name="marca" value="<?= htmlspecialchars($maquinaria['marca']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Modelo:</label>
                    <input type="text" name="modelo" value="<?= htmlspecialchars($maquinaria['modelo']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Año:</label>
                    <input type="text" name="año" id="año" value="<?= htmlspecialchars($maquinaria['año']) ?>" required>
                    <div id="anio_error" class="error-message" style="display:none;color:red;font-size:0.9em;margin-top:5px;"></div>
                </div>

                <div class="form-group">
                    <label>Estado:</label>
                    <select name="estado_maquinaria" required>
                        <option value="Disponible" <?= $maquinaria['estado_maquinaria'] == 'Disponible' ? 'selected' : '' ?>>Disponible</option>
                        <option value="Alquilada" <?= $maquinaria['estado_maquinaria'] == 'Alquilada' ? 'selected' : '' ?>>Alquilada</option>
                        <option value="Mantenimiento" <?= $maquinaria['estado_maquinaria'] == 'Mantenimiento' ? 'selected' : '' ?>>Mantenimiento</option>

                    </select>
                </div>
                <div class="form-group">
                    <label>Precio Diario:</label>
                    <input type="text" name="precio_diario" id="precio_diario" value="<?= htmlspecialchars($maquinaria['precio_diario']) ?>" required>
                    <div id="precio_error" class="error-message" style="display:none;color:red;font-size:0.9em;margin-top:5px;"></div>
                </div>

                <div class="form-group">
                    <button type="submit">Actualizar Maquinaria</button>

                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
