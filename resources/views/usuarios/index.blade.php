@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h2 class="h5 fw-extrabold mb-1">Gestión de usuarios</h2>
            <p class="text-muted small mb-0">Administra los accesos y roles del sistema.</p>
        </div>
        <a href="{{ route('usuarios.create') }}" class="btn pos-btn-primary">
            <i class="bi bi-person-plus me-1"></i> Nuevo usuario
        </a>
    </div>

    <div class="pos-card pos-card-compact mb-3">
        <form action="{{ route('usuarios.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-sm-6 col-lg-4">
                <label class="form-label">Buscar</label>
                <input type="text" name="q" class="form-control" placeholder="Nombre, usuario o email..." value="{{ $busqueda }}">
            </div>
            <div class="col-sm-4 col-lg-3">
                <label class="form-label">Rol</label>
                <select name="rol" class="form-select">
                    <option value="">Todos los roles</option>
                    @foreach(['Administrador', 'Vendedor', 'Almacenero'] as $opcion)
                        <option value="{{ $opcion }}" {{ $rol === $opcion ? 'selected' : '' }}>{{ $opcion }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2 col-lg-2">
                <button type="submit" class="btn pos-btn-soft w-100">
                    <i class="bi bi-search me-1"></i> Filtrar
                </button>
            </div>
        </form>
    </div>

    <div class="pos-table-responsive">
        <table class="table pos-table align-middle">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $item)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="pos-avatar" style="width:34px;height:34px;font-size:0.72rem;">
                                    {{ strtoupper(mb_substr($item->nombre, 0, 1)) }}
                                </span>
                                <div>
                                    <div class="fw-bold">{{ $item->nombre }}</div>
                                    <small class="text-muted">@{{ $item->usuario }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted">{{ $item->email }}</td>
                        <td>
                            <span class="badge badge-role-{{ strtolower($item->rol) }}">{{ $item->rol }}</span>
                        </td>
                        <td>
                            @if($item->estado)
                                <span class="badge badge-stock-ok">Activo</span>
                            @else
                                <span class="badge badge-stock-out">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('usuarios.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($item->id_usuario !== auth()->id())
                                <form action="{{ route('usuarios.toggle', $item) }}" method="POST"
                                      class="d-inline"
                                      data-confirm="{{ $item->estado ? '¿Desactivar este usuario?' : '¿Activar este usuario?' }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $item->estado ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                            title="{{ $item->estado ? 'Desactivar' : 'Activar' }}">
                                        <i class="bi {{ $item->estado ? 'bi-toggle-off' : 'bi-toggle-on' }}"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="pos-empty">
                                <i class="bi bi-people"></i>
                                <h6>No hay usuarios registrados</h6>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $usuarios->links('pagination::bootstrap-5') }}
    </div>
@endsection
