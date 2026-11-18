<?php

    // Todo lo que se imprima por accidente (avisos de PHP, espacios en blanco) se guarda en un buffer
    // y se descarta antes de responder, para que la respuesta JSON llegue limpia.
    ob_start();

    // Pon false cuando termines de probar. En modo depuración el mensaje incluye el detalle técnico del error.
    $MODO_DEPURACION = true;

    // ¿La petición viene del formulario de index.php por fetch (sin recargar la página)?
    $es_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'fetch';

    /**
     * Termina el script con un mensaje.
     * - Por fetch: responde JSON y la página NO se recarga (el usuario corrige ahí mismo).
     * - Envío normal: alert + vuelve a index.php (comportamiento anterior).
     */
    function terminar($mensaje, $ok = false, $campo = '') {
        global $es_ajax;
        while (ob_get_level() > 0) { ob_end_clean(); } // descarta cualquier salida accidental anterior
        if ($es_ajax) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['ok' => $ok, 'mensaje' => $mensaje, 'campo' => $campo], JSON_UNESCAPED_UNICODE);
        } else {
            echo '<script>alert(' . json_encode($mensaje) . '); window.location = "../index.php";</script>';
        }
        exit();
    }

    // Errores fatales (archivo que falta, error de sintaxis...): respuesta clara en vez de una página rota
    register_shutdown_function(function () {
        global $es_ajax, $MODO_DEPURACION;
        $e = error_get_last();
        if ($es_ajax && $e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            error_log('registro_usuario_be.php: ' . $e['message']);
            while (ob_get_level() > 0) { ob_end_clean(); }
            header('Content-Type: application/json; charset=UTF-8');
            $msg = 'Error interno del servidor.' . ($MODO_DEPURACION ? ' Detalle: ' . $e['message'] : '');
            echo json_encode(['ok' => false, 'mensaje' => $msg, 'campo' => ''], JSON_UNESCAPED_UNICODE);
        }
    });

    // Excepciones no capturadas (por ejemplo, errores de MySQL)
    set_exception_handler(function ($e) {
        global $MODO_DEPURACION;
        error_log('registro_usuario_be.php: ' . $e->getMessage());
        terminar('Error interno del servidor.' . ($MODO_DEPURACION ? ' Detalle: ' . $e->getMessage() : ''));
    });

    include 'conexion_be.php';
    include 'verificar_captcha.php';
    require_once __DIR__ . '/politica_password.php';

    // =======================================================
    // 0. VALIDACIÓN DEL CAPTCHA
    // =======================================================
    $captcha_token = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
    if (!verificar_captcha($captcha_token)) {
        terminar("Por favor, confirma que no eres un robot.", false, 'captcha');
    }

    // Recibir datos y limpiar espacios accidentales
    $nombre_completo = isset($_POST['nombre_completo']) ? trim($_POST['nombre_completo']) : '';
    $email           = isset($_POST['email']) ? trim($_POST['email']) : '';
    $usuario         = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $password        = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Comprobación de campos vacíos específicos
    if (empty($nombre_completo) && empty($email) && empty($usuario) && empty($password)) {
        terminar("Todos los campos están en blanco. Por favor, llena el formulario.");
    } elseif (empty($nombre_completo)) {
        terminar("El campo 'Nombre Completo' no puede estar vacío.", false, 'nombre_completo');
    } elseif (empty($email)) {
        terminar("El campo 'Correo Electrónico' no puede estar vacío.", false, 'email');
    } elseif (empty($usuario)) {
        terminar("El campo 'Usuario' no puede estar vacío.", false, 'usuario');
    } elseif (empty($password)) {
        terminar("El campo 'Contraseña' no puede estar vacío.", false, 'password');
    }

    // =======================================================
    // POLÍTICA DE CONTRASEÑAS (mayúscula, minúscula, número y longitud mínima)
    // =======================================================
    $errores_password = validar_password($password);
    if (!empty($errores_password)) {
        terminar(mensaje_politica_password($errores_password), false, 'password');
    }

    // Verificar que el correo no se repita en la base de datos (consulta preparada)
    $stmt = mysqli_prepare($conexion, "SELECT id FROM usuarios WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        terminar("Este correo ya está registrado, intenta con otro...", false, 'email');
    }
    mysqli_stmt_close($stmt);

    // Verificar que el usuario no se repita en la base de datos (consulta preparada)
    $stmt = mysqli_prepare($conexion, "SELECT id FROM usuarios WHERE usuario = ?");
    mysqli_stmt_bind_param($stmt, "s", $usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        terminar("Este usuario ya está registrado, intenta con otro...", false, 'usuario');
    }
    mysqli_stmt_close($stmt);

    // Generar la contraseña segura una vez que sabemos que todo es válido
    $password_segura = password_hash($password, PASSWORD_DEFAULT);

    // Insertar en la base de datos (consulta preparada)
    $stmt = mysqli_prepare($conexion, "INSERT INTO usuarios(nombre_completo, email, usuario, password) VALUES(?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $nombre_completo, $email, $usuario, $password_segura);
    $ejecutar = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($ejecutar) {
        terminar("Usuario almacenado exitosamente", true);
    } else {
        terminar("Inténtalo de nuevo, usuario no almacenado");
    }

?>