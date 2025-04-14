
<?php
require 'db.php';

$id = $_GET['id'];  // Aquí asumes que el 'id' es siempre válido y numérico

$resultado = $conn->query("SELECT * FROM productos WHERE id = $id");
echo json_encode($resultado->fetch_assoc());
?>