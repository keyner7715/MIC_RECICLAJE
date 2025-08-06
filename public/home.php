<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Alquiler de Maquinaria</title>
    <link rel="stylesheet" href="../public/login.css">
    <link rel="stylesheet" href="../public/forms.css">
</head>
<body>
    <!-- Fondo con imagen y formulario centrado -->
    <div class="login-bg-split">
        <div class="login-bg-image">
            <img src="../public/maquinaria.jpg" alt="Fondo de Login">
        </div>

        <div class="login-bg-form">
            <div class="form-container">
                <!-- Recuadros informativos: Misión, Visión y Alcance -->
                <div class="info-cards">
                    <div class="info-card">
                        <h2>Misión</h2>
                        <p>Brindar servicios de seguridad integral y confiable, protegiendo la integridad de personas, bienes e instalaciones, mediante un equipo altamente capacitado, comprometido con la ética, el respeto y la excelencia operativa.</p>
                    </div>
                    <div class="info-card">
                        <h2>Visión</h2>
                        <p>Ser la empresa de seguridad privada líder a nivel nacional, reconocida por su profesionalismo, innovación tecnológica y compromiso con la tranquilidad de nuestros clientes y la sociedad.</p>
                    </div>
                    <div class="info-card">
                        <h2>Alcance</h2>
                        <p>Este sistema permite gestionar guardias, supervisores, clientes, zonas de vigilancia y turnos de trabajo; además de optimizar las asignaciones de personal y el control de servicios contratados, facilitando una operación eficiente y segura para la empresa y sus clientes.</p>
                    </div>
                </div>

                <form method="POST" action="../auth_services/login_form.php">
                    <div class="form-actions">
                        <button type="submit" class="btn">Ingresar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>