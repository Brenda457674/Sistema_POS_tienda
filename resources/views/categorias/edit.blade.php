@extends('layouts.app')

@section('title', 'Editar categoría')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="pos-card">
                <div class="pos-card-title"><i class="bi bi-pencil-square"></i> Editar «{{ $categoria->nombre }}»</div>

                <form action="{{ route('categorias.update', $categoria) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')
                    @include('categorias._form')

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('categorias.index') }}" class="btn btn-light">Cancelar</a>
                        <button type="submit" class="btn pos-btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
