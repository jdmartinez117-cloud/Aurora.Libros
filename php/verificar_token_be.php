<?php
// verificar_token_be.php
// Lo consulta el navegador cada pocos segundos para saber si la sesión sigue siendo válida.
// NO actualiza 'ultima_actividad' (así el bloqueo por inactividad sigue funcionando).

session_start(['read_and_close' => true]);

header('Content-Type: application/json');
header('Cache-Control: no-store');

include_once 'conexion_be.php';

$id = isset($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : 0;
$token = isset($_SESSION['session_token']) ? $_SESSION['session_token'] : '';

// Sin sesión (expiró o ya se cerró)
if ($id <= 0 || $token === '') {
    echo json_encode(['valido' => false, 'motivo' => 'sin_sesion']);
    exit;
}

$stmt = mysqli_prepare($conexion, "SELECT session_token FROM usuarios WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$fila = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if ($fila && !empty($fila['session_token']) && hash_equals($fila['session_token'], $token)) {
    echo json_encode(['valido' => true]);
} else {
    echo json_encode(['valido' => false, 'motivo' => 'duplicada']);
}
?>
