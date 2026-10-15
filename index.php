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
    <!-- SCRIPT DE GOOGLE reCAPTCHA (renderizado manual/explícito) -->
    <script src="https://www.google.com/recaptcha/api.js?onload=iniciarCaptchas&render=explicit" async defer></script>
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

                    <!-- WIDGET CAPTCHA (se renderiza al abrir este formulario) -->
                    <div id="captcha-login"></div>

                    <button>Acceder al Catálogo</button>

                     <!-- ACTIVADOR DEL MODAL ADMIN -->
                    <div class="admin-link-container">
                        <a href="#" id="btn-open-admin">
                            <i class="fa-solid fa-user-shield"></i> Acceso Administrador
                        </a>
                    </div>
                </form>
                
                
                
                <!-- Registro -->
                <form action="php/registro_usuario_be.php" method="POST" class="formulario__register">
                    <h2>Nueva Cuenta</h2>
                    <input type="text" placeholder="Nombre Completo" name="nombre_completo">
                    <input type="text" placeholder="Correo Electrónico" name="email">
                    <input type="text" placeholder="Nombre de Usuario" name="usuario">
                    <input type="password" placeholder="Contraseña" name="password">

                    <!-- WIDGET CAPTCHA (se renderiza al abrir este formulario) -->
                    <div id="captcha-register"></div>

                    <button>Unirse a la Librería</button>
                </form>
            </div>
        </div>  
    </main>

     <!-- VENTANA MODAL / OVERLAY ADMINISTRADOR -->
    <div id="modalAdmin" class="modal-overlay">
        <div class="modal-content">
            <span class="close-modal" id="btn-close-admin">&times;</span>
            
            <div class="admin-forms-wrapper">
                <!-- LOGIN ADMIN -->
                <form action="php/login_admin_be.php" method="POST" id="admin-login-form">
                    <h2>Acceso administrador</h2>
                    <div class="input-group">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="text" placeholder="Correo Administrativo" name="email" required>
                    </div>
                    <div class="input-group">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" placeholder="Contraseña" name="password" required>
                    </div>

                    <!-- WIDGET CAPTCHA (se renderiza al abrir el modal admin) -->
                    <div id="captcha-admin-login"></div>

                    <button type="submit">Iniciar Sesión</button>
                    <p class="change-form">¿Nuevo admin? <span id="to-admin-register">Regístrate</span></p>
                </form>

                <!-- REGISTRO ADMIN (Oculto inicialmente) -->
                <form action="php/registro_admin_be.php" method="POST" id="admin-register-form" style="display: none;">
                    <h2>Admin: Registro</h2>
                    <input type="text" placeholder="Nombre Completo" name="nombre_completo" required>
                    <input type="text" placeholder="Correo Electrónico" name="email" required>
                    <input type="text" placeholder="Usuario" name="usuario" required>
                    <input type="text" placeholder="Cargo en Librería" name="cargo" required>
                    <input type="password" placeholder="Contraseña" name="password" required>

                    <!-- WIDGET CAPTCHA (se renderiza al abrir el registro admin) -->
                    <div id="captcha-admin-register"></div>

                    <button type="submit">Crear Cuenta</button>
                    <p class="change-form">¿Ya tienes cuenta? <span id="to-admin-login">Inicia Sesión</span></p>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>

    <!-- CONTROL DE RENDERIZADO DE CAPTCHA POR FORMULARIO -->
    <style>
        /* Reduce el tamaño visual del captcha para que no desborde la tarjeta */
        #captcha-login, #captcha-register, #captcha-admin-login, #captcha-admin-register {
            transform: scale(0.85);
            transform-origin: left top;
            margin-bottom: -10px;
        }
    </style>
    <script>
        const CAPTCHA_SITE_KEY = '6Lc67MstAAAAAAqW0b52TUvteJQL0nRkqSu2gnXF';
        let captchaLoginListo = false;
        let captchaRegisterListo = false;
        let captchaAdminLoginListo = false;
        let captchaAdminRegisterListo = false;

        // Se llama automáticamente cuando Google termina de cargar su script
        function iniciarCaptchas() {
            // El login está visible desde el inicio, así que lo renderizamos de una vez
            renderizarCaptcha('captcha-login', 'login');
        }

        function renderizarCaptcha(idContenedor, tipo) {
            const yaListo = {
                'login': captchaLoginListo,
                'register': captchaRegisterListo,
                'admin-login': captchaAdminLoginListo,
                'admin-register': captchaAdminRegisterListo
            }[tipo];

            const contenedor = document.getElementById(idContenedor);
            if (!contenedor || yaListo || typeof grecaptcha === 'undefined') return;

            grecaptcha.render(idContenedor, {
                sitekey: CAPTCHA_SITE_KEY,
                callback: function () {
                    // Se ejecuta apenas el usuario verifica correctamente
                    contenedor.style.display = 'none';
                },
                'expired-callback': function () {
                    // Si el captcha expira (tardó mucho en enviar el form), lo mostramos de nuevo
                    contenedor.style.display = 'block';
                }
            });

            if (tipo === 'login') captchaLoginListo = true;
            if (tipo === 'register') captchaRegisterListo = true;
            if (tipo === 'admin-login') captchaAdminLoginListo = true;
            if (tipo === 'admin-register') captchaAdminRegisterListo = true;
        }

        // Al dar clic en "Registrarme", renderiza el captcha de registro
        document.getElementById('btn__registrarse').addEventListener('click', function () {
            renderizarCaptcha('captcha-register', 'register');
        });

        // Al dar clic en "Iniciar Sesión" (volver al login), asegura que su captcha exista
        document.getElementById('btn__iniciar-sesion').addEventListener('click', function () {
            renderizarCaptcha('captcha-login', 'login');
        });

        // Al abrir el modal de administrador
        document.getElementById('btn-open-admin').addEventListener('click', function () {
            renderizarCaptcha('captcha-admin-login', 'admin-login');
        });

        // Al cambiar a registro de administrador dentro del modal
        document.getElementById('to-admin-register').addEventListener('click', function () {
            renderizarCaptcha('captcha-admin-register', 'admin-register');
        });
    </script>
</body>
</html>