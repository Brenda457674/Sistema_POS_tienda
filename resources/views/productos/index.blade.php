@extends('layouts.app')

@section('title', 'Productos')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h2 class="h5 fw-extrabold mb-1">Catálogo de productos</h2>
            <p class="text-muted small mb-0">Consulta precios, stock y estado de tus productos.</p>
        </div>
        @if(auth()->user()->esAdministrador() || auth()->user()->esAlmacenero())
            <a href="{{ route('productos.create') }}" class="btn pos-btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Nuevo producto
            </a>
        @endif
    </div>

    <div class="pos-card pos-card-compact mb-3">
        <form action="{{ route('productos.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-sm-6 col-lg-4">
                <label class="form-label">Buscar</label>
                <input type="text" name="q" class="form-control" placeholder="Nombre, código o descripción..." value="{{ $busqueda }}">
            </div>
            <div class="col-sm-4 col-lg-3">
                <label class="form-label">Categoría</label>
                <select name="categoria" class="form-select">
                    <option value="">Todas</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id_categoria }}" {{ (string) $categoriaSel === (string) $cat->id_categoria ? 'selected' : '' }}>
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-3 col-lg-2">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="1" {{ $estado === '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ $estado === '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="col-sm-3 col-lg-2">
                <button type="submit" class="btn pos-btn-soft w-100">
                    <i class="bi bi-search me-1"></i> Filtrar
                </button>
            </div>
        </form>
    </div>

    <div class="row g-3">
        @forelse($productos as $producto)
            <div class="col-sm-6 col-xl-4">
                <div class="pos-card h-100 d-flex flex-column">
                    <div class="d-flex gap-3">
                        <div class="pos-product-thumb">
                            @if($producto->imagen && is_file(public_path('imagenes/productos/'.$producto->imagen)))
                                <img src="{{ asset('imagenes/productos/'.$producto->imagen) }}" alt="{{ $producto->nombre }}">
                            @else
                                <i class="bi bi-box-seam"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <h3 class="h6 fw-extrabold mb-1">{{ $producto->nombre }}</h3>
                                @if(!$producto->estado)
                                    <span class="badge badge-stock-out">Inactivo</span>
                                @endif
                            </div>
                            <small class="text-muted d-block">Código: <strong>{{ $producto->codigo }}</strong></small>
                            <small class="text-muted d-block">{{ $producto->categoria?->nombre ?? 'Sin categoría' }}</small>
                            <div class="fw-extrabold text-primary mt-2">S/ {{ number_format($producto->precio, 2) }}</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <div>
                            @if($producto->sinStock())
                                <span class="badge badge-stock-out"><i class="bi bi-x-circle me-1"></i>Sin stock</span>
                            @elseif($producto->stockBajo())
                                <span class="badge badge-stock-low"><i class="bi bi-exclamation-triangle me-1"></i>Stock bajo</span>
                            @else
                                <span class="badge badge-stock-ok"><i class="bi bi-check-circle me-1"></i>Stock OK</span>
                            @endif
                            <div class="small text-muted mt-1">
                                Stock: <strong>{{ $producto->stock }}</strong> / mín. {{ $producto->stock_minimo }}
                            </div>
                        </div>

                        @if(auth()->user()->esAdministrador() || auth()->user()->esAlmacenero())
                            <div class="text-end">
                                <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('productos.toggle', $producto) }}" method="POST"
                                      class="d-inline"
                                      data-confirm="{{ $producto->estado ? '¿Desactivar este producto?' : '¿Activar este producto?' }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $producto->estado ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                            title="{{ $producto->estado ? 'Desactivar' : 'Activar' }}">
                                        <i class="bi {{ $producto->estado ? 'bi-toggle-off' : 'bi-toggle-on' }}"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="pos-card">
                    <div class="pos-empty">
                        <i class="bi bi-box-seam"></i>
                        <h6>No se encontraron productos</h6>
                        <p class="small mb-0">Prueba con otros filtros o registra un producto nuevo.</p>
                    </div>
                </div>
            </div>
        @endforelse

    </div>

    <div class="mt-3">
        {{ $productos->links('pagination::bootstrap-5') }}
    </div>
@endsection
