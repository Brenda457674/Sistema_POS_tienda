<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = now()->toDateString();

        $ventasHoy = Venta::whereDate('fecha_venta', $hoy);

        $ventasDelDia = [
            'cantidad' => (clone $ventasHoy)->count(),
            'total' => (clone $ventasHoy)->sum('total'),
        ];

        $stockBajo = Producto::where('estado', true)
            ->where('stock', '>', 0)
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->count();

        $sinStock = Producto::where('estado', true)
            ->where('stock', '<=', 0)
            ->count();

        $ventasRecientes = Venta::with(['cliente', 'metodoPago', 'usuario'])
            ->orderByDesc('fecha_venta')
            ->limit(6)
            ->get();

        $masVendidos = DetalleVenta::query()
            ->selectRaw('id_producto, SUM(cantidad) as unidades_vendidas, SUM(subtotal) as monto_vendido')
            ->groupBy('id_producto')
            ->orderByDesc('unidades_vendidas')
            ->limit(5)
            ->get()
            ->map(function ($fila) {
                $producto = Producto::with('categoria')->find($fila->id_producto);

                return [
                    'producto' => $producto,
                    'unidades' => (int) $fila->unidades_vendidas,
                    'monto' => (float) $fila->monto_vendido,
                ];
            })
            ->filter(fn ($fila) => $fila['producto'] !== null)
            ->values();

        return view('dashboard', [
            'ventasDelDia' => $ventasDelDia,
            'totalProductos' => Producto::where('estado', true)->count(),
            'totalClientes' => Cliente::where('estado', true)->count(),
            'stockBajo' => $stockBajo,
            'sinStock' => $sinStock,
            'ventasRecientes' => $ventasRecientes,
            'masVendidos' => $masVendidos,
            'totalVentasMes' => Venta::whereBetween('fecha_venta', [
                now()->startOfMonth(),
                now()->endOfDay(),
            ])->sum('total'),
        ]);
    }
}
