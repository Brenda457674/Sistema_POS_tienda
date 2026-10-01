@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
    <p class="text-muted mb-4">Consulta ventas, productos más vendidos y alertas de stock.</p>

    {{-- Filtros de fecha --}}
    <div class="pos-card pos-card-compact mb-3">
        <form action="{{ route('reportes.index') }}" method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="tab" value="{{ $pestana }}">
            <div class="col-sm-4 col-lg-3">
                <label class="form-label">Desde</label>
                <input type="date" name="desde" class="form-control" value="{{ $desde }}">
            </div>
            <div class="col-sm-4 col-lg-3">
                <label class="form-label">Hasta</label>
                <input type="date" name="hasta" class="form-control" value="{{ $hasta }}">
            </div>
            <div class="col-sm-4 col-lg-3">
                <button type="submit" class="btn pos-btn-soft w-100">
                    <i class="bi bi-funnel me-1"></i> Aplicar filtros
                </button>
            </div>
        </form>
    </div>

    {{-- Resumen --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="pos-stat">
                <span class="pos-stat-icon purple"><i class="bi bi-receipt"></i></span>
                <div class="pos-stat-info">
                    <span class="label">Ventas en el rango</span>
                    <span class="value">{{ $resumenVentas['cantidad'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="pos-stat">
                <span class="pos-stat-icon mint"><i class="bi bi-cash-stack"></i></span>
                <div class="pos-stat-info">
                    <span class="label">Total vendido</span>
                    <span class="value" style="font-size:1.25rem;">S/ {{ number_format($resumenVentas['total'], 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="pos-stat">
                <span class="pos-stat-icon pink"><i class="bi bi-tag"></i></span>
                <div class="pos-stat-info">
                    <span class="label">Descuentos otorgados</span>
                    <span class="value" style="font-size:1.25rem;">S/ {{ number_format($resumenVentas['descuento'], 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Pestañas --}}
    <ul class="nav nav-pills pos-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $pestana === 'ventas' ? 'active' : '' }}"
               href="{{ route('reportes.index', ['tab' => 'ventas', 'desde' => $desde, 'hasta' => $hasta]) }}">
                <i class="bi bi-receipt me-1"></i> Ventas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $pestana === 'productos' ? 'active' : '' }}"
               href="{{ route('reportes.index', ['tab' => 'productos', 'desde' => $desde, 'hasta' => $hasta]) }}">
                <i class="bi bi-trophy me-1"></i> Más vendidos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $pestana === 'stock' ? 'active' : '' }}"
               href="{{ route('reportes.index', ['tab' => 'stock', 'desde' => $desde, 'hasta' => $hasta]) }}">
                <i class="bi bi-exclamation-triangle me-1"></i> Stock bajo
            </a>
        </li>
    </ul>
    {{-- Contenido según pestaña --}}
    @if($pestana === 'productos')
        <div class="pos-card">
            <div class="pos-card-title"><i class="bi bi-trophy"></i> Productos más vendidos</div>
            <div class="pos-table-responsive">
                <table class="table pos-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th class="text-center">Unidades vendidas</th>
                            <th class="text-end">Monto generado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($masVendidos as $indice => $fila)
                            <tr>
                                <td><span class="pos-rank">{{ $indice + 1 }}</span></td>
                                <td class="fw-bold">{{ $fila->producto->nombre }}</td>
                                <td class="text-muted">{{ $fila->producto->categoria?->nombre ?? '—' }}</td>
                                <td class="text-center fw-extrabold">{{ $fila->unidades }}</td>
                                <td class="text-end fw-bold text-primary">S/ {{ number_format($fila->monto, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="pos-empty">
                                        <i class="bi bi-graph-up-arrow"></i>
                                        <h6>Sin ventas en este rango</h6>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($pestana === 'stock')
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="pos-card h-100">
                    <div class="pos-card-title"><i class="bi bi-exclamation-triangle text-warning"></i> Stock bajo ({{ $stockBajo->count() }})</div>
                    @forelse($stockBajo as $producto)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-bold small">{{ $producto->nombre }}</div>
                                <small class="text-muted">Mínimo: {{ $producto->stock_minimo }}</small>
                            </div>
                            <span class="badge badge-stock-low">Quedan {{ $producto->stock }}</span>
                        </div>
                    @empty
                        <div class="pos-empty">
                            <i class="bi bi-check-circle"></i>
                            <h6>Ningún producto con stock bajo</h6>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="col-lg-6">
                <div class="pos-card h-100">
                    <div class="pos-card-title"><i class="bi bi-x-octagon text-danger"></i> Sin stock ({{ $sinStock->count() }})</div>
                    @forelse($sinStock as $producto)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-bold small">{{ $producto->nombre }}</div>
                                <small class="text-muted">{{ $producto->categoria?->nombre ?? 'Sin categoría' }}</small>
                            </div>
                            <span class="badge badge-stock-out">Agotado</span>
                        </div>
                    @empty
                        <div class="pos-empty">
                            <i class="bi bi-check-circle"></i>
                            <h6>Todos los productos tienen stock</h6>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @else
        <div class="pos-card">
            <div class="pos-card-title">
                <i class="bi bi-receipt"></i>
                Ventas del {{ \Illuminate\Support\Carbon::parse($desde)->format('d/m/Y') }}
                al {{ \Illuminate\Support\Carbon::parse($hasta)->format('d/m/Y') }}
            </div>
            <div class="pos-table-responsive">
                <table class="table pos-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th># Venta</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Método</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                            <tr>
                                <td class="fw-bold">#{{ $venta->id_venta }}</td>
                                <td class="text-muted small">{{ $venta->fecha_venta?->format('d/m/Y H:i') }}</td>
                                <td>{{ $venta->cliente?->nombre ?? '—' }}</td>
                                <td class="text-muted">{{ $venta->usuario?->nombre ?? '—' }}</td>
                                <td>{{ $venta->metodoPago?->nombre ?? '—' }}</td>
                                <td class="text-end fw-extrabold text-primary">S/ {{ number_format($venta->total, 2) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('ventas.show', $venta) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="pos-empty">
                                        <i class="bi bi-calendar-x"></i>
                                        <h6>No hay ventas en este rango de fechas</h6>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection


