let carrito = [];

/* function cargarProductos(select) {
    const familia = select.value;
    const productosSelect = document.getElementById('productos');
    
    if (!familia) return; // Si no hay familia seleccionada, no hace nada

    fetch('obtener_productos.php?familia=' + familia)
        .then(response => response.json())
        .then(data => {
            productosSelect.innerHTML = '<option value="">Seleccione...</option>'; // Limpiar productos

            data.forEach(producto => {
                productosSelect.innerHTML += `
                    <option value="${producto.id}" data-nombre="${producto.nombre}" data-precio="${producto.precio}">
                        ${producto.nombre} - $${producto.precio}
                    </option>
                `;
            });
        });
}
 */

function cargarProductos(select) {
    const familia = select.value;
    const productosSelect = document.getElementById('productos');

    // Usamos encodeURIComponent por si hay espacios o caracteres raros
    fetch('obtener_productos.php?familia=' + encodeURIComponent(familia))
        .then(response => response.json())
        .then(data => {
            console.log("Productos recibidos:", data);  // Verifica los datos en la consola

            productosSelect.innerHTML = '<option value="">SELECCIONE...</option>';

            data.forEach(producto => {
                productosSelect.innerHTML += `
                    <option value="${producto.id}" data-nombre="${producto.nombre}" data-precio="${producto.precio}">
                        ${producto.nombre} - $${producto.precio}
                    </option>
                `;
            });
        })
        .catch(error => {
            console.error("Error al cargar productos:", error);
        });
}



function agregarProducto(select) {
    const option = select.options[select.selectedIndex];
    if (!option.value) return;

    const productoId = parseInt(option.value);
    const nombre = option.dataset.nombre;
    const precio = parseFloat(option.dataset.precio);

    const existente = carrito.find(p => p.producto_id === productoId);
    if (existente) {
        existente.cantidad++;
        existente.subtotal = existente.cantidad * existente.precio;
    } else {
        carrito.push({
            producto_id: productoId,
            nombre: nombre,
            precio: precio,
            cantidad: 1,
            subtotal: precio
        });
    }

    actualizarTabla();
    select.selectedIndex = 0;
}

function actualizarTabla() {
    const tbody = document.querySelector('#lista tbody');
    tbody.innerHTML = '';
    let total = 0;

    carrito.forEach((item, index) => {
        total += item.subtotal;
        tbody.innerHTML += `
            <tr>
                <td>${item.nombre}</td>
                <td>${item.cantidad}</td>
                <td>$${item.subtotal.toFixed(2)}</td>
                <td>
                    <button onclick="modificarCantidad(${index}, 1)">+</button>
                    <button onclick="modificarCantidad(${index}, -1)">-</button>
                    <button onclick="eliminarProducto(${index})">🗑</button>
                </td>
            </tr>
        `;
    });

    document.getElementById('total').innerText = `Total: $${total.toFixed(2)}`;
}

function modificarCantidad(index, cambio) {
    carrito[index].cantidad += cambio;
    if (carrito[index].cantidad <= 0) {
        carrito.splice(index, 1);
    } else {
        carrito[index].subtotal = carrito[index].cantidad * carrito[index].precio;
    }
    actualizarTabla();
}

function eliminarProducto(index) {
    carrito.splice(index, 1);
    actualizarTabla();
}

function registrarVenta() {
    if (carrito.length === 0) {
        mostrarMensaje("No hay productos en la venta.", "error");
        return;
    }

    const total = carrito.reduce((acc, item) => acc + item.subtotal, 0);

    fetch('registrar_venta.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            productos: carrito,
            total: total
        })
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

// Función para mostrar el mensaje tipo Toast
function mostrarMensaje(mensaje, tipo) {
    const toast = document.createElement('div');
    toast.className = 'toast ' + tipo;
    toast.textContent = mensaje;

    // Añadir el toast al body
    document.body.appendChild(toast);

    // Hacer que el mensaje desaparezca después de 3 segundos
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// CALCULADORA

let expresion = "";

function calc(valor) {
    // Permitir agregar el punto decimal solo si no hay uno ya en la expresión
    if (valor === '.' && expresion.includes('.')) return;  // Evitar agregar múltiples puntos
    expresion += valor;
    document.getElementById('pantalla').value = expresion;
}

function borrar() {
    expresion = "";
    document.getElementById('pantalla').value = "";
}

function calcular() {
    try {
        const resultado = eval(expresion);
        if (!isFinite(resultado)) throw new Error("Resultado inválido");

        document.getElementById('pantalla').value = resultado.toFixed(2);  // Limitar a dos decimales
        expresion = "";
    } catch (err) {
        document.getElementById('pantalla').value = "Error";
        expresion = "";
    }
}

function usarTotal() {
    const total = carrito.reduce((acc, item) => acc + item.subtotal, 0);
    expresion += total.toFixed(2);
    document.getElementById('pantalla').value = expresion;
}

function mostrarAdminPanel() {
    const panel = document.getElementById("adminPanel");
    panel.style.display = panel.style.display === "none" ? "block" : "none";

    if (panel.style.display === "block") {
        cargarPanelAdmin();
    }
}

function cargarPanelAdmin() {
    fetch('admin_productos.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById('adminContent').innerHTML = html;
        });
}

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
    .then(respuesta => {
        alert(respuesta); // O podés usar un div flotante si preferís
        location.reload(); // Recargar la página para actualizar la lista
    });

    return false;
}

function editarProducto(id) {
    fetch(`obtener_producto.php?id=${id}`)
    .then(res => res.json())
    .then(producto => {
        document.getElementById("formularioEditarProducto").style.display = "block";
        document.getElementById("editarId").value = producto.id;
        document.getElementById("editarNombre").value = producto.nombre;
        document.getElementById("editarDescripcion").value = producto.descripcion;
        document.getElementById("editarPrecio").value = producto.precio;
        document.getElementById("editarFamilia").value = producto.familia;
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
    .then(respuesta => {
        alert(respuesta);
        location.reload();
    });

    return false;
}

function cancelarEdicion() {
    document.getElementById("formularioEditarProducto").style.display = "none";
}


function actualizarPrecios() {
    const porcentaje = parseFloat(document.getElementById("porcentajePrecio").value);

    if (isNaN(porcentaje)) {
        alert("Por favor ingrese un número válido.");
        return false;
    }

    if (!confirm(`¿Estás seguro de que querés aplicar un ${porcentaje}% a todos los precios?`)) {
        return false;
    }

    fetch('actualizar_precios.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `porcentaje=${porcentaje}`
    })
    .then(res => res.text())
    .then(respuesta => {
        alert(respuesta);
        location.reload();
    });

    return false;
}

function toggleProductos(familia) {
    const productosDiv = document.getElementById("productos-" + familia);
    
    // Comprobamos si los productos están actualmente visibles
    if (productosDiv.style.display === "none" || productosDiv.style.display === "") {
        // Si están ocultos, los mostramos
        productosDiv.style.display = "block";
    } else {
        // Si están visibles, los ocultamos
        productosDiv.style.display = "none";
    }
}