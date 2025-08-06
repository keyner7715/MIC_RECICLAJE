<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para listar órdenes
verificarPermiso('listar');

try {
    // Consulta para obtener las órdenes de alquiler con nombre del cliente
    $sql = "SELECT o.id_orden, o.fecha_inicio, o.fecha_fin, o.estado_orden, o.total_dias, c.nombre_cliente
            FROM ordenes_alquiler o
            INNER JOIN clientes c ON o.id_cliente = c.id_cliente
            ORDER BY o.id_orden DESC";
    $stmt = $pdo->query($sql);
    $ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $ordenes = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Órdenes de Alquiler</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <div class="container">
        <h2>Órdenes de Alquiler</h2>
        <div class="actions">
            <?php if (tienePermiso('crear')): ?>
                <a href="C_ordenes_trabajo.php" class="btn-primary">Nueva Orden</a>
            <?php endif; ?>
            <a href="../public/menu.php" class="btn-primary">Inicio</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Estado</th>
                    <th>Total Dias</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ordenes)): ?>
                    <tr>
                        <td colspan="7">No hay órdenes registradas</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($ordenes as $orden): ?>
                        <tr>
                            <td><?= $orden['id_orden'] ?></td>
                            <td><?= htmlspecialchars($orden['nombre_cliente']) ?></td>
                            <td><?= htmlspecialchars($orden['fecha_inicio']) ?></td>
                            <td><?= htmlspecialchars($orden['fecha_fin']) ?></td>
                            <td><?= htmlspecialchars($orden['estado_orden']) ?></td>
                            <td><?= number_format($orden['total_dias'],) ?></td>
                            <td>
                                <?php if (tienePermiso('editar')): ?>
                                    <a href="U_ordenes_trabajo.php?id=<?= $orden['id_orden'] ?>" class="btn-edit">Editar</a>
                                <?php endif; ?>
                                <?php if (tienePermiso('eliminar')): ?>
                                    <a href="D_ordenes_trabajo.php?id=<?= $orden['id_orden'] ?>" class="btn-delete" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
