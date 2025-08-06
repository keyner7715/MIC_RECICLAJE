<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verifica si el rol puede entrar a esta página
verificarPermiso('listar');

try {
    $sql = "SELECT * FROM usuario ORDER BY id_usuario DESC";
    $stmt = $pdo->query($sql);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    $usuarios = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista de Usuarios</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <div class="container">
        <h2>Lista de Usuarios</h2>

        <div class="actions">
            <?php if (tienePermiso('crear')): ?>
                <a href="crear_usuario.php" class="btn-primary">Nuevo usuario</a>
            <?php endif; ?>
            <a href="../public/menu.php" class="btn-primary">Inicio</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre usuario</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="4">No hay usuarios registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['id_usuario']) ?></td>
                            <td><?= htmlspecialchars($usuario['nombre_usuario']) ?></td>
                            <td><?= htmlspecialchars($usuario['rol']) ?></td>
                            <td><?= htmlspecialchars($usuario['estado']) ?></td>
                            <td>
                                <?php if (tienePermiso('editar')): ?>
                                    <a href="U_usuario.php?id=<?= $usuario['id_usuario'] ?>" class="btn-edit">Editar</a>
                                <?php endif; ?>
                                <?php if (tienePermiso('eliminar')): ?>
                                    <a href="delete_usuario.php?id=<?= $usuario['id_usuario'] ?>"
                                       class="btn-delete"
                                       onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</a>
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
