<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

include 'conexion_be.php';

$response = [
    'status' => 'success',
    'kpis' => [
        'total_usuarios' => 0,
        'usuarios_activos_24h' => 0,
        'total_pedidos' => 0,
        'total_ventas' => 0.00,
        'total_libros' => 0,
        'stock_total' => 0
    ],
    'ventas_dias' => [],
    'categorias' => [],
    'top_libros' => [],
    'actividad_reciente' => [],
    'debug_errores' => []
];

try {
    // 1. KPIS GENERALES
    // Total Usuarios
    $res = mysqli_query($conexion, "SELECT COUNT(*) as total FROM usuarios");
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $response['kpis']['total_usuarios'] = (int)$row['total'];
    }

    // Usuarios activos últimas 24h
    $res = mysqli_query($conexion, "SELECT COUNT(DISTINCT usuario_id) as activos FROM actividad_usuarios WHERE tipo_actividad = 'LOGIN' AND fecha >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $response['kpis']['usuarios_activos_24h'] = (int)$row['activos'];
    }

    // Total Pedidos y Total Ventas
    $res = mysqli_query($conexion, "SELECT COUNT(*) as total_pedidos, COALESCE(SUM(total), 0) as total_ventas FROM pedidos");
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $response['kpis']['total_pedidos'] = (int)$row['total_pedidos'];
        $response['kpis']['total_ventas'] = (float)$row['total_ventas'];
    }

    // Total Libros y Stock Total
    $res = mysqli_query($conexion, "SELECT COUNT(*) as total_libros, COALESCE(SUM(stock), 0) as stock_total FROM libros");
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $response['kpis']['total_libros'] = (int)$row['total_libros'];
        $response['kpis']['stock_total'] = (int)$row['stock_total'];
    }

    // 2. GRÁFICO: VENTAS POR FECHA (Muestra todas las fechas registradas ordenadas)
    $sql_ventas = "SELECT DATE(fecha) as dia, COUNT(*) as cant_pedidos, COALESCE(SUM(total), 0) as suma_ventas 
                   FROM pedidos 
                   GROUP BY DATE(fecha) 
                   ORDER BY dia ASC";
    $res_ventas = mysqli_query($conexion, $sql_ventas);
    if ($res_ventas) {
        while ($row = mysqli_fetch_assoc($res_ventas)) {
            $response['ventas_dias'][] = [
                'dia' => date('d/m', strtotime($row['dia'])),
                'pedidos' => (int)$row['cant_pedidos'],
                'ventas' => (float)$row['suma_ventas']
            ];
        }
    } else {
        $response['debug_errores']['ventas'] = mysqli_error($conexion);
    }

    // 3. GRÁFICO: LIBROS POR CATEGORÍA
    $sql_cat = "SELECT category, COUNT(*) as cantidad FROM libros GROUP BY category ORDER BY cantidad DESC";
    $res_cat = mysqli_query($conexion, $sql_cat);
    if ($res_cat) {
        while ($row = mysqli_fetch_assoc($res_cat)) {
            $response['categorias'][] = [
                'categoria' => $row['category'] ?: 'General',
                'cantidad' => (int)$row['cantidad']
            ];
        }
    } else {
        $response['debug_errores']['categorias'] = mysqli_error($conexion);
    }

    // 4. GRÁFICO: TOP 5 LIBROS MÁS VENDIDOS (Auto-adaptativo)
    // Verificamos si existe la columna 'cantidad' en detalle_pedidos
    $check_cant = mysqli_query($conexion, "SHOW COLUMNS FROM detalle_pedidos LIKE 'cantidad'");
    $campo_conteo = ($check_cant && mysqli_num_rows($check_cant) > 0) ? "COALESCE(SUM(dp.cantidad), COUNT(*))" : "COUNT(*)";
    // Usamos COUNT(*) para no depender de si existe dp.id
    $sql_top = "SELECT l.titulo, $campo_conteo as total_vendidos 
                FROM detalle_pedidos dp 
                JOIN libros l ON dp.libro_id = l.id 
                GROUP BY l.id, l.titulo 
                ORDER BY total_vendidos DESC 
                LIMIT 5";
                
    $res_top = mysqli_query($conexion, $sql_top);
    if ($res_top) {
        while ($row = mysqli_fetch_assoc($res_top)) {
            $response['top_libros'][] = [
                'titulo' => $row['titulo'],
                'vendidos' => (int)$row['total_vendidos']
            ];
        }
    } else {
        $response['debug_errores']['top_libros'] = mysqli_error($conexion);
    }

    // 5. TABLA: ACTIVIDAD RECIENTE (Con nombre de usuario real)
    $sql_act = "SELECT a.id, a.tipo_actividad, a.descripcion, a.fecha, 
                       COALESCE(u.nombre_completo, u.usuario, 'Usuario') as nombre_usuario 
                FROM actividad_usuarios a 
                LEFT JOIN usuarios u ON a.usuario_id = u.id 
                ORDER BY a.fecha DESC 
                LIMIT 12";
    $res_act = mysqli_query($conexion, $sql_act);
    if ($res_act) {
        while ($row = mysqli_fetch_assoc($res_act)) {
            $response['actividad_reciente'][] = [
                'id' => $row['id'],
                'usuario' => $row['nombre_usuario'],
                'tipo' => $row['tipo_actividad'],
                'descripcion' => $row['descripcion'],
                'fecha' => date('d/m/Y H:i', strtotime($row['fecha']))
            ];
        }
    } else {
        $response['debug_errores']['actividad'] = mysqli_error($conexion);
    }

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>