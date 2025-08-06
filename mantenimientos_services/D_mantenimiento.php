<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para eliminar
verificarPermiso('eliminar');

$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        // Verificar si el mantenimiento existe
        $sql_check = "SELECT * FROM mantenimiento WHERE id_mantenimiento = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$id]);
        if ($stmt_check->fetch()) {
            // Eliminar el mantenimiento
            $sql = "DELETE FROM mantenimiento WHERE id_mantenimiento = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            echo "<script>alert('Mantenimiento eliminado exitosamente'); window.location.href='R_mantenimiento.php';</script>";
        } else {
            echo "<script>alert('Mantenimiento no encontrado'); window.location.href='R_mantenimiento.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error al eliminar: " . $e->getMessage() . "'); window.location.href='R_mantenimiento.php';</script>";
    }
} else {
    echo "<script>alert('ID no válido'); window.location.href='R_mantenimiento.php';</script>";
}
?>
