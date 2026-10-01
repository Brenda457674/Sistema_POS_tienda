<div class="row g-3">
    <div class="col-md-6">
        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
        <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $categoria->nombre ?? '') }}" maxlength="100" required>
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="estado" class="form-label">Estado</label>
        <select name="estado" id="estado" class="form-select">
            <option value="1" {{ old('estado', $categoria->estado ?? 1) ? 'selected' : '' }}>Activa</option>
            <option value="0" {{ old('estado', $categoria->estado ?? 1) ? '' : 'selected' }}>Inactiva</option>
        </select>
    </div>

    <div class="col-12">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea name="descripcion" id="descripcion" rows="3"
                  class="form-control @error('descripcion') is-invalid @enderror"
                  maxlength="255" placeholder="Descripción opcional de la categoría...">{{ old('descripcion', $categoria->descripcion ?? '') }}</textarea>
        @error('descripcion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
