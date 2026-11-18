// favoritos_be.php
<?php
session_start();
include 'conexion_be.php';

if(!isset($_SESSION['id_usuario'])){
    die("Debes iniciar sesión");
}

$id_usuario = $_SESSION['id_usuario']; // ID privado del usuario actual
$id_producto = $_POST['id_producto'];  // ID del libro seleccionado

$query = "INSERT INTO favoritos (usuario_id, producto_id) VALUES ('$id_usuario', '$id_producto')";
mysqli_query($conexion, $query);
?>
