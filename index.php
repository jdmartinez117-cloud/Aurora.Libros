<?php
        session_start();

        if(isset($_SESSION['usuario'])){
            header("location: php/bienvenida.php");
        }

?>


  <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurora.Libros | Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
    
    <!-- Título de la Librería -->
    <header class="main-header">
        <div class="logo">
            AURORA<span>.LIBROS</span>
        </div>
    </header>

    <main>
        <div class="contendor__todo">
            <div class="caja__trasera">
                <div class="caja__trasera-login">
                    <h3>¿Ya eres lector?</h3>
                    <p>Inicia sesión para continuar tu historia.</p>
                    <button id="btn__iniciar-sesion">Iniciar Sesión</button>
                </div>
                <div class="caja__trasera-register">
                    <h3>¿Nuevo por aquí?</h3>
                    <p>Regístrate y descubre nuevos mundos.</p>
                    <button id="btn__registrarse">Registrarme</button>
                </div>
            </div>

            <div class="contenedor__login-register">
                <!-- Login -->
                <form action="php/login_usuario_be.php" method="POST" class="formulario__login">
                    <h2>Entrar</h2>
                    <div class="input-group">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="text" placeholder="Correo Electrónico" name="email">
                    </div>
                    <div class="input-group">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" placeholder="Contraseña" name="password">
                    </div>
                    <button>Acceder al Catálogo</button>
                </form>

                <!-- Registro -->
                <form action="php/registro_usuario_be.php" method="POST" class="formulario__register">
                    <h2>Nueva Cuenta</h2>
                    <input type="text" placeholder="Nombre Completo" name="nombre_completo">
                    <input type="text" placeholder="Correo Electrónico" name="email">
                    <input type="text" placeholder="Nombre de Usuario" name="usuario">
                    <input type="password" placeholder="Contraseña" name="password">
                    <button>Unirse a la Librería</button>
                </form>
            </div>
        </div>  
    </main>

    <script src="assets/js/script.js"></script>
</body>
</html>