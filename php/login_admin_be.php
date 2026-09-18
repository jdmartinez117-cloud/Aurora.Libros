<?php
    session_start();
    
    // Incluimos la conexión (asegúrate de que la ruta sea correcta)
    include 'conexion_be.php';

    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Encriptamiento de contraseña (debe coincidir con el registro del admin)
    $password = hash('sha512', $password);

    // IMPORTANTE: Cambia 'administradores' por el nombre real de tu tabla de admins en la DB
    $validar_login = mysqli_query($conexion, "SELECT * FROM administradores WHERE email='$email' and password='$password'");

    if(mysqli_num_rows($validar_login) > 0){
        // Creamos una sesión específica para el administrador
        $_SESSION['admin'] = $email;
        
        // Redirigimos al panel de control del administrador
        header("location: ./index_admin.php"); 
        exit;
    } else {
        echo '
            <script>
                alert("Acceso denegado. Credenciales de administrador incorrectas.");
                window.location = "../index.php";
            </script>
        ';
        exit;
    }
?>