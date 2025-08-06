<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar que el usuario tiene permiso para listar
verificarPermiso('listar');

try {
    $sql = "SELECT * FROM clientes ORDER BY id_cliente DESC";
    $stmt = $pdo->query($sql);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
    $clientes = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista de Clientes</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>

    <div class="container">
        <h2>Lista de Clientes</h2>

        <div class="actions">
            <?php if (tienePermiso('crear')): ?>
                <a href="C_clientes.php" class="btn-primary">Nuevo Cliente</a>
            <?php endif; ?>
            <a href="../public/menu.php" class="btn-primary">Inicio</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombres</th>
                    <th>RUC/Cédula</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clientes)): ?>
                    <tr>
                        <td colspan="5">No hay clientes registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><?= $cliente['id_cliente'] ?></td>
                            <td><?= htmlspecialchars($cliente['nombre_cliente']) ?></td>
                            <td><?= htmlspecialchars($cliente['ruc_cedula']) ?></td>
                            <td><?= htmlspecialchars($cliente['direccion']) ?></td>
                            <td><?= htmlspecialchars($cliente['telefono']) ?></td>
                            <td><?= htmlspecialchars($cliente['correo']) ?></td>
                            <td>
                                <?php if (tienePermiso('editar')): ?>
                                    <a href="U_clientes.php?id=<?= $cliente['id_cliente'] ?>" class="btn-edit">Editar</a>
                                <?php endif; ?>
                                <br></br>
                                
                                <?php if (tienePermiso('eliminar')): ?>
                                    <a href="D_clientes.php?id=<?= $cliente['id_cliente'] ?>" class="btn-delete" onclick="return confirm('¿Deseas eliminar este cliente?')">Eliminar</a>
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
