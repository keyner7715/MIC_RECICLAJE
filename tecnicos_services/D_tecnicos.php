<?php
require_once '../config/db.php';

$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        // Verificar si el tecnico existe
        $sql_check = "SELECT id_tecnico FROM tecnicos WHERE id_tecnico = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$id]);
        
        if ($stmt_check->fetch()) {
            // Eliminar el tecnico
            $sql = "DELETE FROM tecnicos WHERE id_tecnico = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            echo "<script>alert('Técnico eliminado exitosamente'); window.location.href='R_tecnicos.php';</script>";
        } else {
            echo "<script>alert('Técnico no encontrado'); window.location.href='R_tecnicos.php';</script>";
        }
    } catch(PDOException $e) {
        echo "<script>alert('Error al eliminar: " . $e->getMessage() . "'); window.location.href='R_tecnicos.php';</script>";
    }
} else {
    echo "<script>alert('ID de técnico no válido'); window.location.href='R_tecnicos.php';</script>";
}
?>
