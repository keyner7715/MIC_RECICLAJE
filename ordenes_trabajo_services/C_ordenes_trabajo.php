<?php
require_once '../config/db.php';

// Obtener clientes para el select
$clientes = $pdo->query("SELECT id_cliente, nombre_cliente FROM clientes")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_cliente = $_POST['id_cliente'] ?? '';
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';
    $estado_orden = $_POST['estado_orden'] ?? 'pendiente';
    $total_dias = $_POST['total_dias'] ?? 0;

    try {
        $sql = "INSERT INTO ordenes_alquiler (id_cliente, fecha_inicio, fecha_fin, estado_orden, total_dias) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_cliente, $fecha_inicio, $fecha_fin, $estado_orden, $total_dias]);
        echo "<script>alert('Orden de alquiler registrada exitosamente'); window.location.href='R_ordenes_trabajo.php';</script>";
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Orden de Alquiler</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <div class="container">
        <h2>Registrar Orden de Alquiler</h2>
        <form method="POST">
            <div class="form-group">
                <label for="id_cliente">Cliente:</label>
                <select name="id_cliente" id="id_cliente" required>
                    <option value="">Seleccione un cliente</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?= $cliente['id_cliente'] ?>"><?= htmlspecialchars($cliente['nombre_cliente']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha_inicio">Fecha de Inicio:</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" required>
            </div>
            <div class="form-group">
                <label for="fecha_fin">Fecha de Fin:</label>
                <input type="date" name="fecha_fin" id="fecha_fin" required>
            </div>
            <div class="form-group">
                <label for="estado_orden">Estado de la Orden:</label>
                <select name="estado_orden" id="estado_orden">
                    <option value="pendiente">Pendiente</option>
                    <option value="activa">Activa</option>
                    <option value="finalizada">Finalizada</option>
                    <option value="cancelada">Cancelada</option>
                </select>
            </div>
            <div class="form-group">
                <label for="total_dias">Total Días:</label>
                <input type="text" name="total_dias" id="total_dias" value="0" required readonly>
            </div>
            <div class="form-group">
                <button type="submit">Registrar</button>
            </div>
        </form>
        <a href="R_ordenes_trabajo.php" class="btn-primary">Volver</a>
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
    </div>
</body>
</html>
