// sw.js — Service Worker de Aurora.Libros
const CACHE_NAME = 'aurora-cache-v1';

// Archivos estáticos fundamentales que se guardan en el dispositivo
const STATIC_ASSETS = [
  './',
  'index.php',
  'php/bienvenida.php',
  'php/index_admin.php',
  'assets/css/estilos.css',
  'assets/js/script.js',
  'php/styles.css',
  'php/script.js',
  'php/estilos_admin.css',
  'php/script_admin.js',
  'manifest.json',
  // Librerías CDN esenciales
  'https://cdn.tailwindcss.com',
  'https://unpkg.com/vue@3/dist/vue.global.js',
  'https://cdn.jsdelivr.net/npm/chart.js',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
];

// 1. INSTALACIÓN: Guardar recursos clave en caché
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log('[PWA] Cacheando interfaz principal...');
      return cache.addAll(STATIC_ASSETS).catch(err => console.warn('Error precacheando:', err));
    })
  );
  self.skipWaiting();
});

// 2. ACTIVACIÓN: Limpiar cachés antiguas si se actualiza la versión
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) {
            console.log('[PWA] Eliminando caché vieja:', key);
            return caches.delete(key);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// 3. INTERCEPTOR DE PETICIONES (Network First con Fallback a Caché)
self.addEventListener('fetch', (event) => {
  const req = event.request;

  // Solo manejamos peticiones GET (no interceptamos POST de formularios)
  if (req.method !== 'GET') return;

  event.respondWith(
    fetch(req)
      .then((networkRes) => {
        // Si hay internet, clonamos la respuesta fresca en caché
        if (networkRes && networkRes.status === 200) {
          const resClone = networkRes.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(req, resClone));
        }
        return networkRes;
      })
      .catch(() => {
        // SI NO HAY INTERNET: Buscamos el recurso en caché
        return caches.match(req).then((cachedRes) => {
          if (cachedRes) return cachedRes;

          // Si es una navegación y no está en caché, entregamos bienvenida.php o index.php
          if (req.mode === 'navigate') {
            return caches.match('php/bienvenida.php') || caches.match('index.php');
          }
        });
      })
  );
});