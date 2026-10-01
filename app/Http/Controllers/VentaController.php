<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\MetodoPago;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    /**
     * Punto de venta (formulario POS).
     */
    public function create()
    {
        $clientes = Cliente::where('estado', true)->orderBy('nombre')->get();
        $metodosPago = MetodoPago::where('estado', true)->orderBy('id_metodo_pago')->get();

        return view('pos.create', compact('clientes', 'metodosPago'));
    }

    /**
     * Registrar una venta completa (transaccional):
     * venta + detalle + descuento de stock + movimiento de inventario.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_producto' => ['required', 'integer', 'exists:productos,id_producto'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
            'id_cliente' => ['required', 'integer', 'exists:clientes,id_cliente'],
            'id_metodo_pago' => ['required', 'integer', 'exists:metodos_pago,id_metodo_pago'],
            'tipo_comprobante' => ['required', 'in:Boleta,Factura,Ticket'],
            'descuento' => ['nullable', 'numeric', 'min:0'],
            'monto_pagado' => ['nullable', 'numeric', 'min:0'],
        ], [
            'items.required' => 'El carrito está vacío.',
            'items.min' => 'El carrito está vacío.',
            'items.*.cantidad.min' => 'La cantidad debe ser al menos 1.',
            'id_cliente.required' => 'Debes seleccionar un cliente.',
            'id_metodo_pago.required' => 'Debes seleccionar un método de pago.',
            'tipo_comprobante.in' => 'El tipo de comprobante no es válido.',
            'descuento.numeric' => 'El descuento debe ser un valor numérico.',
            'descuento.min' => 'El descuento no puede ser negativo.',
            'monto_pagado.numeric' => 'El monto pagado debe ser un valor numérico.',
        ], [
            'items' => 'productos',
            'id_cliente' => 'cliente',
            'id_metodo_pago' => 'método de pago',
            'tipo_comprobante' => 'tipo de comprobante',
            'monto_pagado' => 'monto pagado',
        ]);

        $cliente = Cliente::where('estado', true)->find($datos['id_cliente']);
        if (! $cliente) {
            return $this->respuestaError('El cliente seleccionado no está activo.');
        }

        $metodoPago = MetodoPago::where('estado', true)->find($datos['id_metodo_pago']);
        if (! $metodoPago) {
            return $this->respuestaError('El método de pago seleccionado no está activo.');
        }
        $descuento = round((float) ($datos['descuento'] ?? 0), 2);
        $esEfectivo = $metodoPago->nombre === 'Efectivo';

        try {
            $venta = DB::transaction(function () use ($datos, $cliente, $metodoPago, $descuento, $esEfectivo, $request) {
                $subtotalGeneral = 0;
                $lineas = [];

                foreach ($datos['items'] as $item) {
                    $producto = Producto::where('estado', true)
                        ->lockForUpdate()
                        ->find($item['id_producto']);

                    if (! $producto) {
                        throw new \RuntimeException('Uno de los productos ya no está disponible.');
                    }

                    $cantidad = (int) $item['cantidad'];

                    if ($producto->stock < $cantidad) {
                        throw new \RuntimeException(
                            "Stock insuficiente para «{$producto->nombre}». Disponible: {$producto->stock}."
                        );
                    }

                    $lineaSubtotal = round($producto->precio * $cantidad, 2);
                    $subtotalGeneral += $lineaSubtotal;

                    $lineas[] = [
                        'producto' => $producto,
                        'cantidad' => $cantidad,
                        'precio' => (float) $producto->precio,
                        'subtotal' => $lineaSubtotal,
                    ];
                }

                if ($descuento > $subtotalGeneral) {
                    throw new \RuntimeException('El descuento no puede ser mayor que el subtotal.');
                }

                $total = round($subtotalGeneral - $descuento, 2);

                $montoPagado = $esEfectivo
                    ? round((float) $datos['monto_pagado'], 2)
                    : $total;

                if ($esEfectivo && $montoPagado < $total) {
                    throw new \RuntimeException('El monto pagado es menor que el total a pagar.');
                }

                $vuelto = $esEfectivo ? round($montoPagado - $total, 2) : 0;

                $venta = Venta::create([
                    'id_usuario' => $request->user()->id_usuario,
                    'id_cliente' => $cliente->id_cliente,
                    'id_metodo_pago' => $metodoPago->id_metodo_pago,
                    'tipo_comprobante' => $datos['tipo_comprobante'],
                    'fecha_venta' => now(),
                    'subtotal' => $subtotalGeneral,
                    'descuento' => $descuento,
                    'total' => $total,
                    'monto_pagado' => $montoPagado,
                    'vuelto' => $vuelto,
                    'estado' => 'Completada',
                ]);

                foreach ($lineas as $linea) {
                    $producto = $linea['producto'];

                    DetalleVenta::create([
                        'id_venta' => $venta->id_venta,
                        'id_producto' => $producto->id_producto,
                        'cantidad' => $linea['cantidad'],
                        'precio_unitario' => $linea['precio'],
                        'subtotal' => $linea['subtotal'],
                    ]);

                    $producto->stock -= $linea['cantidad'];
                    $producto->save();

                    MovimientoInventario::create([
                        'id_producto' => $producto->id_producto,
                        'id_usuario' => $request->user()->id_usuario,
                        'tipo' => 'Salida',
                        'cantidad' => $linea['cantidad'],
                        'stock_final' => $producto->stock,
                        'motivo' => "Venta #{$venta->id_venta}",
                        'fecha_movimiento' => now(),
                    ]);
                }

                return $venta;
            });
        } catch (\RuntimeException $e) {
            return $this->respuestaError($e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'mensaje' => 'Venta realizada correctamente.',
                'id_venta' => $venta->id_venta,
                'comprobante' => route('ventas.comprobante', $venta),
            ]);
        }

        return redirect()->route('ventas.show', $venta)
            ->with('success', 'Venta registrada correctamente.');
    }

    private function respuestaError(string $mensaje)
    {
        if (request()->expectsJson()) {
            return response()->json(['ok' => false, 'mensaje' => $mensaje], 422);
        }

        return back()->with('error', $mensaje);
    }
    /**
     * Historial de ventas con filtros.
     */
    public function index(Request $request)
    {
        $desde = $request->query('desde');
        $hasta = $request->query('hasta');
        $metodo = $request->query('metodo');

        $ventas = Venta::query()
            ->with(['cliente', 'usuario', 'metodoPago'])
            ->when($desde, fn ($query, $desde) => $query->whereDate('fecha_venta', '>=', $desde))
            ->when($hasta, fn ($query, $hasta) => $query->whereDate('fecha_venta', '<=', $hasta))
            ->when($metodo, fn ($query, $metodo) => $query->where('id_metodo_pago', $metodo))
            ->orderByDesc('fecha_venta')
            ->paginate(15)
            ->withQueryString();

        return view('ventas.index', [
            'ventas' => $ventas,
            'metodosPago' => MetodoPago::orderBy('id_metodo_pago')->get(),
            'desde' => $desde,
            'hasta' => $hasta,
            'metodo' => $metodo,
        ]);
    }

    /**
     * Detalle de una venta.
     */
    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'usuario', 'metodoPago', 'detalles.producto']);

        return view('ventas.show', compact('venta'));
    }

    /**
     * Comprobante imprimible de la venta.
     */
    public function comprobante(Venta $venta)
    {
        $venta->load(['cliente', 'usuario', 'metodoPago', 'detalles.producto']);

        return view('ventas.comprobante', compact('venta'));
    }
}


