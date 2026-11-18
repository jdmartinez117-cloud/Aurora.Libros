// ver_favoritos.php
<?php
session_start();
$id_actual = $_SESSION['id_usuario'];

// Consultamos productos uniendo las tablas para obtener títulos y precios
$query = "SELECT p.titulo, p.precio, p.imagen 
          FROM productos p
          INNER JOIN favoritos f ON p.id = f.producto_id
          WHERE f.usuario_id = '$id_actual'"; // <--- AQUÍ ESTÁ LA MAGIA DE LA PRIVACIDAD

$mis_favoritos = mysqli_query($conexion, $query);

while($row = mysqli_fetch_assoc($mis_favoritos)){
    echo "Libro: " . $row['titulo'];
}
?>
