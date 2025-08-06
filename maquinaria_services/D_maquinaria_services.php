<?php
require_once '../config/db.php';

$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        // Verificar si la maquinaria existe
        $sql_check = "SELECT id_maquinaria FROM maquinarias WHERE id_maquinaria = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$id]);
        
        if ($stmt_check->fetch()) {
            // Eliminar la maquinaria
            $sql = "DELETE FROM maquinarias WHERE id_maquinaria = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            echo "<script>alert('Maquinaria eliminada exitosamente'); window.location.href='R_maquinaria_services.php';</script>";
        } else {
            echo "<script>alert('Maquinaria no encontrada'); window.location.href='R_maquinaria_services.php';</script>";
        }
    } catch(PDOException $e) {
        echo "<script>alert('Error al eliminar: " . $e->getMessage() . "'); window.location.href='R_maquinaria_services.php';</script>";
    }
} else {
    echo "<script>alert('ID de maquinaria no válido'); window.location.href='R_maquinaria_services.php';</script>";
}
?>
