 <?php

    include 'conexion_be.php';

    // Recibir datos y limpiar espacios accidentales
    $nombre_completo = isset($_POST['nombre_completo']) ? trim($_POST['nombre_completo']) : '';
    $email           = isset($_POST['email']) ? trim($_POST['email']) : '';
    $usuario         = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $password        = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Comprobación de campos vacíos específicos
    if (empty($nombre_completo) && empty($email) && empty($usuario) && empty($password)) {
        echo '<script>alert("Todos los campos están en blanco. Por favor, llena el formulario."); window.location = "../index.php";</script>';
        exit();
    } elseif (empty($nombre_completo)) {
        echo '<script>alert("El campo \'Nombre Completo\' no puede estar vacío."); window.location = "../index.php";</script>';
        exit();
    } elseif (empty($email)) {
        echo '<script>alert("El campo \'Correo Electrónico\' no puede estar vacío."); window.location = "../index.php";</script>';
        exit();
    } elseif (empty($usuario)) {
        echo '<script>alert("El campo \'Usuario\' no puede estar vacío."); window.location = "../index.php";</script>';
        exit();
    } elseif (empty($password)) {
        echo '<script>alert("El campo \'Contraseña\' no puede estar vacío."); window.location = "../index.php";</script>';
        exit();
    }

    // Verificar que el correo no se repita en la base de datos
    $verificar_correo = mysqli_query($conexion, "SELECT * FROM usuarios WHERE email = '$email'");
    if (mysqli_num_rows($verificar_correo) > 0) {
        echo '
        <script>
            alert("Este correo ya está registrado, intenta con otro...");
            window.location = "../index.php";
        </script>    
        ';
        exit();
    }

    // Verificar que el usuario no se repita en la base de datos
    $verificar_usuario = mysqli_query($conexion, "SELECT * FROM usuarios WHERE usuario = '$usuario'");
    if (mysqli_num_rows($verificar_usuario) > 0) {
        echo '
        <script>
            alert("Este usuario ya está registrado, intenta con otro...");
            window.location = "../index.php";
        </script>    
        ';
        exit();
    }

    // Generar la contraseña segura una vez que sabemos que todo es válido
    $password_segura = password_hash($password, PASSWORD_DEFAULT);

    // Insertar en la base de datos
    $query = "INSERT INTO usuarios(nombre_completo, email, usuario, password)
              VALUES('$nombre_completo', '$email', '$usuario', '$password_segura')";

    $ejecutar = mysqli_query($conexion, $query);

    if ($ejecutar) {
        echo '
            <script>
                alert("Usuario almacenado exitosamente");
                window.location = "../index.php";
            </script>    
        ';
    } else {
        echo '
            <script>
                alert("Inténtalo de nuevo, usuario no almacenado");
                window.location = "../index.php";
            </script>    
        ';
    }

    mysqli_close($conexion);

?>