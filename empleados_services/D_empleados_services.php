<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar que el usuario tiene permiso para eliminar
verificarPermiso('eliminar');

$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        // Verificar si el empleado existe
        $sql_check = "SELECT id_empleado, nombre_empleado FROM empleados WHERE id_empleado = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$id]);
        $empleado = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        if ($empleado) {
            // Verificar si el empleado tiene recolecciones asociadas
            $sql_recolecciones = "SELECT COUNT(*) as total FROM recolecciones WHERE id_empleado = ?";
            $stmt_recolecciones = $pdo->prepare($sql_recolecciones);
            $stmt_recolecciones->execute([$id]);
            $recolecciones_count = $stmt_recolecciones->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Verificar si el empleado tiene recolecciones de proveedores asociadas
            $sql_recolecciones_prov = "SELECT COUNT(*) as total FROM recoleccion_proveedor WHERE id_empleado = ?";
            $stmt_recolecciones_prov = $pdo->prepare($sql_recolecciones_prov);
            $stmt_recolecciones_prov->execute([$id]);
            $recolecciones_prov_count = $stmt_recolecciones_prov->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Verificar si el empleado es responsable de algún centro de acopio
            $sql_centros = "SELECT COUNT(*) as total FROM centros_acopio WHERE id_responsable = ?";
            $stmt_centros = $pdo->prepare($sql_centros);
            $stmt_centros->execute([$id]);
            $centros_count = $stmt_centros->fetch(PDO::FETCH_ASSOC)['total'];
            
            if ($recolecciones_count > 0 || $recolecciones_prov_count > 0 || $centros_count > 0) {
                $mensaje_error = "No se puede eliminar el empleado '{$empleado['nombre_empleado']}' porque tiene registros asociados:\\n";
                if ($recolecciones_count > 0) {
                    $mensaje_error .= "- {$recolecciones_count} recolecciones de clientes\\n";
                }
                if ($recolecciones_prov_count > 0) {
                    $mensaje_error .= "- {$recolecciones_prov_count} recolecciones de proveedores\\n";
                }
                if ($centros_count > 0) {
                    $mensaje_error .= "- Es responsable de {$centros_count} centro(s) de acopio\\n";
                }
                echo "<script>alert('{$mensaje_error}'); window.location.href='R_empleados_services.php';</script>";
            } else {
                // Eliminar el empleado
                $sql = "DELETE FROM empleados WHERE id_empleado = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id]);

                echo "<script>alert('Empleado \"{$empleado['nombre_empleado']}\" eliminado exitosamente'); window.location.href='R_empleados_services.php';</script>";
            }
        } else {
            echo "<script>alert('Empleado no encontrado'); window.location.href='R_empleados_services.php';</script>";
        }
    } catch(PDOException $e) {
        echo "<script>alert('Error al eliminar: " . $e->getMessage() . "'); window.location.href='R_empleados_services.php';</script>";
    }
} else {
    echo "<script>alert('ID de empleado no válido'); window.location.href='R_empleados_services.php';</script>";
}
?>
