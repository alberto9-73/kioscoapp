<?php
require 'db.php';

$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$precio = $_POST['precio'];
$familia = $_POST['familia'];

$stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, familia) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssds", $nombre, $descripcion, $precio, $familia);
$stmt->execute();

echo "Producto agregado correctamente.";
?>
