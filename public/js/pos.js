/**
 * POS Tienda — Lógica del punto de venta (carrito, totales y registro de ventas)
 */
document.addEventListener('DOMContentLoaded', function () {
    const cfg = window.POS_CONFIG || {};

    const inputBusqueda = document.getElementById('posBusqueda');
    const contResultados = document.getElementById('posResultados');
    const conteoResultados = document.getElementById('posResultadosCount');
    const tbodyCarrito = document.getElementById('posCarrito');
    const btnVaciar = document.getElementById('posVaciar');
    const inputDescuento = document.getElementById('posDescuento');
    const elSubtotal = document.getElementById('posSubtotal');
    const elTotal = document.getElementById('posTotal');
    const selCliente = document.getElementById('posCliente');
    const selMetodo = document.getElementById('posMetodo');
    const selComprobante = document.getElementById('posComprobante');
    const inputMonto = document.getElementById('posMonto');
    const elVuelto = document.getElementById('posVuelto');
    const btnRegistrar = document.getElementById('posRegistrar');
    const elError = document.getElementById('posError');

    /** @type {Array<{id:number,codigo:string,nombre:string,precio:number,cantidad:number,stock:number}>} */
    let carrito = [];
    let temporizadorBusqueda = null;

    const money = (n) => 'S/ ' + Number(n).toFixed(2);

    function mostrarError(mensaje) {
        if (!elError) return;
        elError.textContent = mensaje || '';
        elError.classList.toggle('d-none', !mensaje);
    }

    /* =========================================================
       BÚSQUEDA DE PRODUCTOS
       ========================================================= */

    async function buscarProductos(termino) {
        try {
            const respuesta = await fetch(cfg.rutaBuscar + '?q=' + encodeURIComponent(termino), {
                headers: { 'Accept': 'application/json' },
            });
            const productos = await respuesta.json();
            renderResultados(productos);
        } catch (e) {
            contResultados.innerHTML =
                '<div class="pos-empty"><i class="bi bi-wifi-off"></i><h6 class="small">No se pudo conectar</h6></div>';
        }
    }

    function renderResultados(productos) {
        conteoResultados.textContent = productos.length;

        if (!productos.length) {
            contResultados.innerHTML =
                '<div class="pos-empty"><i class="bi bi-emoji-frown"></i>' +
                '<h6 class="small">No se encontraron productos</h6>' +
                '<p class="small mb-0">Prueba con otro nombre o código.</p></div>';
            return;
        }

        contResultados.innerHTML = productos.map((p) => {
            const enCarrito = carrito.find((c) => c.id === p.id);
            const disponible = p.stock - (enCarrito ? enCarrito.cantidad : 0);
            const agotado = p.agotado || disponible <= 0;

            return `
                <div class="pos-result-item ${agotado ? 'disabled' : ''}">
                    <div class="pos-result-info">
                        <strong>${p.nombre}</strong>
                        <small class="text-muted d-block">${p.codigo} · ${p.categoria || 'Sin categoría'}</small>
                    </div>
                    <div class="pos-result-meta">
                        <span class="fw-extrabold text-primary">${money(p.precio)}</span>
                        <small class="${p.stock <= 0 ? 'text-danger' : 'text-muted'}">Stock: ${p.stock}</small>
                    </div>
                    <button type="button" class="btn btn-sm pos-btn-soft pos-add"
                            data-id="${p.id}" data-codigo="${p.codigo}" data-nombre="${p.nombre.replace(/"/g, '&quot;')}"
                            data-precio="${p.precio}" data-stock="${p.stock}" ${agotado ? 'disabled' : ''}>
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>`;
        }).join('');
    }

    inputBusqueda?.addEventListener('input', function () {
        clearTimeout(temporizadorBusqueda);
        temporizadorBusqueda = setTimeout(() => buscarProductos(this.value.trim()), 300);
    });

    inputBusqueda?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(temporizadorBusqueda);
            buscarProductos(this.value.trim());
        }
    });

    contResultados?.addEventListener('click', function (e) {
        const btn = e.target.closest('.pos-add');
        if (!btn) return;
        agregarAlCarrito({
            id: parseInt(btn.dataset.id, 10),
            codigo: btn.dataset.codigo,
            nombre: btn.dataset.nombre,
            precio: parseFloat(btn.dataset.precio),
            stock: parseInt(btn.dataset.stock, 10),
            cantidad: 1,
        });
    });

    function agregarAlCarrito(producto) {
        mostrarError('');
        const existente = carrito.find((c) => c.id === producto.id);

        if (existente) {
            if (existente.cantidad + 1 > producto.stock) {
                mostrarError(`No hay más stock de «${producto.nombre}». Disponible: ${producto.stock}.`);
                return;
            }
            existente.cantidad += 1;
        } else {
            if (producto.stock < 1) {
                mostrarError(`«${producto.nombre}» no tiene stock disponible.`);
                return;
            }
            carrito.push(producto);
        }

        renderCarrito();
    }

    /* =========================================================
       CARRITO
       ========================================================= */

    function vacioTemplate() {
        const tr = document.createElement('tr');
        tr.id = 'posCarritoVacio';
        tr.innerHTML = `<td colspan="4">
            <div class="pos-empty py-4">
                <i class="bi bi-cart-x"></i>
                <h6 class="small">El carrito está vacío</h6>
                <p class="small mb-0">Agrega productos desde el buscador.</p>
            </div></td>`;
        return tr;
    }

    function renderCarrito() {
        if (!carrito.length) {
            tbodyCarrito.innerHTML = '';
            tbodyCarrito.appendChild(vacioTemplate());
        } else {
            tbodyCarrito.innerHTML = carrito.map((item) => `
                <tr>
                    <td>
                        <div class="fw-bold small">${item.nombre}</div>
                        <small class="text-muted">${money(item.precio)} c/u</small>
                    </td>
                    <td>
                        <div class="input-group input-group-sm justify-content-center">
                            <button type="button" class="btn btn-outline-secondary pos-menos" data-id="${item.id}">−</button>
                            <input type="text" class="form-control text-center" value="${item.cantidad}" readonly>
                            <button type="button" class="btn btn-outline-secondary pos-mas" data-id="${item.id}"
                                    ${item.cantidad >= item.stock ? 'disabled' : ''}>+</button>
                        </div>
                    </td>
                    <td class="text-end fw-bold">${money(item.precio * item.cantidad)}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger pos-quitar" data-id="${item.id}">
                            <i class="bi bi-x"></i>
                        </button>
                    </td>
                </tr>`).join('');
        }

        actualizarTotales();
        if (inputBusqueda) buscarProductos(inputBusqueda.value.trim());
    }

    tbodyCarrito?.addEventListener('click', function (e) {
        const btn = e.target.closest('button');
        if (!btn) return;
        const id = parseInt(btn.dataset.id, 10);
        const item = carrito.find((c) => c.id === id);
        if (!item) return;

        if (btn.classList.contains('pos-mas')) {
            if (item.cantidad + 1 > item.stock) {
                mostrarError(`Stock insuficiente de «${item.nombre}». Disponible: ${item.stock}.`);
                return;
            }
            item.cantidad += 1;
        } else if (btn.classList.contains('pos-menos')) {
            item.cantidad -= 1;
            if (item.cantidad < 1) carrito = carrito.filter((c) => c.id !== id);
        } else if (btn.classList.contains('pos-quitar')) {
            carrito = carrito.filter((c) => c.id !== id);
        } else {
            return;
        }

        mostrarError('');
        renderCarrito();
    });

    btnVaciar?.addEventListener('click', function () {
        if (!carrito.length) return;
        carrito = [];
        mostrarError('');
        renderCarrito();
    });
    /* =========================================================
       TOTALES, VUELTO Y ENVÍO DE LA VENTA
       ========================================================= */

    function calcularSubtotal() {
        return carrito.reduce((suma, item) => suma + item.precio * item.cantidad, 0);
    }

    function esEfectivo() {
        const opcion = selMetodo?.selectedOptions[0];
        return opcion && opcion.dataset.nombre === 'Efectivo';
    }

    function actualizarTotales() {
        const subtotal = calcularSubtotal();
        let descuento = parseFloat(inputDescuento?.value || 0);
        if (isNaN(descuento) || descuento < 0) descuento = 0;
        if (descuento > subtotal) {
            descuento = subtotal;
            if (inputDescuento) inputDescuento.value = subtotal.toFixed(2);
        }

        const total = Math.max(subtotal - descuento, 0);

        if (elSubtotal) elSubtotal.textContent = money(subtotal);
        if (elTotal) elTotal.textContent = money(total);

        // Vuelto solo cuando el pago es en efectivo
        const efectivo = esEfectivo();
        const monto = parseFloat(inputMonto?.value || 0);

        if (elVuelto) {
            if (efectivo && !isNaN(monto) && monto >= total) {
                elVuelto.textContent = money(monto - total);
                elVuelto.classList.remove('text-danger');
                elVuelto.classList.add('text-success');
            } else {
                elVuelto.textContent = money(0);
                elVuelto.classList.remove('text-success');
                if (efectivo && monto > 0 && monto < total) {
                    elVuelto.classList.add('text-danger');
                }
            }
        }

        if (btnRegistrar) {
            btnRegistrar.disabled = carrito.length === 0 || !selCliente?.value;
        }
    }

    inputDescuento?.addEventListener('input', actualizarTotales);
    inputMonto?.addEventListener('input', actualizarTotales);
    selCliente?.addEventListener('change', actualizarTotales);
    selMetodo?.addEventListener('change', function () {
        const wrap = document.getElementById('posMontoWrap');
        if (wrap) wrap.classList.toggle('d-none', !esEfectivo());
        actualizarTotales();
    });

    btnRegistrar?.addEventListener('click', async function () {
        mostrarError('');

        if (!carrito.length) {
            mostrarError('El carrito está vacío.');
            return;
        }
        if (!selCliente.value) {
            mostrarError('Debes seleccionar un cliente.');
            return;
        }

        const descuento = parseFloat(inputDescuento.value || 0) || 0;
        const total = Math.max(calcularSubtotal() - descuento, 0);
        const monto = parseFloat(inputMonto.value || 0) || 0;

        if (esEfectivo() && monto < total) {
            mostrarError('El monto recibido es menor que el total a pagar.');
            return;
        }

        btnRegistrar.disabled = true;

        try {
            const respuesta = await fetch(cfg.rutaVentas, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': cfg.token,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    items: carrito.map((c) => ({ id_producto: c.id, cantidad: c.cantidad })),
                    id_cliente: parseInt(selCliente.value, 10),
                    id_metodo_pago: parseInt(selMetodo.value, 10),
                    tipo_comprobante: selComprobante.value,
                    descuento: descuento,
                    monto_pagado: esEfectivo() ? monto : total,
                }),
            });

            const datos = await respuesta.json();

            if (respuesta.ok && datos.ok) {
                mostrarExito(datos.mensaje || 'Venta realizada correctamente.', () => {
                    window.location.href = datos.comprobante;
                });
                return;
            }

            mostrarError(datos.mensaje || 'No se pudo registrar la venta.');
        } catch (e) {
            mostrarError('Error de conexión. Intenta nuevamente.');
        }

        btnRegistrar.disabled = false;
    });

    function mostrarExito(mensaje, alCerrar) {
        let fondo = document.createElement('div');
        fondo.className = 'pos-success-overlay';
        fondo.innerHTML = `
            <div class="pos-success-card">
                <div class="pos-success-icon"><i class="bi bi-check-lg"></i></div>
                <h4 class="fw-extrabold mt-3 mb-2">¡Venta registrada!</h4>
                <p class="text-muted mb-4">${mensaje}</p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn pos-btn-primary" data-accion="comprobante">
                        <i class="bi bi-printer me-1"></i> Ver comprobante
                    </button>
                    <button type="button" class="btn btn-light" data-accion="cerrar">Continuar</button>
                </div>
            </div>`;
        document.body.appendChild(fondo);

        const limpiar = () => {
            fondo.remove();
            carrito = [];
            if (inputDescuento) inputDescuento.value = 0;
            if (inputMonto) inputMonto.value = '';
            renderCarrito();
        };

        fondo.querySelector('[data-accion="comprobante"]').addEventListener('click', () => {
            fondo.remove();
            alCerrar();
        });
        fondo.querySelector('[data-accion="cerrar"]').addEventListener('click', limpiar);
    }

    // Estado inicial
    if (inputBusqueda) buscarProductos('');
    actualizarTotales();
});


