@extends('layouts.app')

@section('title', 'Editar usuario')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="pos-card">
                <div class="pos-card-title"><i class="bi bi-person-gear"></i> Editar «{{ $usuario->nombre }}»</div>

                <form action="{{ route('usuarios.update', $usuario) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')
                    @include('usuarios._form')

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('usuarios.index') }}" class="btn btn-light">Cancelar</a>
                        <button type="submit" class="btn pos-btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
