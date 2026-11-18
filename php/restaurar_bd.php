<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Aumentar límites para procesar archivos grandes
ini_set('memory_limit', '256M');
set_time_limit(300);

include 'conexion_be.php';

$nombre_bd = 'login_register_db';
mysqli_select_db($conexion, $nombre_bd);
mysqli_set_charset($conexion, 'utf8mb4');

// 1. Validar subida del archivo
if (!isset($_FILES['archivo_sql']) || $_FILES['archivo_sql']['error'] !== UPLOAD_ERR_OK) {
    $error_msg = 'Error al subir el archivo.';
    if (isset($_FILES['archivo_sql'])) {
        switch ($_FILES['archivo_sql']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $error_msg = 'El archivo supera el tamaño máximo permitido por el servidor.';
                break;
            case UPLOAD_ERR_NO_FILE:
                $error_msg = 'No se seleccionó ningún archivo.';
                break;
        }
    }
    echo json_encode(['status' => 'error', 'message' => $error_msg]);
    exit;
}

$archivo_tmp = $_FILES['archivo_sql']['tmp_name'];
$nombre_archivo = $_FILES['archivo_sql']['name'];

if (!str_ends_with(strtolower($nombre_archivo), '.sql')) {
    echo json_encode(['status' => 'error', 'message' => 'El archivo debe tener extensión .sql']);
    exit;
}

// 2. Leer archivo línea por línea y procesar consultas
$lineas = file($archivo_tmp, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if ($lineas === false || count($lineas) === 0) {
    echo json_encode(['status' => 'error', 'message' => 'El archivo SQL está vacío o no se pudo leer.']);
    exit;
}

// Desactivar temporalmente revisión de claves foráneas
mysqli_query($conexion, "SET FOREIGN_KEY_CHECKS = 0;");
mysqli_query($conexion, "SET NAMES utf8mb4;");

$query_actual = '';
$consultas_exitosas = 0;
$errores = [];

foreach ($lineas as $linea) {
    $linea_trim = trim($linea);

    // Omitir líneas de comentarios SQL
    if (empty($linea_trim) || 
        str_starts_with($linea_trim, '--') || 
        str_starts_with($linea_trim, '/*') || 
        str_starts_with($linea_trim, '#')) {
        continue;
    }

    $query_actual .= $linea . "\n";

    // Si la línea termina con ';' significa fin de la sentencia
    if (str_ends_with($linea_trim, ';')) {
        if (!mysqli_query($conexion, $query_actual)) {
            $errores[] = mysqli_error($conexion);
            // Si hay un error crítico, detenemos el proceso
            if (count($errores) > 5) break;
        } else {
            $consultas_exitosas++;
        }
        $query_actual = '';
    }
}

// Reactivar claves foráneas
mysqli_query($conexion, "SET FOREIGN_KEY_CHECKS = 1;");

// 3. Resultado final
if (count($errores) > 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Ocurrieron errores al restaurar: ' . implode(' | ', array_slice($errores, 0, 3))
    ]);
} else {
    // Registrar restauración exitosa
    @mysqli_query($conexion, "INSERT INTO actividad_usuarios (usuario_id, tipo_actividad, descripcion) VALUES (NULL, 'RESTAURACION', 'Base de datos login_register_db restaurada con éxito')");

    echo json_encode([
        'status' => 'success',
        'message' => "Base de datos `{$nombre_bd}` restaurada completamente ({$consultas_exitosas} sentencias ejecutadas)."
    ]);
}
?>