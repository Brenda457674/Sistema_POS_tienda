@extends('layouts.app')

@section('title', 'Ventas')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h2 class="h5 fw-extrabold mb-1">Historial de ventas</h2>
            <p class="text-muted small mb-0">Consulta todas las ventas realizadas.</p>
        </div>
        <a href="{{ route('pos') }}" class="btn pos-btn-primary">
            <i class="bi bi-cart-plus me-1"></i> Nueva venta
        </a>
    </div>

    <div class="pos-card pos-card-compact mb-3">
        <form action="{{ route('ventas.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-sm-6 col-lg-3">
                <label class="form-label">Desde</label>
                <input type="date" name="desde" class="form-control" value="{{ $desde }}">
            </div>
            <div class="col-sm-6 col-lg-3">
                <label class="form-label">Hasta</label>
                <input type="date" name="hasta" class="form-control" value="{{ $hasta }}">
            </div>
            <div class="col-sm-6 col-lg-3">
                <label class="form-label">Método de pago</label>
                <select name="metodo" class="form-select">
                    <option value="">Todos</option>
                    @foreach($metodosPago as $mp)
                        <option value="{{ $mp->id_metodo_pago }}"
                            {{ (string) request('metodo') === (string) $mp->id_metodo_pago ? 'selected' : '' }}>
                            {{ $mp->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn pos-btn-soft flex-fill">
                        <i class="bi bi-funnel me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('ventas.index') }}" class="btn btn-light">Limpiar</a>
                </div>
            </div>
        </form>
    </div>

    <div class="pos-table-responsive">
        <table class="table pos-table align-middle">
            <thead>
                <tr>
                    <th># Venta</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Vendedor</th>
                    <th>Comprobante</th>
                    <th>Método</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventas as $venta)
                    <tr>
                        <td class="fw-bold">#{{ $venta->id_venta }}</td>
                        <td class="text-muted small">{{ $venta->fecha_venta?->format('d/m/Y H:i') }}</td>
                        <td>{{ $venta->cliente?->nombre ?? '—' }}</td>
                        <td class="text-muted">{{ $venta->usuario?->nombre ?? '—' }}</td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary-emphasis">{{ $venta->tipo_comprobante }}</span>
                        </td>
                        <td>{{ $venta->metodoPago?->nombre ?? '—' }}</td>
                        <td class="text-end fw-extrabold text-primary">S/ {{ number_format($venta->total, 2) }}</td>
                        <td class="text-end">
                            <a href="{{ route('ventas.show', $venta) }}" class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('ventas.comprobante', $venta) }}" class="btn btn-sm btn-outline-secondary" title="Comprobante" target="_blank">
                                <i class="bi bi-printer"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="pos-empty">
                                <i class="bi bi-receipt"></i>
                                <h6>No hay ventas con estos filtros</h6>
                                <p class="small mb-0">Prueba con otro rango de fechas o registra una venta.</p>
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
@endsection
