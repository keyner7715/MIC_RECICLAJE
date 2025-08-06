<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para listar centros de acopio
verificarPermiso('listar');

try {
    // Consulta para obtener los centros de acopio con información del responsable
    $sql = "SELECT c.id_centro, c.nombre_centro, c.direccion, c.id_responsable, 
                   e.nombre_empleado as nombre_responsable, e.cargo
            FROM centros_acopio c
            LEFT JOIN empleados e ON c.id_responsable = e.id_empleado
            ORDER BY c.id_centro DESC";
    $stmt = $pdo->query($sql);
    $centros = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $centros = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Centros de Acopio</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <div class="container">
        <h2>Centros de Acopio</h2>
        
        <div class="actions">
            <?php if (tienePermiso('crear')): ?>
                <a href="C_gestion_acopio.php" class="btn-primary">Nuevo Centro</a>
            <?php endif; ?>
            <a href="../public/menu.php" class="btn-primary">Inicio</a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Centro</th>
                    <th>Dirección</th>
                    <th>Responsable</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($centros)): ?>
                    <tr>
                        <td colspan="5">No hay centros de acopio registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($centros as $centro): ?>
                        <tr>
                            <td><?= htmlspecialchars($centro['id_centro']) ?></td>
                            <td><?= htmlspecialchars($centro['nombre_centro']) ?></td>
                            <td><?= htmlspecialchars($centro['direccion'] ?? 'Sin dirección') ?></td>
                            <td><?= htmlspecialchars($centro['nombre_responsable'] ?? 'Sin responsable asignado') ?></td>
                            <td>
                                <?php if (tienePermiso('editar')): ?>
                                    <a href="U_gestion_acopio.php?id=<?= $centro['id_centro'] ?>" class="btn-edit">Editar</a>
                                <?php endif; ?>
                                <br></br>
                                
                                <?php if (tienePermiso('eliminar')): ?>
                                    <a href="D_gestion_acopio.php?id=<?= $centro['id_centro'] ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de eliminar este centro de acopio?')">Eliminar</a>
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
