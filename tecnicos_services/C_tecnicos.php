<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_tecnico = $_POST['nombre_tecnico'] ?? '';
    $especialidad = $_POST['especialidad'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $correo = $_POST['correo'] ?? '';

    try {
        $sql = "INSERT INTO tecnicos (nombre_tecnico, especialidad, telefono, correo) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre_tecnico, $especialidad, $telefono, $correo]);

        echo "<script>alert('Técnico creado exitosamente'); window.location.href='R_tecnicos.php';</script>";
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
    <title>Crear Técnico</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Crear Nuevo Técnico</h1>
            <nav>
                <ul>
                    <li><a href="../public/menu.php">Inicio</a></li>
                    <li><a href="R_tecnicos.php">Ver Técnicos</a></li>

                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="form-container">
                <h2>Registrar Nuevo Técnico</h2>

                <form method="POST">
                    <div class="form-group">
                        <label for="nombre_tecnico">Nombre:</label>
                        <input type="text" id="nombre_tecnico" name="nombre_tecnico" required placeholder="Ingrese el nombre del técnico">
                    </div>
                    <div class="form-group">
                        <label for="especialidad">Especialidad:</label>
                        <input type="text" id="especialidad" name="especialidad" required placeholder="Ingrese la especialidad del técnico">
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono:</label>
                        <input type="text" id="telefono" name="telefono" required placeholder="Ingrese el teléfono del supervisor">
                        <div id="telefono_error" class="error-message" style="display:none;color:red;font-size:0.9em;margin-top:5px;"></div>
                    </div>

                    <div class="form-group">
                        <label for="correo">Correo Electrónico:</label>
                        <input type="email" id="correo" name="correo" required placeholder="Ingrese el correo del supervisor">
                    </div>
                    
                    <div class="form-group">
                        <button type="submit">Crear técnico</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

</body>
<script src="../js/tecnicos_form.js"></script>
</html>
