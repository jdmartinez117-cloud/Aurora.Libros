<?php
// tests/Unit/PasswordPolicyTest.php
require_once __DIR__ . '/../../php/politica_password.php';
require_once __DIR__ . '/../../php/control_intentos.php';

class PasswordPolicyTest {

    public static function run(&$passed, &$failed) {
        echo "\n\033[1;34m[1. PRUEBAS UNITARIAS - Lógica de Negocio y Políticas]\033[0m\n";

        // Caso 1: Contraseña vacía debe fallar en todos los criterios
        self::assert(
            !empty(validar_password('')),
            "Contraseña vacía debe retornar errores de validación",
            $passed, $failed
        );

        // Caso 2: Menos de 8 caracteres
        $errLargo = validar_password('Ab1');
        self::assert(
            in_array('Debe tener al menos 8 caracteres.', $errLargo),
            "Contraseña corta ('Ab1') debe fallar por longitud mínima",
            $passed, $failed
        );

        // Caso 3: Sin mayúscula
        $errMayus = validar_password('auroralibros123');
        self::assert(
            in_array('Debe incluir al menos una letra MAYÚSCULA.', $errMayus),
            "Contraseña sin mayúsculas ('auroralibros123') debe requerir al menos una mayúscula",
            $passed, $failed
        );

        // Caso 4: Sin minúscula
        $errMinus = validar_password('AURORA_LIBROS_123');
        self::assert(
            in_array('Debe incluir al menos una letra minúscula.', $errMinus),
            "Contraseña en mayúsculas sostenidas debe requerir al menos una minúscula",
            $passed, $failed
        );

        // Caso 5: Sin número
        $errNum = validar_password('AuroraLibrosSecret');
        self::assert(
            in_array('Debe incluir al menos un número.', $errNum),
            "Contraseña sin dígitos ('AuroraLibrosSecret') debe requerir al menos un número",
            $passed, $failed
        );

        // Caso 6: Contraseña que cumple todos los requisitos
        $errValida = validar_password('AuroraSegura2026');
        self::assert(
            empty($errValida),
            "Contraseña válida ('AuroraSegura2026') debe pasar sin errores",
            $passed, $failed
        );

        // Caso 7: Función auxiliar de formato de errores
        $msg = mensaje_politica_password(['Falta un número.']);
        self::assert(
            strpos($msg, 'La contraseña no cumple la política de seguridad') !== false,
            "mensaje_politica_password() genera el encabezado esperado",
            $passed, $failed
        );

        // Caso 8: Normalización de identificadores en control de intentos
        $idNormalizado = intentos_normalizar('  Lector.Aurora@Email.COM  ');
        self::assert(
            $idNormalizado === 'lector.aurora@email.com',
            "intentos_normalizar() elimina espacios exteriores y convierte a minúsculas",
            $passed, $failed
        );

        // Caso 9: Texto de tiempo de bloqueo
        self::assert(
            texto_tiempo_bloqueo(45) === '1 minuto' && texto_tiempo_bloqueo(120) === '2 minutos',
            "texto_tiempo_bloqueo() redondea correctamente segundos a minutos en singular/plural",
            $passed, $failed
        );
    }

    private static function assert($condition, $description, &$passed, &$failed) {
        if ($condition) {
            $passed++;
            echo "  \033[32m✔ [PASSED]\033[0m $description\n";
        } else {
            $failed++;
            echo "  \033[31m✘ [FAILED]\033[0m $description\n";
        }
    }
}

// Ejecución directa desde consola si se llama este archivo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    $passed = 0; $failed = 0;
    PasswordPolicyTest::run($passed, $failed);
    echo "\n---------------------------------------------------------------\n";
    echo "Resultado Unitarias: $passed Aprobadas | $failed Fallidas\n";
    exit($failed > 0 ? 1 : 0);
}
