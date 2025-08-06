<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para listar detalles de alquiler
verificarPermiso('listar');

try {
    // Consulta para obtener el detalle de alquiler con nombre de maquinaria, precio diario, estado de la orden y nombre del cliente
    $sql = "SELECT d.id_detalle, d.id_orden, m.nombre_maquinaria, m.precio_diario, o.estado_orden, d.dias_alquiler, d.subtotal, c.nombre_cliente
            FROM detalle_alquiler d
            INNER JOIN maquinarias m ON d.id_maquinaria = m.id_maquinaria
            INNER JOIN ordenes_alquiler o ON d.id_orden = o.id_orden
            INNER JOIN clientes c ON o.id_cliente = c.id_cliente
            ORDER BY d.id_detalle DESC";
    $stmt = $pdo->query($sql);
    $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $detalles = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detalle de Alquiler</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <div class="container">
        <h2>Detalle de Alquiler</h2>
        <div class="actions">
            <?php if (tienePermiso('crear')): ?>
                <a href="C_detalle_alquiler.php" class="btn-primary">Nuevo Detalle</a>
            <?php endif; ?>
            <a href="../public/menu.php" class="btn-primary">Inicio</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID_Detalle</th>
                    <th>ID_Orden</th>
                    <th>Cliente</th>
                    <th>Maquinaria</th>
                    <th>Precio Diario</th>
                    <th>Estado Orden</th>
                    <th>Días de Alquiler</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($detalles)): ?>
                    <tr>
                        <td colspan="8">No hay detalles registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($detalles as $detalle): ?>
                        <tr>
                            <td><?= $detalle['id_detalle'] ?></td>
                            <td><?= $detalle['id_orden'] ?></td>
                            <td><?= htmlspecialchars($detalle['nombre_cliente']) ?></td>
                            <td><?= htmlspecialchars($detalle['nombre_maquinaria']) ?></td>
                            <td><?= number_format($detalle['precio_diario'], 2) ?></td>
                            <td><?= htmlspecialchars($detalle['estado_orden']) ?></td>
                            <td><?= $detalle['dias_alquiler'] ?></td>
                            <td><?= number_format($detalle['subtotal'], 2) ?></td>
                            <td>
                                <?php if (tienePermiso('editar')): ?>
                                    <a href="U_detalle_alquiler.php?id=<?= $detalle['id_detalle'] ?>" class="btn-edit">Editar</a>
                                <?php endif; ?>
                                <br></br>
                                <?php if (tienePermiso('eliminar')): ?>
                                    <a href="D_detalle_alquiler.php?id=<?= $detalle['id_detalle'] ?>" class="btn-delete" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
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
