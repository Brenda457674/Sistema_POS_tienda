@extends('layouts.app')

@section('title', 'Editar cliente')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="pos-card">
                <div class="pos-card-title"><i class="bi bi-pencil-square"></i> Editar «{{ $cliente->nombre }}»</div>

                <form action="{{ route('clientes.update', $cliente) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')
                    @include('clientes._form')

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('clientes.index') }}" class="btn btn-light">Cancelar</a>
                        <button type="submit" class="btn pos-btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
