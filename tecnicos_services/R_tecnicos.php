<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para listar tecnicos (acceso a la página)
verificarPermiso('listar');

try {
    $sql = "SELECT * FROM tecnicos ORDER BY id_tecnico DESC";
    $stmt = $pdo->query($sql);
    $tecnicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    $tecnicos = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Listar Técnicos</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    
    <div class="container">

        <h2>Lista de Técnicos</h2>

        <div class="actions">
            <?php if (tienePermiso('crear')): ?>
                <a href="C_tecnicos.php" class="btn-primary">Nuevo técnico</a>
            <?php endif; ?>
            <a href="../public/menu.php" class="btn-primary">Inicio</a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Especilidad</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tecnicos)): ?>
                    <tr>
                        <td colspan="6">No hay técnicos registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tecnicos as $tecnico): ?>
                        <tr>
                            <td><?= $tecnico['id_tecnico'] ?></td>
                            <td><?= htmlspecialchars($tecnico['nombre_tecnico']) ?></td>
                            <td><?= htmlspecialchars($tecnico['especialidad']) ?></td>
                            <td><?= htmlspecialchars($tecnico['telefono']) ?></td>
                            <td><?= htmlspecialchars($tecnico['correo']) ?></td>
                            <td>
                                <?php if (tienePermiso('editar')): ?>
                                    <a href="U_tecnicos.php?id=<?= $tecnico['id_tecnico'] ?>" class="btn-edit">Editar</a>
                                <?php endif; ?>
                                
                                <?php if (tienePermiso('eliminar')): ?>
                                    <a href="D_tecnicos.php?id=<?= $tecnico['id_tecnico'] ?>" class="btn-delete" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
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
