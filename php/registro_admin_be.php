<?php

    include 'conexion_be.php';

    $nombre_completo = $_POST['nombre_completo'];
    $email = $_POST['email'];
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
    // [NUEVO] Se recibe el dato adicional del cargo del administrativo
    $cargo = $_POST['cargo']; 
    
    // Encriptación de contraseña
    $password = hash('sha512', $password);

    // [NUEVO] El query ahora apunta a la tabla 'administradores' e incluye la columna 'cargo'
    $query = "INSERT INTO administradores(nombre_completo, email, usuario, password, cargo)
            VALUES('$nombre_completo', '$email', '$usuario', '$password', '$cargo')";

    // [NUEVO] Verificar que el correo no se repita en la tabla de ADMINISTRADORES
    $verificar_correo = mysqli_query($conexion, "SELECT * FROM administradores WHERE email ='$email' ");

    if(mysqli_num_rows($verificar_correo) > 0 ){
        echo '
        <script>
            alert("Este correo de administrador ya está registrado, intenta con otro...");
            window.location = "../index.php";
        </script>    
        ';
        exit();
    }

    // [NUEVO] Verificar que el usuario no se repita en la tabla de ADMINISTRADORES
    $verificar_usuario = mysqli_query($conexion, "SELECT * FROM administradores WHERE usuario ='$usuario' ");

    if(mysqli_num_rows($verificar_usuario) > 0 ){
        echo '
        <script>
            alert("Este nombre de usuario administrador ya está registrado, intenta con otro...");
            window.location = "../index.php";
        </script>    
        ';
        exit();
    }    
    
    $ejecutar = mysqli_query($conexion, $query);

    if($ejecutar){
        echo '
            <script>
                alert("Administrador almacenado exitosamente");
                window.location = "../index.php";
            </script>    
        ';
    }else{
        echo '
            <script>
                alert("Inténtalo de nuevo, administrador no almacenado");
                window.location = "../index.php";
            </script>    
        ';
    }

    mysqli_close($conexion);

?>