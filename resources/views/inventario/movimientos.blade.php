@extends('layouts.app')

@section('title', 'Movimientos de inventario')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h2 class="h5 fw-extrabold mb-1">Movimientos de inventario</h2>
            <p class="text-muted small mb-0">Entradas, salidas y ajustes registrados.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventario.index') }}" class="btn btn-light">
                <i class="bi bi-box-seam me-1"></i> Ver stock
            </a>
            <a href="{{ route('inventario.create') }}" class="btn pos-btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Nuevo movimiento
            </a>
        </div>
    </div>

    <div class="pos-card pos-card-compact mb-3">
        <form action="{{ route('inventario.movimientos') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-sm-5 col-lg-3">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-select">
                    <option value="">Todos</option>
                    @foreach(['Entrada', 'Salida', 'Ajuste'] as $opcion)
                        <option value="{{ $opcion }}" {{ $tipo === $opcion ? 'selected' : '' }}>{{ $opcion }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-5 col-lg-4">
                <label class="form-label">Producto</label>
                <select name="producto" class="form-select">
                    <option value="">Todos</option>
                    @foreach($productos as $prod)
                        <option value="{{ $prod->id_producto }}" {{ (string) $productoId === (string) $prod->id_producto ? 'selected' : '' }}>
                            {{ $prod->nombre }} ({{ $prod->codigo }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2 col-lg-2">
                <button type="submit" class="btn pos-btn-soft w-100">
                    <i class="bi bi-funnel me-1"></i> Filtrar
                </button>
            </div>
        </form>
    </div>

    <div class="pos-table-responsive">
        <table class="table pos-table align-middle">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-center">Stock final</th>
                    <th>Motivo</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movimientos as $movimiento)
                    <tr>
                        <td class="text-muted small">{{ $movimiento->fecha_movimiento?->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="fw-bold">{{ $movimiento->producto?->nombre ?? '—' }}</div>
                            <small class="text-muted">{{ $movimiento->producto?->codigo ?? '' }}</small>
                        </td>
                        <td>
                            @if($movimiento->tipo === 'Entrada')
                                <span class="badge badge-stock-ok"><i class="bi bi-arrow-down-circle me-1"></i>Entrada</span>
                            @elseif($movimiento->tipo === 'Salida')
                                <span class="badge badge-stock-out"><i class="bi bi-arrow-up-circle me-1"></i>Salida</span>
                            @else
                                <span class="badge badge-stock-low"><i class="bi bi-arrow-left-right me-1"></i>Ajuste</span>
                            @endif
                        </td>
                        <td class="text-center fw-bold">{{ $movimiento->cantidad }}</td>
                        <td class="text-center fw-extrabold">{{ $movimiento->stock_final }}</td>
                        <td class="text-muted small">{{ $movimiento->motivo }}</td>
                        <td class="text-muted">{{ $movimiento->usuario?->nombre ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="pos-empty">
                                <i class="bi bi-arrow-left-right"></i>
                                <h6>No hay movimientos registrados</h6>
                                <p class="small mb-0">Los movimientos aparecerán aquí automáticamente.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $movimientos->links('pagination::bootstrap-5') }}
    </div>
@endsection
