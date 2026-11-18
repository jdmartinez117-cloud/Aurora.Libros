/* ==========================================================================
   AURORA.LIBROS - Alternar entre Login y Registro (página index.php)
   ========================================================================== */

(function () {
    var btnLogin = document.getElementById('btn__iniciar-sesion');
    var btnRegister = document.getElementById('btn__registrarse');

    var formLogin = document.querySelector('.formulario__login');
    var formRegister = document.querySelector('.formulario__register');
    var contenedor = document.querySelector('.contenedor__login-register');
    var cajaLogin = document.querySelector('.caja__trasera-login');
    var cajaRegister = document.querySelector('.caja__trasera-register');

    if (!btnLogin || !btnRegister || !formLogin || !formRegister || !contenedor || !cajaLogin || !cajaRegister) {
        console.error('login.js: faltan elementos del formulario en index.php');
        return;
    }

    // Posición horizontal de la tarjeta blanca en pantallas grandes.
    // Si en tu diseño queda desalineada, ajusta estos dos valores.
    var POS_LOGIN = '10px';
    var POS_REGISTER = '410px';
    var ANCHO_MOVIL = 850;

    function esEscritorio() {
        return window.innerWidth > ANCHO_MOVIL;
    }

    function ajustarAncho() {
        if (esEscritorio()) {
            cajaRegister.style.display = 'block';
            cajaLogin.style.display = 'block';
        } else {
            cajaRegister.style.display = 'block';
            cajaRegister.style.opacity = '1';
            cajaLogin.style.display = 'none';
            formLogin.style.display = 'block';
            contenedor.style.left = '0px';
            formRegister.style.display = 'none';
        }
    }

    function mostrarLogin() {
        formLogin.style.display = 'block';
        formRegister.style.display = 'none';
        if (esEscritorio()) {
            contenedor.style.left = POS_LOGIN;
            cajaRegister.style.opacity = '1';
            cajaLogin.style.opacity = '0';
        } else {
            contenedor.style.left = '0px';
            cajaRegister.style.display = 'block';
            cajaLogin.style.display = 'none';
        }
    }

    function mostrarRegistro() {
        formRegister.style.display = 'block';
        formLogin.style.display = 'none';
        if (esEscritorio()) {
            contenedor.style.left = POS_REGISTER;
            cajaRegister.style.opacity = '0';
            cajaLogin.style.opacity = '1';
        } else {
            contenedor.style.left = '0px';
            cajaRegister.style.display = 'none';
            cajaLogin.style.display = 'block';
            cajaLogin.style.opacity = '1';
        }
    }

    btnLogin.addEventListener('click', mostrarLogin);
    btnRegister.addEventListener('click', mostrarRegistro);
    window.addEventListener('resize', ajustarAncho);

    ajustarAncho();
})();