<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Comprobante Venta #{{ $venta->id_venta }} · POS Tienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Nunito', 'Segoe UI', system-ui, sans-serif;
            background: #f0eff7;
            color: #3b3854;
            padding: 2rem 1rem;
        }
        .ticket {
            max-width: 460px;
            margin: 0 auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 16px 40px rgba(88, 76, 180, 0.12);
            overflow: hidden;
        }
        .ticket-head {
            background: linear-gradient(135deg, #7c6cf2, #ff8fab);
            color: #fff;
            padding: 1.4rem;
            text-align: center;
        }
        .ticket-head h1 { font-size: 1.25rem; font-weight: 800; }
        .ticket-head p { font-size: 0.8rem; opacity: 0.9; }
        .ticket-body { padding: 1.4rem; }
        .ticket-meta { font-size: 0.82rem; margin-bottom: 1rem; }
        .ticket-meta div {
            display: flex;
            justify-content: space-between;
            padding: 0.22rem 0;
            border-bottom: 1px dashed #ecebf5;
        }
        .ticket-meta span:first-child { color: #8b88a3; }
        .ticket-meta span:last-child { font-weight: 700; text-align: right; }
        table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
        thead th {
            background: #efecfe;
            color: #6355e0;
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 0.5rem 0.4rem;
            text-align: left;
        }
        tbody td { padding: 0.5rem 0.4rem; border-bottom: 1px solid #ecebf5; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .totals { margin-top: 1rem; font-size: 0.85rem; }
        .totals div {
            display: flex;
            justify-content: space-between;
            padding: 0.2rem 0;
        }
        .totals .grand {
            font-size: 1.1rem;
            font-weight: 800;
            color: #6355e0;
            border-top: 2px solid #7c6cf2;
            margin-top: 0.4rem;
            padding-top: 0.5rem;
        }
        .ticket-footer {
            text-align: center;
            padding: 1rem 1.4rem 1.4rem;
            font-size: 0.78rem;
            color: #8b88a3;
        }
        .ticket-footer .gracias { font-weight: 800; color: #3b3854; font-size: 0.95rem; }
        .acciones {
            max-width: 460px;
            margin: 1rem auto;
            display: flex;
            gap: 0.6rem;
            justify-content: center;
        }
        .btn {
            border: none;
            border-radius: 10px;
            padding: 0.6rem 1.2rem;
            font-weight: 700;
            font-size: 0.88rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .btn-print { background: #7c6cf2; color: #fff; box-shadow: 0 8px 18px rgba(124,108,242,.3); }
        .btn-back { background: #fff; color: #3b3854; border: 1px solid #ecebf5; }
        .badge-type {
            display: inline-block;
            background: #ffeef3;
            color: #e0577f;
            border-radius: 999px;
            padding: 0.15rem 0.7rem;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .acciones { display: none !important; }
            .ticket { box-shadow: none; border-radius: 0; max-width: 100%; }
            @page { margin: 8mm; }
        }
    </style>
</head>
<body>
    <div class="acciones">
        <button type="button" class="btn btn-print" onclick="window.print()">
            <i class="bi bi-printer"></i> Imprimir
        </button>
        <a href="{{ route('ventas.show', $venta) }}" class="btn btn-back">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="ticket">
        <div class="ticket-head">
            <h1>🛍️ POS Tienda</h1>
            <p>Sistema de punto de venta</p>
        </div>

        <div class="ticket-body">
            <div class="ticket-meta">
                <div><span>Comprobante</span><span>{{ $venta->tipo_comprobante }} <span class="badge-type">N° {{ str_pad((string) $venta->id_venta, 6, '0', STR_PAD_LEFT) }}</span></span></div>
                <div><span>Fecha</span><span>{{ $venta->fecha_venta?->format('d/m/Y H:i') }}</span></div>
                <div><span>Vendedor</span><span>{{ $venta->usuario?->nombre ?? '—' }}</span></div>
                <div><span>Cliente</span><span>{{ $venta->cliente?->nombre ?? '—' }}</span></div>
                <div><span>Documento</span><span>{{ $venta->cliente?->documento ?? '—' }}</span></div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-end">P. unit.</th>
                        <th class="text-end">Importe</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($venta->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->producto?->nombre ?? 'Producto' }}</td>
                            <td class="text-center">{{ $detalle->cantidad }}</td>
                            <td class="text-end">{{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td class="text-end">{{ number_format($detalle->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals">
                <div><span>Subtotal</span><span>S/ {{ number_format($venta->subtotal, 2) }}</span></div>
                <div><span>Descuento</span><span>- S/ {{ number_format($venta->descuento, 2) }}</span></div>
                <div class="grand"><span>TOTAL</span><span>S/ {{ number_format($venta->total, 2) }}</span></div>
                <div><span>Método de pago</span><span>{{ $venta->metodoPago?->nombre ?? '—' }}</span></div>
                <div><span>Monto pagado</span><span>S/ {{ number_format($venta->monto_pagado, 2) }}</span></div>
                <div><span>Vuelto</span><span>S/ {{ number_format($venta->vuelto, 2) }}</span></div>
            </div>
        </div>

        <div class="ticket-footer">
            <p class="gracias">¡Gracias por tu compra! 💜</p>
            <p class="mt-1">Este comprobante acredita tu compra en POS Tienda.</p>
        </div>
    </div>
</body>
</html>

