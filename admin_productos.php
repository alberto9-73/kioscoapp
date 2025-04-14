

<?php
require 'db.php';

$resultado = $conn->query("SELECT * FROM productos ORDER BY familia, nombre");

$productosPorFamilia = [];
while ($row = $resultado->fetch_assoc()) {
    $productosPorFamilia[$row['familia']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Productos</title>
    <script src="app.js"></script>
    <link rel="stylesheet" href="estilos.css">
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: justify; }
        button { margin-right: 5px; }
        #formularioNuevoProducto { border: 1px solid #ccc; padding: 15px; margin-top: 20px; background: #f9f9f9; width: 300px; }
    </style>
</head>
<body>

<!-- Botón Volver -->
<button class="navegacion"  onclick="window.location.href='index.php'">← Cerrar Adminstrador</button>


<!-- Botón para ver Resumen Diario -->
<a href="resumen_diario.php" style="display: inline-block; margin: 10px 0; background: #007BFF; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px;">📅 Ver Resumen Diario</a>
<a href="resumen_mensual.php" style="display: inline-block; margin: 10px 0; background:rgb(142, 40, 167); color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px;">🗓️ Ver Resumen Mensual</a>

<!-- Botón para agregar nuevo producto -->
<button class="navegacion" onclick="mostrarFormularioNuevoProducto()">➕ Nuevo Producto</button>   

<div style="margin-top: 30px; padding: 10px; border: 1px solid #ccc; background: #f2f2f2; width: 300px; ">
    <h4>Actualizar precios masivamente</h4>
    <div style="   justify-content: right;">
    <form onsubmit="return actualizarPrecios();">
        <label>Porcentaje (%):</label><br>
        <input type="number" id="porcentajePrecio" step="0.01" required><br><br>
        <button type="submit">Aplicar a todos los precios</button>
    </form>
    </div>
</div>
<center>
<!-- Formulario para nuevo producto -->
<div id="formularioNuevoProducto" style="display: none;">
    <h4>Agregar nuevo producto</h4>
    <form class="admin-form" onsubmit="return guardarNuevoProducto();">
        <label>Nombre:</label><br>
        <input type="text" id="nuevoNombre" required><br><br>

        <label>Descripción:</label><br>
        <input type="text" id="nuevaDescripcion"><br><br>

        <label>Precio:</label><br>
        <input type="number" id="nuevoPrecio" step="0.01" required><br><br>

       
       
        <label for="nuevaFamilia">Familia:</label>
        <select id="nuevaFamilia" required>
        <option value="">Seleccione Familia</option>
        <option value="Cigarrillos">Cigarrillos</option>
        <option value="Golocinas">Golocinas</option>
        <option value="Almacen">Almacen</option>
        <option value="Gaseosas">Gaseosas</option>
        <option value="Cervezas">Cervezas</option>
        <option value="Bebidas Alcoholicas">Bebidas Alcoholicas</option>
        <option value="Helados">Helados</option>
        <option value="Snacks">Snacks</option>
    </select>

        <button type="submit">Guardar Producto</button>
        
        <button ><a style="text-decoration: none; color: white; "  href="admin_productos.php" class="boton">Cerrar fomulario</a></button>
    </form>
</div>

<!-- Formulario de edición de productos -->
<div id="formularioEditarProducto" style="display: none; margin-top: 20px;">
    <h4>Editar producto</h4>
    <form class="admin-form" onsubmit="return guardarEdicionProducto();">
        <input type="hidden" id="editarId">
        
        <label>Nombre:</label><br>
        <input type="text" id="editarNombre" required><br><br>

        <label>Descripción:</label><br>
        <input type="text" id="editarDescripcion"><br><br>

        <label>Precio:</label><br>
        <input type="number" id="editarPrecio" step="0.01" required><br><br>

        <label>Familia:</label><br>
        <input type="text" id="editarFamilia" required><br><br>

        <button type="submit">Guardar Cambios</button>
        <button type="button" onclick="cancelarEdicion()">Cancelar</button>
    </form>
</div>
</center>
<h3>Lista de Productos</h3>

<?php foreach ($productosPorFamilia as $familia => $productos): ?>
    <div class="familia">
        <button onclick="toggleProductos('<?php echo $familia; ?>')">Mostrar <?php echo ucfirst($familia); ?></button>
        <div class="productos" id="productos-<?php echo $familia; ?>" style="display: none;">
            <table>
                <tr>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?= htmlspecialchars($producto['nombre']) ?></td>
                        <td><?= number_format($producto['precio'], 2) ?></td>
                        <td>
                            <button onclick="editarProducto(<?= $producto['id'] ?>)">Editar</button>
                            <button onclick="eliminarProducto(<?= $producto['id'] ?>)">Eliminar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    <br>
<?php endforeach; ?>
</body>
</html>
