<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Inicio') · POS Tienda</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/pos.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
@php($usuario = auth()->user())

<div class="pos-layout">

    {{-- ================= SIDEBAR ================= --}}
    <aside class="pos-sidebar" id="posSidebar">
        <div class="pos-brand">
            <span class="pos-brand-icon"><i class="bi bi-bag-heart-fill"></i></span>
            <div class="pos-brand-text">
                <strong>POS Tienda</strong>
                <small>Sistema de ventas</small>
            </div>
        </div>

        <nav class="pos-nav">
            @if($usuario && $usuario->esAdministrador())
                <a href="{{ route('dashboard') }}" class="pos-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i><span>Dashboard</span>
                </a>
            @endif

            @if($usuario && ($usuario->esAdministrador() || $usuario->esVendedor()))
                <a href="{{ route('pos') }}" class="pos-nav-link {{ request()->routeIs('pos') ? 'active' : '' }}">
                    <i class="bi bi-cart4"></i><span>Punto de venta</span>
                </a>
                <a href="{{ route('ventas.index') }}" class="pos-nav-link {{ request()->routeIs('ventas.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i><span>Ventas</span>
                </a>
                <a href="{{ route('clientes.index') }}" class="pos-nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i><span>Clientes</span>
                </a>
            @endif

            <a href="{{ route('productos.index') }}" class="pos-nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i><span>Productos</span>
            </a>

            @if($usuario && ($usuario->esAdministrador() || $usuario->esAlmacenero()))
                <a href="{{ route('categorias.index') }}" class="pos-nav-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i><span>Categorías</span>
                </a>
                <a href="{{ route('inventario.index') }}" class="pos-nav-link {{ request()->routeIs('inventario*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard2-data"></i><span>Inventario</span>
                </a>
            @endif

            @if($usuario && $usuario->esAdministrador())
                <a href="{{ route('reportes.index') }}" class="pos-nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up"></i><span>Reportes</span>
                </a>
                <a href="{{ route('usuarios.index') }}" class="pos-nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i><span>Usuarios</span>
                </a>
            @endif
        </nav>

        <div class="pos-sidebar-footer">
            <i class="bi bi-heart-fill text-danger"></i>
            <small>Hecho con cariño</small>
        </div>
    </aside>
    <div class="pos-sidebar-overlay" id="posSidebarOverlay"></div>
    {{-- ================= CONTENIDO ================= --}}
    <div class="pos-main">
        <nav class="pos-navbar">
            <div class="d-flex align-items-center gap-2">
                <button class="pos-btn-icon d-lg-none" id="posSidebarToggle" type="button" aria-label="Abrir menú">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="pos-page-title mb-0">@yield('title', 'Inicio')</h1>
            </div>

            <div class="d-flex align-items-center gap-3">
                <span class="pos-date d-none d-md-inline">
                    <i class="bi bi-calendar3 me-1"></i>{{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
                </span>

                @auth
                    <div class="dropdown">
                        <a href="#" class="pos-user dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="pos-avatar">{{ $usuario->iniciales() }}</span>
                            <span class="pos-user-info d-none d-sm-block">
                                <strong>{{ $usuario->nombre }}</strong>
                                <small>{{ $usuario->rol }}</small>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end pos-user-menu">
                            <li>
                                <span class="dropdown-item-text small text-muted">
                                    <i class="bi bi-person-circle me-1"></i>Sesión iniciada como
                                    <strong>{{ $usuario->usuario }}</strong>
                                </span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-1"></i>Cerrar sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
            </div>
        </nav>

        <main class="pos-content">
            @include('partials.alerts')
            @yield('content')
        </main>
    </div>

</div>

{{-- Modal de confirmación reutilizable --}}
<div class="modal fade" id="posConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content pos-confirm">
            <div class="modal-body text-center p-4">
                <div class="pos-confirm-icon"><i class="bi bi-exclamation-triangle"></i></div>
                <h5 class="modal-title fw-bold mt-3" id="posConfirmTitle">¿Estás seguro?</h5>
                <p class="text-muted mb-0" id="posConfirmMessage">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 pb-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="posConfirmAccept">Sí, continuar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
