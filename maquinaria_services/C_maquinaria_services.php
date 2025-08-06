<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_maqui = $_POST['nombre_maquinaria'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $marca = $_POST['marca'] ?? '';
    $modelo = $_POST['modelo'] ?? '';
    $año = $_POST['año'] ?? '';
    $estado_maquinaria = $_POST['estado_maquinaria'] ?? '';
    $precio_diario = $_POST['precio_diario'] ?? '';

    try {
        $sql = "INSERT INTO maquinarias (nombre_maquinaria, tipo, marca, modelo, año, estado_maquinaria, precio_diario) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre_maqui, $tipo, $marca, $modelo, $año, $estado_maquinaria, $precio_diario]);

        echo "<script>alert('Maquinaria creada exitosamente'); window.location.href='R_maquinaria_services.php';</script>";
    } catch(PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicado de nombre
            echo "<script>alert('El nombre de la maquinaria ya está registrado.'); window.history.back();</script>";
        } else {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Maquinaria</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Crear Nueva Maquinaria</h1>
            <nav>
                <ul>
                    <li><a href="../public/menu.php">Inicio</a></li>
                    <li><a href="R_maquinaria_services.php">Ver Maquinarias</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="form-container">
                <h2>Registrar Nueva Maquinaria</h2>

                <form method="POST">
                    <div class="form-group">
                        <label for="nombre_maquinaria">Nombre:</label>
                        <input type="text" id="nombre_maquinaria" name="nombre_maquinaria" required placeholder="Ingrese el nombre">
                    </div>

                    <div class="form-group">
                        <label for="tipo">Tipo:</label>
                        <input type="text" id="tipo" name="tipo" required placeholder="Ingrese el tipo">
                    </div>

                    <div class="form-group">
                        <label for="marca">Marca:</label>
                        <input type="text" id="marca" name="marca" required placeholder="Ingrese la marca">
                    </div>

                    <div class="form-group">
                        <label for="modelo">Modelo:</label>
                        <input type="text" id="modelo" name="modelo" required placeholder="Ingrese el modelo">
                    </div>

                    <div class="form-group">
                        <label for="año">Año:</label>
                        <input type="text" id="año" name="año" required placeholder="Ingrese el año">
                        <div id="anio_error" class="error-message" style="display:none;color:red;font-size:0.9em;margin-top:5px;"></div>
                    </div>

                    <div class="form-group">
                        <label for="estado_maquinaria">Estado:</label>
                        <select id="estado_maquinaria" name="estado_maquinaria" required>
                            <option value="Activo">Disponible</option>
                            <option value="Inactivo">Alquilada</option>
                            <option value="Mantenimiento">Mantenimiento</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="precio_diario">Precio Diario:</label>
                        <input type="text" id="precio_diario" name="precio_diario" required placeholder="Ingrese el precio diario">
                        <div id="precio_error" class="error-message" style="display:none;color:red;font-size:0.9em;margin-top:5px;"></div>
                    </div>

                    </div>

                    <div class="form-group">
                        <button type="submit">Crear Maquinaria</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

</body>
<script src="../js/maquinaria_forms.js"></script>
</html>



