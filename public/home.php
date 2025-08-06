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
            <img src="../public/reciclaje.jpg" alt="Fondo de Login">
        </div>

        <div class="login-bg-form">
            <div class="form-container">
                <!-- Recuadros informativos: Misión, Visión y Alcance -->
                <div class="info-cards">
                    <div class="info-card">
                        <h2>Misión</h2>
                        <p>Gestionar residuos reciclables de manera integral, transformando desechos en recursos valiosos para contribuir al cuidado del medio ambiente.</p>
                    </div>
                    <div class="info-card">
                        <h2>Visión</h2>
                        <p>Ser líderes en reciclaje nacional, promoviendo la economía circular e innovación sostenible para un futuro más verde.</p>
                    </div>
                    <div class="info-card">
                        <h2>Alcance</h2>
                        <p>Sistema de gestión para recolección, clasificación y procesamiento de materiales reciclables, controlando centros de acopio, clientes y proveedores.</p>
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