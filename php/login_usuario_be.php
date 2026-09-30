<?php
    session_start();

    include 'conexion_be.php';
    include 'verificar_captcha.php';

    // =======================================================
    // 0. VALIDACIÓN DEL CAPTCHA (INTACTA)
    // =======================================================
    $captcha_token = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
    if (!verificar_captcha($captcha_token)) {
        echo '
            <script>
                alert("Por favor, confirma que no eres un robot.");
                window.location = "../index.php";
            </script>
        ';
        exit;
    }

    // =======================================================
    // 1. CAPTURA Y LIMPIEZA DE DATOS
    // =======================================================
    // Acepta tanto 'email' como 'correo' según el name de tu formulario
    $email = isset($_POST['email']) ? trim($_POST['email']) : (isset($_POST['correo']) ? trim($_POST['correo']) : '');
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($email) && empty($password)) {
        echo '
            <script>
                alert("Por favor, completa todos los campos (correo y contraseña).");
                window.location = "../index.php";
            </script>
        ';
        exit;
    } elseif (empty($email)) {
        echo '
            <script>
                alert("Por favor, ingresa tu correo electrónico.");
                window.location = "../index.php";
            </script>
        ';
        exit;
    } elseif (empty($password)) {
        echo '
            <script>
                alert("Por favor, ingresa tu contraseña.");
                window.location = "../index.php";
            </script>
        ';
        exit;
    }

    // =======================================================
    // 2. CONSULTA SEGURA (Sentencia Preparada)
    // =======================================================
    $stmt = mysqli_prepare($conexion, "SELECT * FROM usuarios WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    // =======================================================
    // 3. VALIDACIÓN DE CONTRASEÑA Y SESIÓN
    // =======================================================
    if (mysqli_num_rows($resultado) > 0) {
        $usuario_datos = mysqli_fetch_assoc($resultado);
        $login_valido = false;

        // CASO A: Contraseña con password_hash moderno
        if (password_verify($password, $usuario_datos['password'])) {
            $login_valido = true;
        } 
        // CASO B: Usuario antiguo con hash SHA-512 (Migración automática)
        elseif ($usuario_datos['password'] === hash('sha512', $password)) {
            $login_valido = true;
            $nuevo_hash = password_hash($password, PASSWORD_DEFAULT);
            $id_usuario = (int)$usuario_datos['id'];
            mysqli_query($conexion, "UPDATE usuarios SET password = '$nuevo_hash' WHERE id = '$id_usuario'");
        }

        // SI LA CONTRASEÑA ES CORRECTA:
        if ($login_valido) {
            $usuario_id = (int)$usuario_datos['id'];

            // Variables de sesión
            $_SESSION['usuario'] = $usuario_datos['email'];
            $_SESSION['id_usuario'] = $usuario_id;
            $_SESSION['nombre_usuario'] = $usuario_datos['nombre_completo'];
            $_SESSION['ultima_actividad'] = time();

            // --- REGISTRAR EN ACTIVIDAD_USUARIOS CON EL ID REAL ---
            $tipo_act = 'LOGIN';
            $desc_act = 'Inicio de sesión en la plataforma';

            $stmt_act = mysqli_prepare($conexion, "INSERT INTO actividad_usuarios (usuario_id, tipo_actividad, descripcion) VALUES (?, ?, ?)");
            if ($stmt_act) {
                mysqli_stmt_bind_param($stmt_act, "iss", $usuario_id, $tipo_act, $desc_act);
                mysqli_stmt_execute($stmt_act);
                mysqli_stmt_close($stmt_act);
            }

            header("location: ./bienvenida.php");
            exit;

        } else {
            // Contraseña incorrecta
            echo '
               <script>
                    alert("Contraseña incorrecta. Por favor, inténtalo de nuevo.");
                    window.location = "../index.php";
                </script>
            ';
            exit;
        }
    } else {
        // Correo no registrado
        echo '
           <script>
                alert("El usuario o correo electrónico no existe. Verifica tus datos.");
                window.location = "../index.php";
            </script>
        ';
        exit;
    }
?>