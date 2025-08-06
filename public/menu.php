<?php
session_start();
if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: ../auth_services/login_auth.php");
    exit();
}

$rol = $_SESSION['rol'] ?? 'Rol no definido'; // Obtenemos el rol guardado o mensaje por defecto
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Gestion de Reciclaje</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .header-box {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.10);
            padding: 1.5rem 2rem;
            display: inline-block;
            margin: 1.5rem auto 0 auto;
            min-width: 320px;
            max-width: 95vw;
            text-align: center;
            position: relative;
            z-index: 2;
        }
        header {
            background-image: url('../public/reciclaje.jpg'); /* Cambia la ruta si tu imagen está en otra carpeta */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }
        .services-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .service-category {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .service-category h3 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .service-link {
            display: block;
            background: #3498db;
            color: white;
            text-decoration: none;
            padding: 0.8rem 1rem;
            margin: 0.5rem 0;
            border-radius: 5px;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .service-link:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .service-description {
            font-size: 0.9rem;
            color: #666;
            margin-top: 0.5rem;
        }
        
        .header-content {
            text-align: center;
            padding: 1rem 0; /* Reducido de 2rem a 1rem */
            position: relative;
        }

        .header-content h1 {
            color: #2c3e50;
            margin-bottom: 0.5rem; /* Reducido */
            font-size: 1.6rem;     /* Reducido de valor por defecto */
            line-height: 1.2;
        }
        
        .header-content p {
            color: #7f8c8d;
            font-size: 1.1rem;
        }
        
        .logout-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #e74c3c;
            color: white;
            text-decoration: none;
            padding: 0.6rem 1.2rem;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .logout-btn:hover {
            background: #c0392b;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-box">

                <h1>Panel de Reciclaje</h1>
                <p>Gestión centralizada </p>
                <p>Rol: <strong><?= htmlspecialchars($_SESSION['rol']) ?></strong></p>
                <p>Bienvenido, <strong><?= htmlspecialchars($_SESSION['nombre_usuario']) ?></strong></p>

            </div>
        </div>
        <a href="../auth_services/logout.php" class="logout-btn">Cerrar Sesión</a>
    </header>

    <main>
        <div class="container fade-in">
            <div class="services-grid">

                <!-- Servicios de Clientes -->
                <div class="service-category">
                    <h3>👥 Gestión de Clientes</h3>
                    <a href="../clientes_services/R_clientes.php" class="service-link">Panel de Clientes</a>
                    <div class="service-description">Gestion de Clientes</div>

                </div>

                <!-- Servicios de Usuarios -->
                <div class="service-category">
                    <h3>👤 Gestión de Usuarios</h3>
                    <a href="../usuarios_services/R_usuario.php" class="service-link">Crear Usuario</a>
                    <div class="service-description">Microservicio de gestión de usuarios</div>
                </div>

                <!-- Servicios de Empleados -->
                <div class="service-category">
                    <h3>👷 Gestión de Empleados</h3>
                    <a href="../empleados_services/R_empleados_services.php" class="service-link">Listar Empleados</a>
                    <div class="service-description">Microservicio de gestión de empleados</div>
                </div>
                <!-- Servicios de Proveedores -->
                <div class="service-category">
                    <h3> 📦Gestión de Proveedores</h3>
                    <a href="../proveedores_services/R_proveedores.php" class="service-link">Listar Proveedores</a>
                    <div class="service-description">Microservicio de gestión de proveedores</div>
                </div>
                <!-- Servicios de Tipo de Material -->
                <div class="service-category">
                    <h3> 📒Gestión de Tipos de Material</h3>
                    <a href="../tipo_material_services/R_tipo_material.php" class="service-link">Listar Tipos de Material</a>
                    <div class="service-description">Microservicio de gestión de tipos de material</div>
                </div>
                <!-- Servicios de Material -->
                <div class="service-category">  
                    <h3>📦 Gestión de Material</h3>
                    <a href="../material_services/R_material.php" class="service-link">Listar Material</a>
                    <div class="service-description">Microservicio de gestión de materiales</div>
                </div>
                <!-- Servicios de Centro de Acopio -->
                <div class="service-category">
                    <h3> 🏢 Gestión de Centros de Acopio</h3>
                    <a href="../gestion_acopio_services/R_gestion_acopio.php" class="service-link">Listar Centros de Acopio</a>
                    <div class="service-description">Microservicio de gestión de centros de acopio</div>
                </div>
                <!-- Servicios de Ordenes de Trabajo -->
                <div class="service-category">
                    <h3>📖 Gestión de Ordenes de Trabajo</h3>
                    <a href="../ordenes_trabajo_services/R_ordenes_trabajo.php" class="service-link">Listar Ordenes de Trabajo</a>
                    <div class="service-description">Microservicio de gestión de ordenes de trabajo</div>
                </div>
                <!-- Servicios de Detalle de Alquiler -->
                <div class="service-category">
                    <h3>📑 Gestión de Detalle de Alquiler</h3>
                    <a href="../detalle_alquiler_services/R_detalle_alquiler.php" class="service-link">Listar Detalle de Alquiler</a>
                    <div class="service-description">Microservicio de gestión de detalle de alquiler</div>
                </div>
    </main>

    <footer>
        <div class="container">
            <p>MIC BASE DE DATOS</p>
        </div>
    </footer>
</body>
</html>
