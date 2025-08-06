<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para eliminar
verificarPermiso('eliminar');

$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        // Verificar si el material existe
        $sql_check = "SELECT id_material FROM materiales WHERE id_material = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$id]);

        if ($stmt_check->fetch()) {
            // Eliminar el material
            $sql = "DELETE FROM materiales WHERE id_material = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            echo "<script>alert('Material eliminado exitosamente'); window.location.href='R_material.php';</script>";
        } else {
            echo "<script>alert('Material no encontrado'); window.location.href='R_material.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error al eliminar: " . $e->getMessage() . "'); window.location.href='R_material.php';</script>";
    }
} else {
    echo "<script>alert('ID de material no válido'); window.location.href='R_material.php';</script>";
}
?>
