<?php
require_once '../config/db.php';

$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        // Obtener la orden para mostrar información antes de eliminar
        $sql = "SELECT o.*, c.nombre_cliente FROM ordenes_alquiler o JOIN clientes c ON o.id_cliente = c.id_cliente WHERE o.id_orden = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $orden = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$orden) {
            echo "<script>alert('Orden no encontrada'); window.location.href='R_ordenes_trabajo.php';</script>";
            exit;
        }

        // Si se confirma la eliminación
        if (isset($_POST['confirmar'])) {
            // Verificar si existen detalles asociados
            $sql_detalle = "SELECT COUNT(*) FROM detalle_alquiler WHERE id_orden = ?";
            $stmt_detalle = $pdo->prepare($sql_detalle);
            $stmt_detalle->execute([$id]);
            $tiene_detalles = $stmt_detalle->fetchColumn();

            if ($tiene_detalles > 0) {
                echo "<script>alert('No se puede eliminar la orden porque tiene detalles de alquiler asociados. Elimine primero los detalles.'); window.location.href='R_ordenes_trabajo.php';</script>";
                exit;
            }

            $sql = "DELETE FROM ordenes_alquiler WHERE id_orden = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            echo "<script>alert('Orden eliminada exitosamente'); window.location.href='R_ordenes_trabajo.php';</script>";
            exit;
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "<script>alert('ID no proporcionado'); window.location.href='R_ordenes_trabajo.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Orden de Alquiler</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
<div class="container">
    <h2>Eliminar Orden de Alquiler</h2>
    <?php if (!empty($orden)): ?>
        <p>¿Está seguro que desea eliminar la siguiente orden?</p>
        <ul>
            <li><strong>Cliente:</strong> <?= htmlspecialchars($orden['nombre_cliente']) ?></li>
            <li><strong>Fecha de Inicio:</strong> <?= htmlspecialchars($orden['fecha_inicio']) ?></li>
            <li><strong>Fecha de Fin:</strong> <?= htmlspecialchars($orden['fecha_fin']) ?></li>
            <li><strong>Total Días:</strong> <?= htmlspecialchars($orden['total_dias']) ?></li>
            <li><strong>Estado:</strong> <?= htmlspecialchars($orden['estado_orden']) ?></li>
        </ul>
        <form method="POST">
            <button type="submit" name="confirmar">Eliminar</button>
            <a href="R_ordenes_trabajo.php" class="btn-volver">Cancelar</a>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
