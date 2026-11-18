<?php
// tests/run_tests.php
// Ejecutor principal de pruebas automatizadas para Aurora.Libros

$inicio = microtime(true);

echo "===============================================================\n";
echo "   SUITE DE PRUEBAS DE SOFTWARE (100% BLINDADA) - AURORA.LIBROS \n";
echo "===============================================================\n";
echo "Fecha y hora: " . date('Y-m-d H:i:s') . "\n";
echo "PHP Version : " . phpversion() . "\n";

$passed = 0;
$failed = 0;

// 1. Pruebas Unitarias
require_once __DIR__ . '/Unit/PasswordPolicyTest.php';
PasswordPolicyTest::run($passed, $failed);

// 2. Pruebas de Integración
require_once __DIR__ . '/Integration/DatabaseAuthTest.php';
DatabaseAuthTest::run($passed, $failed);

// 3. Pruebas del Sistema
require_once __DIR__ . '/System/ApiSystemTest.php';
ApiSystemTest::run($passed, $failed);

// 4. Pruebas de Seguridad
require_once __DIR__ . '/Security/SecurityTest.php';
SecurityTest::run($passed, $failed);

$duracion = round((microtime(true) - $inicio) * 1000, 2);
$total = $passed + $failed;

echo "\n===============================================================\n";
echo "                       RESUMEN DE PRUEBAS                      \n";
echo "===============================================================\n";
echo "Total de pruebas ejecutadas : $total\n";
echo "\033[32mPruebas exitosas (Passed)   : $passed\033[0m\n";

if ($failed > 0) {
    echo "\033[31mPruebas fallidas (Failed)   : $failed\033[0m\n";
} else {
    echo "Pruebas fallidas (Failed)   : 0\n";
}

echo "Tiempo total de ejecución   : {$duracion} ms\n";
echo "===============================================================\n";

if ($failed > 0) {
    echo "\033[31mRESULTADO GLOBAL: REPROBADO (Revisa los errores arriba)\033[0m\n\n";
    exit(1);
} else {
    echo "\033[32mRESULTADO GLOBAL: APROBADO EXITOSAMENTE (100% BLINDADO)\033[0m\n\n";
    exit(0);
}
