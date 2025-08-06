<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para eliminar
verificarPermiso('eliminar');

$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        // Verificar si el cliente existe
        $sql_check = "SELECT id_cliente FROM clientes WHERE id_cliente = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$id]);

        if ($stmt_check->fetch()) {
            // Eliminar el cliente
            $sql = "DELETE FROM clientes WHERE id_cliente = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            echo "<script>alert('Cliente eliminado exitosamente'); window.location.href='R_clientes.php';</script>";
        } else {
            echo "<script>alert('Cliente no encontrado'); window.location.href='R_clientes.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error al eliminar: " . $e->getMessage() . "'); window.location.href='R_clientes.php';</script>";
    }
} else {
    echo "<script>alert('ID de cliente no válido'); window.location.href='R_clientes.php';</script>";
}
?>
