
<?php
require_once '../config/db.php';

$id = $_GET['id'] ?? 0;
$orden = null;

// Obtener lista de clientes para el select
$clientes = $pdo->query("SELECT id_cliente, nombre_cliente FROM clientes")->fetchAll(PDO::FETCH_ASSOC);

// Obtener datos de la orden actual
if ($id) {
    try {
        $sql = "SELECT * FROM ordenes_alquiler WHERE id_orden = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $orden = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$orden) {
            echo "<script>alert('Orden no encontrada'); window.location.href='R_ordenes_trabajo.php';</script>";
            exit;
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_cliente = $_POST['id_cliente'] ?? '';
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';
    $estado_orden = $_POST['estado_orden'] ?? 'pendiente';
    $total_dias = $_POST['total_dias'] ?? 0;

    try {
        $sql = "UPDATE ordenes_alquiler SET id_cliente = ?, fecha_inicio = ?, fecha_fin = ?, estado_orden = ?, total_dias = ? WHERE id_orden = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_cliente, $fecha_inicio, $fecha_fin, $estado_orden, $total_dias, $id]);

        echo "<script>alert('Orden actualizada exitosamente'); window.location.href='R_ordenes_trabajo.php';</script>";
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Orden de Alquiler</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
<div class="container">
    <h2>Actualizar Orden de Alquiler</h2>

    <?php if ($orden): ?>
        <form method="POST">
            <div class="form-group">
                <label for="id_cliente">Cliente:</label>
                <select name="id_cliente" id="id_cliente" required>
                    <option value="">Seleccione un cliente</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?= $cliente['id_cliente'] ?>" <?= $orden['id_cliente'] == $cliente['id_cliente'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cliente['nombre_cliente']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha_inicio">Fecha de Inicio:</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" value="<?= htmlspecialchars($orden['fecha_inicio']) ?>" required>
            </div>
            <div class="form-group">
                <label for="fecha_fin">Fecha de Fin:</label>
                <input type="date" name="fecha_fin" id="fecha_fin" value="<?= htmlspecialchars($orden['fecha_fin']) ?>" required>
            </div>
            <div class="form-group">
                <label for="estado_orden">Estado de la Orden:</label>
                <select name="estado_orden" id="estado_orden">
                    <option value="pendiente" <?= $orden['estado_orden'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="activa" <?= $orden['estado_orden'] == 'activa' ? 'selected' : '' ?>>Activa</option>
                    <option value="finalizada" <?= $orden['estado_orden'] == 'finalizada' ? 'selected' : '' ?>>Finalizada</option>
                    <option value="cancelada" <?= $orden['estado_orden'] == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                </select>
            </div>
            <div class="form-group">
                <label for="total_dias">Total Días:</label>
                <input type="text" name="total_dias" id="total_dias" value="<?= (int)(strtotime($orden['fecha_fin']) - strtotime($orden['fecha_inicio'])) >= 0 ? ((strtotime($orden['fecha_fin']) - strtotime($orden['fecha_inicio'])) / (60*60*24) + 1) : 0 ?>" required readonly>
            </div>
            <div class="form-group">
                <button type="submit">Actualizar</button>
            </div>
        </form>
    <?php endif; ?>
    <a href="R_ordenes_trabajo.php" class="btn-volver">Volver</a>
</div>
<script>
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const totalDias = document.getElementById('total_dias');

    function calcularDias() {
        const inicio = new Date(fechaInicio.value);
        const fin = new Date(fechaFin.value);
        if (fechaInicio.value && fechaFin.value && fin >= inicio) {
            const diff = (fin - inicio) / (1000 * 60 * 60 * 24) + 1;
            totalDias.value = diff;
        } else {
            totalDias.value = 0;
        }
    }

    fechaInicio.addEventListener('change', calcularDias);
    fechaFin.addEventListener('change', calcularDias);
</script>
</body>
</html>



<script>
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const totalDias = document.getElementById('total_dias');

    function calcularDias() {
        const inicio = new Date(fechaInicio.value);
        const fin = new Date(fechaFin.value);
        if (fechaInicio.value && fechaFin.value && fin >= inicio) {
            const diff = (fin - inicio) / (1000 * 60 * 60 * 24) + 1;
            totalDias.value = diff;
        } else {
            totalDias.value = 0;
        }
    }

    fechaInicio.addEventListener('change', calcularDias);
    fechaFin.addEventListener('change', calcularDias);
</script>
</body>
</html>
