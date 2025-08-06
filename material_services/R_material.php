<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para listar materiales
verificarPermiso('listar');

try {
    // Consulta para obtener materiales con tipo de material
    $sql = "SELECT m.id_material, m.nombre_material, m.descripcion, m.id_tipo_material, tm.nombre_tipo
            FROM materiales m
            LEFT JOIN tipos_material tm ON m.id_tipo_material = tm.id_tipo_material
            ORDER BY m.id_material DESC";
    $stmt = $pdo->query($sql);
    $materiales = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $materiales = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista de Materiales</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <div class="container">
        <h2>Lista de Materiales</h2>
        
        <div class="actions">
            <?php if (tienePermiso('crear')): ?>
                <a href="C_material.php" class="btn-primary">Nuevo Material</a>
            <?php endif; ?>
            <a href="../public/menu.php" class="btn-primary">Inicio</a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Material</th>
                    <th>Descripción</th>
                    <th>Tipo de Material</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($materiales)): ?>
                    <tr>
                        <td colspan="5">No hay materiales registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($materiales as $material): ?>
                        <tr>
                            <td><?= htmlspecialchars($material['id_material']) ?></td>
                            <td><?= htmlspecialchars($material['nombre_material']) ?></td>
                            <td><?= htmlspecialchars($material['descripcion'] ?? 'Sin descripción') ?></td>
                            <td><?= htmlspecialchars($material['nombre_tipo'] ?? 'Sin tipo asignado') ?></td>
                            <td>
                                <?php if (tienePermiso('editar')): ?>
                                    <a href="U_material.php?id=<?= $material['id_material'] ?>" class="btn-edit">Editar</a>
                                <?php endif; ?>
                                <br></br>
                                
                                <?php if (tienePermiso('eliminar')): ?>
                                    <a href="D_material.php?id=<?= $material['id_material'] ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de eliminar este material?')">Eliminar</a>
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
