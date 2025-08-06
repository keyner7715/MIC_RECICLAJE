<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para listar proveedores (acceso a la página)
verificarPermiso('listar');

try {
    $sql = "SELECT * FROM proveedores ORDER BY id_proveedor DESC";
    $stmt = $pdo->query($sql);
    $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    $proveedores = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Listar Proveedores</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    
    <div class="container">

        <h2>Lista de Proveedores</h2>

        <div class="actions">
            <?php if (tienePermiso('crear')): ?>
                <a href="C_proveedores.php" class="btn-primary">Nuevo Proveedor</a>
            <?php endif; ?>
            <a href="../public/menu.php" class="btn-primary">Inicio</a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Proveedor</th>
                    <th>Tipo Proveedor</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($proveedores)): ?>
                    <tr>
                        <td colspan="7">No hay proveedores registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($proveedores as $proveedor): ?>
                        <tr>
                            <td><?= htmlspecialchars($proveedor['id_proveedor']) ?></td>
                            <td><?= htmlspecialchars($proveedor['nombre_proveedor'] ?? 'No especificado') ?></td>
                            <td><?= htmlspecialchars($proveedor['tipo_proveedor'] ?? 'No especificado') ?></td>
                            <td><?= htmlspecialchars($proveedor['direccion'] ?? 'No especificada') ?></td>
                            <td><?= htmlspecialchars($proveedor['telefono'] ?? 'No especificado') ?></td>
                            <td><?= htmlspecialchars($proveedor['correo'] ?? 'No especificado') ?></td>
                            <td>
                                <?php if (tienePermiso('editar')): ?>
                                    <a href="U_proveedores.php?id=<?= $proveedor['id_proveedor'] ?>" class="btn-edit">Editar</a>
                                <?php endif; ?>
                                <br></br>
                                
                                <?php if (tienePermiso('eliminar')): ?>
                                    <a href="D_proveedores.php?id=<?= $proveedor['id_proveedor'] ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de eliminar este proveedor?')">Eliminar</a>
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
