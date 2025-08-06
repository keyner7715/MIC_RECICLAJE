<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para eliminar disponibilidad
verificarPermiso('eliminar');

$id = $_GET['id'] ?? '';

if ($id) {
    try {
        // Verificar si el registro existe
        $sql_check = "SELECT * FROM disponibilidad_maquinaria WHERE id_disponibilidad = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$id]);

        if ($stmt_check->fetch()) {
            // Eliminar el registro
            $sql_delete = "DELETE FROM disponibilidad_maquinaria WHERE id_disponibilidad = ?";
            $stmt_delete = $pdo->prepare($sql_delete);
            $stmt_delete->execute([$id]);
            echo "<script>alert('Registro eliminado exitosamente'); window.location.href='R_disponibilidad.php';</script>";
        } else {
            echo "<script>alert('Registro no encontrado'); window.location.href='R_disponibilidad.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error al eliminar: " . $e->getMessage() . "'); window.location.href='R_disponibilidad.php';</script>";
    }
} else {
    echo "<script>alert('ID no válido'); window.location.href='R_disponibilidad.php';</script>";
}
?>
