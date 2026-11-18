<?php
// control_sesion.php
// Incluir al inicio de cada página protegida en PHP

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once 'conexion_be.php';

// =========================================================================
// ⏱️ CONFIGURACIÓN DE TIEMPOS DE SESIÓN (AJUSTA AQUÍ)
// =========================================================================
// 1. TIEMPO PARA BLOQUEAR LA PANTALLA (EN MINUTOS):
// El usuario verá la pantalla de bloqueo y deberá ingresar su contraseña.
$minutos_para_bloqueo = 10; // <-- EN MINUTOS (ejemplo: 10 minutos)

// 2. TIEMPO PARA CIERRE TOTAL / LOGOUT (EN MINUTOS):
// Si sigue inactivo y nadie desbloquea, la sesión se destruye por completo.
$minutos_para_logout  = 15; // <-- EN MINUTOS (ejemplo: 15 minutos)


// -------------------------------------------------------------------------
// CONVERSIÓN A SEGUNDOS (Cálculo automático: 1 minuto = 60 segundos):
define('TIEMPO_BLOQUEO', $minutos_para_bloqueo * 60); // Total en SEGUNDOS (600 s)
define('TIEMPO_LOGOUT',  $minutos_para_logout * 60);  // Total en SEGUNDOS (900 s)
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

// =========================================================================
// 🔐 SESIÓN ÚNICA: el token de esta sesión debe coincidir con el de la BD.
// Si el usuario inició sesión en otro navegador, el token cambió y esta
// sesión queda invalidada.
// =========================================================================
$token_sesion = isset($_SESSION['session_token']) ? $_SESSION['session_token'] : '';
$id_sesion_usuario = isset($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : 0;
$token_valido = false;

if ($token_sesion !== '' && $id_sesion_usuario > 0) {
    $stmt_tk = mysqli_prepare($conexion, "SELECT session_token FROM usuarios WHERE id = ?");
    mysqli_stmt_bind_param($stmt_tk, "i", $id_sesion_usuario);
    mysqli_stmt_execute($stmt_tk);
    $res_tk = mysqli_stmt_get_result($stmt_tk);
    $fila_tk = mysqli_fetch_assoc($res_tk);
    mysqli_stmt_close($stmt_tk);

    if ($fila_tk && !empty($fila_tk['session_token'])) {
        $token_valido = hash_equals($fila_tk['session_token'], $token_sesion);
    }
}

if (!$token_valido) {
    session_unset();
    session_destroy();
    echo '
        <script>
        alert("Tu sesión se cerró porque se inició sesión en otro dispositivo o navegador.");
        window.location = "../index.php";
        </script>
    ';
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

// =========================================================================
// ⚡ CIERRE INSTANTÁNEO: inyecta un vigilante en la página (antes de </body>)
// que revisa cada 5 segundos si esta sesión sigue siendo válida.
// =========================================================================
ob_start(function ($html) {
    $pos = strripos($html, '</body>');
    if ($pos === false) {
        return $html;
    }
    $script = <<<'JS'
<script>
(function () {
    var avisado = false;
    function revisar() {
        fetch('verificar_token_be.php', { cache: 'no-store', credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (d.valido || avisado) { return; }
                avisado = true;
                if (d.motivo === 'duplicada') {
                    alert('Tu sesión se cerró porque se inició sesión en otro dispositivo o navegador.');
                }
                window.location = '../index.php';
            })
            .catch(function () {});
    }
    setInterval(revisar, 5000);
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) { revisar(); }
    });
})();
</script>
JS;
    return substr_replace($html, $script, $pos, 0);
});
?>