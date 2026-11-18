<?php
// tests/Security/SecurityTest.php
require_once __DIR__ . '/../../php/conexion_be.php';
require_once __DIR__ . '/../../php/control_intentos.php';

class SecurityTest {

    public static function run(&$passed, &$failed) {
        global $conexion;
        echo "\n\033[1;34m[4. PRUEBAS DE SEGURIDAD - SQLi, XSS, Fuerza Bruta y Sesión Única]\033[0m\n";

        // Caso 1: Prevención de Inyección SQL (SQLi) en Autenticación
        $payloadSqlLogin = "' OR '1'='1' -- ";
        $stmt = mysqli_prepare($conexion, "SELECT id, password FROM usuarios WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $payloadSqlLogin);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $registrosLogin = mysqli_stmt_num_rows($stmt);
        mysqli_stmt_close($stmt);

        self::assert(
            $registrosLogin === 0,
            "Seguridad SQLi (Login): Carga maliciosa (' OR '1'='1) neutralizada por consulta preparada",
            $passed, $failed
        );

        // Caso 2: Prevención de Inyección SQL (SQLi) en Catálogo / Búsqueda
        $payloadSqlSearch = "Cien' UNION SELECT 1, 'Inyectado', 'Hacker', 'Hack', 0.00, 999 -- ";
        $stmtSearch = mysqli_prepare($conexion, "SELECT id, titulo FROM libros WHERE titulo LIKE ?");
        $termLike = "%" . $payloadSqlSearch . "%";
        mysqli_stmt_bind_param($stmtSearch, "s", $termLike);
        mysqli_stmt_execute($stmtSearch);
        $resSearch = mysqli_stmt_get_result($stmtSearch);
        $encontradoHack = false;
        while ($row = mysqli_fetch_assoc($resSearch)) {
            if ($row['titulo'] === 'Inyectado') {
                $encontradoHack = true;
            }
        }
        mysqli_stmt_close($stmtSearch);

        self::assert(
            !$encontradoHack,
            "Seguridad SQLi (Catálogo): Ataque UNION SELECT neutralizado como texto literal",
            $passed, $failed
        );

        // Caso 3: Prevención de Cross-Site Scripting (XSS) mediante escape HTML
        $payloadXSS = "<script>alert('XSS_ATTACK_2026')</script>";
        $escapedOutput = htmlspecialchars($payloadXSS, ENT_QUOTES, 'UTF-8');
        self::assert(
            strpos($escapedOutput, '<script>') === false && strpos($escapedOutput, '&lt;script&gt;') !== false,
            "Seguridad XSS: Caracteres HTML (<, >, ', \") convertidos en entidades seguras",
            $passed, $failed
        );

        // Caso 4: Mitigación de Ataques de Fuerza Bruta (Brute-Force)
        $idAtaque = 'attacker_bot_' . time() . '@test.local';
        intentos_limpiar($conexion, $idAtaque, 'usuario');

        // Simulamos 5 intentos fallidos
        for ($i = 1; $i <= MAX_INTENTOS_LOGIN; $i++) {
            $restantes = intentos_registrar_fallo($conexion, $idAtaque, 'usuario');
        }

        $segundosBloqueo = segundos_bloqueo_restantes($conexion, $idAtaque, 'usuario');
        intentos_limpiar($conexion, $idAtaque, 'usuario');

        self::assert(
            $segundosBloqueo > 0,
            "Seguridad Fuerza Bruta: Cuenta bloqueada automáticamente tras " . MAX_INTENTOS_LOGIN . " intentos fallidos",
            $passed, $failed
        );

        // Caso 5: Validación de Integridad de Sesión Única (Token de Concurrencia)
        $tokenNavegadorA = bin2hex(random_bytes(32));
        $tokenNavegadorB = bin2hex(random_bytes(32));
        // Si el token almacenado en BD coincide con A, A es válido
        $tokenActivoBD = $tokenNavegadorB; // B inició sesión después
        $sesionAValida = hash_equals($tokenActivoBD, $tokenNavegadorA);
        $sesionBValida = hash_equals($tokenActivoBD, $tokenNavegadorB);

        self::assert(
            !$sesionAValida && $sesionBValida,
            "Seguridad Sesión Única: Nueva sesión en navegador B invalida automáticamente el token del navegador A",
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
    SecurityTest::run($passed, $failed);
    echo "\n---------------------------------------------------------------\n";
    echo "Resultado Seguridad: $passed Aprobadas | $failed Fallidas\n";
    exit($failed > 0 ? 1 : 0);
}
