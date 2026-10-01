@extends('layouts.guest')

@section('title', 'Iniciar sesión')

@section('content')
    <div class="pos-guest-card">
        <div class="text-center mb-4">
            <div class="pos-guest-logo"><i class="bi bi-bag-heart-fill"></i></div>
            <h1 class="h3 fw-extrabold mt-3 mb-1">POS Tienda</h1>
            <p class="text-muted mb-0">Inicia sesión para continuar al sistema</p>
        </div>

        <form action="{{ route('login') }}" method="POST" novalidate>
            @csrf

            <div class="mb-3">
                <label for="usuario" class="form-label fw-semibold">Usuario</label>
                <div class="input-group pos-input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input
                        type="text"
                        name="usuario"
                        id="usuario"
                        class="form-control @error('usuario') is-invalid @enderror"
                        value="{{ old('usuario') }}"
                        placeholder="Tu nombre de usuario"
                        autocomplete="username"
                        required
                        autofocus
                    >
                    @error('usuario')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-semibold">Contraseña</label>
                <div class="input-group pos-input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    >
                    <button class="input-group-text" type="button" id="togglePassword" aria-label="Mostrar contraseña">
                        <i class="bi bi-eye"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn pos-btn-primary w-100 py-2 fw-bold">
                <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar sesión
            </button>
        </form>

        <p class="text-center small text-muted mt-4 mb-0">
            <i class="bi bi-info-circle me-1"></i>
            Consulta al administrador si no tienes una cuenta.
        </p>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('togglePassword')?.addEventListener('click', function () {
        const input = document.getElementById('password');
        const icon = this.querySelector('i');
        const visible = input.type === 'text';
        input.type = visible ? 'password' : 'text';
        icon.className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';
    });
</script>
@endpush
