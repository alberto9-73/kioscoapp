
<?php
require 'db.php';

// Obtener la fecha desde la URL o usar la fecha actual
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

// Preparar la consulta segura para obtener total recaudado en ventas y detalles de los productos vendidos
$sqlTotal = "
    SELECT p.nombre, p.descripcion, SUM(pv.cantidad) AS cantidad_total, SUM(pv.subtotal) AS total_producto
    FROM productos_vendidos pv
    JOIN productos p ON pv.producto_id = p.id
    JOIN ventas v ON pv.venta_id = v.id
    WHERE DATE(v.fecha) = ?
    GROUP BY p.id
";

// Usar una consulta preparada para prevenir inyecciones SQL
$stmt = $conn->prepare($sqlTotal);
$stmt->bind_param("s", $fecha);
$stmt->execute();
$result = $stmt->get_result();

$productosVendidos = [];
$totalVentas = 0;

while ($row = $result->fetch_assoc()) {
    $productosVendidos[] = $row;
    $totalVentas += $row['total_producto'];
}

// Contar total de ventas realizadas
$sqlVentas = "SELECT COUNT(*) AS total FROM ventas WHERE DATE(fecha) = ?";
$stmtVentas = $conn->prepare($sqlVentas);
$stmtVentas->bind_param("s", $fecha);
$stmtVentas->execute();
$ventasRealizadas = $stmtVentas->get_result()->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen Diario</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        form { margin-bottom: 20px; background: #f1f1f1; padding: 10px; display: inline-block; }
        .resumen-box { border: 1px solid #ccc; padding: 20px; background: #fafafa; width: 300px; }
        .resumen-box p { font-size: 16px; margin: 10px 0; }
        table { border-collapse: collapse; width: 80%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        a.boton { display: inline-block; margin-top: 20px; background: #007BFF; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

<h2>📊 Resumen de Ventas</h2>

<form method="get">
    <label>Seleccionar fecha:</label>
    <input type="date" name="fecha" value="<?= $fecha ?>">
    <button type="submit">Ver</button>
</form>
<a href="admin_productos.php" class="boton">← Volver al Admintrador</a>
<a href="index.php" class="boton">← Cerrar</a>

<div class="resumen-box">
    <p><strong>📅 Fecha seleccionada:</strong> <?= $fecha ?></p>
    <p><strong>🧾 Ventas realizadas:</strong> <?= $ventasRealizadas ?></p>
    <p><strong>💰 Total recaudado:</strong> $<?= number_format($totalVentas, 2) ?></p>
</div>

<h3>🧾 Detalle de Productos Vendidos</h3>
<table>
    <tr>
        <th>Producto</th>
        <th>Descripción</th>
        <th>Cantidad Vendida</th>
        <th>Total Producto</th>
    </tr>
    <?php foreach ($productosVendidos as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['nombre']) ?></td>
            <td><?= htmlspecialchars($row['descripcion']) ?></td>
            <td><?= $row['cantidad_total'] ?></td>
            <td>$<?= number_format($row['total_producto'], 2) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<a href="admin_productos.php" class="boton">← Volver al Admintrador</a>
<a href="index.php" class="boton">← Cerrar</a>
</body>
</html>
