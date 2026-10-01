@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h2 class="h5 fw-extrabold mb-1">Gestión de clientes</h2>
            <p class="text-muted small mb-0">Administra los clientes y consulta su historial.</p>
        </div>
        <a href="{{ route('clientes.create') }}" class="btn pos-btn-primary">
            <i class="bi bi-person-plus me-1"></i> Nuevo cliente
        </a>
    </div>

    <div class="pos-card pos-card-compact mb-3">
        <form action="{{ route('clientes.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-sm-8 col-lg-5">
                <label class="form-label">Buscar</label>
                <input type="text" name="q" class="form-control" placeholder="Nombre, documento, teléfono o email..." value="{{ $busqueda }}">
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
                    <th>Nombre</th>
                    <th>Documento</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Compras</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $cliente)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $cliente->nombre }}</div>
                            <small class="text-muted">{{ $cliente->direccion ?: 'Sin dirección' }}</small>
                        </td>
                        <td><span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $cliente->documento }}</span></td>
                        <td class="text-muted">{{ $cliente->telefono ?: '—' }}</td>
                        <td class="text-muted">{{ $cliente->email ?: '—' }}</td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary-emphasis">{{ $cliente->ventas_count }}</span>
                        </td>
                        <td>
                            @if($cliente->estado)
                                <span class="badge badge-stock-ok">Activo</span>
                            @else
                                <span class="badge badge-stock-out">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-sm btn-outline-secondary" title="Historial">
                                <i class="bi bi-clock-history"></i>
                            </a>
                            <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('clientes.toggle', $cliente) }}" method="POST"
                                  class="d-inline"
                                  data-confirm="{{ $cliente->estado ? '¿Desactivar este cliente?' : '¿Activar este cliente?' }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $cliente->estado ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                        title="{{ $cliente->estado ? 'Desactivar' : 'Activar' }}">
                                    <i class="bi {{ $cliente->estado ? 'bi-toggle-off' : 'bi-toggle-on' }}"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="pos-empty">
                                <i class="bi bi-people"></i>
                                <h6>No hay clientes registrados</h6>
                                <p class="small mb-0">Registra un cliente para comenzar a vender.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $clientes->links('pagination::bootstrap-5') }}
    </div>
@endsection
