<?php
require 'db.php';

$id = $_POST['id'];
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$precio = $_POST['precio'];
$familia = $_POST['familia'];

$stmt = $conn->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, familia=? WHERE id=?");
$stmt->bind_param("ssdsi", $nombre, $descripcion, $precio, $familia, $id);
$stmt->execute();

echo "Producto actualizado correctamente.";
?>
