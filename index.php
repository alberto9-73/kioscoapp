<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta</title>
    <link rel="stylesheet" href="estilos.css">
    <script src="app.js" defer></script>
</head>
<body>
<button onclick="mostrarAdminPanel()">Administración</button>

<div id="adminPanel" style="display: none;">
    <h2>Panel de Administración</h2>
    <div id="adminContent"></div>
</div>

<h1>Punto de Venta - Kiosco</h1>

<!--  <div class="contenedor">
    Selector de productos 
    <div class="familias">
        <h2>Seleccionar producto 📖</h2>

        <label for="familia">FAMILIA DE PRODUCTOS</label>
        <select class="select" id="familia" onchange="cargarProductos(this)">
            <option value="">SELECCIONE FAMILIA DE PRODUCTOS...</option>
            <option value="Golosinas">GOLOSINAS</option>
            <option value="Gaseosas">GASEOSAS</option>
            <option value="Cigarrillos">CIGARRILLOS</option>
            <option value="Almacen">ALMACEN</option>
        </select>
        <br><br>

        <label for="productos">SELECCIONE PRODUCTO</label>
        <select class="select" id="productos" onchange="agregarProducto(this)">
            <option value="">SELECCIONE...</option>
            Los productos serán cargados dinámicamente con nombre, marca y descripción -->
    <!--     </select> -->
    </div> 
    <div class="contenedor">
    <div class="familias">
        <h2>Seleccionar producto 📖</h2>

        <label for="familia">FAMILIA DE PRODUCTOS</label>
        <select class="select" id="familia" onchange="cargarProductos(this)">
            <option value="">TODOS LOS PRODUCTOS</option>
            <option value="Golosinas">GOLOSINAS</option>
            <option value="Gaseosas">GASEOSAS</option>
            <option value="Cigarrillos">CIGARRILLOS</option>
            <option value="Almacen">ALMACÉN</option>
            <option value="Cervezas">CERVEZAS</option>
        <option value="Bebidas Alcoholicas">BBEBIDAS ALCOHOLICAS</option>
        <option value="Helados">HELADOS</option>
        <option value="Snacks">SNACKS</option>
        </select>
        <br><br>

        <label for="productos">SELECCIONE PRODUCTO</label>
        <select class="select" id="productos" onchange="agregarProducto(this)">
            <option value="">SELECCIONE...</option>
        </select>
    </div>

    <!-- Carrito de ventas -->
    <div class="carrito">
        <h2>CARRITO DE VENTA 🛒</h2>
        <table id="lista">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Aquí se mostrarán los productos añadidos al carrito -->
            </tbody>
        </table>
        <div id="total" class="total-final">
            Total: $0.00
        </div>
        <button onclick="registrarVenta()">Registrar Venta</button>
    </div>

    <!-- Calculadora -->
    <div class="calculadora">
     
        <input type="text" id="pantalla" disabled>
        <div class="teclado">
            <button onclick="calc('1')">1</button>
            <button onclick="calc('2')">2</button>
            <button onclick="calc('3')">3</button>
            <button onclick="calc('4')">4</button>
            <button onclick="calc('5')">5</button>
            <button onclick="calc('6')">6</button>
            <button onclick="calc('7')">7</button>
            <button onclick="calc('8')">8</button>
            <button onclick="calc('9')">9</button>
            <button onclick="calc('0')">0</button>
            <button onclick="calc('+')">+</button>
            <button onclick="calc('-')">-</button>
            <button onclick="calc('*')">*</button>
            <button onclick="calc('/')">/</button>
            <button onclick="calc('.')">,</button>
            <button style=" background-color: #f44336;" onclick="borrar()">C</button>
            <button style=" background-color:rgb(54, 228, 244);" onclick="calcular()">=</button>
            <button onclick="usarTotal()">Total</button>
        </div>
    </div>
</div>
</body>
</html>
