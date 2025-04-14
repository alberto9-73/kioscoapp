
let carrito = [];

// ================= CARGAR PRODUCTOS =================
function cargarProductos(select) {
    const familia = select.value;
    const productosSelect = document.getElementById('productos');

    if (!familia) return;

    productosSelect.innerHTML = '<option value="">Cargando...</option>';

    fetch(`obtener_productos.php?familia=${encodeURIComponent(familia)}`)
        .then(res => res.json())
        .then(data => {
            productosSelect.innerHTML = '<option value="">SELECCIONE...</option>';
            const opciones = data.map(p =>
                `<option value="${p.id}" data-nombre="${p.nombre}" data-precio="${p.precio}">
                    ${p.nombre} - $${parseFloat(p.precio).toFixed(2)}
                </option>`
            ).join('');
            productosSelect.innerHTML += opciones;
        })
        .catch(err => {
            console.error("Error al cargar productos:", err);
            productosSelect.innerHTML = '<option value="">Error al cargar</option>';
        });
}

// ================= AGREGAR PRODUCTO =================
function agregarProducto(select) {
    const option = select.options[select.selectedIndex];
    if (!option.value) return;

    const id = parseInt(option.value);
    const nombre = option.dataset.nombre;
    const precio = parseFloat(option.dataset.precio);

    const existente = carrito.find(p => p.producto_id === id);
    if (existente) {
        existente.cantidad++;
        existente.subtotal = existente.precio * existente.cantidad;
    } else {
        carrito.push({ producto_id: id, nombre, precio, cantidad: 1, subtotal: precio });
    }

    actualizarTabla();
    select.selectedIndex = 0;
}

// ================= ACTUALIZAR TABLA =================
function actualizarTabla() {
    const tbody = document.querySelector('#lista tbody');
    let total = 0;

    const filas = carrito.map((item, i) => {
        total += item.subtotal;
        return `
            <tr>
                <td>${item.nombre}</td>
                <td>${item.cantidad}</td>
                <td>$${item.subtotal.toFixed(2)}</td>
                <td>
                    <button onclick="modificarCantidad(${i}, 1)">+</button>
                    <button onclick="modificarCantidad(${i}, -1)">-</button>
                    <button onclick="eliminarProducto(${i})">🗑</button>
                </td>
            </tr>`;
    }).join('');

    tbody.innerHTML = filas;
    document.getElementById('total').innerText = `Total: $${total.toFixed(2)}`;
}

// ================= MODIFICAR / ELIMINAR PRODUCTO =================
function modificarCantidad(i, cambio) {
    carrito[i].cantidad += cambio;
    if (carrito[i].cantidad <= 0) carrito.splice(i, 1);
    else carrito[i].subtotal = carrito[i].cantidad * carrito[i].precio;
    actualizarTabla();
}

function eliminarProducto(i) {
    carrito.splice(i, 1);
    actualizarTabla();
}

// ================= REGISTRAR VENTA =================
function registrarVenta() {
    if (carrito.length === 0) return mostrarMensaje("No hay productos en la venta.", "error");

    const total = carrito.reduce((acc, item) => acc + item.subtotal, 0);

    fetch('registrar_venta.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ productos: carrito, total })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            mostrarMensaje("Venta registrada con éxito.", "success");
            carrito = [];
            actualizarTabla();
        } else {
            mostrarMensaje("Error al registrar venta.", "error");
        }
    });
}

// ================= MENSAJE TIPO TOAST =================
function mostrarMensaje(mensaje, tipo) {
    const toast = document.createElement('div');
    toast.className = `toast ${tipo}`;
    toast.textContent = mensaje;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// ================= CALCULADORA =================
let expresion = "";

function calc(val) {
    if (val === '.' && expresion.includes('.')) return;
    expresion += val;
    document.getElementById('pantalla').value = expresion;
}

function borrar() {
    expresion = "";
    document.getElementById('pantalla').value = "";
}

function calcular() {
    try {
        const res = eval(expresion);
        if (!isFinite(res)) throw new Error("Resultado inválido");
        document.getElementById('pantalla').value = res.toFixed(2);
        expresion = "";
    } catch {
        document.getElementById('pantalla').value = "Error";
        expresion = "";
    }
}

function usarTotal() {
    const total = carrito.reduce((acc, i) => acc + i.subtotal, 0);
    expresion += total.toFixed(2);
    document.getElementById('pantalla').value = expresion;
}

// ================= ADMIN PANEL =================
function mostrarAdminPanel() {
    const panel = document.getElementById("adminPanel");
    panel.style.display = panel.style.display === "block" ? "none" : "block";

    if (panel.style.display === "block") cargarPanelAdmin();
}

function cargarPanelAdmin() {
    fetch('admin_productos.php')
        .then(res => res.text())
        .then(html => document.getElementById('adminContent').innerHTML = html);
}

// ================= GESTIÓN PRODUCTOS =================
function mostrarFormularioNuevoProducto() {
    document.getElementById("formularioNuevoProducto").style.display = "block";
}

function guardarNuevoProducto() {
    const nombre = document.getElementById("nuevoNombre").value;
    const descripcion = document.getElementById("nuevaDescripcion").value;
    const precio = document.getElementById("nuevoPrecio").value;
    const familia = document.getElementById("nuevaFamilia").value;

    fetch('guardar_producto.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `nombre=${encodeURIComponent(nombre)}&descripcion=${encodeURIComponent(descripcion)}&precio=${precio}&familia=${encodeURIComponent(familia)}`
    })
    .then(res => res.text())
    .then(alert)
    .finally(() => location.reload());

    return false;
}

function editarProducto(id) {
    fetch(`obtener_producto.php?id=${id}`)
        .then(res => res.json())
        .then(p => {
            document.getElementById("formularioEditarProducto").style.display = "block";
            document.getElementById("editarId").value = p.id;
            document.getElementById("editarNombre").value = p.nombre;
            document.getElementById("editarDescripcion").value = p.descripcion;
            document.getElementById("editarPrecio").value = p.precio;
            document.getElementById("editarFamilia").value = p.familia;
        });
}

function guardarEdicionProducto() {
    const id = document.getElementById("editarId").value;
    const nombre = document.getElementById("editarNombre").value;
    const descripcion = document.getElementById("editarDescripcion").value;
    const precio = document.getElementById("editarPrecio").value;
    const familia = document.getElementById("editarFamilia").value;

    fetch('editar_producto.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}&nombre=${encodeURIComponent(nombre)}&descripcion=${encodeURIComponent(descripcion)}&precio=${precio}&familia=${encodeURIComponent(familia)}`
    })
    .then(res => res.text())
    .then(alert)
    .finally(() => location.reload());

    return false;
}

function cancelarEdicion() {
    document.getElementById("formularioEditarProducto").style.display = "none";
}

function actualizarPrecios() {
    const porcentaje = parseFloat(document.getElementById("porcentajePrecio").value);
    if (isNaN(porcentaje)) return alert("Ingrese un número válido.");

    if (!confirm(`¿Aplicar ${porcentaje}% a todos los precios?`)) return;

    fetch('actualizar_precios.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `porcentaje=${porcentaje}`
    })
    .then(res => res.text())
    .then(alert)
    .finally(() => location.reload());
}

// ================= MOSTRAR / OCULTAR FAMILIAS =================
function toggleProductos(familia) {
    const div = document.getElementById("productos-" + familia);
    div.style.display = (div.style.display === "none" || div.style.display === "") ? "block" : "none";
}
