<?php
include 'conexion_be.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. (Opcional) Borrar la imagen física de la carpeta para no llenar el servidor
    $res = mysqli_query($conexion, "SELECT image FROM libros WHERE id = $id");
    $libro = mysqli_fetch_assoc($res);
    if (file_exists("img_libros/" . $libro['image'])) {
        unlink("img_libros/" . $libro['image']);
    }

    // 2. Borrar de la base de datos
    $query = "DELETE FROM libros WHERE id = $id";
    if (mysqli_query($conexion, $query)) {
        echo '<script>alert("Libro eliminado"); window.location="index_admin.php";</script>';
    } else {
        echo "Error al eliminar: " . mysqli_error($conexion);
    }
}
?>