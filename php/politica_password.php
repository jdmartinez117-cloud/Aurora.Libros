<?php
// politica_password.php
// Política de contraseñas y parámetros de bloqueo de Aurora.Libros.
// Se usa con: require_once __DIR__ . '/politica_password.php';

// ---------- CONFIGURACIÓN (cambia aquí los valores) ----------
if (!defined('PASSWORD_MIN_LENGTH'))    define('PASSWORD_MIN_LENGTH', 8);    // 0 = no exigir longitud mínima
if (!defined('MAX_INTENTOS_LOGIN'))     define('MAX_INTENTOS_LOGIN', 5);     // intentos fallidos permitidos
if (!defined('MINUTOS_BLOQUEO_LOGIN'))  define('MINUTOS_BLOQUEO_LOGIN', 15); // duración del bloqueo

/**
 * Revisa una contraseña contra la política.
 * Devuelve un arreglo con los requisitos que NO cumple (vacío = contraseña válida).
 */
if (!function_exists('validar_password')) {
    function validar_password($password) {
        $errores = [];

        if (PASSWORD_MIN_LENGTH > 0 && mb_strlen($password, 'UTF-8') < PASSWORD_MIN_LENGTH) {
            $errores[] = 'Debe tener al menos ' . PASSWORD_MIN_LENGTH . ' caracteres.';
        }
        if (!preg_match('/\p{Lu}/u', $password)) {
            $errores[] = 'Debe incluir al menos una letra MAYÚSCULA.';
        }
        if (!preg_match('/\p{Ll}/u', $password)) {
            $errores[] = 'Debe incluir al menos una letra minúscula.';
        }
        if (!preg_match('/\d/', $password)) {
            $errores[] = 'Debe incluir al menos un número.';
        }

        return $errores;
    }
}

/**
 * Convierte la lista de errores en un mensaje listo para mostrar en un alert().
 */
if (!function_exists('mensaje_politica_password')) {
    function mensaje_politica_password($errores) {
        return "La contraseña no cumple la política de seguridad:\n- " . implode("\n- ", $errores);
    }
}