@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <p class="text-muted mb-4">Bienvenido/a, <strong>{{ auth()->user()->nombre }}</strong>. Aquí tienes el resumen de hoy.</p>

    {{-- ===================== TARJETAS ===================== --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="pos-stat">
                <span class="pos-stat-icon purple"><i class="bi bi-cash-stack"></i></span>
                <div class="pos-stat-info">
                    <span class="label">Ventas de hoy</span>
                    <span class="value">{{ $ventasDelDia['cantidad'] }}</span>
                    <small class="text-muted">S/ {{ number_format($ventasDelDia['total'], 2) }}</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="pos-stat">
                <span class="pos-stat-icon blue"><i class="bi bi-box-seam"></i></span>
                <div class="pos-stat-info">
                    <span class="label">Productos activos</span>
                    <span class="value">{{ $totalProductos }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="pos-stat">
                <span class="pos-stat-icon mint"><i class="bi bi-people"></i></span>
                <div class="pos-stat-info">
                    <span class="label">Clientes activos</span>
                    <span class="value">{{ $totalClientes }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="pos-stat">
                <span class="pos-stat-icon pink"><i class="bi bi-graph-up-arrow"></i></span>
                <div class="pos-stat-info">
                    <span class="label">Ventas del mes</span>
                    <span class="value" style="font-size:1.2rem;">S/ {{ number_format($totalVentasMes, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6">
            <a href="{{ route('inventario.index', ['filtro' => 'bajo']) }}" class="pos-stat text-decoration-none">
                <span class="pos-stat-icon amber"><i class="bi bi-exclamation-triangle"></i></span>
                <div class="pos-stat-info">
                    <span class="label">Productos con stock bajo</span>
                    <span class="value">{{ $stockBajo }}</span>
                    <small class="text-muted">Revisa el inventario →</small>
                </div>
            </a>
        </div>
        <div class="col-sm-6">
            <a href="{{ route('inventario.index', ['filtro' => 'agotado']) }}" class="pos-stat text-decoration-none">
                <span class="pos-stat-icon red"><i class="bi bi-x-octagon"></i></span>
                <div class="pos-stat-info">
                    <span class="label">Productos sin stock</span>
                    <span class="value">{{ $sinStock }}</span>
                    <small class="text-muted">Revisa el inventario →</small>
                </div>
            </a>
        </div>
    </div>
    <div class="row g-3">
        {{-- Ventas recientes --}}
        <div class="col-lg-7">
            <div class="pos-card h-100">
                <div class="pos-card-title">
                    <i class="bi bi-receipt"></i> Ventas recientes
                    <a href="{{ route('ventas.index') }}" class="ms-auto small text-decoration-none">Ver todas →</a>
                </div>

                <div class="pos-table-responsive">
                    <table class="table pos-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Método</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ventasRecientes as $venta)
                                <tr>
                                    <td>
                                        <a href="{{ route('ventas.show', $venta) }}" class="fw-bold text-decoration-none">
                                            #{{ $venta->id_venta }}
                                        </a>
                                    </td>
                                    <td class="text-muted small">{{ $venta->fecha_venta?->format('d/m/Y H:i') }}</td>
                                    <td>{{ $venta->cliente?->nombre ?? '—' }}</td>
                                    <td><span class="badge bg-primary-subtle text-primary-emphasis">{{ $venta->metodoPago?->nombre ?? '—' }}</span></td>
                                    <td class="text-end fw-extrabold text-primary">S/ {{ number_format($venta->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="pos-empty">
                                            <i class="bi bi-cart-x"></i>
                                            <h6>Aún no hay ventas</h6>
                                            <p class="small mb-0">Registra tu primera venta desde el POS.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Productos más vendidos --}}
        <div class="col-lg-5">
            <div class="pos-card h-100">
                <div class="pos-card-title"><i class="bi bi-trophy"></i> Más vendidos</div>

                @forelse($masVendidos as $indice => $fila)
                    <div class="d-flex align-items-center gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <span class="pos-rank">{{ $indice + 1 }}</span>
                        <div class="flex-grow-1">
                            <div class="fw-bold small">{{ $fila['producto']->nombre }}</div>
                            <small class="text-muted">{{ $fila['producto']->categoria?->nombre ?? 'Sin categoría' }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-extrabold text-primary">{{ $fila['unidades'] }} u.</div>
                            <small class="text-muted">S/ {{ number_format($fila['monto'], 2) }}</small>
                        </div>
                    </div>
                @empty
                    <div class="pos-empty">
                        <i class="bi bi-graph-up-arrow"></i>
                        <h6>Sin datos todavía</h6>
                        <p class="small mb-0">Los productos más vendidos aparecerán aquí.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

