<?php
require 'db.php';

$porcentaje = floatval($_POST['porcentaje']);
$factor = 1 + ($porcentaje / 100); // ej: 10% → 1.10

$conn->query("UPDATE productos SET precio = ROUND(precio * $factor, 2)");

echo "Precios actualizados en un $porcentaje%.";
?>
