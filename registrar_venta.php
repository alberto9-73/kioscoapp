<?php
require 'db.php';

// Recibimos los datos (POST con JSON)
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['productos']) || !isset($data['total'])) {
    http_response_code(400);
    echo json_encode(["error" => "Datos incompletos"]);
    exit;
}

$productos = $data['productos']; // array de objetos: producto_id, cantidad, subtotal
$total = $data['total'];

$conn->begin_transaction();

try {
    // Insertamos la venta
    $stmtVenta = $conn->prepare("INSERT INTO ventas (total) VALUES (?)");
    $stmtVenta->bind_param("d", $total);
    $stmtVenta->execute();
    $venta_id = $stmtVenta->insert_id;

    // Insertamos cada producto vendido
    $stmtDetalle = $conn->prepare("INSERT INTO productos_vendidos (venta_id, producto_id, cantidad, subtotal) VALUES (?, ?, ?, ?)");

    foreach ($productos as $item) {
        $stmtDetalle->bind_param("iiid", $venta_id, $item['producto_id'], $item['cantidad'], $item['subtotal']);
        $stmtDetalle->execute();
    }

    $conn->commit();
    echo json_encode(["success" => true, "venta_id" => $venta_id]);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["error" => "Error al registrar la venta"]);
}
?>
