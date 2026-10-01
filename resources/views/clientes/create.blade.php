@extends('layouts.app')

@section('title', 'Nuevo cliente')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="pos-card">
                <div class="pos-card-title"><i class="bi bi-person-plus"></i> Registrar cliente</div>

                <form action="{{ route('clientes.store') }}" method="POST" novalidate>
                    @csrf
                    @include('clientes._form')

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('clientes.index') }}" class="btn btn-light">Cancelar</a>
                        <button type="submit" class="btn pos-btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
