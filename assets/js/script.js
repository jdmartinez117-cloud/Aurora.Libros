  document.getElementById("btn__iniciar-sesion").addEventListener("click", iniciarSesion);
  document.getElementById("btn__registrarse").addEventListener("click", register);
  window.addEventListener("resize", anchoPagina);

//Declaracion de Variables
  var contenedor_login_register = document.querySelector(".contenedor__login-register");
  var formulario_login = document.querySelector(".formulario__login");
  var formulario_register = document.querySelector(".formulario__register");
  var caja_trasera_login = document.querySelector(".caja__trasera-login");
  var caja_trasera_register = document.querySelector(".caja__trasera-register");


  function anchoPagina () {

      if (window.innerWidth > 850) {
        caja_trasera_login.style.display = "block";
        caja_trasera_register.style.display = "block";
      } else {
        caja_trasera_register.style.display = "block";
        caja_trasera_register.style.opacity = "1";
        caja_trasera_login.style.display = "none";
        formulario_login.style.display = "block";
        formulario_register.style.display = "none";
        contenedor_login_register.style.left = "0px";
  }
}
    anchoPagina();

  function iniciarSesion() {

      if (window.innerWidth > 850) {
        formulario_register.style.display = "none";
        contenedor_login_register.style.left = "10px";
        formulario_login.style.display = "block";
        caja_trasera_register.style.opacity = "1";
        caja_trasera_login.style.opacity = "0";
      } else {
        formulario_register.style.display = "none";
        contenedor_login_register.style.left = "0px";
        formulario_login.style.display = "block";
        caja_trasera_register.style.display = "block";
        caja_trasera_login.style.display = "none";
  }
}

  function register() { 

      if (window.innerWidth > 850) {
          formulario_register.style.display = "block";
          contenedor_login_register.style.left = "410px";
          formulario_login.style.display = "none";
          caja_trasera_register.style.opacity = "0";
          caja_trasera_login.style.opacity = "1";
      } else {
          formulario_register.style.display = "block";
          contenedor_login_register.style.left = "0px";
          formulario_login.style.display = "none";
          caja_trasera_register.style.display = "none";
          caja_trasera_login.style.display = "block";
          caja_trasera_login.style.opacity = "1";
    }
  }
// --- LÓGICA MODAL ADMINISTRADOR ---
const modalAdmin = document.getElementById("modalAdmin");
const btnOpenAdmin = document.getElementById("btn-open-admin");
const btnCloseAdmin = document.getElementById("btn-close-admin");

const adminLoginForm = document.getElementById("admin-login-form");
const adminRegForm = document.getElementById("admin-register-form");

const toReg = document.getElementById("to-admin-register");
const toLogin = document.getElementById("to-admin-login");

// Abrir modal
btnOpenAdmin.addEventListener("click", (e) => {
    e.preventDefault();
    modalAdmin.style.display = "flex";
});

// Cerrar modal
btnCloseAdmin.addEventListener("click", () => {
    modalAdmin.style.display = "none";
});

// Cerrar al hacer clic fuera del cuadro
window.addEventListener("click", (e) => {
    if (e.target == modalAdmin) {
        modalAdmin.style.display = "none";
    }
});

// Conmutar Login -> Registro Admin
toReg.addEventListener("click", () => {
    adminLoginForm.style.display = "none";
    adminRegForm.style.display = "block";
});

// Conmutar Registro -> Login Admin
toLogin.addEventListener("click", () => {
    adminRegForm.style.display = "none";
    adminLoginForm.style.display = "block";
});
// =========================================================================
// CONTROL DEL ACUERDO DE DATOS PERSONALES (REGISTRO)
// =========================================================================
document.addEventListener('DOMContentLoaded', () => {
    // Buscamos el formulario de registro (ajusta la clase o id si difiere en tu proyecto)
    const formRegistro = document.querySelector('.formulario__register') || document.getElementById('form-register');

    if (formRegistro) {
        formRegistro.addEventListener('submit', function(e) {
            const inputAcepta = document.getElementById('input_acepta_terminos');

            // Si aún no ha aceptado el acuerdo a través del modal, detenemos el envío
            if (!inputAcepta || inputAcepta.value !== '1') {
                e.preventDefault(); // Detiene el envío del formulario

                // Validamos que los campos obligatorios del navegador sean válidos
                if (!formRegistro.checkValidity()) {
                    formRegistro.reportValidity();
                    return;
                }

                // Abrimos la ventana del acuerdo
                abrirAcuerdoDatos();
            }
        });
    }
});

function abrirAcuerdoDatos() {
    const modal = document.getElementById('modal-terminos');
    if (modal) {
        modal.style.display = 'flex';
        // Reiniciamos el scroll al inicio del texto
        const bodyScroll = modal.querySelector('.modal-terminos-body');
        if (bodyScroll) bodyScroll.scrollTop = 0;
    }
}

function cerrarAcuerdoDatos() {
    const modal = document.getElementById('modal-terminos');
    if (modal) {
        modal.style.display = 'none';
    }
    const inputAcepta = document.getElementById('input_acepta_terminos');
    if (inputAcepta) inputAcepta.value = '0';
}

function aceptarYRegistrar() {
    const inputAcepta = document.getElementById('input_acepta_terminos');
    if (inputAcepta) {
        inputAcepta.value = '1'; // Marcamos como aceptado obligatoriamente
    }

    // Cerramos el modal visualmente
    const modal = document.getElementById('modal-terminos');
    if (modal) modal.style.display = 'none';

    // Enviamos el formulario con éxito
    const formRegistro = document.querySelector('.formulario__register') || document.getElementById('form-register');
    if (formRegistro) {
        formRegistro.submit();
    }
}