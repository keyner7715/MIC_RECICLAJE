<?php
require_once '../config/db.php';

$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        // Obtener el detalle para mostrar información antes de eliminar
        $sql = "SELECT d.*, o.id_orden, c.nombre_cliente, m.nombre_maquinaria FROM detalle_alquiler d "
             . "JOIN ordenes_alquiler o ON d.id_orden = o.id_orden "
             . "JOIN clientes c ON o.id_cliente = c.id_cliente "
             . "JOIN maquinarias m ON d.id_maquinaria = m.id_maquinaria "
             . "WHERE d.id_detalle = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $detalle = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$detalle) {
            echo "<script>alert('Detalle no encontrado'); window.location.href='R_detalle_alquiler.php';</script>";
            exit;
        }

        // Si se confirma la eliminación
        if (isset($_POST['confirmar'])) {
            $sql = "DELETE FROM detalle_alquiler WHERE id_detalle = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            echo "<script>alert('Detalle de alquiler eliminado exitosamente'); window.location.href='R_detalle_alquiler.php';</script>";
            exit;
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "<script>alert('ID no proporcionado'); window.location.href='R_detalle_alquiler.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Detalle de Alquiler</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
<div class="container">
    <h2>Eliminar Detalle de Alquiler</h2>
    <?php if (!empty($detalle)): ?>
        <p>¿Está seguro que desea eliminar el siguiente detalle?</p>
        <ul>
            <li><strong>Orden #:</strong> <?= htmlspecialchars($detalle['id_orden']) ?></li>
            <li><strong>Cliente:</strong> <?= htmlspecialchars($detalle['nombre_cliente']) ?></li>
            <li><strong>Maquinaria:</strong> <?= htmlspecialchars($detalle['nombre_maquinaria']) ?></li>
            <li><strong>Días de Alquiler:</strong> <?= htmlspecialchars($detalle['dias_alquiler']) ?></li>
            <li><strong>Subtotal:</strong> $<?= htmlspecialchars($detalle['subtotal']) ?></li>
        </ul>
        <form method="POST">
            <button type="submit" name="confirmar">Eliminar</button>
            <a href="R_detalle_alquiler.php" class="btn-volver">Cancelar</a>
        </form>
    <?php endif; ?>
</div>
</body>
</html>