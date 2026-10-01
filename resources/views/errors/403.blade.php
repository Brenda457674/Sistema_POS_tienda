@extends('layouts.guest')

@section('title', 'Sin acceso')

@section('content')
    <div class="pos-guest-card text-center" style="max-width: 420px;">
        <div class="pos-guest-logo"><i class="bi bi-shield-lock"></i></div>
        <h1 class="fw-extrabold display-6 mt-3">403</h1>
        <p class="text-muted mb-4">
            No tienes permisos para acceder a esta sección.<br>
            Si crees que es un error, contacta al administrador.
        </p>
        <a href="{{ url('/') }}" class="btn pos-btn-primary w-100">
            <i class="bi bi-house-door me-1"></i> Volver al inicio
        </a>
    </div>
@endsection
