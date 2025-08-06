<?php
require_once '../config/db.php';

$id = $_GET['id'] ?? 0;
$tecnico = null;

// Obtener datos del tecnicos
if ($id) {
    try {
        $sql = "SELECT * FROM tecnicos WHERE id_tecnico = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $tecnico = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tecnico) {
            echo "<script>alert('Técnico no encontrado'); window.location.href='R_tecnicos.php';</script>";
            exit;
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_tecnico = $_POST['nombre_tecnico'] ?? '';
    $especialidad = $_POST['especialidad'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $correo = $_POST['correo'] ?? '';

    try {
        $sql = "UPDATE tecnicos SET nombre_tecnico = ?, especialidad = ?, telefono = ?, correo = ? WHERE id_tecnico = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre_tecnico, $especialidad, $telefono, $correo, $id]);

        echo "<script>alert('Técnico actualizado exitosamente'); window.location.href='R_tecnicos.php';</script>";
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Actualizar técnico</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <div class="container">
        <h2>Actualizar técnico</h2>

        <?php if ($tecnico): ?>
            <form method="POST">
                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="nombre_tecnico" value="<?= htmlspecialchars($tecnico['nombre_tecnico']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Especialidad:</label>
                    <input type="text" name="especialidad" value="<?= htmlspecialchars($tecnico['especialidad']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($tecnico['telefono']) ?>" required>
                    <div id="telefono_error" class="error-message" style="display:none;color:red;font-size:0.9em;margin-top:5px;"></div>
                </div>

                <div class="form-group">
                    <label>Correo Electrónico:</label>
                    <input type="email" name="correo" value="<?= htmlspecialchars($tecnico['correo']) ?>" required>
                </div>
                
                <div class="form-group">
                    <button type="submit">Actualizar técnico</button>

                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
<script src="../js/tecnicos_form.js"></script>
</html>
