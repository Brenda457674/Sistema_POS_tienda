<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $desde = $request->query('desde') ?: now()->toDateString();
        $hasta = $request->query('hasta') ?: now()->toDateString();
        $pestana = $request->query('tab', 'ventas');

        /* -------- Reporte de ventas por rango de fechas -------- */
        $ventas = Venta::query()
            ->with(['cliente', 'usuario', 'metodoPago'])
            ->when($desde, fn ($query, $desde) => $query->whereDate('fecha_venta', '>=', $desde))
            ->when($hasta, fn ($query, $hasta) => $query->whereDate('fecha_venta', '<=', $hasta))
            ->orderByDesc('fecha_venta')
            ->get();

        $resumenVentas = [
            'cantidad' => $ventas->count(),
            'total' => $ventas->sum('total'),
            'descuento' => $ventas->sum('descuento'),
        ];

        /* -------- Productos más vendidos en el rango -------- */
        $idsVentas = $ventas->pluck('id_venta');

        $masVendidos = collect();
        if ($idsVentas->isNotEmpty()) {
            $masVendidos = DetalleVenta::query()
                ->selectRaw('id_producto, SUM(cantidad) as unidades, SUM(subtotal) as monto')
                ->whereIn('id_venta', $idsVentas)
                ->groupBy('id_producto')
                ->orderByDesc('unidades')
                ->limit(10)
                ->get()
                ->map(function ($fila) {
                    $fila->producto = Producto::find($fila->id_producto);

                    return $fila;
                })
                ->filter(fn ($fila) => $fila->producto !== null)
                ->values();
        }

        /* -------- Productos con stock bajo / sin stock -------- */
        $stockBajo = Producto::where('estado', true)
            ->where('stock', '>', 0)
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->with('categoria')
            ->orderBy('stock')
            ->get();

        $sinStock = Producto::where('estado', true)
            ->where('stock', '<=', 0)
            ->with('categoria')
            ->get();

        return view('reportes.index', [
            'ventas' => $ventas,
            'resumenVentas' => $resumenVentas,
            'masVendidos' => $masVendidos,
            'stockBajo' => $stockBajo,
            'sinStock' => $sinStock,
            'desde' => $desde,
            'hasta' => $hasta,
            'pestana' => $pestana,
        ]);
    }
}
