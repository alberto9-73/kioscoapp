<?php
require 'db.php';

// Indicamos que la respuesta será JSON
header('Content-Type: application/json');

// Obtenemos el parámetro familia
$familia = $_GET['familia'] ?? '';
$productos = [];

// Log para depuración
error_log("Familia recibida: " . $familia);

// Validamos la conexión a la base de datos
if (!$conn) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión a la base de datos"]);
    exit;
}

try {
    // Si hay una familia especificada, usamos prepared statement
    if ($familia) {
        $stmt = $conn->prepare("SELECT id, nombre, precio, descripcion FROM productos WHERE familia = ?");
        $stmt->bind_param("s", $familia);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        // Si no se especificó familia, traemos todos los productos
        $result = $conn->query("SELECT id, nombre, precio, descripcion FROM productos");
    }

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
    } else {
        error_log("No se encontraron productos o error en la consulta");
    }

    // Devolvemos el resultado en formato JSON
    echo json_encode($productos);

} catch (Exception $e) {
    // Manejamos errores inesperados
    http_response_code(500);
    error_log("Error en obtener_productos.php: " . $e->getMessage());
    echo json_encode(["error" => "Error al obtener productos"]);
}
?>
