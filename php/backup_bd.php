<?php
session_start();

include 'conexion_be.php';

// 1. Asegurar la conexión a la base de datos principal
$nombre_bd = 'login_register_db';
mysqli_select_db($conexion, $nombre_bd);
mysqli_set_charset($conexion, 'utf8mb4');

// Configuración de fecha y nombre
date_default_timezone_set('America/Bogota'); // Ajusta a tu zona horaria si difiere
$fecha_actual = date('Y-m-d_H-i-s');
$nombre_archivo = "backup_{$nombre_bd}_{$fecha_actual}.sql";

// 2. Cabecera limpia y directivas seguras
$sql_dump  = "-- ============================================================\n";
$sql_dump .= "-- AURORA.LIBROS - COPIA DE SEGURIDAD TOTAL\n";
$sql_dump .= "-- Base de datos: `{$nombre_bd}`\n";
$sql_dump .= "-- Fecha de exportación: " . date('Y-m-d H:i:s') . "\n";
$sql_dump .= "-- ============================================================\n\n";

$sql_dump .= "CREATE DATABASE IF NOT EXISTS `{$nombre_bd}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;\n";
$sql_dump .= "USE `{$nombre_bd}`;\n\n";
$sql_dump .= "SET FOREIGN_KEY_CHECKS = 0;\n";
$sql_dump .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
$sql_dump .= "SET NAMES utf8mb4;\n\n";

// 3. Obtener listado de todas las tablas
$tablas = [];
$res_tablas = mysqli_query($conexion, "SHOW TABLES FROM `{$nombre_bd}`");
while ($row = mysqli_fetch_row($res_tablas)) {
    $tablas[] = $row[0];
}

// 4. Extraer estructura y registros de cada tabla
foreach ($tablas as $tabla) {
    // --- Estructura ---
    $sql_dump .= "-- Estructura de tabla para `{$tabla}`\n";
    $sql_dump .= "DROP TABLE IF EXISTS `{$tabla}`;\n";

    $res_create = mysqli_query($conexion, "SHOW CREATE TABLE `{$tabla}`");
    $row_create = mysqli_fetch_row($res_create);
    $sql_dump .= $row_create[1] . ";\n\n";

    // --- Registros ---
    $res_datos = mysqli_query($conexion, "SELECT * FROM `{$tabla}`");
    $num_filas = mysqli_num_rows($res_datos);

    if ($num_filas > 0) {
        $sql_dump .= "-- Registros de la tabla `{$tabla}` ({$num_filas} filas)\n";
        $num_columnas = mysqli_num_fields($res_datos);

        while ($fila = mysqli_fetch_row($res_datos)) {
            $sql_dump .= "INSERT INTO `{$tabla}` VALUES (";
            for ($i = 0; $i < $num_columnas; $i++) {
                if (is_null($fila[$i])) {
                    $sql_dump .= "NULL";
                } else {
                    // Escapar caracteres para SQL seguro
                    $valor = mysqli_real_escape_string($conexion, $fila[$i]);
                    // Convertir saltos de línea literales en secuencias escapadas
                    $valor = str_replace(["\r\n", "\r", "\n"], "\\n", $valor);
                    $sql_dump .= "'" . $valor . "'";
                }

                if ($i < ($num_columnas - 1)) {
                    $sql_dump .= ", ";
                }
            }
            $sql_dump .= ");\n";
        }
        $sql_dump .= "\n";
    }
}

$sql_dump .= "SET FOREIGN_KEY_CHECKS = 1;\n";
$sql_dump .= "-- Fin de la copia de seguridad\n";

// Registrar en actividad si existe la tabla
@mysqli_query($conexion, "INSERT INTO actividad_usuarios (usuario_id, tipo_actividad, descripcion) VALUES (NULL, 'BACKUP', 'Copia de seguridad de login_register_db descargada')");

// 5. Forzar descarga inmediata del archivo SQL
header('Content-Type: application/sql; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . strlen($sql_dump));

echo $sql_dump;
exit;
?>