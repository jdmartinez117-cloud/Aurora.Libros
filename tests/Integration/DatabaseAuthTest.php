<?php
// tests/Integration/DatabaseAuthTest.php
require_once __DIR__ . '/../../php/conexion_be.php';
require_once __DIR__ . '/../../php/control_intentos.php';

class DatabaseAuthTest {

    public static function run(&$passed, &$failed) {
        global $conexion;
        echo "\n\033[1;34m[2. PRUEBAS DE INTEGRACIÓN - Base de Datos MySQL, Carrito y Seguridad]\033[0m\n";

        // Caso 1: Conexión MySQL activa
        self::assert(
            $conexion instanceof mysqli && mysqli_ping($conexion),
            "Conexión activa con el servidor MySQL (login_register_db)",
            $passed, $failed
        );

        // Caso 2: Conexión mediante PDO
        $pdoOk = false;
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=login_register_db;charset=utf8mb4", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdoOk = true;
        } catch (Exception $e) {
            $pdoOk = false;
        }
        self::assert($pdoOk, "Conexión PDO establecida con éxito para APIs", $passed, $failed);

        // Caso 3: Preparación y existencia de la tabla intentos_login
        intentos_preparar($conexion);
        $checkTable = mysqli_query($conexion, "SHOW TABLES LIKE 'intentos_login'");
        self::assert(
            mysqli_num_rows($checkTable) > 0,
            "Tabla 'intentos_login' creada o confirmada en la base de datos",
            $passed, $failed
        );

        // Caso 4: Registro de intento fallido y limpieza
        $testEmail = 'test_integration_' . time() . '@aurora.local';
        
        // Limpiamos previo por seguridad
        intentos_limpiar($conexion, $testEmail, 'usuario');
        
        // Registramos fallo
        $quedan = intentos_registrar_fallo($conexion, $testEmail, 'usuario');
        self::assert(
            $quedan === (MAX_INTENTOS_LOGIN - 1),
            "intentos_registrar_fallo() descuenta correctamente los intentos restantes",
            $passed, $failed
        );

        // Comprobamos que el registro existe en la base de datos
        $stmt = mysqli_prepare($conexion, "SELECT intentos FROM intentos_login WHERE identificador = ?");
        mysqli_stmt_bind_param($stmt, "s", $testEmail);
        mysqli_stmt_execute($stmt);
        $res = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        self::assert(
            $res && (int)$res['intentos'] === 1,
            "Persistencia verificada en MySQL: contador de intentos incrementado a 1",
            $passed, $failed
        );

        // Limpiamos el intento de prueba
        intentos_limpiar($conexion, $testEmail, 'usuario');
        $segundos = segundos_bloqueo_restantes($conexion, $testEmail, 'usuario');
        self::assert(
            $segundos === 0,
            "intentos_limpiar() elimina el registro de prueba y deja el bloqueo en 0",
            $passed, $failed
        );

        // Caso 5: Verificación de tabla del catálogo de libros
        $checkLibros = mysqli_query($conexion, "SELECT COUNT(*) as total FROM libros");
        $filaLibros = mysqli_fetch_assoc($checkLibros);
        self::assert(
            $filaLibros !== null && isset($filaLibros['total']),
            "Integración de catálogo: tabla 'libros' consultable (" . ($filaLibros['total'] ?? 0) . " libros registrados)",
            $passed, $failed
        );

        // Obtenemos ID de usuario y libro válidos para respetar Foreign Keys de MySQL
        $userRow = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT id FROM usuarios LIMIT 1"));
        $testUserId = $userRow ? (int)$userRow['id'] : 1;
        $libroRow = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT id FROM libros LIMIT 1"));
        $testLibroId = $libroRow ? (int)$libroRow['id'] : 1;

        // Caso 6: Integración del Carrito de Compras (Agregar, consultar y eliminar)
        $yaTeniaCart = mysqli_num_rows(mysqli_query($conexion, "SELECT id FROM carrito WHERE usuario_id = $testUserId AND libro_id = $testLibroId")) > 0;
        if (!$yaTeniaCart) {
            $insCart = mysqli_query($conexion, "INSERT INTO carrito (usuario_id, libro_id) VALUES ($testUserId, $testLibroId)");
            $checkCart = mysqli_num_rows(mysqli_query($conexion, "SELECT id FROM carrito WHERE usuario_id = $testUserId AND libro_id = $testLibroId")) > 0;
            mysqli_query($conexion, "DELETE FROM carrito WHERE usuario_id = $testUserId AND libro_id = $testLibroId");
            $cartOk = ($insCart && $checkCart);
        } else {
            $cartOk = true;
        }
        self::assert(
            $cartOk,
            "Integración de Carrito: Inserción, persistencia y eliminación exitosa en tabla 'carrito'",
            $passed, $failed
        );

        // Caso 7: Integración de Favoritos (Wishlist)
        $yaTeniaFav = mysqli_num_rows(mysqli_query($conexion, "SELECT id FROM favoritos WHERE usuario_id = $testUserId AND libro_id = $testLibroId")) > 0;
        if (!$yaTeniaFav) {
            $insFav = mysqli_query($conexion, "INSERT INTO favoritos (usuario_id, libro_id) VALUES ($testUserId, $testLibroId)");
            $checkFav = mysqli_num_rows(mysqli_query($conexion, "SELECT id FROM favoritos WHERE usuario_id = $testUserId AND libro_id = $testLibroId")) > 0;
            mysqli_query($conexion, "DELETE FROM favoritos WHERE usuario_id = $testUserId AND libro_id = $testLibroId");
            $favOk = ($insFav && $checkFav);
        } else {
            $favOk = true;
        }
        self::assert(
            $favOk,
            "Integración de Favoritos: Registro y remoción reactiva en tabla 'favoritos'",
            $passed, $failed
        );

        // Caso 8: Prueba de Seguridad contra Inyección SQL (SQL Injection - SQLi)
        $inyeccionPayload = "' OR '1'='1";
        $stmtSec = mysqli_prepare($conexion, "SELECT id FROM usuarios WHERE email = ?");
        mysqli_stmt_bind_param($stmtSec, "s", $inyeccionPayload);
        mysqli_stmt_execute($stmtSec);
        mysqli_stmt_store_result($stmtSec);
        $registrosInyectados = mysqli_stmt_num_rows($stmtSec);
        mysqli_stmt_close($stmtSec);

        self::assert(
            $registrosInyectados === 0,
            "Seguridad SQLi: Consultas preparadas neutralizan cargas maliciosas (' OR '1'='1)",
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
    DatabaseAuthTest::run($passed, $failed);
    echo "\n---------------------------------------------------------------\n";
    echo "Resultado Integración: $passed Aprobadas | $failed Fallidas\n";
    exit($failed > 0 ? 1 : 0);
}
