const { createApp, ref, computed, onMounted, onUnmounted } = Vue;

createApp({
    setup() {
        

        // --- DATOS DEL SLIDER PRINCIPAL ---
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

        // --- DATOS DE CATEGORÍAS ---
        const categories = ref([
            { name: 'Literatura', icon: 'fa-solid fa-feather-pointed' },
            { name: 'Académicos', icon: 'fa-solid fa-graduation-cap' },
            { name: 'Infantiles', icon: 'fa-solid fa-child-reaching' },
            { name: 'Misterio', icon: 'fa-solid fa-magnifying-glass' },
            { name: 'Ciencia Ficción', icon: 'fa-solid fa-rocket' },
            { name: 'Autoayuda', icon: 'fa-solid fa-seedling' },
            { name: 'Historia', icon: 'fa-solid fa-landmark' },
            { name: 'Arte', icon: 'fa-solid fa-palette' }
        ]);

        const books = ref([
            { id: 1, title: 'Cien años de soledad', author: 'Gabriel García Márquez', category: 'Literatura', price: 25, image: 'https://www.rae.es/sites/default/files/portada_cien_anos_de_soledad_0.jpg', description: 'La obra maestra del realismo mágico que narra la historia de la familia Buendía en el pueblo de Macondo.', stock: 15 },
                { id: 2, title: 'Sapiens: De animales a dioses', author: 'Yuval Noah Harari', category: 'Académicos', price: 30, image: 'https://edicioneshispanicas.com/wp-content/uploads/9788419399717-scaled.jpg', description: 'Un recorrido apasionante por la historia de nuestra especie, desde los primeros humanos hasta hoy.', stock: 8 },
                { id: 3, title: 'El Principito', author: 'Antoine de Saint-Exupéry', category: 'Infantiles', price: 15, image: 'https://edicioneshispanicas.com/wp-content/uploads/91a-8et2JPL.jpg', description: 'Un cuento poético sobre la amistad, el amor y la importancia de lo invisible a los ojos.', stock: 20 },
                { id: 4, title: 'El Resplandor', author: 'Stephen King', category: 'Misterio', price: 22, image: 'https://cdn.kobo.com/book-images/8f267b66-af50-47da-b5bd-8e793dc3647c/1200/1200/False/el-resplandor-3.jpg', description: 'Terror psicológico en un hotel aislado por la nieve. Un clásico inolvidable.', stock: 5 },
                { id: 5, title: 'Dune', author: 'Frank Herbert', category: 'Ciencia Ficción', price: 28, image: 'https://www.penguinlibros.com/co/1349120/dune-nueva-edicion-1.jpg', description: 'La mayor epopeya de ciencia ficción jamás escrita sobre el planeta desértico Arrakis.', stock: 10 },
                { id: 6, title: 'Hábitos Atómicos', author: 'James Clear', category: 'Autoayuda', price: 20, image: 'https://proassets.planetadelibros.com.co/usuaris/libros/fotos/443/original/442378_habitos-atomicos-en-accion_9788411193016_3d_202512151233.png', description: 'Cambios pequeños para resultados extraordinarios en tu vida diaria.', stock: 30 },
                { id: 7, title: 'Historia Mínima de México', author: 'Varios Autores', category: 'Académicos', price: 18, image: 'https://http2.mlstatic.com/D_NQ_NP_643473-MLM75620092246_042024-O.webp', description: 'Una síntesis clara y profunda de los procesos históricos que formaron la nación.', stock: 12 },
                { id: 8, title: 'Orgullo y Prejuicio', author: 'Jane Austen', category: 'Literatura', price: 19, image: 'https://proassets.planetadelibros.com.co/usuaris/libros/fotos/383/original/portada_orgullo-y-prejuicio_jane-austen_202308011307.jpg', description: 'Una comedia romántica clásica sobre la sociedad británica del siglo XIX.', stock: 7 }
        ]);

        // --- ESTADOS DE NAVEGACIÓN ---
        const activeSlide = ref(0);
        const activeGenreIndex = ref(0);
        const visibleGenres = ref(6); // Ajustar según diseño
        const activeCategory = ref('Literatura');
        const cart = ref([]);
        const wishlist = ref([]);
        const isWishlistOpen = ref(false);
        const isCartOpen = ref(false);
        const searchQuery = ref('');

        let heroTimer = null;
        let genreTimer = null;

        // --- LÓGICA HERO SLIDER ---
        const startHero = () => {
            heroTimer = setInterval(() => { nextSlide(); }, 5000);
        };
        const pauseHero = () => clearInterval(heroTimer);
        const nextSlide = () => { activeSlide.value = (activeSlide.value + 1) % slides.value.length; };
        const prevSlide = () => { activeSlide.value = activeSlide.value === 0 ? slides.value.length - 1 : activeSlide.value - 1; };

        // --- LÓGICA GÉNERO CAROUSEL ---
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

        // --- ACCIONES GENERALES ---
       

        const toggleWishlist = (book) => {
            const idx = wishlist.value.findIndex(b => b.id === book.id);
            if (idx > -1) wishlist.value.splice(idx, 1);
            else wishlist.value.push(book);
        };

        const isInWishlist = (book) => wishlist.value.some(b => b.id === book.id);

        const addToCart = (book) => { cart.value.push(book); };
        const removeFromCart = (idx) => cart.value.splice(idx, 1);
        const openDetail = (book) => selectedBook.value = book;
        
        const totalCart = computed(() => {
            return cart.value.reduce((acc, book) => acc + book.price, 0);
        });

        const filteredBooks = computed(() => {
            return books.value.filter(b => b.category === activeCategory.value);
        });

        const selectedBook = ref(null);

        

        // Lifecycle hooks
        onMounted(() => {
            startHero();
            startGenreAuto();
        });

        return {
            openDetail,
            removeFromCart,
            selectedBook,
            totalCart,
            isWishlistOpen,
            isCartOpen
            , slides, activeSlide, nextSlide, prevSlide, pauseHero, startHero,
            categories, activeGenreIndex, visibleGenres, nextGenre, prevGenre, pauseGenreAuto, startGenreAuto,
            activeCategory, books, filteredBooks, cart, wishlist, toggleWishlist, isInWishlist, addToCart, searchQuery
        }
    }
}).mount('#app');