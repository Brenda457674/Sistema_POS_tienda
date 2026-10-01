@extends('layouts.app')

@section('title', 'Registrar movimiento')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="pos-card">
                <div class="pos-card-title"><i class="bi bi-arrow-left-right"></i> Registrar movimiento de inventario</div>

                <div class="alert alert-info small pos-alert">
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>Entrada:</strong> suma stock · <strong>Salida:</strong> resta stock ·
                    <strong>Ajuste:</strong> establece el stock real contado.
                </div>

                <form action="{{ route('inventario.store') }}" method="POST" novalidate>
                    @csrf

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="id_producto" class="form-label">Producto <span class="text-danger">*</span></label>
                            <select name="id_producto" id="id_producto" class="form-select @error('id_producto') is-invalid @enderror" required>
                                <option value="">Selecciona un producto...</option>
                                @foreach($productos as $producto)
                                    <option value="{{ $producto->id_producto }}"
                                        {{ (string) old('id_producto') === (string) $producto->id_producto ? 'selected' : '' }}>
                                        {{ $producto->nombre }} ({{ $producto->codigo }}) — Stock: {{ $producto->stock }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_producto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                            <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                @foreach(['Entrada', 'Salida', 'Ajuste'] as $opcion)
                                    <option value="{{ $opcion }}" {{ old('tipo') === $opcion ? 'selected' : '' }}>
                                        {{ $opcion }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="cantidad" class="form-label">Cantidad <span class="text-danger">*</span></label>
                            <input type="number" name="cantidad" id="cantidad"
                                   class="form-control @error('cantidad') is-invalid @enderror"
                                   value="{{ old('cantidad') }}" min="0" required>
                            @error('cantidad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div class="pos-vuelto" id="stockEstimado">Stock actual: —</div>
                        </div>

                        <div class="col-12">
                            <label for="motivo" class="form-label">Motivo <span class="text-danger">*</span></label>
                            <input type="text" name="motivo" id="motivo"
                                   class="form-control @error('motivo') is-invalid @enderror"
                                   value="{{ old('motivo') }}" maxlength="255"
                                   placeholder="Ej: Reposición de mercadería, conteo físico..." required>
                            @error('motivo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('inventario.index') }}" class="btn btn-light">Cancelar</a>
                        <button type="submit" class="btn pos-btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Registrar movimiento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selProducto = document.getElementById('id_producto');
        const elStock = document.getElementById('stockEstimado');

        const actualizar = function () {
            const opcion = selProducto.selectedOptions[0];
            if (!opcion || !selProducto.value) {
                elStock.textContent = 'Stock actual: —';
                return;
            }
            const texto = opcion.textContent;
            const match = texto.match(/Stock:\s*(\d+)/);
            elStock.textContent = 'Stock actual: ' + (match ? match[1] : '—');
        };

        selProducto?.addEventListener('change', actualizar);
        actualizar();
    });
</script>
@endpush
