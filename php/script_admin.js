// ============================================================
// VARIABLES GLOBALES PARA INSTANCIAS DE CHART.JS
// ============================================================
let chartVentasInstancia = null;
let chartCategoriasInstancia = null;
let chartTopLibrosInstancia = null;

// ============================================================
// NAVEGACIÓN Y CARGA INICIAL
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    const navItems = document.querySelectorAll('.sidebar-nav li');
    const sections = document.querySelectorAll('.view-section');
    const titleHeader = document.getElementById('current-view-title');

    navItems.forEach(item => {
        item.addEventListener('click', () => {
            const target = item.getAttribute('data-target');

            // 1. Alternar clase activa en el menú
            navItems.forEach(nav => nav.classList.remove('active'));
            item.classList.add('active');

            // 2. Mostrar la sección correspondiente
            sections.forEach(section => {
                section.classList.remove('active');
                if (section.id === target) {
                    section.classList.add('active');
                }
            });

            // 3. Actualizar el título de la cabecera
            const spanText = item.querySelector('span');
            if (titleHeader && spanText) {
                titleHeader.innerText = spanText.innerText;
            }

            // 4. Si se activa el dashboard, recargar los datos y gráficos
            if (target === 'dashboard') {
                cargarDatosDashboard();
            }

            // Efecto suave de transición
            const mainContent = document.querySelector('.main-content');
            if (mainContent) {
                mainContent.style.opacity = '0';
                setTimeout(() => { mainContent.style.opacity = '1'; }, 100);
            }
        });
    });

    // Soporte para activar sección desde parámetro URL (?view=pedidos, etc.)
    const urlParams = new URLSearchParams(window.location.search);
    const view = urlParams.get('view');
    if (view) {
        const targetItem = document.querySelector(`.sidebar-nav li[data-target="${view}"]`);
        if (targetItem) targetItem.click();
    } else {
        // Cargar datos del dashboard al abrir el panel
        cargarDatosDashboard();
    }

    // Botón refrescar dashboard
    const btnRef = document.getElementById('btn-refresh-dashboard');
    if (btnRef) {
        btnRef.addEventListener('click', cargarDatosDashboard);
    }
});

// ============================================================
// LÓGICA DE DATOS DEL DASHBOARD
// ============================================================
async function cargarDatosDashboard() {
    try {
        const respuesta = await fetch('api_dashboard.php');
        const data = await respuesta.json();

        if (data.status !== 'success') {
            console.error('Error al obtener datos del dashboard:', data.message);
            return;
        }

        // 1. TARJETAS KPI
        const elUsuarios = document.getElementById('kpi-usuarios');
        const elActivos = document.getElementById('kpi-activos-24h');
        const elPedidos = document.getElementById('kpi-pedidos');
        const elVentas = document.getElementById('kpi-ventas');
        const elLibros = document.getElementById('kpi-libros');
        const elStock = document.getElementById('kpi-stock');

        if (elUsuarios) elUsuarios.textContent = data.kpis.total_usuarios;
        if (elActivos) elActivos.innerHTML = `<i class="fa-solid fa-circle text-emerald-500"></i> ${data.kpis.usuarios_activos_24h} activos hoy`;
        if (elPedidos) elPedidos.textContent = data.kpis.total_pedidos;
        if (elVentas) elVentas.textContent = '$' + Number(data.kpis.total_ventas).toLocaleString('es-CO', { minimumFractionDigits: 2 });
        if (elLibros) elLibros.textContent = data.kpis.total_libros;
        if (elStock) elStock.textContent = `${data.kpis.stock_total} unidades en stock`;

        // 2. DIBUJAR GRÁFICOS (con micro-delay para que el canvas tenga su ancho real)
        setTimeout(() => {
            renderizarGraficoVentas(data.ventas_dias);
            renderizarGraficoCategorias(data.categorias);
            renderizarGraficoTopLibros(data.top_libros);
        }, 50);

        // 3. TABLA DE ACTIVIDAD
        renderizarTablaActividad(data.actividad_reciente);

    } catch (error) {
        console.error('Error comunicándose con api_dashboard.php:', error);
    }
}

function renderizarGraficoVentas(ventasDias) {
    const ctx = document.getElementById('chartVentas');
    if (!ctx) return;

    const labels = (ventasDias && ventasDias.length) ? ventasDias.map(v => v.dia) : ['Sin ventas'];
    const valores = (ventasDias && ventasDias.length) ? ventasDias.map(v => v.ventas) : [0];

    if (chartVentasInstancia) chartVentasInstancia.destroy();

    chartVentasInstancia = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Ventas ($)',
                data: valores,
                borderColor: '#b45309',
                backgroundColor: 'rgba(180, 83, 9, 0.12)',
                tension: 0.35,
                fill: true,
                pointBackgroundColor: '#b45309',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });
}

function renderizarGraficoCategorias(categorias) {
    const ctx = document.getElementById('chartCategorias');
    if (!ctx) return;

    const labels = (categorias && categorias.length) ? categorias.map(c => c.categoria) : ['Sin datos'];
    const valores = (categorias && categorias.length) ? categorias.map(c => c.cantidad) : [1];

    if (chartCategoriasInstancia) chartCategoriasInstancia.destroy();

    chartCategoriasInstancia = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: valores,
                backgroundColor: [
                    '#b45309', '#d97706', '#f59e0b', '#fbbf24', 
                    '#78350f', '#451a03', '#92400e', '#a16207'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12 } }
            }
        }
    });
}

