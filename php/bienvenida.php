<?php

    session_start();

    if(!isset($_SESSION['usuario'])){
        echo '
            <script>
            alert("Debes de Iniciar Sesion");
            window.location = "index.php";
            </script>
        ';
        session_destroy();
        die();
    }
    // ---  OBTENER LIBROS DE LA BASE DE DATOS ---
    include 'conexion_be.php';
    $query = "SELECT * FROM libros";
    $resultado = mysqli_query($conexion, $query);
    $libros_db = [];
    while($row = mysqli_fetch_assoc($resultado)){
        $libros_db[] = $row;
    }


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librería Aurora | Tu Mundo en Letras</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div id="app">

       <!-- ENCABEZADO -->
    <header class="header-glass">
        <nav class="container mx-auto px-6 py-4 flex items-center justify-between">
            <div class="text-3xl font-bold tracking-tighter text-amber-800 font-['Playfair_Display']">AURORA.LIBROS</div>
            
            <div class="flex items-center space-x-6">
                <!-- Buscador -->
                <div class="relative hidden sm:block">
                     <input v-model="searchQuery" type="text" placeholder="Busca tu próximo libro..." 
                           class="pl-10 pr-4 py-2 bg-stone-100 border-none rounded-full focus:ring-2 focus:ring-amber-400 w-64 text-sm">
                    <i class="fa fa-search absolute left-4 top-2.5 text-stone-400"></i>
                </div>

                <!-- Favoritos -->
                <button @click="isWishlistOpen = true" class="relative group" title="Favoritos">
                    <i class="fa-regular fa-heart text-xl text-stone-700 group-hover:text-red-500 transition-colors"></i>
                    <span v-if="wishlist.length" class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] rounded-full w-4 h-4 flex items-center justify-center">{{wishlist.length}}</span>
                </button>

                <!-- Carrito -->
                <button @click="isCartOpen = true" class="relative group" title="Carrito">
                    <i class="fa-solid fa-book-open text-xl text-stone-700 group-hover:text-amber-700 transition-colors"></i>
                    <span v-if="cart.length" class="absolute -top-2 -right-2 bg-amber-700 text-white text-[10px] rounded-full w-4 h-4 flex items-center justify-center">{{cart.length}}</span>
                </button>

                                <!-- BOTÓN Y MENÚ DESPLEGABLE DE CONFIGURACIÓN -->
                <div class="relative" id="user-settings-wrapper">
                    <!-- 1. Agregado @click.stop para que no suba a window -->
                    <button @click.stop="toggleSettings" type="button" class="btn-config-user group" title="Configuración">
                        <i class="fa-solid fa-gear text-xl text-stone-700 group-hover:text-amber-700 transition-transform duration-300" :class="{'rotate-90 text-amber-700': isSettingsOpen}"></i>
                    </button>

                    <!-- 2. Agregado @click.stop en el contenedor para que hacer clic adentro tampoco lo cierre -->
                    <div v-if="isSettingsOpen" @click.stop class="user-dropdown-menu animate-pop-in">
                        <div class="dropdown-header">
                            <span class="user-badge">Lector Aurora</span>
                            <p class="user-greeting">Mi Cuenta</p>
                        </div>
                        
                        <ul class="dropdown-list">
                            <li>
                                <a href="javascript:void(0)" class="dropdown-item">
                                    <i class="fa-regular fa-user"></i>
                                    <span>Mi Perfil</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" class="dropdown-item">
                                    <i class="fa-solid fa-sliders"></i>
                                    <span>Configuración de Cuenta</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" class="dropdown-item">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <span>Direcciones de Entrega</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" class="dropdown-item">
                                    <i class="fa-regular fa-bell"></i>
                                    <span>Notificaciones</span>
                                </a>
                            </li>

                            <!-- SWITCH MODO OSCURO -->
                            <li class="dropdown-divider"></li>
                            <li class="dropdown-item dark-mode-toggle" @click.stop="toggleDarkMode">
                                <div class="flex items-center gap-3">
                                    <i :class="isDarkMode ? 'fa-solid fa-moon text-amber-400' : 'fa-regular fa-sun text-stone-500'"></i>
                                    <span>Modo Oscuro</span>
                                </div>
                                <div class="switch-toggle" :class="{'switch-active': isDarkMode}">
                                    <div class="switch-circle"></div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- LOGOUT (Visible siempre en la barra superior) -->
                <a href="cerrar_sesion.php" class="logout-btn text-stone-400 hover:text-stone-800" title="Cerrar Sesión">
                    <i class="fa-solid fa-right-from-bracket text-xl"></i>
                </a>
            </div> <!-- Cierre de flex items-center space-x-6 -->
        </nav>
    </header>
    
    <main class="pt-24 container mx-auto px-6 pb-20">
        
        <!-- ==============================================
             BARRA DE PESTAÑAS PRINCIPALES
             ============================================== -->
        <div class="tabs-nav-container mb-8">
            <button @click="currentTab = 'inicio'" 
                    :class="currentTab === 'inicio' ? 'tab-btn-active' : 'tab-btn-inactive'" 
                    class="tab-btn">
                <i class="fa-solid fa-house mr-2"></i> Inicio
            </button>
            <button @click="currentTab = 'explorar_todo'" 
                    :class="currentTab === 'explorar_todo' ? 'tab-btn-active' : 'tab-btn-inactive'" 
                    class="tab-btn">
                <i class="fa-solid fa-arrow-down-a-z mr-2"></i> Explorar Todo (A - Z)
                <span class="tab-badge">{{books.length}}</span>
            </button>
            <button @click="currentTab = 'categorias'" 
                    :class="currentTab === 'categorias' ? 'tab-btn-active' : 'tab-btn-inactive'" 
                    class="tab-btn">
                <i class="fa-solid fa-layer-group mr-2"></i> Por Categorías
            </button>
        </div>


        <!-- ====================================================================
             VISTA 1: INICIO (HERO, NOVEDADES Y RECOMENDACIONES)
             ==================================================================== -->
        <div v-if="currentTab === 'inicio'" class="space-y-16">
            
            <!-- SLIDER PRINCIPAL (HERO) -->
            <section class="main-slider group" @mouseenter="pauseHero" @mouseleave="startHero">
                <div class="slider-wrapper" :style="{ transform: `translateX(-${activeSlide * 100}%)` }">
                    <div v-for="(slide, index) in slides" :key="index" class="slide-item">
                        <img :src="slide.img" class="slider-img">
                        <div class="slider-content">
                            <span class="promo-tag">{{ slide.tag }}</span>
                            <h1 class="slider-title">{{ slide.title }}</h1>
                            <p class="slider-subtitle font-serif italic text-amber-200 text-xl mb-2">{{ slide.subtitle }}</p>
                            <p class="slider-desc text-lg opacity-90 max-w-lg mb-8">{{ slide.desc }}</p>
                            <button @click="currentTab = 'explorar_todo'" class="btn-primary px-10 py-4 w-max">Explorar Selección</button>
                        </div>
                    </div>
                </div>
                <!-- Controles Hero -->
                <button @click="prevSlide" class="slider-arrow left-4 opacity-0 group-hover:opacity-100"><i class="fa fa-chevron-left"></i></button>
                <button @click="nextSlide" class="slider-arrow right-4 opacity-0 group-hover:opacity-100"><i class="fa fa-chevron-right"></i></button>
                <div class="slider-dots">
                    <span v-for="(_, i) in slides" @click="activeSlide = i" :class="{'active': activeSlide === i}"></span>
                </div>
            </section>

            <!-- SECCIÓN 1: AGREGADOS RECIENTEMENTE -->
            <section>
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="section-tag bg-emerald-100 text-emerald-800">Novedades</span>
                            <h2 class="text-3xl font-bold font-serif text-stone-800">Agregados Recientemente</h2>
                        </div>
                        <p class="text-stone-500 text-sm">Los últimos títulos incorporados a las estanterías de Aurora.</p>
                    </div>
                    <button @click="currentTab = 'explorar_todo'" class="text-sm font-semibold text-amber-800 hover:underline flex items-center gap-1">
                        Ver todos <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="book in recentBooks" :key="book.id" class="book-card group">
                        <div class="book-image-container">
                            <span class="card-corner-badge bg-emerald-600 text-white">Nuevo</span>
                            <img :src="'img_libros/' + book.image" class="book-img">
                            <div class="book-overlay">
                                <button @click="toggleWishlist(book)" class="action-btn" title="Favoritos">
                                    <i :class="isInWishlist(book) ? 'fa-solid text-red-500' : 'fa-regular'" class="fa-heart"></i>
                                </button>
                                <button @click="addToCart(book)" class="action-btn-primary" title="Añadir">
                                    <i class="fa-solid fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div @click="openDetail(book)" class="cursor-pointer pt-4 px-1">
                            <h3 class="text-base font-bold text-stone-800 font-serif leading-tight mb-1 group-hover:text-amber-800 transition-colors line-clamp-2 h-10">
                                {{book.titulo || book.title}}
                            </h3>
                            <p class="text-xs uppercase tracking-wider text-stone-500 font-semibold mb-2">
                                {{book.author}}
                            </p>
                            <div class="flex items-center justify-between border-t border-stone-100 pt-2">
                                <p class="text-lg font-bold text-stone-900">${{book.price}}</p>
                                <span class="text-[10px] bg-stone-100 text-stone-500 px-2 py-0.5 rounded font-semibold">{{book.category}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECCIÓN 2: RECOMENDADOS PARA TI (PLANTILLA BASE) -->
            <section class="bg-amber-50/50 p-8 rounded-3xl border border-amber-100">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="section-tag bg-amber-100 text-amber-800">Para Ti</span>
                            <h2 class="text-3xl font-bold font-serif text-stone-800">Recomendaciones Especiales</h2>
                        </div>
                        <p class="text-stone-500 text-sm">Sugerencias basadas en tendencias y selecciones editoriales destacadas.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="book in recommendedBooks" :key="'rec-' + book.id" class="book-card group bg-white p-3 rounded-2xl shadow-sm">
                        <div class="book-image-container">
                            <span class="card-corner-badge bg-amber-700 text-white">{{book.recomBadge}}</span>
                            <img :src="'img_libros/' + book.image" class="book-img">
                            <div class="book-overlay">
                                <button @click="toggleWishlist(book)" class="action-btn">
                                    <i :class="isInWishlist(book) ? 'fa-solid text-red-500' : 'fa-regular'" class="fa-heart"></i>
                                </button>
                                <button @click="addToCart(book)" class="action-btn-primary">
                                    <i class="fa-solid fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div @click="openDetail(book)" class="cursor-pointer pt-4 px-1">
                            <h3 class="text-base font-bold text-stone-800 font-serif leading-tight mb-1 group-hover:text-amber-800 transition-colors line-clamp-2 h-10">
                                {{book.titulo || book.title}}
                            </h3>
                            <p class="text-xs uppercase tracking-wider text-stone-500 font-semibold mb-2">
                                {{book.author}}
                            </p>
                            <div class="flex items-center justify-between border-t border-stone-100 pt-2">
                                <p class="text-lg font-bold text-amber-800">${{book.price}}</p>
                                <span class="text-[10px] bg-amber-50 text-amber-700 px-2 py-0.5 rounded font-semibold">{{book.category}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>


        <!-- ====================================================================
             VISTA 2: EXPLORAR TODO (A - Z EN ORDEN ALFABÉTICO)
             ==================================================================== -->
        <div v-if="currentTab === 'explorar_todo'" class="animate-fade-in">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-4 border-b border-stone-200">
                <div>
                    <h2 class="text-3xl font-bold font-serif text-stone-800">Catálogo Completo (A - Z)</h2>
                    <p class="text-stone-500 text-sm">Todos los títulos ordenados alfabéticamente por título.</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold px-3 py-1.5 bg-stone-100 text-stone-600 rounded-full">
                        {{allBooksSorted.length}} ejemplares disponibles
                    </span>
                </div>
            </div>

            <div v-if="allBooksSorted.length === 0" class="text-center py-20">
                <i class="fa-solid fa-book-open-reader text-5xl text-stone-300 mb-4"></i>
                <p class="text-stone-500">No se encontraron libros que coincidan con la búsqueda.</p>
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div v-for="book in allBooksSorted" :key="'all-' + book.id" class="book-card group">
                    <div class="book-image-container">
                        <img :src="'img_libros/' + book.image" class="book-img">
                        <div class="book-overlay">
                            <button @click="toggleWishlist(book)" class="action-btn">
                                <i :class="isInWishlist(book) ? 'fa-solid text-red-500' : 'fa-regular'" class="fa-heart"></i>
                            </button>
                            <button @click="addToCart(book)" class="action-btn-primary">
                                <i class="fa-solid fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div @click="openDetail(book)" class="cursor-pointer pt-4 px-1">
                        <h3 class="text-base font-bold text-stone-800 font-serif leading-tight mb-1 group-hover:text-amber-800 transition-colors line-clamp-2 h-10">
                            {{book.titulo || book.title}}
                        </h3>
                        <p class="text-xs uppercase tracking-wider text-stone-500 font-semibold mb-2">
                            {{book.author}}
                        </p>
                        <div class="flex items-center justify-between border-t border-stone-100 pt-2">
                            <p class="text-lg font-bold text-stone-900">${{book.price}}</p>
                            <span class="text-[10px] bg-stone-100 text-stone-500 px-2 py-0.5 rounded font-semibold">{{book.category}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- ====================================================================
             VISTA 3: EXPLORAR POR CATEGORÍAS (CARRUSEL + FILTRO EXISTENTE)
             ==================================================================== -->
        <div v-if="currentTab === 'categorias'" class="animate-fade-in space-y-12">
            <!-- Carrusel de Géneros -->
            <section class="relative">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="section-title !mb-0">Filtrar por Género Literario</h2>
                    <div class="flex space-x-2">
                        <button @click="prevGenre" class="genre-nav-btn"><i class="fa fa-chevron-left"></i></button>
                        <button @click="nextGenre" class="genre-nav-btn"><i class="fa fa-chevron-right"></i></button>
                    </div>
                </div>
                
                <div class="genre-carousel-container" @mouseenter="pauseGenreAuto" @mouseleave="startGenreAuto">
                    <div class="genre-wrapper" :style="{ transform: `translateX(-${activeGenreIndex * (100/visibleGenres)}%)` }">
                        <div v-for="cat in categories" 
                             :key="cat.name"
                             @click="activeCategory = cat.name"
                             :class="activeCategory === cat.name ? 'category-card-active' : 'category-card-inactive'"
                             class="category-card group">
                            <i :class="cat.icon" class="text-3xl mb-4 transition-transform group-hover:scale-110"></i>
                            <span class="font-bold text-sm uppercase tracking-wider">{{cat.name}}</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Grilla filtrada por Categoría -->
            <section>
                <h3 class="text-2xl font-bold font-serif text-stone-800 mb-6">Libros en "{{activeCategory}}"</h3>
                <div v-if="filteredBooks.length === 0" class="text-center py-16">
                    <i class="fa-solid fa-box-open text-4xl text-stone-300 mb-3"></i>
                    <p class="text-stone-500">No hay libros registrados en esta categoría.</p>
                </div>
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="book in filteredBooks" :key="'cat-' + book.id" class="book-card group">
                        <div class="book-image-container">
                            <img :src="'img_libros/' + book.image" class="book-img">
                            <div class="book-overlay">
                                <button @click="toggleWishlist(book)" class="action-btn">
                                    <i :class="isInWishlist(book) ? 'fa-solid text-red-500' : 'fa-regular'" class="fa-heart"></i>
                                </button>
                                <button @click="addToCart(book)" class="action-btn-primary">
                                    <i class="fa-solid fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div @click="openDetail(book)" class="cursor-pointer pt-4 px-1">
                            <h3 class="text-base font-bold text-stone-800 font-serif leading-tight mb-1 group-hover:text-amber-800 transition-colors line-clamp-2 h-10">
                                {{book.titulo || book.title}}
                            </h3>
                            <p class="text-xs uppercase tracking-wider text-stone-500 font-semibold mb-2">
                                {{book.author}}
                            </p>
                            <div class="flex items-center justify-between border-t border-stone-100 pt-2">
                                <p class="text-lg font-bold text-stone-900">${{book.price}}</p>
                                <span class="text-[10px] bg-stone-100 text-stone-500 px-2 py-0.5 rounded font-semibold">{{book.category}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

    </main>

    <!-- SIDEBAR: FAVORITOS -->
    <div :class="isWishlistOpen ? 'translate-x-0' : 'translate-x-full'" class="fixed right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl z-[80] transition-transform duration-500">
        <div class="p-8 h-full flex flex-col">
            <div class="flex justify-between items-center mb-10">
                <h3 class="text-3xl font-bold font-serif">Mis Favoritos</h3>
                <button @click="isWishlistOpen = false" class="text-stone-400 hover:text-stone-900"><i class="fa fa-times text-2xl"></i></button>
            </div>
            <div class="flex-grow overflow-y-auto space-y-6">
                <div v-if="wishlist.length === 0" class="text-center py-20">
                    <i class="fa-regular fa-heart text-5xl text-stone-200 mb-4"></i>
                    <p class="text-stone-400">Aún no tienes libros guardados.</p>
                </div>
                <div v-for="item in wishlist" class="flex space-x-4 items-center animate-fade-in">
                   <img :src="'img_libros/' + item.image" class="w-20 h-28 object-cover rounded-lg shadow-md">
                    <div class="flex-grow">
                        <h4 class="font-bold text-stone-800 leading-tight">{{item.title}}</h4>
                        <p class="text-sm text-stone-500">{{item.author}}</p>
                        <p class="text-amber-700 font-bold mt-1">${{item.price}}</p>
                        <button @click="addToCart(item)" class="text-xs font-bold text-amber-600 mt-2 hover:underline">Mover al carrito</button>
                    </div>
                    <button @click="toggleWishlist(item)" class="text-stone-300 hover:text-red-500"><i class="fa fa-trash"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- SIDEBAR: CARRITO -->
    <div :class="isCartOpen ? 'translate-x-0' : 'translate-x-full'" class="fixed right-0 top-0 h-full w-full max-w-md bg-stone-50 shadow-2xl z-[80] transition-transform duration-500 border-l border-stone-200">
        <div class="p-8 h-full flex flex-col">
            <div class="flex justify-between items-center mb-10">
                <h3 class="text-3xl font-bold font-serif text-stone-800">Carrito</h3>
                <button @click="isCartOpen = false" class="text-stone-400 hover:text-stone-900"><i class="fa fa-times text-2xl"></i></button>
            </div>
            <div class="flex-grow overflow-y-auto space-y-6">
                <div v-if="cart.length === 0" class="text-center py-20">
                    <i class="fa-regular fa-shopping-cart text-5xl text-stone-200 mb-4"></i>
                    <p class="text-stone-400">Tu carrito está vacío.</p>
                </div>
                    <!-- -->
                <div v-for="(item, index) in cart" :key="index" class="flex space-x-4 items-center bg-white p-4 rounded-2xl shadow-sm">
                    <img :src="'img_libros/' + item.image" class="w-16 h-20 object-cover rounded-md shadow">
                    <div class="flex-grow">
                        <h4 class="font-bold text-stone-800 text-sm leading-tight">{{item.title}}</h4>
                        <p class="text-indigo-600 font-bold">${{item.price}}</p>
                    </div>
                    <!--  -->
                    <button @click="removeFromCart(index)" class="text-stone-300 hover:text-stone-800">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>
                    <!--  -->
            </div>
            <div v-if="cart.length > 0" class="pt-6 border-t">
                <div class="flex justify-between text-2xl font-bold mb-6 text-stone-800">
                    <span>Total:</span>
                    <span>${{totalCart}}</span>
                </div>
                <button @click="isCheckoutOpen = true; isCartOpen = false" 
                        class="w-full bg-stone-800 text-white py-4 rounded-2xl font-bold hover:bg-stone-900 transition-all shadow-lg">
                    Revisar Pedido
                </button>
            </div>
        </div>
    </div>




    <!-- DETALLE DEL LIBRO (MODAL) -->
    <div v-if="selectedBook" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6">
        <!-- Fondo oscuro con desenfoque -->
        <div class="absolute inset-0 bg-stone-900/80 backdrop-blur-md" @click="selectedBook = null"></div>
        
        <!-- Contenedor del Modal -->
        <div class="bg-white w-full max-w-4xl rounded-[2rem] relative z-10 overflow-hidden shadow-2xl animate-pop-in flex flex-col md:flex-row">
            
            <!-- Botón Cerrar (Esquina superior derecha) -->
            <button @click="selectedBook = null" class="absolute top-6 right-6 z-20 bg-white/80 backdrop-blur-sm hover:bg-white text-stone-800 w-10 h-10 rounded-full shadow-lg flex items-center justify-center transition-all">
                <i class="fa fa-times text-xl"></i>
            </button>

            <!-- Columna Izquierda: Imagen -->
            <div class="w-full md:w-5/12 bg-stone-100 flex items-center justify-center p-8">
                <img :src="'img_libros/' + selectedBook.image" 
                     :alt="selectedBook.titulo"
                     class="w-full max-w-[280px] h-auto object-cover rounded-xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] transform -rotate-2 hover:rotate-0 transition-transform duration-500">
            </div>

            <!-- Columna Derecha: Información -->
            <div class="w-full md:w-7/12 p-8 md:p-12 flex flex-col">
                
                <!-- Etiqueta de Categoría -->
                <span class="text-amber-600 text-xs font-bold uppercase tracking-[0.2em] mb-4 block">
                    {{selectedBook.category}}
                </span>

                <!-- Título Gigante (Nombre de la DB: titulo) -->
                <h2 class="text-3xl md:text-5xl font-bold font-serif text-stone-900 leading-tight mb-4">
                    {{selectedBook.titulo}}
                </h2>
                
                <!-- Autor con línea decorativa -->
                <div class="flex items-center gap-4 mb-8">
                    <span class="h-px w-8 bg-amber-500"></span>
                    <p class="text-xl text-stone-500 italic">
                        {{selectedBook.author}}
                    </p>
                </div>
                
                <!-- Descripción con scroll si es muy larga -->
                <div class="prose prose-stone mb-8">
                    <p class="text-stone-600 leading-relaxed text-base line-clamp-6 md:line-clamp-none">
                        {{selectedBook.description}}
                    </p>
                </div>
                
                <!-- Footer del Modal: Precio y Botón -->
                <div class="mt-auto pt-8 border-t border-stone-100 flex flex-wrap items-center justify-between gap-6">
                    <div>
                        <p class="text-xs text-stone-400 uppercase font-bold tracking-widest mb-1">Precio Online</p>
                        <p class="text-4xl font-bold text-stone-900">${{selectedBook.price}}</p>
                    </div>
                    
                    <button @click="addToCart(selectedBook)" 
                            class="flex-grow md:flex-grow-0 bg-stone-800 text-white px-10 py-5 rounded-2xl font-bold hover:bg-amber-700 transition-all shadow-xl hover:shadow-amber-900/20 flex items-center justify-center gap-3 group">
                        <i class="fa-solid fa-cart-plus group-hover:scale-110 transition-transform"></i>
                        Añadir al carrito
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL DE RESUMEN DE COMPRA (CHECKOUT) -->
    <div v-if="isCheckoutOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-stone-900/80 backdrop-blur-sm" @click="isCheckoutOpen = false"></div>
        
        <div class="bg-white w-full max-w-2xl rounded-3xl relative z-10 overflow-hidden shadow-2xl animate-pop-in">
            <div class="p-8 border-b border-stone-100 flex justify-between items-center">
                <h3 class="text-2xl font-bold font-serif text-stone-800">Resumen de tu Compra</h3>
                <button @click="isCheckoutOpen = false" class="text-stone-400 hover:text-stone-600">
                    <i class="fa fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-8 max-h-[60vh] overflow-y-auto">
                <div v-for="item in cart" :key="item.id" class="flex justify-between items-center mb-4 pb-4 border-b border-stone-50">
                    <div class="flex items-center space-x-4">
                        <img :src="'img_libros/' + item.image" class="w-12 h-16 object-cover rounded shadow-sm">
                        <div>
                            <p class="font-bold text-stone-800">{{item.title}}</p>
                            <p class="text-xs text-stone-500">{{item.author}}</p>
                        </div>
                    </div>
                    <p class="font-bold text-stone-700">${{item.price}}</p>
                </div>
            </div>

            <div class="p-8 bg-stone-50">
                <div class="flex justify-between items-center mb-6">
                    <span class="text-stone-500 font-medium">Total a pagar:</span>
                    <span class="text-3xl font-bold text-amber-800">${{totalCart}}</span>
                </div>
                <div class="flex space-x-4">
                    <button @click="isCheckoutOpen = false" class="flex-1 py-4 rounded-xl font-bold text-stone-500 hover:bg-stone-200 transition-colors">
                        Cancelar
                    </button>
                    <button @click="processCheckout" 
                            :disabled="isProcessing"
                            class="flex-1 bg-amber-700 text-white py-4 rounded-xl font-bold hover:bg-amber-800 transition-all shadow-lg flex items-center justify-center">
                        <span v-if="isProcessing"><i class="fa fa-spinner fa-spin mr-2"></i> Procesando...</span>
                        <span v-else>Confirmar y Pagar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================ -->
<!-- INTEGRACIÓN DEL PASO 2: CONEXIÓN PHP -> VUE -->
<!-- ============================================================ -->
<script>
    /**
     * Esta variable global captura el ID del usuario desde PHP.
     * Importante: Asegúrate que en tu archivo de login (validar.php)
     * hayas puesto: $_SESSION['id_usuario'] = $fila['id'];
     */
    const BOOKS_FROM_DB = <?php echo json_encode($libros_db); ?>;
    const SESSION_USER_ID = <?php echo isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 'null'; ?>;
    
    // Opcional: Para depuración, puedes ver el ID en la consola del navegador
    console.log("Libros cargados:", BOOKS_FROM_DB);
    console.log("Sesión iniciada para el usuario ID:", SESSION_USER_ID);
    

</script>

<script src="script.js"></script>
</body>
</html>
