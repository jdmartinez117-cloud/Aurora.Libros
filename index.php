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
    <meta name="google" content="notranslate">
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

                    <!-- Requisitos de la contraseña: se marcan en vivo (verde = cumplido, rojo = falta) -->
                    <div class="pw-checklist">
                        <p class="pw-titulo">Requisitos de la contraseña:</p>
                        <ul class="pw-lista">
                            <li class="pw-chip" data-regla="largo"><span class="pw-icono">✗</span> Mínimo 8 caracteres</li>
                            <li class="pw-chip" data-regla="mayus"><span class="pw-icono">✗</span> Una letra mayúscula</li>
                            <li class="pw-chip" data-regla="minus"><span class="pw-icono">✗</span> Una letra minúscula</li>
                            <li class="pw-chip" data-regla="numero"><span class="pw-icono">✗</span> Un número</li>
                        </ul>
                        <p class="pw-resumen" aria-live="polite">0 de 4 requisitos cumplidos</p>
                    </div>

                    <!-- WIDGET CAPTCHA (se renderiza al abrir este formulario) -->
                    <div id="captcha-register"></div>

                    <button>Unirse a la Librería</button>

                    <!-- Mensajes del registro (errores del servidor, sin recargar la página) -->
                    <p id="registro-msg" style="display:none; font-size:13px; margin:8px 0 0; white-space:pre-line;"></p>
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

                    <!-- Requisitos de la contraseña: se marcan en vivo (verde = cumplido, rojo = falta) -->
                    <div class="pw-checklist">
                        <p class="pw-titulo">Requisitos de la contraseña:</p>
                        <ul class="pw-lista">
                            <li class="pw-chip" data-regla="largo"><span class="pw-icono">✗</span> Mínimo 8 caracteres</li>
                            <li class="pw-chip" data-regla="mayus"><span class="pw-icono">✗</span> Una letra mayúscula</li>
                            <li class="pw-chip" data-regla="minus"><span class="pw-icono">✗</span> Una letra minúscula</li>
                            <li class="pw-chip" data-regla="numero"><span class="pw-icono">✗</span> Un número</li>
                        </ul>
                        <p class="pw-resumen" aria-live="polite">0 de 4 requisitos cumplidos</p>
                    </div>

                    <!-- WIDGET CAPTCHA (se renderiza al abrir el registro admin) -->
                    <div id="captcha-admin-register"></div>

                    <button type="submit">Crear Cuenta</button>
                    <p class="change-form">¿Ya tienes cuenta? <span id="to-admin-login">Inicia Sesión</span></p>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
    <!-- CAMBIO DE IDIOMA ES/EN (debe ir después de script.js) -->
    <script src="assets/js/idiomas.js"></script>

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

        // Guarda el id de cada widget para poder reiniciarlo (un captcha ya usado no sirve otra vez)
        const captchaIds = {};

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

            captchaIds[tipo] = grecaptcha.render(idContenedor, {
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

        // Reinicia un captcha ya usado y lo vuelve a mostrar
        function reiniciarCaptcha(idContenedor, tipo) {
            if (typeof grecaptcha === 'undefined' || captchaIds[tipo] === undefined) return;
            grecaptcha.reset(captchaIds[tipo]);
            const contenedor = document.getElementById(idContenedor);
            if (contenedor) contenedor.style.display = 'block';
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

    <!-- ESTILO DE LOS REQUISITOS DE CONTRASEÑA (botones tipo píldora) -->
    <style>
        .pw-checklist { margin: 4px 0 12px; text-align: left; }
        .pw-checklist .pw-titulo { font-size: 12px; font-weight: 600; color: #57534e; margin: 0 0 6px; }
        /* Un requisito debajo de otro, todos alineados a la izquierda y del mismo ancho */
        .pw-checklist .pw-lista { display: grid; grid-template-columns: max-content; justify-content: start; gap: 6px; margin: 0; padding: 0; list-style: none; }
        .pw-chip {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 11px; border-radius: 999px;
            font-size: 12px; font-weight: 600; line-height: 1.2;
            border: 1.5px solid #d6d3d1; background: #f5f5f4; color: #78716c;
            transition: all .2s ease; user-select: none;
        }
        .pw-chip .pw-icono { font-weight: 800; }
        .pw-chip.falta { border-color: #fca5a5; background: #fef2f2; color: #b91c1c; }
        .pw-chip.ok    { border-color: #86efac; background: #f0fdf4; color: #15803d; }
        .pw-checklist .pw-resumen { font-size: 12px; font-weight: 600; margin: 8px 0 0; color: #78716c; }
        .pw-checklist .pw-resumen.ok { color: #15803d; }
        .formulario__register button:disabled,
        #admin-register-form button:disabled { opacity: .55; cursor: not-allowed; }
    </style>

    <!-- POLÍTICA DE CONTRASEÑAS.
         - Los 4 requisitos se marcan en vivo mientras se escribe (verde = cumplido, rojo = falta).
         - El botón de registro se activa solo cuando se cumplen los 4.
         - El registro de usuario se envía con fetch: la página NO se recarga y los campos se conservan.
         - El servidor valida siempre la política (php/politica_password.php).
         Si cambias PASSWORD_MIN_LENGTH en php/politica_password.php, cambia también el 8 de aquí. -->
    <script>
        (function () {
            var REGLAS = {
                largo:  function (p) { return p.length >= 8; },
                mayus:  function (p) { return /\p{Lu}/u.test(p); },
                minus:  function (p) { return /\p{Ll}/u.test(p); },
                numero: function (p) { return /\d/.test(p); }
            };

            function cumpleTodo(p) {
                return Object.keys(REGLAS).every(function (k) { return REGLAS[k](p); });
            }

            document.querySelectorAll('.formulario__register, #admin-register-form').forEach(function (form) {
                var campo   = form.querySelector('input[name="password"]');
                var panel   = form.querySelector('.pw-checklist');
                var boton   = form.querySelector('button:not([type="button"])');
                if (!campo || !panel || !boton) return;

                var chips    = panel.querySelectorAll('.pw-chip');
                var resumen  = panel.querySelector('.pw-resumen');
                var enviando = false;

                // Repinta los 4 requisitos y activa/desactiva el botón de registro
                function actualizar() {
                    var p = campo.value, hechas = 0;

                    chips.forEach(function (chip) {
                        var cumplida = REGLAS[chip.getAttribute('data-regla')](p);
                        if (cumplida) hechas++;
                        chip.classList.toggle('ok', cumplida);
                        chip.classList.toggle('falta', !cumplida && p.length > 0);
                        chip.querySelector('.pw-icono').textContent = cumplida ? '✓' : '✗';
                    });

                    var completo = (hechas === chips.length);
                    resumen.textContent = completo ? '¡Contraseña lista!' : hechas + ' de ' + chips.length + ' requisitos cumplidos';
                    resumen.classList.toggle('ok', completo);

                    boton.disabled = enviando || !completo;
                    boton.title = completo ? '' : 'Completa los requisitos de la contraseña para continuar';
                }

                campo.addEventListener('input', actualizar);
                actualizar();

                // REGISTRO DE ADMINISTRADOR: envío normal; solo se bloquea si la contraseña no cumple
                if (form.id === 'admin-register-form') {
                    form.addEventListener('submit', function (e) {
                        if (!cumpleTodo(campo.value)) {
                            e.preventDefault();
                            campo.focus();
                        }
                    });
                    return;
                }

                // REGISTRO DE USUARIO: se envía con fetch para no recargar la página
                var msg = document.getElementById('registro-msg');

                function mostrarMensaje(texto, ok) {
                    msg.textContent = texto;
                    msg.style.color = ok ? '#15803d' : '#dc2626';
                    msg.style.display = 'block';
                }

                form.addEventListener('submit', function (e) {
                    e.preventDefault(); // la página no se recarga ni cambia
                    msg.style.display = 'none';

                    // 1) Contraseña: si no cumple, no se envía (los requisitos ya se ven marcados)
                    if (!cumpleTodo(campo.value)) {
                        campo.focus();
                        return;
                    }

                    // 2) Captcha resuelto
                    if (typeof grecaptcha !== 'undefined' && captchaIds['register'] !== undefined &&
                        !grecaptcha.getResponse(captchaIds['register'])) {
                        mostrarMensaje('Por favor, confirma que no eres un robot.', false);
                        return;
                    }

                    // 3) Envío al servidor
                    enviando = true;
                    actualizar();
                    fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: { 'X-Requested-With': 'fetch' }
                    })
                    .then(function (r) { return r.text(); })
                    .then(function (texto) {
                        var d;
                        try {
                            d = JSON.parse(texto);
                        } catch (err) {
                            // Si el servidor responde algo que no es JSON, se muestra en la consola (F12) para poder verlo
                            console.error('El servidor no devolvió JSON. Respuesta recibida:\n', texto);
                            throw err;
                        }
                        if (d.ok) {
                            form.reset();
                            alert(d.mensaje);
                            document.getElementById('btn__iniciar-sesion').click(); // vuelve al login
                        } else {
                            mostrarMensaje(d.mensaje, false);
                            if (d.campo === 'password') { campo.focus(); }
                        }
                    })
                    .catch(function () {
                        mostrarMensaje('No se pudo completar el registro. Inténtalo de nuevo.', false);
                    })
                    .then(function () {
                        enviando = false;
                        actualizar();
                        reiniciarCaptcha('captcha-register', 'register'); // el captcha usado ya no sirve
                    });
                });
            });
        })();
    </script>
</body>
</html>