function renderizarGraficoTopLibros(topLibros) {
    const ctx = document.getElementById('chartTopLibros');
    if (!ctx) return;

    const tieneDatos = topLibros && topLibros.length > 0;
    const labels = tieneDatos 
        ? topLibros.map(l => l.titulo.length > 22 ? l.titulo.substring(0, 22) + '...' : l.titulo) 
        : ['Sin ventas registradas'];
    const valores = tieneDatos 
        ? topLibros.map(l => l.vendidos) 
        : [0];

    // Colores cálidos de la identidad de Aurora
    const colores = ['#b45309', '#d97706', '#f59e0b', '#fbbf24', '#78350f'];

    if (chartTopLibrosInstancia) chartTopLibrosInstancia.destroy();

    chartTopLibrosInstancia = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Unidades vendidas',
                data: valores,
                backgroundColor: tieneDatos ? colores.slice(0, valores.length) : '#e7e5e4',
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ` ${context.raw} unidad(es) vendida(s)`;
                        }
                    }
                }
            },
            scales: {
                x: { 
                    beginAtZero: true, 
                    ticks: { 
                        precision: 0,
                        stepSize: 1
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                y: { 
                    grid: { display: false },
                    ticks: { font: { weight: '600' } }
                }
            }
        }
    });
}

function renderizarTablaActividad(actividad) {
    const tbody = document.getElementById('tbody-actividad');
    if (!tbody) return;

    if (!actividad || actividad.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="text-center py-6 text-stone-400">No hay actividad registrada aún.</td></tr>`;
        return;
    }

    tbody.innerHTML = actividad.map(act => {
        let badgeClass = 'badge-general';
        let icono = 'fa-bell';

        if (act.tipo === 'LOGIN') {
            badgeClass = 'badge-login';
            icono = 'fa-arrow-right-to-bracket';
        } else if (act.tipo === 'COMPRA') {
            badgeClass = 'badge-compra';
            icono = 'fa-bag-shopping';
        }

        return `
            <tr>
                <td class="font-semibold text-stone-800">${act.usuario}</td>
                <td>
                    <span class="activity-badge ${badgeClass}">
                        <i class="fa-solid ${icono} mr-1"></i> ${act.tipo}
                    </span>
                </td>
                <td class="text-stone-600 text-xs">${act.descripcion}</td>
                <td class="text-stone-400 text-xs whitespace-nowrap">${act.fecha}</td>
            </tr>
        `;
    }).join('');
}

// ============================================================
// FUNCIONES DE MODALES Y GESTIÓN DE LIBROS
// ============================================================
function toggleModal(id, show) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = show ? 'block' : 'none';
}

function filtrarLibros() {
    const input = document.getElementById('busqueda-libro').value.toLowerCase();
    const rows = document.querySelectorAll('#tabla-libros tbody tr');

    rows.forEach(row => {
        const texto = row.innerText.toLowerCase();
        row.style.display = texto.includes(input) ? '' : 'none';
    });
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = "none";
    }
};

function prepararNuevoLibro() {
    document.getElementById('form-libro').reset();
    document.getElementById('edit-id').value = "";
    document.getElementById('modal-titulo-texto').innerText = "Registrar Nuevo Ejemplar";
    document.getElementById('btn-submit-modal').innerText = "Guardar Libro";
    document.getElementById('edit-image').required = true;
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
    
    document.getElementById('edit-id').value = libro.id;
    document.getElementById('edit-titulo').value = libro.titulo;
    document.getElementById('edit-author').value = libro.author;
    document.getElementById('edit-category').value = libro.category;
    document.getElementById('edit-price').value = libro.price;
    document.getElementById('edit-stock').value = libro.stock;
    document.getElementById('edit-description').value = libro.description;
    
    document.getElementById('edit-image').required = false;
    document.getElementById('aviso-imagen').style.display = "block";
    toggleModal('modal-libro', true);
}

// ============================================================
// MENÚ DESPLEGABLE Y MODO OSCURO DEL ADMINISTRADOR
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    const btnToggleSettings = document.getElementById('btn-toggle-admin-settings');
    const adminDropdown = document.getElementById('admin-dropdown-menu');
    const settingsContainer = document.getElementById('admin-settings-container');
    const darkTrigger = document.getElementById('admin-dark-mode-trigger');
    const themeSwitch = document.getElementById('admin-theme-switch');
    const themeIcon = document.getElementById('admin-theme-icon');

    // Desplegable de ajustes
    if (btnToggleSettings && adminDropdown) {
        btnToggleSettings.addEventListener('click', (e) => {
            e.stopPropagation();
            adminDropdown.classList.toggle('show');
        });

        window.addEventListener('click', (e) => {
            if (settingsContainer && !settingsContainer.contains(e.target)) {
                adminDropdown.classList.remove('show');
            }
        });
    }

    // Modo oscuro recordado en localStorage
    const savedAdminTheme = localStorage.getItem('aurora_admin_theme');
    if (savedAdminTheme === 'dark') {
        document.body.classList.add('dark-admin');
        if (themeSwitch) themeSwitch.classList.add('active');
        if (themeIcon) {
            themeIcon.classList.replace('fa-moon', 'fa-sun');
            themeIcon.classList.replace('text-amber-500', 'text-yellow-400');
        }
    }

    // Alternar modo oscuro al hacer clic
    if (darkTrigger && themeSwitch) {
        darkTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isDark = document.body.classList.toggle('dark-admin');
            themeSwitch.classList.toggle('active', isDark);

            if (isDark) {
                localStorage.setItem('aurora_admin_theme', 'dark');
                if (themeIcon) themeIcon.classList.replace('fa-moon', 'fa-sun');
            } else {
                localStorage.setItem('aurora_admin_theme', 'light');
                if (themeIcon) themeIcon.classList.replace('fa-sun', 'fa-moon');
            }
        });
    }
});