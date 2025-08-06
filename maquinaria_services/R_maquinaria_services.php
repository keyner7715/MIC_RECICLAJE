<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para listar maquinarias (acceso a la página)
verificarPermiso('listar');

try {
    $sql = "SELECT * FROM maquinarias ORDER BY id_maquinaria DESC";
    $stmt = $pdo->query($sql);
    $maquinarias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    $maquinarias = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Listar Maquinarias</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    
    <div class="container">

        <h2>Lista de Maquinarias</h2>

        <div class="actions">
            <?php if (tienePermiso('crear')): ?>
                <a href="C_maquinaria_services.php" class="btn-primary">Nueva Maquinaria</a>
            <?php endif; ?>
            <a href="../public/menu.php" class="btn-primary">Inicio</a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Nombre Maquinaria</th>
                    <th>Tipo</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Año</th>
                    <th>Estado</th>
                    <th>Precio Diario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($maquinarias)): ?>
                    <tr>
                        <td colspan="5">No hay maquinarias registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($maquinarias as $maquinaria): ?>
                        <tr>
                            <td><?= htmlspecialchars($maquinaria['id_maquinaria']) ?></td>
                            <td><?= htmlspecialchars($maquinaria['nombre_maquinaria']) ?></td>
                            <td><?= htmlspecialchars($maquinaria['tipo']) ?></td>
                            <td><?= htmlspecialchars($maquinaria['marca']) ?></td>
                            <td><?= htmlspecialchars($maquinaria['modelo']) ?></td>
                            <td><?= htmlspecialchars($maquinaria['año']) ?></td>
                            <td><?= htmlspecialchars($maquinaria['estado_maquinaria']) ?></td>
                            <td><?= htmlspecialchars($maquinaria['precio_diario']) ?></td>
                            <td>
                                <?php if (tienePermiso('editar')): ?>
                                    <a href="U_maquinaria_services.php?id=<?= $maquinaria['id_maquinaria'] ?>" class="btn-edit">Editar</a>
                                <?php endif; ?>
                                <br></br>
                                
                                <?php if (tienePermiso('eliminar')): ?>
                                    <a href="D_maquinaria_services.php?id=<?= $maquinaria['id_maquinaria'] ?>" class="btn-delete" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
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
