/* ==========================================================
   idiomas.js - Cambio de idioma Español <-> Inglés
   Aurora.Libros (login, tienda y panel de administrador)

   Cómo funciona: recorre el texto de la página y, si el texto
   está en el diccionario, lo cambia al inglés. Al volver a
   Español restaura el texto original. No hay que tocar el HTML.
   ========================================================== */
(function () {
    'use strict';

    // Evita que el traductor automático de Chrome vuelva a traducir la página
    document.documentElement.setAttribute('translate', 'no');
    if (!document.querySelector('meta[name="google"]')) {
        var metaNT = document.createElement('meta');
        metaNT.name = 'google';
        metaNT.content = 'notranslate';
        document.head.appendChild(metaNT);
    }

    var STORAGE_KEY = 'aurora_lang';
    var ATTRS = ['placeholder', 'title', 'alt', 'aria-label'];
    var SKIP = { SCRIPT: 1, STYLE: 1, TEXTAREA: 1, NOSCRIPT: 1 };

    /* ---------------- DICCIONARIO ESPAÑOL -> INGLÉS ---------------- */
    var DICT = {
        /* ===== LOGIN (index.php) ===== */
        'Iniciar Sesión': 'Log In',
        '¿Ya eres lector?': 'Already a reader?',
        'Inicia sesión para continuar tu historia.': 'Log in to continue your story.',
        '¿Nuevo por aquí?': 'New here?',
        'Regístrate y descubre nuevos mundos.': 'Sign up and discover new worlds.',
        'Registrarme': 'Sign Up',
        'Entrar': 'Sign In',
        'Correo Electrónico': 'Email',
        'Contraseña': 'Password',
        'Nombre Completo': 'Full Name',
        'Nombre de Usuario': 'Username',
        'Acceder al Catálogo': 'Enter the Catalog',
        'Acceso Administrador': 'Admin Access',
        'Nueva Cuenta': 'New Account',
        'Unirse a la Librería': 'Join the Bookstore',
        'Acceso administrador': 'Admin access',
        'Correo Administrativo': 'Admin Email',
        '¿Nuevo admin?': 'New admin?',
        'Regístrate': 'Sign up',
        'Admin: Registro': 'Admin: Sign Up',
        'Usuario': 'User',
        'Cargo en Librería': 'Position at the Bookstore',
        'Crear Cuenta': 'Create Account',
        'Mínimo 8 caracteres, con mayúscula, minúscula y número.': 'At least 8 characters, with an uppercase letter, a lowercase letter and a number.',
        'Requisitos de la contraseña:': 'Password requirements:',
        'Mínimo 8 caracteres': 'At least 8 characters',
        'Una letra mayúscula': 'One uppercase letter',
        'Una letra minúscula': 'One lowercase letter',
        'Un número': 'One number',
        '¡Contraseña lista!': 'Password ready!',
        'Completa los requisitos de la contraseña para continuar': 'Complete the password requirements to continue',
        '¿Ya tienes cuenta?': 'Already have an account?',
        'Inicia Sesión': 'Log In',

        /* ===== TIENDA (bienvenida.php) ===== */
        'Librería Aurora | Tu Mundo en Letras': 'Aurora Bookstore | Your World in Letters',
        'Busca tu próximo libro...': 'Search your next book...',
        'Favoritos': 'Favorites',
        'Carrito': 'Cart',
        'Configuración': 'Settings',
        'Cerrar Sesión': 'Log Out',
        'Lector Aurora': 'Aurora Reader',
        'Mi Cuenta': 'My Account',
        'Mi Perfil': 'My Profile',
        'Configuración de Cuenta': 'Account Settings',
        'Direcciones de Entrega': 'Delivery Addresses',
        'Notificaciones': 'Notifications',
        'Modo Oscuro': 'Dark Mode',
        'Inicio': 'Home',
        'Explorar Todo (A - Z)': 'Browse All (A - Z)',
        'Por Categorías': 'By Category',
        'Explorar Selección': 'Explore Selection',
        'Nuevos Mundos': 'New Worlds',
        'Aventuras Sin Límites': 'Adventures Without Limits',
        'Ciencia ficción y fantasía': 'Science fiction and fantasy',
        'Comunidad Aurora': 'Aurora Community',
        'Promoción Especial': 'Special Offer',
        'Noches de Lectura Inolvidables': 'Unforgettable Reading Nights',
        'Redescubre las grandes obras': 'Rediscover the great works',
        'Redescubre las grandes obras de la literatura universal con hasta 30% de descuento. Sumérgete en historias eternas.': 'Rediscover the great works of world literature with up to 30% off. Immerse yourself in timeless stories.',
        'Autoayuda': 'Self-Help',
        'Historia': 'History',
        'Arte': 'Art',
        'No se encontraron eventos coincidentes en el registro de auditoría.': 'No matching events found in the audit log.',
        'Tu Próxima Historia, Cada Mes': 'Your Next Story, Every Month',
        'Suscripción personalizada': 'Personalized subscription',
        'Recibe una selección única en casa. Suscríbete y convierte la lectura en tu mejor hábito mensual.': 'Receive a unique selection at home. Subscribe and make reading your best monthly habit.',
        'Explora nuevos mundos con nuestra colección exclusiva. Descubre universos extraordinarios donde todo es posible.': 'Explore new worlds with our exclusive collection. Discover extraordinary universes where everything is possible.',
        'Novedades': 'New Arrivals',
        'Agregados Recientemente': 'Recently Added',
        'Los últimos títulos incorporados a las estanterías de Aurora.': 'The latest titles added to the Aurora shelves.',
        'Ver todos': 'View all',
        'Nuevo': 'New',
        'Para Ti': 'For You',
        'Recomendaciones Especiales': 'Special Recommendations',
        'Sugerencias basadas en tendencias y selecciones editoriales destacadas.': 'Suggestions based on trends and featured editorial picks.',
        'Catálogo Completo (A - Z)': 'Full Catalog (A - Z)',
        'Todos los títulos ordenados alfabéticamente por título.': 'All titles sorted alphabetically by title.',
        'No se encontraron libros que coincidan con la búsqueda.': 'No books found matching your search.',
        'Filtrar por Género Literario': 'Filter by Literary Genre',
        'No hay libros registrados en esta categoría.': 'There are no books registered in this category.',
        'Mis Favoritos': 'My Favorites',
        'Aún no tienes libros guardados.': "You don't have any saved books yet.",
        'Mover al carrito': 'Move to cart',
        'Tu carrito está vacío.': 'Your cart is empty.',
        'Revisar Pedido': 'Review Order',
        'Precio Online': 'Online Price',
        'Añadir al carrito': 'Add to cart',
        'Resumen de tu Compra': 'Order Summary',
        'Total a pagar:': 'Total to pay:',
        'Cancelar': 'Cancel',
        'Procesando...': 'Processing...',
        'Confirmar y Pagar': 'Confirm and Pay',
        'Sesión bloqueada': 'Session locked',
        'Por tu seguridad, ingresa tu contraseña para continuar.': 'For your security, enter your password to continue.',
        'Desbloquear': 'Unlock',
        'Cerrar sesión': 'Log out',
        '98% compatible': '98% match',
        'Selección Aurora': 'Aurora Pick',
        'Más leído': 'Most read',
        'Tendencia': 'Trending',
        'Sin autor': 'No author',

        /* ===== PANEL ADMIN (index_admin.php) ===== */
        'Aurora.Libros | Panel de Control': 'Aurora.Libros | Control Panel',
        'Productos': 'Products',
        'Pedidos / Compras': 'Orders / Purchases',
        'Salir': 'Exit',
        'Panel de Control': 'Control Panel',
        'Resumen estadístico y actividad en tiempo real de Aurora.Libros': 'Statistical summary and real-time activity of Aurora.Libros',
        'Actualizar': 'Refresh',
        'Bienvenido,': 'Welcome,',
        'Administrador': 'Administrator',
        'Mi Perfil y Cargo': 'My Profile and Position',
        'Seguridad y Contraseña': 'Security and Password',
        'Ajustes del Catálogo': 'Catalog Settings',
        'Registro de Actividad': 'Activity Log',
        'Usuarios Registrados': 'Registered Users',
        'Pedidos Completados': 'Completed Orders',
        'Total de compras': 'Total purchases',
        'Ingresos Totales': 'Total Revenue',
        'Ventas acumuladas': 'Accumulated sales',
        'Títulos en Catálogo': 'Titles in Catalog',
        'Evolución de Ventas (Últimos 30 días)': 'Sales Evolution (Last 30 days)',
        'Libros por Categoría': 'Books by Category',
        'Top 5 Libros Más Vendidos': 'Top 5 Best-Selling Books',
        'Actividad Reciente de Clientes': 'Recent Customer Activity',
        'Acción': 'Action',
        'Detalle': 'Detail',
        'Fecha': 'Date',
        'Cargando actividad...': 'Loading activity...',
        'Libros en Inventario': 'Books in Inventory',
        'Añadir Nuevo Libro': 'Add New Book',
        'Buscar por título, autor o categoría...': 'Search by title, author or category...',
        'Imagen': 'Image',
        'Título': 'Title',
        'Autor': 'Author',
        'Categoría': 'Category',
        'Precio': 'Price',
        'Acciones': 'Actions',
        'Historial de Ventas Agrupado': 'Grouped Sales History',
        'ID Pedido': 'Order ID',
        'Fecha y Hora': 'Date and Time',
        'Cliente': 'Customer',
        'Detalle de Productos': 'Product Details',
        'Total Pagado': 'Total Paid',
        'Libros (Excel)': 'Books (Excel)',
        'Libros (PDF)': 'Books (PDF)',
        'Usuarios (Excel)': 'Users (Excel)',
        'Usuarios (PDF)': 'Users (PDF)',
        'Ventas (Excel)': 'Sales (Excel)',
        'Ventas (PDF)': 'Sales (PDF)',
        'Exportar Excel': 'Export Excel',
        'Exportar PDF': 'Export PDF',
        'Registrar Nuevo Ejemplar': 'Register New Copy',
        'Título del Libro': 'Book Title',
        'Precio ($)': 'Price ($)',
        'Stock (Cantidad)': 'Stock (Quantity)',
        'Portada (Imagen)': 'Cover (Image)',
        '* Dejar vacío para conservar la imagen actual': '* Leave empty to keep the current image',
        'Descripción': 'Description',
        'Guardar Libro': 'Save Book',
        'Respaldo de Base de Datos': 'Database Backup',
        'Genera y descarga un archivo SQL completo con todas las tablas (libros, usuarios, pedidos y actividad) para proteger la información de la librería.': "Generate and download a complete SQL file with all tables (books, users, orders and activity) to protect the bookstore's information.",
        'Descargar Copia de Seguridad (.SQL)': 'Download Backup (.SQL)',
        'Restaurar o Reparar Base de Datos': 'Restore or Repair Database',
        'Selecciona un archivo': 'Select a',
        'de copia de seguridad. Esta acción': 'backup file. This action will',
        'reemplazará y sobreescribirá': 'replace and overwrite',
        'por completo todas las tablas y datos actuales del sistema.': 'all current tables and data in the system completely.',
        'Seleccionar archivo .SQL': 'Select .SQL file',
        'Restaurar Base de Datos': 'Restore Database',
        'AUDITORÍA ACTIVA (FULL AUDIT TRAIL)': 'ACTIVE AUDIT (FULL AUDIT TRAIL)',
        'Registro de Eventos y Actividad (Logs)': 'Event and Activity Log',
        'Filtrar por usuario, acción o IP...': 'Filter by user, action or IP...',
        'Todos': 'All',
        'Sesiones': 'Sessions',
        'Compras': 'Purchases',
        'Carrito / Favoritos': 'Cart / Favorites',
        'Sistema / DB': 'System / DB',
        'Usuario / Actor': 'User / Actor',
        'Nivel / Evento': 'Level / Event',
        'Descripción del Evento': 'Event Description',
        'Dirección IP': 'IP Address',
        'Cargando registro de auditoría...': 'Loading audit log...',

        /* ===== CATEGORÍAS DE LIBROS ===== */
        'Literatura': 'Literature',
        'Académicos': 'Academic',
        'Misterio': 'Mystery',
        'Ciencia Ficción': 'Science Fiction',
        'Infantiles': "Children's",
        'General': 'General'
    };

    /* Textos que llevan números o variables */
    var REGEX = [
        [/^(\d+) ejemplares disponibles$/, '$1 copies available'],
        [/^Libros en "(.*)"$/, 'Books in "$1"'],
        [/^(\d+) de (\d+) requisitos cumplidos$/, '$1 of $2 requirements met'],
        [/^(\d+) activos hoy$/, '$1 active today'],
        [/^(\d+) unidades en stock$/, '$1 units in stock']
    ];

    /* ---------------- ESTADO ---------------- */
    var lang = 'es';
    try { lang = localStorage.getItem(STORAGE_KEY) || 'es'; } catch (e) {}

    var textStore = new WeakMap(); // nodo de texto -> {orig, shown}
    var attrStore = new WeakMap(); // elemento -> { atributo: {orig, shown} }
    var observer = null;

    // Versión del diccionario en minúsculas (no importa cómo esté escrito el texto)
    var DICT_LC = {};
    Object.keys(DICT).forEach(function (k) { DICT_LC[k.toLowerCase()] = DICT[k]; });

    function translate(text) {
        var t = text.replace(/\s+/g, ' ').trim();
        if (!t) return null;
        var key = t.toLowerCase();
        if (Object.prototype.hasOwnProperty.call(DICT_LC, key)) return DICT_LC[key];
        for (var i = 0; i < REGEX.length; i++) {
            if (REGEX[i][0].test(t)) return t.replace(REGEX[i][0], REGEX[i][1]);
        }
        return null;
    }

    function withSpaces(original, translated) {
        var lead = original.match(/^\s*/)[0];
        var trail = original.match(/\s*$/)[0];
        return lead + translated + trail;
    }

    /* ---------------- NODOS DE TEXTO ---------------- */
    function applyTextNode(node) {
        var rec = textStore.get(node);
        var current = node.nodeValue;

        // Nodo que ya traducimos nosotros y nadie más tocó
        if (rec && current === rec.shown) {
            if (lang === 'es') {
                node.nodeValue = rec.orig;
                textStore.delete(node);
            }
            return;
        }

        // Texto nuevo, o Vue lo cambió por su cuenta
        if (rec) textStore.delete(node);

        if (lang === 'en') {
            var tr = translate(current);
            if (tr !== null) {
                var shown = withSpaces(current, tr);
                if (shown !== current) {
                    textStore.set(node, { orig: current, shown: shown });
                    node.nodeValue = shown;
                }
            }
        }
    }

    /* ---------------- ATRIBUTOS (placeholder, title...) ---------------- */
    function applyAttrs(el) {
        var store = attrStore.get(el);
        for (var i = 0; i < ATTRS.length; i++) {
            var attr = ATTRS[i];
            if (!el.hasAttribute(attr)) continue;
            var current = el.getAttribute(attr);
            var rec = store && store[attr];

            if (rec && current === rec.shown) {
                if (lang === 'es') {
                    el.setAttribute(attr, rec.orig);
                    delete store[attr];
                }
                continue;
            }
            if (rec) delete store[attr];

            if (lang === 'en') {
                var tr = translate(current);
                if (tr !== null && tr !== current) {
                    if (!store) { store = {}; attrStore.set(el, store); }
                    store[attr] = { orig: current, shown: tr };
                    el.setAttribute(attr, tr);
                }
            }
        }
    }

    /* ---------------- RECORRIDO DEL DOM ---------------- */
    function walk(root) {
        if (root.nodeType === 3) { applyTextNode(root); return; }
        if (root.nodeType !== 1) return;
        if (SKIP[root.nodeName]) return;
        if (root.id === 'lang-switch') return;
        applyAttrs(root);
        for (var c = root.firstChild; c; c = c.nextSibling) walk(c);
    }

    /* ---------------- OBSERVADOR (contenido dinámico de Vue/JS) ---------------- */
    function startObserving() {
        observer.observe(document.documentElement, {
            childList: true,
            subtree: true,
            characterData: true,
            attributes: true,
            attributeFilter: ATTRS
        });
    }

    function onMutations(mutations) {
        observer.disconnect(); // evita bucles con nuestros propios cambios
        mutations.forEach(function (m) {
            if (m.type === 'childList') {
                for (var i = 0; i < m.addedNodes.length; i++) walk(m.addedNodes[i]);
            } else if (m.type === 'characterData') {
                applyTextNode(m.target);
            } else if (m.type === 'attributes') {
                applyAttrs(m.target);
            }
        });
        startObserving();
    }

    /* ---------------- BOTÓN ES | EN ---------------- */
    function buildSwitch() {
        var isAdmin = /index_admin/i.test(location.pathname);

        var style = document.createElement('style');
        style.textContent =
            '#lang-switch{position:fixed;bottom:20px;' + (isAdmin ? 'right' : 'left') + ':20px;z-index:99999;' +
            'display:flex;background:#fff;border:1px solid #e7e5e4;border-radius:999px;' +
            'box-shadow:0 4px 14px rgba(0,0,0,.18);overflow:hidden;font-family:Inter,Arial,sans-serif}' +
            '#lang-switch button{border:0;background:transparent;padding:8px 14px;font-size:12px;' +
            'font-weight:700;letter-spacing:.05em;cursor:pointer;color:#57534e}' +
            '#lang-switch button.active{background:#b45309;color:#fff}';
        document.head.appendChild(style);

        var box = document.createElement('div');
        box.id = 'lang-switch';
        box.innerHTML =
            '<button type="button" data-l="es" aria-label="Español">ES</button>' +
            '<button type="button" data-l="en" aria-label="English">EN</button>';
        box.addEventListener('click', function (e) {
            var l = e.target && e.target.getAttribute('data-l');
            if (l) setLang(l);
        });
        document.body.appendChild(box);
        refreshSwitch();
    }

    function refreshSwitch() {
        var btns = document.querySelectorAll('#lang-switch button');
        for (var i = 0; i < btns.length; i++) {
            btns[i].classList.toggle('active', btns[i].getAttribute('data-l') === lang);
        }
    }

    /* ---------------- CAMBIO DE IDIOMA ---------------- */
    function setLang(l) {
        lang = l;
        try { localStorage.setItem(STORAGE_KEY, l); } catch (e) {}
        document.documentElement.lang = l;
        observer.disconnect();
        walk(document.documentElement);
        startObserving();
        refreshSwitch();
    }

    function init() {
        observer = new MutationObserver(onMutations);
        buildSwitch();
        document.documentElement.lang = lang;
        walk(document.documentElement);
        startObserving();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();