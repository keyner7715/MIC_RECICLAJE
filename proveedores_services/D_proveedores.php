<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar que el usuario tiene permiso para eliminar
verificarPermiso('eliminar');

$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        // Verificar si el proveedor existe
        $sql_check = "SELECT id_proveedor, nombre_proveedor FROM proveedores WHERE id_proveedor = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$id]);
        $proveedor = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        if ($proveedor) {
            // Verificar si el proveedor tiene recolecciones asociadas
            $sql_recolecciones = "SELECT COUNT(*) as total FROM recoleccion_proveedor WHERE id_proveedor = ?";
            $stmt_recolecciones = $pdo->prepare($sql_recolecciones);
            $stmt_recolecciones->execute([$id]);
            $recolecciones_count = $stmt_recolecciones->fetch(PDO::FETCH_ASSOC)['total'];
            
            if ($recolecciones_count > 0) {
                $mensaje_error = "No se puede eliminar el proveedor '{$proveedor['nombre_proveedor']}' porque tiene {$recolecciones_count} recolecciones asociadas.\\n\\nPrimero debe eliminar o reasignar las recolecciones relacionadas.";
                echo "<script>alert('{$mensaje_error}'); window.location.href='R_proveedores.php';</script>";
            } else {
                // Eliminar el proveedor
                $sql = "DELETE FROM proveedores WHERE id_proveedor = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id]);

                echo "<script>alert('Proveedor \"{$proveedor['nombre_proveedor']}\" eliminado exitosamente'); window.location.href='R_proveedores.php';</script>";
            }
        } else {
            echo "<script>alert('Proveedor no encontrado'); window.location.href='R_proveedores.php';</script>";
        }
    } catch(PDOException $e) {
        echo "<script>alert('Error al eliminar: " . $e->getMessage() . "'); window.location.href='R_proveedores.php';</script>";
    }
} else {
    echo "<script>alert('ID de proveedor no válido'); window.location.href='R_proveedores.php';</script>";
}
?>
