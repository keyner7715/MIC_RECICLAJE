<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para listar tipos de material
verificarPermiso('listar');

try {
    $sql = "SELECT * FROM tipos_material ORDER BY id_tipo_material DESC";
    $stmt = $pdo->query($sql);
    $tipos_material = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    $tipos_material = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista de Tipos de Material</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <div class="container">
        <h2>Lista de Tipos de Material</h2>
        
        <div class="actions">
            <?php if (tienePermiso('crear')): ?>
                <a href="C_tipo_material.php" class="btn-primary">Nuevo Tipo de Material</a>
            <?php endif; ?>
            <a href="../public/menu.php" class="btn-primary">Inicio</a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Tipo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tipos_material)): ?>
                    <tr>
                        <td colspan="3">No hay tipos de material registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tipos_material as $tipo): ?>
                        <tr>
                            <td><?= htmlspecialchars($tipo['id_tipo_material']) ?></td>
                            <td><?= htmlspecialchars($tipo['nombre_tipo']) ?></td>
                            <td>
                                <?php if (tienePermiso('editar')): ?>
                                    <a href="U_tipo_material.php?id=<?= $tipo['id_tipo_material'] ?>" class="btn-edit">Editar</a>
                                <?php endif; ?>
                                <br></br>
                                
                                <?php if (tienePermiso('eliminar')): ?>
                                    <a href="D_tipo_material.php?id=<?= $tipo['id_tipo_material'] ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de eliminar este tipo de material?')">Eliminar</a>
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
