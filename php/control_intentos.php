<?php
// control_intentos.php
// Bloqueo temporal de cuentas tras varios intentos fallidos de inicio de sesión.
//
// Uso en un login:
//   require_once __DIR__ . '/control_intentos.php';
//
//   1) Antes de validar la contraseña:
//        $segundos = segundos_bloqueo_restantes($conexion, $email, 'usuario');   // 0 = libre
//        if ($segundos > 0) { ...mostrar "intenta de nuevo en X"... exit; }
//
//   2) Si la contraseña/correo fallan:
//        $quedan = intentos_registrar_fallo($conexion, $email, 'usuario');       // 0 = ya quedó bloqueada
//
//   3) Si el login es correcto:
//        intentos_limpiar($conexion, $email, 'usuario');
//
// $tipo es 'usuario' o 'admin' (así los contadores no se mezclan).

require_once __DIR__ . '/politica_password.php';

// Crea la tabla automáticamente la primera vez (no hay que ejecutar SQL a mano)
function intentos_preparar($conexion) {
    static $listo = false;
    if ($listo) return;

    mysqli_query($conexion, "CREATE TABLE IF NOT EXISTS intentos_login (
        id INT AUTO_INCREMENT PRIMARY KEY,
        identificador VARCHAR(100) NOT NULL,
        tipo VARCHAR(10) NOT NULL,
        intentos INT NOT NULL DEFAULT 0,
        bloqueado_hasta DATETIME NULL,
        ultimo_intento DATETIME NOT NULL,
        UNIQUE KEY uq_identificador_tipo (identificador, tipo)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $listo = true;
}

function intentos_normalizar($identificador) {
    return mb_substr(mb_strtolower(trim((string)$identificador), 'UTF-8'), 0, 100, 'UTF-8');
}

// Segundos que faltan para que termine el bloqueo (0 = la cuenta NO está bloqueada)
function segundos_bloqueo_restantes($conexion, $identificador, $tipo) {
    intentos_preparar($conexion);
    $id = intentos_normalizar($identificador);

    $stmt = mysqli_prepare($conexion,
        "SELECT TIMESTAMPDIFF(SECOND, NOW(), bloqueado_hasta) AS restante
         FROM intentos_login
         WHERE identificador = ? AND tipo = ? AND bloqueado_hasta IS NOT NULL");
    mysqli_stmt_bind_param($stmt, "ss", $id, $tipo);
    mysqli_stmt_execute($stmt);
    $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if (!$fila) return 0;

    $restante = (int)$fila['restante'];
    if ($restante > 0) return $restante;

    // El bloqueo ya terminó: se reinicia el contador
    intentos_limpiar($conexion, $identificador, $tipo);
    return 0;
}

// Suma un intento fallido. Devuelve cuántos intentos le quedan (0 = quedó bloqueada ahora)
function intentos_registrar_fallo($conexion, $identificador, $tipo) {
    intentos_preparar($conexion);
    $id = intentos_normalizar($identificador);

    $stmt = mysqli_prepare($conexion,
        "INSERT INTO intentos_login (identificador, tipo, intentos, ultimo_intento)
         VALUES (?, ?, 1, NOW())
         ON DUPLICATE KEY UPDATE intentos = intentos + 1, ultimo_intento = NOW()");
    mysqli_stmt_bind_param($stmt, "ss", $id, $tipo);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conexion,
        "SELECT intentos FROM intentos_login WHERE identificador = ? AND tipo = ?");
    mysqli_stmt_bind_param($stmt, "ss", $id, $tipo);
    mysqli_stmt_execute($stmt);
    $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    $intentos = $fila ? (int)$fila['intentos'] : 1;

    if ($intentos >= MAX_INTENTOS_LOGIN) {
        $minutos = MINUTOS_BLOQUEO_LOGIN;
        $stmt = mysqli_prepare($conexion,
            "UPDATE intentos_login
             SET bloqueado_hasta = DATE_ADD(NOW(), INTERVAL ? MINUTE)
             WHERE identificador = ? AND tipo = ?");
        mysqli_stmt_bind_param($stmt, "iss", $minutos, $id, $tipo);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return 0;
    }

    return MAX_INTENTOS_LOGIN - $intentos;
}

// Borra el contador (se llama cuando el login es correcto)
function intentos_limpiar($conexion, $identificador, $tipo) {
    intentos_preparar($conexion);
    $id = intentos_normalizar($identificador);

    $stmt = mysqli_prepare($conexion,
        "DELETE FROM intentos_login WHERE identificador = ? AND tipo = ?");
    mysqli_stmt_bind_param($stmt, "ss", $id, $tipo);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// "1 minuto" / "14 minutos", para los mensajes
function texto_tiempo_bloqueo($segundos) {
    $min = (int)ceil($segundos / 60);
    if ($min < 1) $min = 1;
    return $min . ($min === 1 ? ' minuto' : ' minutos');
}