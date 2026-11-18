/* ==========================================================
   script.js (LOGIN) - Aurora.Libros
   Ubicacion: assets/js/script.js
   Controla: cambio Entrar <-> Registrarme y el modal de administrador.
   Escrito solo con caracteres ASCII (sin tildes) a proposito.
   ========================================================== */
(function () {
    'use strict';

    function iniciar() {
        /* ---------- Entrar / Registrarme ---------- */
        var btnIniciar = document.getElementById('btn__iniciar-sesion');
        var btnRegistrar = document.getElementById('btn__registrarse');

        var formLogin = document.querySelector('.formulario__login');
        var formRegister = document.querySelector('.formulario__register');
        var contenedor = document.querySelector('.contenedor__login-register');
        var cajaLogin = document.querySelector('.caja__trasera-login');
        var cajaRegister = document.querySelector('.caja__trasera-register');

        var hayLogin = formLogin && formRegister && contenedor && cajaLogin && cajaRegister;

        function anchoPagina() {
            if (!hayLogin) { return; }
            if (window.innerWidth > 850) {
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
            if (!hayLogin) { return; }
            if (window.innerWidth > 850) {
                formLogin.style.display = 'block';
                contenedor.style.left = '10px';
                formRegister.style.display = 'none';
                cajaRegister.style.opacity = '1';
                cajaLogin.style.opacity = '0';
            } else {
                formLogin.style.display = 'block';
                contenedor.style.left = '0px';
                formRegister.style.display = 'none';
                cajaRegister.style.display = 'block';
                cajaLogin.style.display = 'none';
            }
        }

        function mostrarRegistro() {
            if (!hayLogin) { return; }
            if (window.innerWidth > 850) {
                formRegister.style.display = 'block';
                contenedor.style.left = '410px';
                formLogin.style.display = 'none';
                cajaRegister.style.opacity = '0';
                cajaLogin.style.opacity = '1';
            } else {
                formRegister.style.display = 'block';
                contenedor.style.left = '0px';
                formLogin.style.display = 'none';
                cajaRegister.style.display = 'none';
                cajaLogin.style.display = 'block';
                cajaLogin.style.opacity = '1';
            }
        }

        if (btnIniciar) { btnIniciar.addEventListener('click', mostrarLogin); }
        if (btnRegistrar) { btnRegistrar.addEventListener('click', mostrarRegistro); }
        window.addEventListener('resize', anchoPagina);
        anchoPagina();

        /* ---------- Modal de administrador ---------- */
        var modal = document.getElementById('modalAdmin');
        var abrir = document.getElementById('btn-open-admin');
        var cerrar = document.getElementById('btn-close-admin');
        var formAdminLogin = document.getElementById('admin-login-form');
        var formAdminReg = document.getElementById('admin-register-form');
        var irRegistro = document.getElementById('to-admin-register');
        var irLogin = document.getElementById('to-admin-login');

        function abrirModal(e) {
            if (e) { e.preventDefault(); }
            if (!modal) { return; }
            modal.classList.add('active', 'show', 'open');
            modal.style.display = 'flex';
            modal.style.opacity = '1';
            modal.style.visibility = 'visible';
        }

        function cerrarModal() {
            if (!modal) { return; }
            modal.classList.remove('active', 'show', 'open');
            modal.style.display = 'none';
        }

        function mostrarAdminRegistro() {
            if (formAdminLogin) { formAdminLogin.style.display = 'none'; }
            if (formAdminReg) { formAdminReg.style.display = 'block'; }
        }

        function mostrarAdminLogin() {
            if (formAdminReg) { formAdminReg.style.display = 'none'; }
            if (formAdminLogin) { formAdminLogin.style.display = 'block'; }
        }

        if (abrir) { abrir.addEventListener('click', abrirModal); }
        if (cerrar) { cerrar.addEventListener('click', cerrarModal); }
        if (irRegistro) { irRegistro.addEventListener('click', mostrarAdminRegistro); }
        if (irLogin) { irLogin.addEventListener('click', mostrarAdminLogin); }

        // Cerrar al hacer clic fuera de la caja o con la tecla Escape
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) { cerrarModal(); }
            });
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { cerrarModal(); }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iniciar);
    } else {
        iniciar();
    }
})();