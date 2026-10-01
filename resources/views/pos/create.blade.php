@extends('layouts.app')

@section('title', 'Punto de venta')

@section('content')
    <div class="row g-3 align-items-start">
        {{-- ===================== BUSCADOR Y PRODUCTOS ===================== --}}
        <div class="col-lg-7">
            <div class="pos-card pos-card-compact mb-2">
                <div class="pos-card-title"><i class="bi bi-search"></i> Buscar productos</div>
                <div class="input-group pos-input-group">
                    <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                    <input type="text" id="posBusqueda" class="form-control"
                           placeholder="Escribe el nombre o código del producto..."
                           autocomplete="off" autofocus>
                </div>
                <small class="text-muted d-block mt-1">
                    <i class="bi bi-lightbulb me-1"></i>
                    Puedes usar el lector de código de barras: escanea y presiona Enter.
                </small>
            </div>

            <div class="pos-card pos-card-compact">
                <div class="pos-card-title">
                    <i class="bi bi-box-seam"></i> Productos disponibles
                    <span class="badge bg-primary-subtle text-primary-emphasis ms-auto" id="posResultadosCount">0</span>
                </div>
                <div id="posResultados" class="pos-results"></div>
            </div>
        </div>

        {{-- ===================== CARRITO ===================== --}}
        <div class="col-lg-5">
            <div class="pos-card pos-cart-panel">
                <div class="pos-card-title">
                    <i class="bi bi-cart3"></i> Carrito de compra
                    <button type="button" class="btn btn-sm btn-outline-danger ms-auto" id="posVaciar" disabled>
                        <i class="bi bi-trash3"></i> Vaciar
                    </button>
                </div>

                <div class="pos-cart-table-wrap">
                    <table class="table pos-cart-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center" style="width:110px;">Cant.</th>
                                <th class="text-end">Subtotal</th>
                                <th style="width:36px;"></th>
                            </tr>
                        </thead>
                        <tbody id="posCarrito">
                            <tr id="posCarritoVacio">
                                <td colspan="4">
                                    <div class="pos-empty py-4">
                                        <i class="bi bi-cart-x"></i>
                                        <h6 class="small">El carrito está vacío</h6>
                                        <p class="small mb-0">Agrega productos desde el buscador.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <hr class="my-3">

                {{-- Totales --}}
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Subtotal</span>
                    <strong id="posSubtotal">S/ 0.00</strong>
                </div>
                <div class="row g-2 align-items-center mb-2">
                    <div class="col-6">
                        <label class="form-label small mb-1">Descuento (S/)</label>
                        <input type="number" id="posDescuento" class="form-control form-control-sm"
                               value="0" min="0" step="0.01">
                    </div>
                    <div class="col-6 text-end">
                        <span class="text-muted d-block small">Total</span>
                        <span class="pos-total fs-4 fw-extrabold" id="posTotal">S/ 0.00</span>
                    </div>
                </div>

                <hr class="my-3">

                {{-- Datos de la venta --}}
                <div class="row g-2">
                    <div class="col-12">
                        <label class="form-label small mb-1">Cliente <span class="text-danger">*</span></label>
                        <select id="posCliente" class="form-select form-select-sm">
                            <option value="">Selecciona un cliente...</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id_cliente }}">
                                    {{ $cliente->nombre }} — {{ $cliente->documento }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small mb-1">Método de pago <span class="text-danger">*</span></label>
                        <select id="posMetodo" class="form-select form-select-sm">
                            @foreach($metodosPago as $metodo)
                                <option value="{{ $metodo->id_metodo_pago }}" data-nombre="{{ $metodo->nombre }}">
                                    {{ $metodo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small mb-1">Comprobante <span class="text-danger">*</span></label>
                        <select id="posComprobante" class="form-select form-select-sm">
                            <option value="Boleta">Boleta</option>
                            <option value="Factura">Factura</option>
                            <option value="Ticket">Ticket</option>
                        </select>
                    </div>
                    <div class="col-6" id="posMontoWrap">
                        <label class="form-label small mb-1">Monto recibido (S/)</label>
                        <input type="number" id="posMonto" class="form-control form-control-sm"
                               value="" min="0" step="0.01" placeholder="0.00">
                    </div>
                    <div class="col-6">
                        <label class="form-label small mb-1">Vuelto</label>
                        <div class="pos-vuelto" id="posVuelto">S/ 0.00</div>
                    </div>
                </div>

                <button type="button" id="posRegistrar" class="btn pos-btn-primary w-100 mt-3 py-2" disabled>
                    <i class="bi bi-check2-circle me-1"></i> Registrar venta
                </button>
                <div id="posError" class="text-danger small mt-2 d-none"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.POS_CONFIG = {
            rutaBuscar: @json(route('productos.buscar')),
            rutaVentas: @json(route('ventas.store')),
            rutaComprobante: @json(route('ventas.comprobante', '__ID__')),
            token: @json(csrf_token())
        };
    </script>
    <script src="{{ asset('js/pos.js') }}"></script>
@endpush

