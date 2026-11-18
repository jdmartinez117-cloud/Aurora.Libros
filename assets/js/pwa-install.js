// assets/js/pwa-install.js
// 1. REGISTRAR EL SERVICE WORKER
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    // Apunta a la raíz sw.js
    const swPath = window.location.pathname.includes('/php/') ? '../sw.js' : 'sw.js';
    navigator.serviceWorker.register(swPath)
      .then(reg => console.log('[PWA] Registrado con alcance:', reg.scope))
      .catch(err => console.error('[PWA] Fallo al registrar:', err));
  });
}

// 2. INDICADOR DE CONEXIÓN (ONLINE / OFFLINE)
function actualizarEstadoConexion() {
  let badge = document.getElementById('pwa-offline-badge');
  if (!navigator.onLine) {
    if (!badge) {
      badge = document.createElement('div');
      badge.id = 'pwa-offline-badge';
      badge.innerHTML = `<i class="fa-solid fa-plane-slash mr-2"></i> Modo Sin Conexión — Navegando en catálogo local`;
      badge.style.cssText = `
        position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);
        background: #78350f; color: #fff; padding: 10px 20px; border-radius: 30px;
        font-size: 0.85rem; font-weight: bold; z-index: 999999; box-shadow: 0 4px 20px rgba(0,0,0,0.3);
      `;
      document.body.appendChild(badge);
    }
  } else {
    if (badge) {
      badge.innerHTML = `<i class="fa-solid fa-wifi mr-2"></i> ¡Conexión restablecida! Sincronizando...`;
      badge.style.background = '#15803d';
      setTimeout(() => badge.remove(), 2500);
    }
  }
}
window.addEventListener('online', actualizarEstadoConexion);
window.addEventListener('offline', actualizarEstadoConexion);

// 3. CAPTURAR EL EVENTO DE INSTALACIÓN
let diferidoPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  diferidoPrompt = e;

  // Si no se ha instalado antes, mostramos un botón flotante sutil
  let btnInstalar = document.getElementById('btn-pwa-instalar');
  if (!btnInstalar) {
    btnInstalar = document.createElement('button');
    btnInstalar.id = 'btn-pwa-instalar';
    btnInstalar.innerHTML = `<i class="fa-solid fa-download mr-2"></i> Instalar Aurora App`;
    btnInstalar.style.cssText = `
      position: fixed; bottom: 20px; right: 20px; background: #b45309; color: #fff;
      padding: 10px 18px; border-radius: 25px; border: none; font-weight: 700;
      cursor: pointer; z-index: 99999; box-shadow: 0 8px 25px rgba(180,83,9,0.4);
      font-size: 0.85rem; transition: transform 0.2s;
    `;
    btnInstalar.onclick = () => {
      btnInstalar.style.display = 'none';
      diferidoPrompt.prompt();
      diferidoPrompt.userChoice.then(() => { diferidoPrompt = null; });
    };
    document.body.appendChild(btnInstalar);
  }
});