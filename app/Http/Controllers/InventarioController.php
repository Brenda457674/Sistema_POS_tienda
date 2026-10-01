<?php

namespace App\Http\Controllers;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    /**
     * Consulta de stock actual con filtros (bajo / sin stock).
     */
    public function index(Request $request)
    {
        $filtro = $request->query('filtro', 'todos');
        $busqueda = $request->query('q');

        $query = Producto::query()
            ->with('categoria')
            ->when($busqueda, function ($query, $busqueda) {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('codigo', 'like', "%{$busqueda}%");
                });
            });

        if ($filtro === 'bajo') {
            $query->where('stock', '>', 0)
                ->whereColumn('stock', '<=', 'stock_minimo');
        } elseif ($filtro === 'agotado') {
            $query->where('stock', '<=', 0);
        }

        $productos = $query->orderBy('stock')
            ->paginate(15)
            ->withQueryString();

        $totales = [
            'todos' => Producto::count(),
            'bajo' => Producto::where('stock', '>', 0)->whereColumn('stock', '<=', 'stock_minimo')->count(),
            'agotado' => Producto::where('stock', '<=', 0)->count(),
        ];

        return view('inventario.index', compact('productos', 'filtro', 'busqueda', 'totales'));
    }

    /**
     * Historial de movimientos de inventario.
     */
    public function movimientos(Request $request)
    {
        $tipo = $request->query('tipo');
        $productoId = $request->query('producto');

        $movimientos = MovimientoInventario::query()
            ->with(['producto', 'usuario'])
            ->when($tipo, fn ($query, $tipo) => $query->where('tipo', $tipo))
            ->when($productoId, fn ($query, $productoId) => $query->where('id_producto', $productoId))
            ->orderByDesc('fecha_movimiento')
            ->paginate(15)
            ->withQueryString();

        return view('inventario.movimientos', [
            'movimientos' => $movimientos,
            'tipo' => $tipo,
            'productoId' => $productoId,
            'productos' => Producto::orderBy('nombre')->get(['id_producto', 'nombre', 'codigo']),
        ]);
    }

    /**
     * Formulario para registrar un movimiento.
     */
    public function create()
    {
        $productos = Producto::where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view('inventario.create', compact('productos'));
    }

    /**
     * Registrar entrada, salida o ajuste de stock.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'id_producto' => ['required', 'integer', 'exists:productos,id_producto'],
            'tipo' => ['required', 'in:Entrada,Salida,Ajuste'],
            'cantidad' => ['required', 'integer', 'min:0'],
            'motivo' => ['required', 'string', 'max:255'],
        ], [
            'id_producto.required' => 'Debes seleccionar un producto.',
            'id_producto.exists' => 'El producto seleccionado no existe.',
            'tipo.required' => 'Debes seleccionar el tipo de movimiento.',
            'tipo.in' => 'El tipo de movimiento no es válido.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad no puede ser negativa.',
            'motivo.required' => 'El motivo del movimiento es obligatorio.',
            'motivo.max' => 'El motivo no puede superar los 255 caracteres.',
        ], [
            'id_producto' => 'producto',
            'tipo' => 'tipo',
            'motivo' => 'motivo',
        ]);

        if ($datos['tipo'] !== 'Ajuste' && (int) $datos['cantidad'] < 1) {
            return back()
                ->withInput()
                ->withErrors(['cantidad' => 'La cantidad debe ser al menos 1 para entradas y salidas.']);
        }

        $producto = Producto::find($datos['id_producto']);
        $cantidad = (int) $datos['cantidad'];
        $stockFinal = $producto->stock;

        switch ($datos['tipo']) {
            case 'Entrada':
                $stockFinal = $producto->stock + $cantidad;
                break;

            case 'Salida':
                if ($cantidad > $producto->stock) {
                    return back()
                        ->withInput()
                        ->withErrors(['cantidad' => "La salida supera el stock disponible ({$producto->stock} unidades)."]);
                }
                $stockFinal = $producto->stock - $cantidad;
                break;

            case 'Ajuste':
                // En un ajuste la cantidad representa el stock real contado.
                $stockFinal = $cantidad;
                break;
        }

        $producto->stock = $stockFinal;
        $producto->save();

        MovimientoInventario::create([
            'id_producto' => $producto->id_producto,
            'id_usuario' => $request->user()->id_usuario,
            'tipo' => $datos['tipo'],
            'cantidad' => $cantidad,
            'stock_final' => $stockFinal,
            'motivo' => $datos['motivo'],
            'fecha_movimiento' => now(),
        ]);

        return redirect()->route('inventario.movimientos')
            ->with('success', "Movimiento de {$datos['tipo']} registrado correctamente. Stock final: {$stockFinal}.");
    }
}
