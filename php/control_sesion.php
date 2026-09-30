<?php
// control_sesion.php
// Incluir al inicio de cada página protegida en PHP

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =========================================================================
// ⏱️ CONFIGURACIÓN DE TIEMPOS DE SESIÓN (AJUSTA AQUÍ)
// =========================================================================
// 1. TIEMPO PARA BLOQUEAR LA PANTALLA (EN MINUTOS):
// El usuario verá la pantalla de bloqueo y deberá ingresar su contraseña.
$minutos_para_bloqueo = 0.25; // <-- EN MINUTOS (ejemplo: 10 minutos)

// 2. TIEMPO PARA CIERRE TOTAL / LOGOUT (EN MINUTOS):
// Si sigue inactivo y nadie desbloquea, la sesión se destruye por completo.
$minutos_para_logout  = 0.5; // <-- EN MINUTOS (ejemplo: 15 minutos)


// -------------------------------------------------------------------------
// CONVERSIÓN A SEGUNDOS (Cálculo automático: 1 minuto = 60 segundos):
define('TIEMPO_BLOQUEO', $minutos_para_bloqueo * 60); // Total en SEGUNDOS (600 s)
define('TIEMPO_LOGOUT',  $minutos_para_logout * 60);  // Total en SEGUNDOS (900 s)
// -------------------------------------------------------------------------
// 💡 NOTA PARA PRUEBAS RÁPIDAS:
// Si quieres probar en SEGUNDOS (por ejemplo 30 segundos y 60 segundos):
// define('TIEMPO_BLOQUEO', 30); // 30 segundos
// define('TIEMPO_LOGOUT', 60);  // 60 segundos
// =========================================================================

// Si no hay sesión iniciada, expulsamos al login principal
if (!isset($_SESSION['usuario'])) {
    echo '
        <script>
        alert("Debes iniciar sesión para acceder.");
        window.location = "../index.php";
        </script>
    ';
    session_destroy();
    exit;
}

$ahora = time();
$ultima = isset($_SESSION['ultima_actividad']) ? $_SESSION['ultima_actividad'] : $ahora;
$inactivo = $ahora - $ultima; // Diferencia en SEGUNDOS

// 1. ¿Superó el tiempo de LOGOUT total?
if ($inactivo >= TIEMPO_LOGOUT) {
    session_unset();
    session_destroy();
    header("location: ../index.php?motivo=expirada");
    exit;
}

// 2. ¿Superó el tiempo de BLOQUEO pero aún no expira?
if ($inactivo >= TIEMPO_BLOQUEO) {
    $_SESSION['bloqueada'] = true;
} else {
    // Sigue activo: actualizamos la última actividad y mantenemos desbloqueado
    $_SESSION['ultima_actividad'] = $ahora;
    $_SESSION['bloqueada'] = false;
}
?>