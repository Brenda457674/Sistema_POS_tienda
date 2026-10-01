<div class="row g-3">
    <div class="col-md-6">
        <label for="nombre" class="form-label">Nombre completo <span class="text-danger">*</span></label>
        <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $usuario->nombre ?? '') }}" maxlength="100" required>
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="usuario" class="form-label">Usuario <span class="text-danger">*</span></label>
        <input type="text" name="usuario" id="usuario" class="form-control @error('usuario') is-invalid @enderror"
               value="{{ old('usuario', $usuario->usuario ?? '') }}" maxlength="50" required>
        @error('usuario')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $usuario->email ?? '') }}" maxlength="150" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="rol" class="form-label">Rol <span class="text-danger">*</span></label>
        <select name="rol" id="rol" class="form-select @error('rol') is-invalid @enderror" required>
            <option value="">Selecciona un rol...</option>
            @foreach(['Administrador', 'Vendedor', 'Almacenero'] as $opcion)
                <option value="{{ $opcion }}" {{ old('rol', $usuario->rol ?? '') === $opcion ? 'selected' : '' }}>
                    {{ $opcion }}
                </option>
            @endforeach
        </select>
        @error('rol')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="password" class="form-label">
            Contraseña {!! isset($usuario) && $usuario->exists ? '' : '<span class="text-danger">*</span>' !!}
        </label>
        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
               autocomplete="new-password"
               placeholder="{{ isset($usuario) && $usuario->exists ? 'Déjala vacío para no cambiarla' : 'Mínimo 6 caracteres' }}">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
        <input type="password" name="password_confirmation" id="password_confirmation"
               class="form-control" autocomplete="new-password" placeholder="Repite la contraseña">
    </div>

    <div class="col-md-6">
        <label for="estado" class="form-label">Estado</label>
        <select name="estado" id="estado" class="form-select">
            <option value="1" {{ old('estado', $usuario->estado ?? 1) ? 'selected' : '' }}>Activo</option>
            <option value="0" {{ old('estado', $usuario->estado ?? 1) ? '' : 'selected' }}>Inactivo</option>
        </select>
    </div>
</div>
