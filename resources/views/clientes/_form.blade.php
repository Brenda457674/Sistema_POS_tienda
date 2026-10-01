<div class="row g-3">
    <div class="col-md-6">
        <label for="nombre" class="form-label">Nombre completo <span class="text-danger">*</span></label>
        <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $cliente->nombre ?? '') }}" maxlength="150" required>
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="documento" class="form-label">Documento (DNI/RUC) <span class="text-danger">*</span></label>
        <input type="text" name="documento" id="documento" class="form-control @error('documento') is-invalid @enderror"
               value="{{ old('documento', $cliente->documento ?? '') }}" maxlength="20" required>
        @error('documento')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="telefono" class="form-label">Teléfono</label>
        <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror"
               value="{{ old('telefono', $cliente->telefono ?? '') }}" maxlength="20">
        @error('telefono')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $cliente->email ?? '') }}" maxlength="150">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="estado" class="form-label">Estado</label>
        <select name="estado" id="estado" class="form-select">
            <option value="1" {{ old('estado', $cliente->estado ?? 1) ? 'selected' : '' }}>Activo</option>
            <option value="0" {{ old('estado', $cliente->estado ?? 1) ? '' : 'selected' }}>Inactivo</option>
        </select>
    </div>

    <div class="col-12">
        <label for="direccion" class="form-label">Dirección</label>
        <input type="text" name="direccion" id="direccion" class="form-control @error('direccion') is-invalid @enderror"
               value="{{ old('direccion', $cliente->direccion ?? '') }}" maxlength="255">
        @error('direccion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
