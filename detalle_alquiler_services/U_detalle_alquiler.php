<?php
require_once '../config/db.php';
require_once '../auth_services/permisos.php';

// Verificar permiso para actualizar detalle de alquiler
verificarPermiso('editar');

$id = $_GET['id'] ?? 0;
$detalle = null;

// Obtener órdenes y maquinarias para los select
try {
    $ordenes = $pdo->query("SELECT o.id_orden, c.nombre_cliente, DATEDIFF(o.fecha_fin, o.fecha_inicio) + 1 AS dias, o.fecha_inicio, o.fecha_fin FROM ordenes_alquiler o INNER JOIN clientes c ON o.id_cliente = c.id_cliente ORDER BY o.id_orden DESC")->fetchAll(PDO::FETCH_ASSOC);
    $maquinarias = $pdo->query("SELECT id_maquinaria, nombre_maquinaria, precio_diario FROM maquinarias ORDER BY nombre_maquinaria")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

// Obtener detalle actual
if ($id) {
    $sql = "SELECT * FROM detalle_alquiler WHERE id_detalle = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $detalle = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$detalle) {
        echo "<script>alert('Detalle no encontrado'); window.location.href='R_detalle_alquiler.php';</script>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_orden = $_POST['id_orden'] ?? '';
    $id_maquinaria = $_POST['id_maquinaria'] ?? '';
    $dias_alquiler = $_POST['dias_alquiler'] ?? '';
    $subtotal = $_POST['subtotal'] ?? '';

    // Obtener fechas de la orden seleccionada
    $orden = null;
    foreach ($ordenes as $o) {
        if ($o['id_orden'] == $id_orden) {
            $orden = $o;
            break;
        }
    }

    if ($orden) {
        $fecha_inicio = $orden['fecha_inicio'];
        $fecha_fin = $orden['fecha_fin'];
        // Verificar disponibilidad de la maquinaria en el rango de fechas
        $sql_disp = "SELECT COUNT(*) as no_disponible FROM disponibilidad_maquinaria WHERE id_maquinaria = ? AND fecha >= ? AND fecha <= ? AND disponible = 0";
        $stmt_disp = $pdo->prepare($sql_disp);
        $stmt_disp->execute([$id_maquinaria, $fecha_inicio, $fecha_fin]);
        $row_disp = $stmt_disp->fetch(PDO::FETCH_ASSOC);
        if ($row_disp && $row_disp['no_disponible'] > 0) {
            echo "<script>alert('La maquinaria seleccionada NO está disponible en alguna de las fechas del periodo de la orden.'); window.history.back();</script>";
            exit;
        }
    }

    try {
        $sql = "UPDATE detalle_alquiler SET id_orden = ?, id_maquinaria = ?, dias_alquiler = ?, subtotal = ? WHERE id_detalle = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_orden, $id_maquinaria, $dias_alquiler, $subtotal, $id]);
        echo "<script>alert('Detalle de alquiler actualizado exitosamente'); window.location.href='R_detalle_alquiler.php';</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Detalle de Alquiler</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <div class="container">
        <h2>Editar Detalle de Alquiler</h2>
<?php if ($detalle): ?>
<form method="POST">
    <div class="form-group">
        <label for="id_orden">Orden de Alquiler:</label>
        <select id="id_orden" name="id_orden" required>
            <option value="">Seleccione una orden</option>
            <?php foreach ($ordenes as $orden): ?>
                <option value="<?= $orden['id_orden'] ?>" data-cliente="<?= htmlspecialchars($orden['nombre_cliente']) ?>" data-dias="<?= $orden['dias'] ?>" <?= $detalle['id_orden'] == $orden['id_orden'] ? 'selected' : '' ?>>
                    Orden #<?= $orden['id_orden'] ?> (<?= htmlspecialchars($orden['nombre_cliente']) ?>, <?= $orden['fecha_inicio'] ?> a <?= $orden['fecha_fin'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Cliente:</label>
        <input type="text" id="nombre_cliente" readonly>
    </div>
    <div class="form-group">
        <label for="id_maquinaria">Maquinaria:</label>
        <select id="id_maquinaria" name="id_maquinaria" required>
            <option value="">Seleccione una maquinaria</option>
            <?php foreach ($maquinarias as $maquinaria): ?>
                <option value="<?= $maquinaria['id_maquinaria'] ?>" data-precio="<?= $maquinaria['precio_diario'] ?>" <?= $detalle['id_maquinaria'] == $maquinaria['id_maquinaria'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($maquinaria['nombre_maquinaria']) ?> (<?= number_format($maquinaria['precio_diario'], 2) ?> por día)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="dias_alquiler">Días de Alquiler:</label>
        <input type="text" min="1" name="dias_alquiler" id="dias_alquiler" value="<?= htmlspecialchars($detalle['dias_alquiler']) ?>" required readonly>
    </div>
    <div class="form-group">
        <label for="subtotal">Subtotal ($):</label>
        <input type="text" step="0.01" name="subtotal" id="subtotal" value="<?= htmlspecialchars($detalle['subtotal']) ?>" required readonly>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-success">Actualizar Detalle</button>
        <a href="R_detalle_alquiler.php" class="btn btn-secondary">Volver</a>
    </div>
</form>
<script>
    const maquinariaSelect = document.getElementById('id_maquinaria');
    const diasInput = document.getElementById('dias_alquiler');
    const subtotalInput = document.getElementById('subtotal');
    const ordenSelect = document.getElementById('id_orden');
    const clienteInput = document.getElementById('nombre_cliente');

    function calcularSubtotal() {
        const selected = maquinariaSelect.options[maquinariaSelect.selectedIndex];
        const precio = parseFloat(selected.getAttribute('data-precio')) || 0;
        const dias = parseInt(diasInput.value) || 0;
        const subtotal = precio * dias;
        subtotalInput.value = subtotal.toFixed(2);
    }

    function actualizarClienteYDias() {
        const selected = ordenSelect.options[ordenSelect.selectedIndex];
        clienteInput.value = selected.getAttribute('data-cliente') || '';
        diasInput.value = selected.getAttribute('data-dias') || '';
        calcularSubtotal();
    }

    maquinariaSelect.addEventListener('change', calcularSubtotal);
    ordenSelect.addEventListener('change', actualizarClienteYDias);

    // Inicializar si ya hay una orden seleccionada
    window.addEventListener('DOMContentLoaded', actualizarClienteYDias);
</script>
<?php endif; ?>
    </div>
</body>
</html>