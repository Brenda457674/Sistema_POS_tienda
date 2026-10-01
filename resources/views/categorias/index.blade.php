@extends('layouts.app')

@section('title', 'Categorías')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h2 class="h5 fw-extrabold mb-1">Gestión de categorías</h2>
            <p class="text-muted small mb-0">Organiza tus productos por categorías.</p>
        </div>
        <a href="{{ route('categorias.create') }}" class="btn pos-btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nueva categoría
        </a>
    </div>

    <div class="pos-card pos-card-compact mb-3">
        <form action="{{ route('categorias.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-sm-8 col-lg-4">
                <label class="form-label">Buscar</label>
                <input type="text" name="q" class="form-control" placeholder="Nombre o descripción..." value="{{ $busqueda }}">
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
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Productos</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categorias as $categoria)
                    <tr>
                        <td class="text-muted">{{ $categoria->id_categoria }}</td>
                        <td class="fw-bold">{{ $categoria->nombre }}</td>
                        <td class="text-muted">{{ $categoria->descripcion ?: '—' }}</td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary-emphasis">
                                {{ $categoria->productos_count }} productos
                            </span>
                        </td>
                        <td>
                            @if($categoria->estado)
                                <span class="badge badge-stock-ok">Activa</span>
                            @else
                                <span class="badge badge-stock-out">Inactiva</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('categorias.toggle', $categoria) }}" method="POST"
                                  class="d-inline"
                                  data-confirm="{{ $categoria->estado ? '¿Desactivar esta categoría?' : '¿Activar esta categoría?' }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $categoria->estado ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                        title="{{ $categoria->estado ? 'Desactivar' : 'Activar' }}">
                                    <i class="bi {{ $categoria->estado ? 'bi-toggle-off' : 'bi-toggle-on' }}"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="pos-empty">
                                <i class="bi bi-tags"></i>
                                <h6>No hay categorías registradas</h6>
                                <p class="small mb-0">Crea una categoría para comenzar.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $categorias->links('pagination::bootstrap-5') }}
    </div>
@endsection
