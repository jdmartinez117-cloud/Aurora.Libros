<?php
session_start();
include 'conexion_be.php';

// Recibir datos
$id          = $_POST['id'] ?? '';
$titulo      = $_POST['titulo'];
$author      = $_POST['author'];
$category    = $_POST['category'];
$price       = $_POST['price'];
$stock       = $_POST['stock'];
$description = $_POST['description'];

// Lógica de imagen
$nueva_imagen = false;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $nombre_img = time() . "." . $extension;
    if(move_uploaded_file($_FILES['image']['tmp_name'], "img_libros/" . $nombre_img)){
        $nueva_imagen = true;
    }
}

if (empty($id)) {
    // --- MODO REGISTRO (INSERT) ---
    $query = "INSERT INTO libros (titulo, author, category, price, stock, description, image) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "sssdiss", $titulo, $author, $category, $price, $stock, $description, $nombre_img);

} else {
    // --- MODO EDICIÓN (UPDATE) ---
    if ($nueva_imagen) {
        // Si subió imagen nueva, actualizamos todo incluyendo el nombre de imagen
        $query = "UPDATE libros SET titulo=?, author=?, category=?, price=?, stock=?, description=?, image=? WHERE id=?";
        $stmt = mysqli_prepare($conexion, $query);
        mysqli_stmt_bind_param($stmt, "sssdissi", $titulo, $author, $category, $price, $stock, $description, $nombre_img, $id);
    } else {
        // Si NO subió imagen, actualizamos todo EXCEPTO la columna 'image'
        $query = "UPDATE libros SET titulo=?, author=?, category=?, price=?, stock=?, description=? WHERE id=?";
        $stmt = mysqli_prepare($conexion, $query);
        mysqli_stmt_bind_param($stmt, "sssdisi", $titulo, $author, $category, $price, $stock, $description, $id);
    }
}

if (mysqli_stmt_execute($stmt)) {
    echo '<script>alert("¡Operación exitosa!"); window.location="index_admin.php";</script>';
} else {
    echo "Error: " . mysqli_error($conexion);
}

mysqli_close($conexion);
?>