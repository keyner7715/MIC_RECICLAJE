<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para listar
verificarPermiso('listar');

try {
    $sql = "SELECT 
                m.id_mantenimiento,
                maq.nombre_maquinaria,
                m.fecha,
                m.descripcion,
                m.costo_mantenimiento,
                t.nombre_tecnico
            FROM mantenimiento m
            INNER JOIN maquinarias maq ON m.id_maquinaria = maq.id_maquinaria
            LEFT JOIN tecnicos t ON m.id_tecnico = t.id_tecnico
            ORDER BY m.fecha DESC, maq.nombre_maquinaria";
    $stmt = $pdo->query($sql);
    $mantenimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    $mantenimientos = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mantenimiento de Maquinaria</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <div class="container">
        <h2>Mantenimiento de Maquinaria</h2>
        <div class="actions">
            <?php if (tienePermiso('crear')): ?>
                <a href="C_mantenimiento.php" class="btn-primary">Nuevo Mantenimiento</a>
            <?php endif; ?>
            <a href="../public/menu.php" class="btn-primary">Inicio</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID Mantenimiento</th>
                    <th>Nombre Maquinaria</th>
                    <th>Fecha</th>
                    <th>Descripcion</th>
                    <th>Costo</th>
                    <th>Nombre Tecnico</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($mantenimientos)): ?>
                    <tr>
                        <td colspan="7">No hay mantenimientos registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($mantenimientos as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['id_mantenimiento']) ?></td>
                            <td><?= htmlspecialchars($m['nombre_maquinaria']) ?></td>
                            <td><?= htmlspecialchars($m['fecha']) ?></td>
                            <td><?= htmlspecialchars($m['descripcion']) ?></td>
                            <td><?= htmlspecialchars($m['costo_mantenimiento']) ?></td>
                            <td><?= htmlspecialchars($m['nombre_tecnico']) ?></td>
                            <td>
                                <?php if (tienePermiso('editar')): ?>
                                    <a href="U_mantenimiento.php?id=<?= urlencode($m['id_mantenimiento']) ?>" class="btn-edit">Editar</a>
                                <?php endif; ?>
                                <br></br>
                                <?php if (tienePermiso('eliminar')): ?>
                                    <a href="D_mantenimiento.php?id=<?= urlencode($m['id_mantenimiento']) ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de eliminar este mantenimiento?')">Eliminar</a>
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
