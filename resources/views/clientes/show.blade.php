@extends('layouts.app')

@section('title', 'Historial del cliente')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h2 class="h5 fw-extrabold mb-1">{{ $cliente->nombre }}</h2>
            <p class="text-muted small mb-0">
                Documento <strong>{{ $cliente->documento }}</strong>
                @if($cliente->telefono) · Tel. {{ $cliente->telefono }} @endif
                @if($cliente->email) · {{ $cliente->email }} @endif
            </p>
        </div>
        <a href="{{ route('clientes.index') }}" class="btn btn-light">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="pos-stat">
                <span class="pos-stat-icon purple"><i class="bi bi-bag-check"></i></span>
                <div class="pos-stat-info">
                    <span class="label">Compras realizadas</span>
                    <span class="value">{{ $cliente->ventas()->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="pos-stat">
                <span class="pos-stat-icon mint"><i class="bi bi-cash-stack"></i></span>
                <div class="pos-stat-info">
                    <span class="label">Total gastado</span>
                    <span class="value">S/ {{ number_format($totalComprado, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="pos-stat">
                <span class="pos-stat-icon pink"><i class="bi bi-geo-alt"></i></span>
                <div class="pos-stat-info">
                    <span class="label">Dirección</span>
                    <span class="value" style="font-size:0.95rem;">{{ $cliente->direccion ?: 'No registrada' }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="pos-stat">
                <span class="pos-stat-icon {{ $cliente->estado ? 'blue' : 'red' }}"><i class="bi bi-circle-square"></i></span>
                <div class="pos-stat-info">
                    <span class="label">Estado</span>
                    <span class="value" style="font-size:0.95rem;">{{ $cliente->estado ? 'Activo' : 'Inactivo' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="pos-card">
        <div class="pos-card-title"><i class="bi bi-receipt"></i> Historial de compras</div>

        <div class="pos-table-responsive">
            <table class="table pos-table align-middle">
                <thead>
                    <tr>
                        <th># Venta</th>
                        <th>Fecha</th>
                        <th>Comprobante</th>
                        <th>Método</th>
                        <th>Vendedor</th>
                        <th>Total</th>
                        <th class="text-end">Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $venta)
                        <tr>
                            <td class="fw-bold">#{{ $venta->id_venta }}</td>
                            <td class="text-muted">{{ $venta->fecha_venta?->format('d/m/Y H:i') }}</td>
                            <td><span class="badge bg-primary-subtle text-primary-emphasis">{{ $venta->tipo_comprobante }}</span></td>
                            <td>{{ $venta->metodoPago?->nombre ?? '—' }}</td>
                            <td class="text-muted">{{ $venta->usuario?->nombre ?? '—' }}</td>
                            <td class="fw-extrabold text-primary">S/ {{ number_format($venta->total, 2) }}</td>
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
                                    <i class="bi bi-cart-x"></i>
                                    <h6>Este cliente aún no ha comprado</h6>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $ventas->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
