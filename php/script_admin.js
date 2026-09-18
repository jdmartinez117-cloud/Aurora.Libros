document.addEventListener('DOMContentLoaded', () => {
    const navItems = document.querySelectorAll('.sidebar-nav li');
    const sections = document.querySelectorAll('.view-section');
    const titleHeader = document.getElementById('current-view-title');

    navItems.forEach(item => {
        item.addEventListener('click', () => {
            const target = item.getAttribute('data-target');

            // 1. Remover clase activa de todos los links
            navItems.forEach(nav => nav.classList.remove('active'));
            // 2. Añadir clase activa al seleccionado
            item.classList.add('active');

            // 3. Ocultar todas las secciones y mostrar la meta
            sections.forEach(section => {
                section.classList.remove('active');
                if(section.id === target) {
                    section.classList.add('active');
                }
            });

            // 4. Cambiar título de la cabecera
            titleHeader.innerText = item.querySelector('span').innerText;
            
            // Efecto sutil de carga
            document.querySelector('.main-content').style.opacity = '0';
            setTimeout(() => {
                document.querySelector('.main-content').style.opacity = '1';
            }, 100);
        });
    });
    // --- Lógica para activar pestaña desde la URL ---
const urlParams = new URLSearchParams(window.location.search);
const view = urlParams.get('view');

if (view) {
    // Buscamos el item del menú que coincida con el data-target de la URL
    const targetItem = document.querySelector(`.sidebar-nav li[data-target="${view}"]`);
    if (targetItem) {
        targetItem.click(); // Simulamos un clic para que cambie la vista
    }
}
});

// --- PEGAR AQUÍ (FUERA DEL DOMContentLoaded) ---

// Función para abrir/cerrar modal
function toggleModal(id, show) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.style.display = show ? 'block' : 'none';
    }
}

// Filtrado de libros en la tabla (Buscador)
function filtrarLibros() {
    let input = document.getElementById('busqueda-libro').value.toLowerCase();
    let rows = document.querySelectorAll('#tabla-libros tbody tr');

    rows.forEach(row => {
        // Solo buscamos en las columnas de texto (Título, Autor, Categoría)
        let texto = row.innerText.toLowerCase();
        row.style.display = texto.includes(input) ? '' : 'none';
    });
}

// Cerrar modal al hacer clic en el fondo oscuro
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = "none";
    }
}

function prepararNuevoLibro() {
    document.getElementById('form-libro').reset(); // Limpia todos los campos
    document.getElementById('edit-id').value = ""; // Asegura que el ID esté vacío
    
    document.getElementById('modal-titulo-texto').innerText = "Registrar Nuevo Ejemplar";
    document.getElementById('btn-submit-modal').innerText = "Guardar Libro";
    
    document.getElementById('edit-image').required = true; // La imagen es obligatoria al crear
    document.getElementById('aviso-imagen').style.display = "none";
    
    toggleModal('modal-libro', true);
}


function eliminarLibro(id) {
    if (confirm("¿Estás seguro de que deseas eliminar este libro?")) {
        window.location.href = "eliminar_libro_be.php?id=" + id;
    }
}

function abrirEditarLibro(libro) {
    document.getElementById('modal-titulo-texto').innerText = "Editar Libro";
    document.getElementById('btn-submit-modal').innerText = "Actualizar Cambios";
    
    // Rellenamos los campos usando los nombres que vienen de la base de datos
    document.getElementById('edit-id').value = libro.id;
    document.getElementById('edit-titulo').value = libro.titulo;
    document.getElementById('edit-author').value = libro.author;
    document.getElementById('edit-category').value = libro.category;
    document.getElementById('edit-price').value = libro.price;
    document.getElementById('edit-stock').value = libro.stock;
    document.getElementById('edit-description').value = libro.description;
    
    // Al editar, la imagen NO es obligatoria (para que no se borre la anterior)
    document.getElementById('edit-image').required = false;
    document.getElementById('aviso-imagen').style.display = "block";
    
    toggleModal('modal-libro', true);
}

// AJUSTE IMPORTANTE: Cuando se presiona el botón "Añadir Nuevo Libro", 
// debemos limpiar el formulario para que no aparezcan los datos del anterior editado.
function limpiarFormulario() {
    document.getElementById('form-libro').reset();
    document.getElementById('edit-id').value = "";
    document.querySelector('.modal-header h4').innerText = "Registrar Nuevo Ejemplar";
    document.getElementById('btn-submit-modal').innerText = "Guardar Libro";
    document.querySelector('input[name="image"]').required = true;
    document.getElementById('aviso-imagen').style.display = "none";
}
// ===================================================
// LÓGICA DE MENÚ DE CONFIGURACIÓN Y MODO OSCURO (ADMIN)
// ===================================================
document.addEventListener('DOMContentLoaded', () => {
    const btnToggleSettings = document.getElementById('btn-toggle-admin-settings');
    const adminDropdown = document.getElementById('admin-dropdown-menu');
    const settingsContainer = document.getElementById('admin-settings-container');
    const darkTrigger = document.getElementById('admin-dark-mode-trigger');
    const themeSwitch = document.getElementById('admin-theme-switch');
    const themeIcon = document.getElementById('admin-theme-icon');

    // 1. Alternar apertura / cierre del desplegable
    if (btnToggleSettings && adminDropdown) {
        btnToggleSettings.addEventListener('click', (e) => {
            e.stopPropagation();
            adminDropdown.classList.toggle('show');
        });

        // Cerrar si hace clic fuera del contenedor
        window.addEventListener('click', (e) => {
            if (settingsContainer && !settingsContainer.contains(e.target)) {
                adminDropdown.classList.remove('show');
            }
        });
    }

    // 2. Comprobar preferencia guardada en localStorage
    const savedAdminTheme = localStorage.getItem('aurora_admin_theme');
    if (savedAdminTheme === 'dark') {
        document.body.classList.add('dark-admin');
        if (themeSwitch) themeSwitch.classList.add('active');
        if (themeIcon) {
            themeIcon.classList.replace('fa-moon', 'fa-sun');
            themeIcon.classList.replace('text-amber-500', 'text-yellow-400');
        }
    }

    // 3. Conmutar Modo Oscuro al hacer clic en el botón / interruptor
    if (darkTrigger && themeSwitch) {
        darkTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isDark = document.body.classList.toggle('dark-admin');
            themeSwitch.classList.toggle('active', isDark);

            if (isDark) {
                localStorage.setItem('aurora_admin_theme', 'dark');
                if (themeIcon) {
                    themeIcon.classList.replace('fa-moon', 'fa-sun');
                }
            } else {
                localStorage.setItem('aurora_admin_theme', 'light');
                if (themeIcon) {
                    themeIcon.classList.replace('fa-sun', 'fa-moon');
                }
            }
        });
    }
});