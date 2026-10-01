<div class="row g-3">
    <div class="col-md-6">
        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
        <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $producto->nombre ?? '') }}" maxlength="150" required>
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
        <input type="text" name="codigo" id="codigo" class="form-control @error('codigo') is-invalid @enderror"
               value="{{ old('codigo', $producto->codigo ?? '') }}" maxlength="50" required>
        @error('codigo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="id_categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
        <select name="id_categoria" id="id_categoria" class="form-select @error('id_categoria') is-invalid @enderror" required>
            <option value="">Selecciona una categoría...</option>
            @foreach($categorias as $cat)
                <option value="{{ $cat->id_categoria }}"
                    {{ (string) old('id_categoria', $producto->id_categoria ?? '') === (string) $cat->id_categoria ? 'selected' : '' }}>
                    {{ $cat->nombre }}
                </option>
            @endforeach
        </select>
        @error('id_categoria')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="estado" class="form-label">Estado</label>
        <select name="estado" id="estado" class="form-select">
            <option value="1" {{ old('estado', $producto->estado ?? 1) ? 'selected' : '' }}>Activo</option>
            <option value="0" {{ old('estado', $producto->estado ?? 1) ? '' : 'selected' }}>Inactivo</option>
        </select>
    </div>

    <div class="col-md-4">
        <label for="precio" class="form-label">Precio (S/) <span class="text-danger">*</span></label>
        <input type="number" name="precio" id="precio" class="form-control @error('precio') is-invalid @enderror"
               value="{{ old('precio', $producto->precio ?? '') }}" step="0.01" min="0" required>
        @error('precio')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="stock" class="form-label">Stock actual <span class="text-danger">*</span></label>
        <input type="number" name="stock" id="stock" class="form-control @error('stock') is-invalid @enderror"
               value="{{ old('stock', $producto->stock ?? 0) }}" min="0" required>
        @error('stock')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="stock_minimo" class="form-label">Stock mínimo <span class="text-danger">*</span></label>
        <input type="number" name="stock_minimo" id="stock_minimo" class="form-control @error('stock_minimo') is-invalid @enderror"
               value="{{ old('stock_minimo', $producto->stock_minimo ?? 3) }}" min="0" required>
        @error('stock_minimo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea name="descripcion" id="descripcion" rows="3"
                  class="form-control @error('descripcion') is-invalid @enderror"
                  placeholder="Descripción opcional...">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
        @error('descripcion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="imagen" class="form-label">Imagen (máx. 2 MB)</label>
        <input type="file" name="imagen" id="imagen" accept="image/*"
               class="form-control @error('imagen') is-invalid @enderror">
        @error('imagen')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @if(isset($producto) && $producto->imagen)
        <div class="col-md-6">
            <label class="form-label">Imagen actual</label>
            <div class="border rounded p-2 text-center bg-white">
                <img src="{{ asset('imagenes/productos/'.$producto->imagen) }}" alt="{{ $producto->nombre }}"
                     style="max-height: 90px;">
            </div>
        </div>
    @endif
</div>
