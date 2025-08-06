
<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para listar
verificarPermiso('listar');

try {
    // Consulta real de la relación técnico-maquinaria con fecha de asignación
    $sql = "SELECT tm.id_tecnico, t.nombre_tecnico, tm.id_maquinaria, m.nombre_maquinaria, tm.fecha_asignacion
            FROM tecnico_maquinaria tm
            INNER JOIN tecnicos t ON tm.id_tecnico = t.id_tecnico
            INNER JOIN maquinarias m ON tm.id_maquinaria = m.id_maquinaria
            ORDER BY tm.fecha_asignacion DESC";
    $stmt = $pdo->query($sql);
    $tecnicos_maquinarias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $tecnicos_maquinarias = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Técnicos y Maquinarias</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <div class="container">
        <h2>Relación Técnicos y Maquinarias</h2>
        <div class="actions">
            <?php if (tienePermiso('crear')): ?>
                <a href="C_tecnicos_maquinarias.php" class="btn-primary">Nuevo Técnico</a>
            <?php endif; ?>
            <a href="../public/menu.php" class="btn-primary">Inicio</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID Técnico</th>
                    <th>Nombre del Técnico</th>
                    <th>ID Maquinaria</th>
                    <th>Nombre de la Maquinaria</th>
                    <th>Fecha de Asignación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tecnicos_maquinarias)): ?>
                    <tr>
                        <td colspan="6">No hay asignaciones registradas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tecnicos_maquinarias as $tm): ?>
                        <tr>
                            <td><?= htmlspecialchars($tm['id_tecnico']) ?></td>
                            <td><?= htmlspecialchars($tm['nombre_tecnico']) ?></td>
                            <td><?= htmlspecialchars($tm['id_maquinaria']) ?></td>
                            <td><?= htmlspecialchars($tm['nombre_maquinaria']) ?></td>
                            <td><?= htmlspecialchars($tm['fecha_asignacion']) ?></td>
                            <td>
                                <?php if (tienePermiso('editar')): ?>
                                    <a href="U_tecnicos_maquinarias.php?id_tecnico=<?= urlencode($tm['id_tecnico']) ?>&id_maquinaria=<?= urlencode($tm['id_maquinaria']) ?>&fecha_asignacion=<?= urlencode($tm['fecha_asignacion']) ?>" class="btn-edit">Editar</a>
                                <?php endif; ?>
                                <br></br>
                                <?php if (tienePermiso('eliminar')): ?>
                                    <a href="D_tecnicos_maquinarias.php?id_tecnico=<?= urlencode($tm['id_tecnico']) ?>&id_maquinaria=<?= urlencode($tm['id_maquinaria']) ?>&fecha_asignacion=<?= urlencode($tm['fecha_asignacion']) ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de eliminar esta asignación?')">Eliminar</a>
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
