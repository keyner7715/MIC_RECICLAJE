<?php
require_once '../config/db.php';

$id_tecnico = $_GET['id_tecnico'] ?? '';
$id_maquinaria = $_GET['id_maquinaria'] ?? '';
$fecha_asignacion = $_GET['fecha_asignacion'] ?? '';

if ($id_tecnico && $id_maquinaria && $fecha_asignacion) {
    try {
        // Verificar si la asignación existe
        $sql_check = "SELECT * FROM tecnico_maquinaria WHERE id_tecnico = ? AND id_maquinaria = ? AND fecha_asignacion = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$id_tecnico, $id_maquinaria, $fecha_asignacion]);

        if ($stmt_check->fetch()) {
            // Eliminar la asignación
            $sql_delete = "DELETE FROM tecnico_maquinaria WHERE id_tecnico = ? AND id_maquinaria = ? AND fecha_asignacion = ?";
            $stmt_delete = $pdo->prepare($sql_delete);
            $stmt_delete->execute([$id_tecnico, $id_maquinaria, $fecha_asignacion]);

            echo "<script>alert('Asignación de técnico a maquinaria eliminada exitosamente'); window.location.href='R_tecnicos_maquinarias.php';</script>";
        } else {
            echo "<script>alert('Asignación de técnico a maquinaria no encontrada'); window.location.href='R_tecnicos_maquinarias.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error al eliminar la asignación de técnico a maquinaria: " . $e->getMessage() . "'); window.location.href='R_tecnicos_maquinarias.php';</script>";
    }
} else {
    echo "<script>alert('Datos de asignación no válidos'); window.location.href='R_tecnicos_maquinarias.php';</script>";
}
?>
