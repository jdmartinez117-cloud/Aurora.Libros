<?php
// desbloquear_be.php
// Recibe la contraseña enviada desde el modal de bloqueo para reactivar la sesión

session_start();
header('Content-Type: application/json');
include 'conexion_be.php';

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['ok' => false, 'msg' => 'Sesión no válida']);
    exit;
}

$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if (empty($password)) {
    echo json_encode(['ok' => false, 'msg' => 'Ingresa tu contraseña']);
    exit;
}

$email = $_SESSION['usuario'];

$stmt = mysqli_prepare($conexion, "SELECT password FROM usuarios WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultado) > 0) {
    $datos = mysqli_fetch_assoc($resultado);

    // Valida tanto el hash moderno (password_hash) como el legacy (sha512)
    if (password_verify($password, $datos['password']) || $datos['password'] === hash('sha512', $password)) {
        
        // =================================================================
        // ⏱️ REINICIO DEL RELOJ DE INACTIVIDAD
        // =================================================================
        $_SESSION['ultima_actividad'] = time(); // Pone el contador de inactividad a 0 SEGUNDOS
        $_SESSION['bloqueada'] = false;        // Desactiva el estado de bloqueo
        // =================================================================
        
        echo json_encode(['ok' => true]);
        exit;
    }
}

echo json_encode(['ok' => false, 'msg' => 'Contraseña incorrecta']);
?>