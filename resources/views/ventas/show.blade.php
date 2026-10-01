@extends('layouts.app')

@section('title', 'Detalle de venta #'.$venta->id_venta)

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h2 class="h5 fw-extrabold mb-1">Venta #{{ $venta->id_venta }}</h2>
            <p class="text-muted small mb-0">
                {{ $venta->fecha_venta?->format('d/m/Y H:i') }} ·
                <span class="badge bg-primary-subtle text-primary-emphasis">{{ $venta->tipo_comprobante }}</span>
                <span class="badge {{ $venta->estado === 'Completada' ? 'badge-stock-ok' : 'badge-stock-out' }}">{{ $venta->estado }}</span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('ventas.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
            <a href="{{ route('ventas.comprobante', $venta) }}" target="_blank" class="btn pos-btn-primary">
                <i class="bi bi-printer me-1"></i> Ver comprobante
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="pos-card h-100 mb-3 mb-lg-0">
                <div class="pos-card-title"><i class="bi bi-bag-check"></i> Productos</div>

                <div class="pos-table-responsive">
                    <table class="table pos-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-end">P. unitario</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($venta->detalles as $detalle)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $detalle->producto?->nombre ?? 'Producto eliminado' }}</div>
                                        <small class="text-muted">{{ $detalle->producto?->codigo ?? '' }}</small>
                                    </td>
                                    <td class="text-center">{{ $detalle->cantidad }}</td>
                                    <td class="text-end">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td class="text-end fw-bold">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="pos-card h-100">
                <div class="pos-card-title"><i class="bi bi-person"></i> Datos de la venta</div>

                <ul class="list-unstyled small mb-3">
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Cliente</span>
                        <strong>{{ $venta->cliente?->nombre ?? '—' }}</strong>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Documento</span>
                        <strong>{{ $venta->cliente?->documento ?? '—' }}</strong>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Vendedor</span>
                        <strong>{{ $venta->usuario?->nombre ?? '—' }}</strong>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Método de pago</span>
                        <strong>{{ $venta->metodoPago?->nombre ?? '—' }}</strong>
                    </li>
                </ul>

                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Subtotal</span>
                    <strong>S/ {{ number_format($venta->subtotal, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Descuento</span>
                    <strong class="text-danger">- S/ {{ number_format($venta->descuento, 2) }}</strong>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-bold">Total</span>
                    <span class="fs-5 fw-extrabold text-primary">S/ {{ number_format($venta->total, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1 small">
                    <span class="text-muted">Monto pagado</span>
                    <span>S/ {{ number_format($venta->monto_pagado, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Vuelto</span>
                    <span>S/ {{ number_format($venta->vuelto, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
