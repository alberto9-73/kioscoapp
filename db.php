<?php
$host = 'localhost';
$user = 'root';       // cambia si tu usuario MySQL es diferente
$pass = '';           // agrega tu contraseña si la tenés
$dbname = 'kiosko_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
