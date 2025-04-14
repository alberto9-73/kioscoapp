<?php
require 'db.php';

$mes = isset($_GET['mes']) ? $_GET['mes'] : date('Y-m');

$sql = "
    SELECT 
        p.nombre,
        p.descripcion,
        SUM(pv.cantidad) AS cantidad_total,
        SUM(pv.subtotal) AS total_por_producto
    FROM productos_vendidos pv
    JOIN productos p ON pv.producto_id = p.id
    JOIN ventas v ON pv.venta_id = v.id
    WHERE DATE_FORMAT(v.fecha, '%Y-%m') = '$mes'
    GROUP BY p.id
    ORDER BY total_por_producto DESC
";

$result = $conn->query($sql);

$totalVentas = 0;
$productos = [];

while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
    $totalVentas += $row['total_por_producto'];
}

$ventasRealizadas = $conn->query("SELECT COUNT(*) as total FROM ventas WHERE DATE_FORMAT(fecha, '%Y-%m') = '$mes'")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen Mensual</title>
    <style>
        table { border-collapse: collapse; width: 90%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        form { margin-bottom: 20px; background: #f1f1f1; padding: 10px; display: inline-block; }
        a.boton { display: inline-block; margin-top: 20px; background: #007BFF; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

<h2>📆 Resumen de Ventas del Mes</h2>

<form method="get">
    <label>Seleccionar mes:</label>
    <input type="month" name="mes" value="<?= $mes ?>">
    <button type="submit">Ver</button>
</form>
<a href="index.php" class="boton">← Volver</a>
<p><strong>Mes seleccionado:</strong> <?= $mes ?></p>
<p><strong>Total de Ventas Realizadas:</strong> <?= $ventasRealizadas ?></p>
<p><strong>Importe Total:</strong> $<?= number_format($totalVentas, 2) ?></p>

<h3>📦 Productos Vendidos</h3>
<table>
    <tr>
        <th>Producto</th>
        <th>Descripción</th>
        <th>Cantidad Total</th>
        <th>Total Vendido</th>
    </tr>
    <?php foreach ($productos as $producto): ?>
        <tr>
            <td><?= htmlspecialchars($producto['nombre']) ?></td>
            <td><?= htmlspecialchars($producto['descripcion']) ?></td>
            <td><?= $producto['cantidad_total'] ?></td>
            <td>$<?= number_format($producto['total_por_producto'], 2) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<a href="admin_productos.php" class="boton">← Volver</a>

</body>
</html>
