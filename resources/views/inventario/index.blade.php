@extends('layouts.app')

@section('title', 'Inventario')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h2 class="h5 fw-extrabold mb-1">Control de inventario</h2>
            <p class="text-muted small mb-0">Consulta el stock actual y las alertas de productos.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventario.movimientos') }}" class="btn btn-light">
                <i class="bi bi-arrow-left-right me-1"></i> Movimientos
            </a>
            <a href="{{ route('inventario.create') }}" class="btn pos-btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Registrar movimiento
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <a href="{{ route('inventario.index') }}" class="pos-stat text-decoration-none {{ $filtro === 'todos' ? 'pos-stat-selected' : '' }}">
                <span class="pos-stat-icon blue"><i class="bi bi-boxes"></i></span>
                <div class="pos-stat-info">
                    <span class="label">Todos los productos</span>
                    <span class="value">{{ $totales['todos'] }}</span>
                </div>
            </a>
        </div>
        <div class="col-sm-4">
            <a href="{{ route('inventario.index', ['filtro' => 'bajo']) }}" class="pos-stat text-decoration-none {{ $filtro === 'bajo' ? 'pos-stat-selected' : '' }}">
                <span class="pos-stat-icon amber"><i class="bi bi-exclamation-triangle"></i></span>
                <div class="pos-stat-info">
                    <span class="label">Stock bajo</span>
                    <span class="value">{{ $totales['bajo'] }}</span>
                </div>
            </a>
        </div>
        <div class="col-sm-4">
            <a href="{{ route('inventario.index', ['filtro' => 'agotado']) }}" class="pos-stat text-decoration-none {{ $filtro === 'agotado' ? 'pos-stat-selected' : '' }}">
                <span class="pos-stat-icon red"><i class="bi bi-x-octagon"></i></span>
                <div class="pos-stat-info">
                    <span class="label">Sin stock</span>
                    <span class="value">{{ $totales['agotado'] }}</span>
                </div>
            </a>
        </div>
    </div>

    <div class="pos-card pos-card-compact mb-3">
        <form action="{{ route('inventario.index') }}" method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="filtro" value="{{ $filtro }}">
            <div class="col-sm-8 col-lg-4">
                <label class="form-label">Buscar producto</label>
                <input type="text" name="q" class="form-control" placeholder="Nombre o código..." value="{{ $busqueda }}">
            </div>
            <div class="col-sm-4 col-lg-2">
                <button type="submit" class="btn pos-btn-soft w-100">
                    <i class="bi bi-search me-1"></i> Buscar
                </button>
            </div>
        </form>
    </div>

    <div class="pos-table-responsive">
        <table class="table pos-table align-middle">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th class="text-center">Stock</th>
                    <th class="text-center">Stock mínimo</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $producto)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $producto->nombre }}</div>
                            <small class="text-muted">Código: {{ $producto->codigo }}</small>
                        </td>
                        <td class="text-muted">{{ $producto->categoria?->nombre ?? '—' }}</td>
                        <td class="text-center fw-extrabold {{ $producto->sinStock() ? 'text-danger' : ($producto->stockBajo() ? 'text-warning' : 'text-success') }}">
                            {{ $producto->stock }}
                        </td>
                        <td class="text-center text-muted">{{ $producto->stock_minimo }}</td>
                        <td>
                            @if($producto->sinStock())
                                <span class="badge badge-stock-out"><i class="bi bi-x-circle me-1"></i>Sin stock</span>
                            @elseif($producto->stockBajo())
                                <span class="badge badge-stock-low"><i class="bi bi-exclamation-triangle me-1"></i>Stock bajo</span>
                            @else
                                <span class="badge badge-stock-ok"><i class="bi bi-check-circle me-1"></i>OK</span>
                            @endif
                            @if(!$producto->estado)
                                <span class="badge badge-stock-out">Inactivo</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="pos-empty">
                                <i class="bi bi-clipboard2-data"></i>
                                <h6>No hay productos para este filtro</h6>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $productos->links('pagination::bootstrap-5') }}
    </div>
@endsection
