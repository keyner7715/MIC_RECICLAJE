<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para eliminar centros de acopio
verificarPermiso('eliminar');

$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        // Obtener el centro para mostrar información antes de eliminar
        $sql = "SELECT c.*, e.nombre_empleado, e.cargo FROM centros_acopio c LEFT JOIN empleados e ON c.id_responsable = e.id_empleado WHERE c.id_centro = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $centro = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$centro) {
            echo "<script>alert('Centro de acopio no encontrado'); window.location.href='R_gestion_acopio.php';</script>";
            exit;
        }

        // Si se confirma la eliminación
        if (isset($_POST['confirmar'])) {
            // Verificar si existen registros asociados (puedes agregar más verificaciones según tu sistema)
            // Por ejemplo, si tienes tablas que referencien a centros_acopio
            /*
            $sql_referencia = "SELECT COUNT(*) FROM otra_tabla WHERE id_centro = ?";
            $stmt_referencia = $pdo->prepare($sql_referencia);
            $stmt_referencia->execute([$id]);
            $tiene_referencias = $stmt_referencia->fetchColumn();

            if ($tiene_referencias > 0) {
                echo "<script>alert('No se puede eliminar el centro porque tiene registros asociados. Elimine primero las referencias.'); window.location.href='R_gestion_acopio.php';</script>";
                exit;
            }
            */

            $sql = "DELETE FROM centros_acopio WHERE id_centro = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            echo "<script>alert('Centro de acopio eliminado exitosamente'); window.location.href='R_gestion_acopio.php';</script>";
            exit;
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "<script>alert('ID no proporcionado'); window.location.href='R_gestion_acopio.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Centro de Acopio</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
<div class="container">
    <h2>Eliminar Centro de Acopio</h2>
    <?php if (!empty($centro)): ?>
        <p>¿Está seguro que desea eliminar el siguiente centro de acopio?</p>
        <ul>
            <li><strong>Nombre:</strong> <?= htmlspecialchars($centro['nombre_centro']) ?></li>
            <li><strong>Dirección:</strong> <?= htmlspecialchars($centro['direccion'] ?? 'Sin dirección') ?></li>
            <li><strong>Responsable:</strong> <?= htmlspecialchars($centro['nombre_empleado'] ?? 'Sin responsable asignado') ?></li>
            <?php if ($centro['cargo']): ?>
                <li><strong>Cargo del Responsable:</strong> <?= htmlspecialchars($centro['cargo']) ?></li>
            <?php endif; ?>
        </ul>
        <form method="POST">
            <button type="submit" name="confirmar">Eliminar</button>
            <a href="R_gestion_acopio.php" class="btn-volver">Cancelar</a>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
