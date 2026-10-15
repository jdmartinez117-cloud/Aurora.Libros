<?php
// estado_sesion_be.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['estado' => 'expirada']);
    exit;
}

// =========================================================================
// ⏱️ CONFIGURACIÓN DE TIEMPOS DE INACTIVIDAD (MODIFICA AQUÍ)
// =========================================================================
$minutos_bloqueo = 0.25;  // <-- MINUTOS sin tocar nada para BLOQUEAR pantalla
$minutos_logout  = 0.5; // <-- MINUTOS sin tocar nada para EXPULSIÓN total

define('TIEMPO_BLOQUEO', $minutos_bloqueo * 60); // 300 SEGUNDOS
define('TIEMPO_LOGOUT',  $minutos_logout * 60);  // 600 SEGUNDOS
// =========================================================================

$ahora = time();

if (!isset($_SESSION['ultima_actividad'])) {
    $_SESSION['ultima_actividad'] = $ahora;
}

// =========================================================================
// 🔒 CERROJO ABSOLUTO:
// Si la sesión YA ESTÁ BLOQUEADA, queda congelada aquí.
// NINGÚN clic ni movimiento de ratón puede quitar el bloqueo.
// Únicamente desbloquear_be.php puede poner $_SESSION['bloqueada'] = false.
// =========================================================================
if (!empty($_SESSION['bloqueada'])) {
    $inactivo_bloqueado = $ahora - $_SESSION['ultima_actividad'];

    // Si además superó el tiempo máximo de logout sin ingresar la clave -> Cierre definitivo
    if ($inactivo_bloqueado >= TIEMPO_LOGOUT) {
        session_unset();
        session_destroy();
        echo json_encode(['estado' => 'expirada']);
        exit;
    }

    // Permanece obligatoriamente bloqueada esperando la contraseña
    echo json_encode([
        'estado' => 'bloqueada',
        'segundos_restantes_logout' => (TIEMPO_LOGOUT - $inactivo_bloqueado)
    ]);
    exit;
}

// =========================================================================
// 🟢 SI NO ESTÁ BLOQUEADA: Los movimientos del usuario resetean el reloj
// =========================================================================
if (isset($_GET['segundos_inactivo'])) {
    $segundos_sin_tocar = (int)$_GET['segundos_inactivo'];

    // Si el usuario se ha estado moviendo, refrescamos el contador
    if ($segundos_sin_tocar < TIEMPO_BLOQUEO) {
        $_SESSION['ultima_actividad'] = $ahora - $segundos_sin_tocar;
    }
}

$inactivo = $ahora - $_SESSION['ultima_actividad'];

// 1. ¿Superó el tiempo de inactividad? -> ACTIVAR BLOQUEO
if ($inactivo >= TIEMPO_BLOQUEO) {
    $_SESSION['bloqueada'] = true; // Se echa el cerrojo
    echo json_encode([
        'estado' => 'bloqueada',
        'segundos_inactivo' => $inactivo
    ]);
    exit;
}

// 2. Continúa activa
echo json_encode([
    'estado' => 'activa',
    'segundos_inactivo' => $inactivo
]);
?>