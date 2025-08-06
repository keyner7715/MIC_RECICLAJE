<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para listar disponibilidad
verificarPermiso('listar');

try {
    // Consulta para obtener la disponibilidad de maquinarias con nombre
    $sql = "SELECT d.id_disponibilidad, d.fecha, d.disponible, m.nombre_maquinaria
            FROM disponibilidad_maquinaria d
            INNER JOIN maquinarias m ON d.id_maquinaria = m.id_maquinaria
            ORDER BY d.fecha DESC, m.nombre_maquinaria ASC";
    $stmt = $pdo->query($sql);
    $disponibilidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $disponibilidades = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Disponibilidad de Maquinarias</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <div class="container">
        <h2>Disponibilidad de Maquinarias</h2>
        <div class="actions">
            <?php if (tienePermiso('crear')): ?>
                <a href="C_disponibilidad.php" class="btn-primary">Nueva Disponibilidad</a>
            <?php endif; ?>
            <a href="../public/menu.php" class="btn-primary">Inicio</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de la Maquinaria</th>
                    <th>Fecha</th>
                    <th>Disponible</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($disponibilidades)): ?>
                    <tr>
                        <td colspan="5">No hay registros de disponibilidad</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($disponibilidades as $disp): ?>
                        <tr>
                            <td><?= $disp['id_disponibilidad'] ?></td>
                            <td><?= htmlspecialchars($disp['nombre_maquinaria']) ?></td>
                            <td><?= htmlspecialchars($disp['fecha']) ?></td>
                            <td><?= $disp['disponible'] ? 'Sí' : 'No' ?></td>
                            <td>
                                <?php if (tienePermiso('editar')): ?>
                                    <a href="U_disponibilidad.php?id=<?= $disp['id_disponibilidad'] ?>" class="btn-edit">Editar</a>
                                <?php endif; ?>
                                <?php if (tienePermiso('eliminar')): ?>
                                    <a href="D_disponibilidad.php?id=<?= $disp['id_disponibilidad'] ?>" class="btn-delete" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
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
