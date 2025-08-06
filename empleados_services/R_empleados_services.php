<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para listar empleados (acceso a la página)
verificarPermiso('listar');

try {
    $sql = "SELECT * FROM empleados ORDER BY id_empleado DESC";
    $stmt = $pdo->query($sql);
    $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    $empleados = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Listar Empleados</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    
    <div class="container">

        <h2>Lista de Empleados</h2>

        <div class="actions">
            <?php if (tienePermiso('crear')): ?>
                <a href="C_empleados_services.php" class="btn-primary">Nuevo Empleado</a>
            <?php endif; ?>
            <a href="../public/menu.php" class="btn-primary">Inicio</a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Empleado</th>
                    <th>Cargo</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($empleados)): ?>
                    <tr>
                        <td colspan="6">No hay empleados registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($empleados as $empleado): ?>
                        <tr>
                            <td><?= htmlspecialchars($empleado['id_empleado']) ?></td>
                            <td><?= htmlspecialchars($empleado['nombre_empleado']) ?></td>
                            <td><?= htmlspecialchars($empleado['cargo'] ?? 'No especificado') ?></td>
                            <td><?= htmlspecialchars($empleado['telefono'] ?? 'No especificado') ?></td>
                            <td><?= htmlspecialchars($empleado['correo'] ?? 'No especificado') ?></td>
                            <td>
                                <?php if (tienePermiso('editar')): ?>
                                    <a href="U_empleados_services.php?id=<?= $empleado['id_empleado'] ?>" class="btn-edit">Editar</a>
                                <?php endif; ?>
                                <br></br>
                                
                                <?php if (tienePermiso('eliminar')): ?>
                                    <a href="D_empleados_services.php?id=<?= $empleado['id_empleado'] ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de eliminar este empleado?')">Eliminar</a>
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
