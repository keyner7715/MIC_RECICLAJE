<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para eliminar tipos de material
verificarPermiso('eliminar');

$id = $_GET['id'] ?? 0;
$mensaje = '';
$error = '';
$tipo_material = null;
$confirmacion = $_GET['confirmar'] ?? '';

if ($id) {
    try {
        // Verificar si el tipo de material existe
        $sql_check = "SELECT * FROM tipos_material WHERE id_tipo_material = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$id]);
        $tipo_material = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        if (!$tipo_material) {
            echo "<script>alert('Tipo de material no encontrado'); window.location.href='R_tipo_material.php';</script>";
            exit;
        }
        
        // Si se confirma la eliminación
        if ($confirmacion === 'si') {
            // Verificar integridad referencial - verificar si está siendo usado en otras tablas
            $tablas_relacionadas = [];
            
            // Verificar en tabla materiales (si existe)
            try {
                $sql_materiales = "SELECT COUNT(*) FROM materiales WHERE id_tipo_material = ?";
                $stmt_materiales = $pdo->prepare($sql_materiales);
                $stmt_materiales->execute([$id]);
                $count_materiales = $stmt_materiales->fetchColumn();
                
                if ($count_materiales > 0) {
                    $tablas_relacionadas[] = "materiales ($count_materiales registros)";
                }
            } catch(PDOException $e) {
                // La tabla materiales podría no existir aún, continuar
            }
            
            // Verificar en tabla recolecciones (si existe relación)
            try {
                $sql_recolecciones = "SELECT COUNT(*) FROM recolecciones WHERE id_tipo_material = ?";
                $stmt_recolecciones = $pdo->prepare($sql_recolecciones);
                $stmt_recolecciones->execute([$id]);
                $count_recolecciones = $stmt_recolecciones->fetchColumn();
                
                if ($count_recolecciones > 0) {
                    $tablas_relacionadas[] = "recolecciones ($count_recolecciones registros)";
                }
            } catch(PDOException $e) {
                // La tabla podría no tener esta relación, continuar
            }
            
            // Si hay registros relacionados, no permitir eliminar
            if (!empty($tablas_relacionadas)) {
                $error = "No se puede eliminar este tipo de material porque está siendo usado en las siguientes tablas: " . implode(', ', $tablas_relacionadas) . ". Elimine primero los registros relacionados.";
            } else {
                // Proceder con la eliminación
                try {
                    $sql_delete = "DELETE FROM tipos_material WHERE id_tipo_material = ?";
                    $stmt_delete = $pdo->prepare($sql_delete);
                    
                    if ($stmt_delete->execute([$id])) {
                        echo "<script>alert('Tipo de material eliminado exitosamente'); window.location.href='R_tipo_material.php';</script>";
                        exit;
                    } else {
                        $error = "Error al eliminar el tipo de material.";
                    }
                } catch(PDOException $e) {
                    $error = "Error de base de datos al eliminar: " . $e->getMessage();
                }
            }
        }
        
    } catch(PDOException $e) {
        $error = "Error al verificar el tipo de material: " . $e->getMessage();
    }
} else {
    echo "<script>alert('ID no válido'); window.location.href='R_tipo_material.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Eliminar Tipo de Material</title>
    <link rel="stylesheet" href="../public/style.css">
    <link rel="stylesheet" href="../public/forms.css">
    <style>
        .confirmacion-container {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .tipo-info {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
            display: inline-block;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .warning-icon {
            font-size: 48px;
            color: #ff6b35;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Eliminar Tipo de Material</h2>
        
        <div class="actions">
            <a href="R_tipo_material.php" class="btn-primary">Ver Tipos de Material</a>
            <a href="../public/menu.php" class="btn-primary">Inicio</a>
        </div>
        
        <?php if ($error): ?>
            <div class="mensaje-error">
                <strong>Error:</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($tipo_material && empty($error)): ?>
            <div class="confirmacion-container">
                <div class="warning-icon">⚠️</div>
                <h3>¿Estás seguro de que deseas eliminar este tipo de material?</h3>
                
                <div class="tipo-info">
                    <strong>ID:</strong> <?= htmlspecialchars($tipo_material['id_tipo_material']) ?><br>
                    <strong>Nombre del Tipo:</strong> <?= htmlspecialchars($tipo_material['nombre_tipo']) ?>
                </div>
                
                <div class="alert alert-warning">
                    <strong>⚠️ Advertencia:</strong> Esta acción no se puede deshacer. 
                    Se verificará que no esté siendo usado en otras tablas antes de eliminarlo.
                </div>
                
                <div style="margin-top: 20px;">
                    <a href="D_tipo_material.php?id=<?= $id ?>&confirmar=si" 
                       class="btn-danger"
                       onclick="return confirm('¿CONFIRMAS que quieres ELIMINAR definitivamente este tipo de material?')">
                        🗑️ Sí, Eliminar Definitivamente
                    </a>
                    
                    <a href="R_tipo_material.php" class="btn-secondary">
                        ❌ Cancelar
                    </a>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!$tipo_material && empty($error)): ?>
            <div class="mensaje-error">
                Tipo de material no encontrado.
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Prevenir navegación accidental
        window.addEventListener('beforeunload', function (e) {
            // Solo mostrar advertencia si estamos en proceso de eliminación
            const url = window.location.href;
            if (url.includes('confirmar=si')) {
                e.preventDefault();
                e.returnValue = '';
                return '';
            }
        });
    </script>
</body>
</html>
