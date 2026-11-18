/* ==========================================================================
   AURORA.ADMIN - Traducción ES/EN del registro de auditoría (logs)
   Traduce los textos que vienen de la base de datos dentro de #tbody-actividad.
   ========================================================================== */

(function () {
    var tbody = document.getElementById('tbody-actividad');
    if (!tbody) return;

    // ---- Descripciones de eventos (regex en español -> texto en inglés) ----
    var REGLAS = [
        [/^Inicio de sesión en la plataforma$/i,                 'Logged in to the platform'],
        [/^Cerr(?:ó|o) sesión.*$/i,                              'Logged out of the platform'],
        [/^Guardó el libro ID #(\d+) en sus favoritos$/i,        'Saved book ID #$1 to favorites'],
        [/^Eliminó el libro ID #(\d+) de sus favoritos$/i,       'Removed book ID #$1 from favorites'],
        [/^Añadió el libro ID #(\d+) al carrito de compras$/i,   'Added book ID #$1 to the shopping cart'],
        [/^Eliminó el libro ID #(\d+) del carrito de compras$/i, 'Removed book ID #$1 from the shopping cart'],
        [/^Realizó una compra.*?#(\d+).*$/i,                     'Completed a purchase (Order #$1)'],
        [/^Copia de seguridad de (\S+) descargada$/i,            'Backup of $1 downloaded'],
        [/^Base de datos (\S+) restaurada con éxito$/i,          'Database $1 restored successfully'],
        [/^Nuevo usuario registrado.*$/i,                        'New user registered']
    ];

    // ---- Etiquetas (badges) y nombres de actor ----
    var TERMINOS = {
        'FAVORITO': 'FAVORITE',
        'RESTAURACION': 'RESTORE',
        'RESTAURACIÓN': 'RESTORE',
        'COMPRA': 'PURCHASE',
        'CARRITO': 'Cart',
        'SESION': 'LOGIN',
        'SESIÓN': 'LOGIN',
        'Sistema / Admin': 'System / Admin',
        'Sistema': 'System',
        'Visitante': 'Visitor'
    };

    var originales = new WeakMap();
    var idioma = detectarIdioma();

    function detectarIdioma() {
        var claves = ['idioma', 'lang', 'language', 'aurora_lang', 'aurora_idioma', 'aurora_language', 'selectedLanguage'];
        try {
            for (var i = 0; i < claves.length; i++) {
                var v = localStorage.getItem(claves[i]);
                if (v) {
                    v = v.toLowerCase();
                    if (v.indexOf('en') === 0) return 'en';
                    if (v.indexOf('es') === 0) return 'es';
                }
            }
        } catch (e) {}
        var htmlLang = (document.documentElement.lang || '').toLowerCase();
        return htmlLang.indexOf('en') === 0 ? 'en' : 'es';
    }

    function traducir(texto) {
        var limpio = texto.trim();
        if (!limpio) return texto;

        if (Object.prototype.hasOwnProperty.call(TERMINOS, limpio)) {
            return texto.replace(limpio, TERMINOS[limpio]);
        }
        for (var i = 0; i < REGLAS.length; i++) {
            if (REGLAS[i][0].test(limpio)) {
                return texto.replace(limpio, limpio.replace(REGLAS[i][0], REGLAS[i][1]));
            }
        }
        return texto;
    }

    // Recorre solo los nodos de texto para no perder los iconos <i> de cada celda
    function aplicar() {
        var walker = document.createTreeWalker(tbody, NodeFilter.SHOW_TEXT, null);
        var nodo;
        while ((nodo = walker.nextNode())) {
            if (!nodo.nodeValue.trim()) continue;
            if (!originales.has(nodo)) originales.set(nodo, nodo.nodeValue);
            var original = originales.get(nodo);
            var nuevo = (idioma === 'en') ? traducir(original) : original;
            if (nodo.nodeValue !== nuevo) nodo.nodeValue = nuevo;
        }
    }

    // Cuando la tabla se vuelve a dibujar (carga, filtros, actualizar), se traduce otra vez
    new MutationObserver(aplicar).observe(tbody, { childList: true, subtree: true });

    // Detecta clics en los botones ES / EN del selector de idioma
    document.addEventListener('click', function (e) {
        var el = e.target;
        for (var i = 0; i < 3 && el; i++, el = el.parentElement) {
            var t = (el.textContent || '').trim().toUpperCase();
            if (t === 'EN') { idioma = 'en'; setTimeout(aplicar, 0); return; }
            if (t === 'ES') { idioma = 'es'; setTimeout(aplicar, 0); return; }
        }
    }, true);

    aplicar();
})();