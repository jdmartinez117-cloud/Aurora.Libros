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
    <!-- El Login ha sido eliminado por completo de aquí -->

    <!-- ENCABEZADO -->
    <header class="header-glass">
        <nav class="container mx-auto px-6 py-4 flex items-center justify-between">
            <div class="text-3xl font-bold tracking-tighter text-amber-800 font-['Playfair_Display']">AURORA.LIBROS</div>
            <div class="flex items-center space-x-6">
                <div class="relative hidden sm:block">
                     <input v-model="searchQuery" type="text" placeholder="Busca tu próximo libro..." 
                           class="pl-10 pr-4 py-2 bg-stone-100 border-none rounded-full focus:ring-2 focus:ring-amber-400 w-64 text-sm">
                    <i class="fa fa-search absolute left-4 top-2.5 text-stone-400"></i>
                </div>
                 <button @click="isWishlistOpen = true" class="relative group">
                    <i class="fa-regular fa-heart text-xl text-stone-700 group-hover:text-red-500 transition-colors"></i>
                    <span v-if="wishlist.length" class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] rounded-full w-4 h-4 flex items-center justify-center">{{wishlist.length}}</span>
                </button>

                <button @click="isCartOpen = true" class="relative group">
                    <i class="fa-solid fa-book-open text-xl text-stone-700 group-hover:text-amber-700 transition-colors"></i>
                    <span v-if="cart.length" class="absolute -top-2 -right-2 bg-amber-700 text-white text-[10px] rounded-full w-4 h-4 flex items-center justify-center">{{cart.length}}</span>
                </button>
                
                <!-- LOGOUT MANTENIDO INTACTO -->
                <a href="cerrar_sesion.php" class="logout-btn text-stone-400 hover:text-stone-800">
                    <i class="fa-solid fa-right-from-bracket text-xl"></i>
                </a>
            </div>
        </nav>
    </header>

    <!-- Se eliminó v-if="isLoggedIn" para que la tienda cargue siempre -->
    <main class="pt-24 container mx-auto px-6 pb-20">
        
        <!-- SLIDER PRINCIPAL (HERO) -->
        <section class="main-slider group" 
                 @mouseenter="pauseHero" 
                 @mouseleave="startHero">
            <div class="slider-wrapper" :style="{ transform: `translateX(-${activeSlide * 100}%)` }">
                <div v-for="(slide, index) in slides" :key="index" class="slide-item">
                    <img :src="slide.img" class="slider-img">
                    <div class="slider-content">
                        <span class="promo-tag">{{ slide.tag }}</span>
                        <h1 class="slider-title">{{ slide.title }}</h1>
                        <p class="slider-subtitle font-serif italic text-amber-200 text-xl mb-2">{{ slide.subtitle }}</p>
                        <p class="slider-desc text-lg opacity-90 max-w-lg mb-8">{{ slide.desc }}</p>
                        <button class="btn-primary px-10 py-4 w-max">Explorar Selección</button>
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

        <!-- CARRUSEL DE GÉNEROS -->
        <section class="mb-16 relative">
            <div class="flex justify-between items-center mb-8">
                <h2 class="section-title !mb-0">Explorar por Género</h2>
                <div class="flex space-x-2">
                    <button @click="prevGenre" class="genre-nav-btn"><i class="fa fa-chevron-left"></i></button>
                    <button @click="nextGenre" class="genre-nav-btn"><i class="fa fa-chevron-right"></i></button>
                </div>
            </div>
            
            <div class="genre-carousel-container" 
                 @mouseenter="pauseGenreAuto" 
                 @mouseleave="startGenreAuto">
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
            <!-- Indicadores Género -->
            <div class="flex justify-center mt-6 space-x-2">
                <span v-for="(_, i) in (categories.length - visibleGenres + 1)" 
                      @click="activeGenreIndex = i"
                      class="h-1 transition-all duration-300 rounded-full cursor-pointer"
                      :class="activeGenreIndex === i ? 'w-8 bg-amber-700' : 'w-2 bg-stone-300'"></span>
            </div>
        </section>

        <!-- GRID DE LIBROS -->
        <section>
            <h2 class="text-4xl font-bold text-stone-800 font-serif mb-10">{{activeCategory}}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
                <div v-for="book in filteredBooks" :key="book.id" class="book-card group">
                    <div class="book-image-container">
                        <img :src="book.image" class="book-img">
                        <div class="book-overlay">
                            <button @click="toggleWishlist(book)" class="action-btn">
                                <i :class="isInWishlist(book) ? 'fa-solid text-red-500' : 'fa-regular'" class="fa-heart"></i>
                            </button>
                            <button @click="addToCart(book)" class="action-btn-primary">
                                <i class="fa-solid fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div @click="openDetail(book)" class="cursor-pointer pt-4">
                        <p class="author-name">{{book.author}}</p>
                        <h3 class="book-title">{{book.title}}</h3>
                        <p class="book-price">${{book.price}}</p>
                    </div>
                </div>
            </div>
        </section>
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
                    <img :src="item.image" class="w-20 h-28 object-cover rounded-lg shadow-md">
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
                <div v-for="(item, index) in cart" class="flex space-x-4 items-center bg-white p-4 rounded-2xl shadow-sm">
                    <img :src="item.image" class="w-16 h-20 object-cover rounded-md shadow">
                    <div class="flex-grow">
                        <h4 class="font-bold text-stone-800 text-sm leading-tight">{{item.title}}</h4>
                        <p class="text-indigo-600 font-bold">${{item.price}}</p>
                    </div>
                    <button @click="removeFromCart(index)" class="text-stone-300 hover:text-stone-800"><i class="fa-solid fa-circle-xmark"></i></button>
                </div>
            </div>
            <div v-if="cart.length > 0" class="pt-6 border-t">
                <div class="flex justify-between text-2xl font-bold mb-6 text-stone-800">
                    <span>Total:</span>
                    <span>${{totalCart}}</span>
                </div>
                <button class="w-full bg-stone-800 text-white py-4 rounded-2xl font-bold hover:bg-stone-900 transition-all shadow-lg">Finalizar Compra</button>
            </div>
        </div>
    </div>

    <!-- DETALLE DEL LIBRO (MODAL) -->
    <div v-if="selectedBook" class="fixed inset-0 z-[90] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70" @click="selectedBook = null"></div>
        <div class="bg-white w-full max-w-3xl rounded-3xl relative z-10 p-8">
            <button @click="selectedBook = null" class="absolute top-4 right-4">
                <i class="fa fa-times text-xl"></i>
            </button>
            <div class="flex gap-8">
                <img :src="selectedBook.image" class="w-48 h-72 object-cover rounded-lg shadow-lg">
                <div>
                    <h2 class="text-3xl font-bold font-serif mb-2">{{selectedBook.title}}</h2>
                    <p class="text-stone-500 italic mb-4">{{selectedBook.author}}</p>
                    <p class="mb-6 text-stone-700">{{selectedBook.description}}</p>
                    <p class="text-2xl font-bold text-amber-700 mb-4">${{selectedBook.price}}</p>
                    <button @click="addToCart(selectedBook)" class="bg-amber-700 text-white px-6 py-3 rounded-xl">Agregar al carrito</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
