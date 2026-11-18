<?php
session_start();
$_SESSION['admin'] = 'admin@aurora.libros';
header("Location: ../../php/index_admin.php");
exit;
