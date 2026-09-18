/* ==========================================================================
   AURORA.LIBROS - CONTROLADOR PRINCIPAL DEL CLIENTE (VUE 3)
   ========================================================================== */

const { createApp, ref, computed, onMounted, onUnmounted } = Vue;

createApp({
    setup() {

        /* ==================================================================
           1. DATOS ESTÁTICOS (SLIDERS Y CATEGORÍAS)
           ================================================================== */
        const slides = ref([
            { 
                tag: 'PROMOCIÓN ESPECIAL',
                title: 'Noches de Lectura Inolvidables', 
                subtitle: 'Redescubre las grandes obras',
                desc: 'Redescubre las grandes obras de la literatura universal con hasta 30% de descuento. Sumérgete en historias eternas.', 
                img: 'https://images.unsplash.com/photo-1516979187457-637abb4f9353?q=80&w=1400' 
            },
            { 
                tag: 'NUEVOS MUNDOS',
                title: 'Aventuras Sin Límites', 
                subtitle: 'Ciencia ficción y fantasía',
                desc: 'Explora nuevos mundos con nuestra colección exclusiva. Descubre universos extraordinarios donde todo es posible.', 
                img: 'https://images.unsplash.com/photo-1514539079130-25950c84af65?q=80&w=1400' 
            },
            { 
                tag: 'COMUNIDAD AURORA',
                title: 'Tu Próxima Historia, Cada Mes', 
                subtitle: 'Suscripción personalizada',
                desc: 'Recibe una selección única en casa. Suscríbete y convierte la lectura en tu mejor hábito mensual.', 
                img: 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?q=80&w=1400' 
            }
        ]);

        const categories = ref([
            { name: 'Literatura',      icon: 'fa-solid fa-feather-pointed' },
            { name: 'Académicos',      icon: 'fa-solid fa-graduation-cap' },
            { name: 'Infantiles',      icon: 'fa-solid fa-child-reaching' },
            { name: 'Misterio',        icon: 'fa-solid fa-magnifying-glass' },
            { name: 'Ciencia Ficción', icon: 'fa-solid fa-rocket' },
            { name: 'Autoayuda',       icon: 'fa-solid fa-seedling' },
            { name: 'Historia',        icon: 'fa-solid fa-landmark' },
            { name: 'Arte',            icon: 'fa-solid fa-palette' }
        ]);


        

        /* ==================================================================
           2. ESTADOS DE DATOS (LIBROS, CARRITO, FAVORITOS)
           ================================================================== */
        const books = ref(typeof BOOKS_FROM_DB !== 'undefined' ? BOOKS_FROM_DB : []);
        const cart = ref([]);
        const wishlist = ref([]);


        /* ==================================================================
           3. ESTADOS DE NAVEGACIÓN Y VISIBILIDAD (MODALES Y VISTAS)
           ================================================================== */
        const activeSlide = ref(0);
        const activeGenreIndex = ref(0);
        const visibleGenres = ref(6);
        const activeCategory = ref('Literatura');
        const searchQuery = ref('');
        const selectedBook = ref(null);

        const isWishlistOpen = ref(false);
        const isCartOpen = ref(false);
        const isCheckoutOpen = ref(false);
        const isProcessing = ref(false);

        let heroTimer = null;
        let genreTimer = null;


        /* ==================================================================
           4. CONFIGURACIÓN Y MODO OSCURO
           ================================================================== */
        const isDarkMode = ref(localStorage.getItem('aurora_theme') === 'dark');
        const isSettingsOpen = ref(false);

        const toggleSettings = () => {
            isSettingsOpen.value = !isSettingsOpen.value;
                console.log("Estado del menú desplegable:", isSettingsOpen.value);
        };

        const toggleDarkMode = () => {
            isDarkMode.value = !isDarkMode.value;
            if (isDarkMode.value) {
                document.body.classList.add('dark-theme');
                localStorage.setItem('aurora_theme', 'dark');
            } else {
                document.body.classList.remove('dark-theme');
                localStorage.setItem('aurora_theme', 'light');
            }
        };


        /* ==================================================================
           5. PROPIEDADES COMPUTADAS (FILTROS Y TOTALES)
           ================================================================== */
        // Filtra tanto por la categoría seleccionada como por el texto del buscador
        const filteredBooks = computed(() => {
            return books.value.filter(b => {
                const bookCategory = b.category || '';
                const bookTitle = (b.titulo || b.title || '').toLowerCase();
                const bookAuthor = (b.author || '').toLowerCase();
                const term = searchQuery.value.toLowerCase().trim();

                const matchesCategory = bookCategory === activeCategory.value;
                const matchesSearch = term === '' || bookTitle.includes(term) || bookAuthor.includes(term);

                return matchesCategory && matchesSearch;
            });
        });

        // Suma el total del carrito con redondeo a 2 decimales
        const totalCart = computed(() => {  
            return cart.value.reduce((acc, book) => {
                return acc + Number(book.price || 0);
            }, 0).toFixed(2);
        });
                // --- 5.1 PESTAÑA ACTIVA DE LA PÁGINA DE INICIO ---
        const currentTab = ref('inicio'); // Opciones: 'inicio', 'explorar_todo', 'categorias'

        // --- 5.2 LIBROS AGREGADOS RECIENTEMENTE (Últimos añadidos por ID descendente) ---
        const recentBooks = computed(() => {
            return [...books.value]
                .sort((a, b) => Number(b.id) - Number(a.id))
                .slice(0, 4); // Muestra los 4 más recientes
        });

        // --- 5.3 RECOMENDADOS PARA TI (Estructura base para el futuro sistema) ---
        const recommendedBooks = computed(() => {
            const badges = ['98% Compatible', 'Selección Aurora', 'Más Leído', 'Tendencia'];
            return books.value.slice(0, 4).map((book, index) => ({
                ...book,
                recomBadge: badges[index % badges.length]
            }));
        });

        // --- 5.4 EXPLORAR TODO: ORDEN ALFABÉTICO (A - Z) ---
        const allBooksSorted = computed(() => {
            const term = searchQuery.value.toLowerCase().trim();
            
            // Filtra si se escribe en el buscador
            const filtered = books.value.filter(b => {
                const title = (b.titulo || b.title || '').toLowerCase();
                const author = (b.author || '').toLowerCase();
                return term === '' || title.includes(term) || author.includes(term);
            });

            // Ordena estrictamente de la A a la Z
            return filtered.sort((a, b) => {
                const nameA = (a.titulo || a.title || '').trim();
                const nameB = (b.titulo || b.title || '').trim();
                return nameA.localeCompare(nameB, 'es', { sensitivity: 'base' });
            });
        });


        /* ==================================================================
           6. COMUNICACIÓN CON EL BACKEND (API FETCH)
           ================================================================== */
        const loadData = async () => {
            try {
                const response = await fetch('api.php?action=get_data');
                const data = await response.json();
                
                // 1. Libros del inventario
                if (data.books && data.books.length > 0) {
                    books.value = data.books;
                }

                // 2. Carrito del usuario activo
                cart.value = data.cart || [];
                
                // 3. Favoritos del usuario activo
                if (data.wishlist_ids) {
                    const favIds = data.wishlist_ids.map(id => String(id));
                    wishlist.value = books.value.filter(b => favIds.includes(String(b.id)));
                }
            } catch (error) {
                console.error("Error al sincronizar datos:", error);
            }
        };


        /* ==================================================================
           7. TEMPORIZADORES Y ANIMACIONES (SLIDERS)
           ================================================================== */
        // Slider Hero Principal
        const startHero = () => {
            heroTimer = setInterval(() => { nextSlide(); }, 5000);
        };
        const pauseHero = () => clearInterval(heroTimer);
        const nextSlide = () => { 
            activeSlide.value = (activeSlide.value + 1) % slides.value.length; 
        };
        const prevSlide = () => { 
            activeSlide.value = activeSlide.value === 0 ? slides.value.length - 1 : activeSlide.value - 1; 
        };

        // Carrusel de Géneros
        const startGenreAuto = () => {
            genreTimer = setInterval(() => { nextGenre(); }, 4000);
        };
        const pauseGenreAuto = () => clearInterval(genreTimer);
        const nextGenre = () => {
            const max = categories.value.length - visibleGenres.value + 1;
            activeGenreIndex.value = (activeGenreIndex.value + 1) % max;
        };
        const prevGenre = () => {
            const max = categories.value.length - visibleGenres.value + 1;
            activeGenreIndex.value = activeGenreIndex.value === 0 ? max - 1 : activeGenreIndex.value - 1;
        };


        /* ==================================================================
           8. ACCIONES DE FAVORITOS (WISHLIST)
           ================================================================== */
        const toggleWishlist = async (book) => {
            const formData = new FormData();
            formData.append('libro_id', book.id);

            try {
                const response = await fetch('api.php?action=toggle_wishlist', {
                    method: 'POST',
                    body: formData
                });
                const res = await response.json();

                if (res.status === 'added') {
                    wishlist.value.push(book);
                } else if (res.status === 'removed') {
                    const idx = wishlist.value.findIndex(b => b.id === book.id);
                    if (idx !== -1) wishlist.value.splice(idx, 1);
                }
            } catch (error) {
                console.error("Error al alternar favorito:", error);
            }
        };

        const isInWishlist = (book) => wishlist.value.some(b => b.id === book.id);


        /* ==================================================================
           9. ACCIONES DEL CARRITO Y CHECKOUT
           ================================================================== */
        const addToCart = async (book) => {
            const formData = new FormData();
            formData.append('libro_id', book.id);

            try {
                const response = await fetch('api.php?action=add_to_cart', {
                    method: 'POST',
                    body: formData
                });
                const resData = await response.json();
                
                if (resData.status === 'success') {
                    cart.value.push(book);
                }
            } catch (error) {
                alert("No se pudo agregar al carrito. Verifica tu conexión.");
            }
        };

        const removeFromCart = async (index) => {
            const book = cart.value[index];
            if (!book) return;

            const formData = new FormData();
            formData.append('libro_id', book.id);

            try {
                const response = await fetch('api.php?action=remove_from_cart', {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    cart.value.splice(index, 1);
                }
            } catch (error) {
                console.error("Error al eliminar del carrito:", error);
            }
        };

        const processCheckout = async () => {
            isProcessing.value = true;
            try {
                const response = await fetch('api.php?action=checkout', { method: 'POST' });
                const res = await response.json();

                if (res.status === 'success') {
                    alert("¡Compra realizada con éxito! Pedido #" + res.pedido_id);
                    cart.value = [];
                    isCheckoutOpen.value = false;
                    isCartOpen.value = false;
                } else {
                    alert("Error al procesar el pedido: " + res.message);
                }
            } catch (error) {
                console.error("Error en checkout:", error);
            } finally {
                isProcessing.value = false;
            }
        };

        const openDetail = (book) => {
            selectedBook.value = book;
        };


        /* ==================================================================
           10. CICLO DE VIDA DEL COMPONENTE
           ================================================================== */
        // Cerrar menú únicamente cuando el clic se haga fuera (en cualquier otra parte de la ventana)
        const handleOutsideClick = () => {
            if (isSettingsOpen.value) {
                isSettingsOpen.value = false;
            }
        };

        onMounted(() => {
            loadData();
            startHero();
            startGenreAuto();

            // Aplica tema oscuro al iniciar si estaba activo
            if (isDarkMode.value) {
                document.body.classList.add('dark-theme');
            }

            // Escuchador para cerrar el menú de configuración al hacer clic fuera
            window.addEventListener('click', handleOutsideClick);
        });

        onUnmounted(() => {
            pauseHero();
            pauseGenreAuto();
            window.removeEventListener('click', handleOutsideClick);
        });


        /* ==================================================================
           11. RETORNO DE VARIABLES Y MÉTODOS AL TEMPLATE
           ================================================================== */
        return {
            currentTab,
            recentBooks,
            recommendedBooks,
            allBooksSorted,
            // Configuración y Modo Oscuro
            isDarkMode,
            isSettingsOpen,
            toggleSettings,
            toggleDarkMode,

            // Sliders y Categorías
            slides,
            activeSlide,
            nextSlide,
            prevSlide,
            pauseHero,
            startHero,
            categories,
            activeGenreIndex,
            visibleGenres,
            nextGenre,
            prevGenre,
            pauseGenreAuto,
            startGenreAuto,
            activeCategory,

            // Datos y Filtros
            books,
            filteredBooks,
            searchQuery,
            selectedBook,
            openDetail,

            // Carrito y Checkout
            cart,
            totalCart,
            isCartOpen,
            addToCart,
            removeFromCart,
            isCheckoutOpen,
            isProcessing,
            processCheckout,

            // Favoritos
            wishlist,
            isWishlistOpen,
            toggleWishlist,
            isInWishlist
        };
    }
}).mount('#app');