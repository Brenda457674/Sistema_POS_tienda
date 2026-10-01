@extends('layouts.app')

@section('title', 'Nueva categoría')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="pos-card">
                <div class="pos-card-title"><i class="bi bi-tag"></i> Registrar categoría</div>

                <form action="{{ route('categorias.store') }}" method="POST" novalidate>
                    @csrf
                    @include('categorias._form')

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('categorias.index') }}" class="btn btn-light">Cancelar</a>
                        <button type="submit" class="btn pos-btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
