<?php
session_start();
require_once '../config/db.php';

// Solo Administrador puede eliminar
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
    die("No tienes permiso para eliminar usuarios.");
}

$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        $sql_check = "SELECT id_usuario FROM usuario WHERE id_usuario = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$id]);
        
        if ($stmt_check->fetch()) {
            $sql = "DELETE FROM usuario WHERE id_usuario = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            
            echo "<script>alert('Usuario eliminado exitosamente'); window.location.href='R_usuario.php';</script>";
        } else {
            echo "<script>alert('Usuario no encontrado'); window.location.href='R_usuario.php';</script>";
        }
    } catch(PDOException $e) {
        echo "<script>alert('Error al eliminar: " . addslashes($e->getMessage()) . "'); window.location.href='R_usuario.php';</script>";
    }
} else {
    echo "<script>alert('ID de usuario no válido'); window.location.href='R_usuario.php';</script>";
}
