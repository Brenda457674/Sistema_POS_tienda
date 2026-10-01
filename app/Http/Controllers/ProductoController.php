<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    private const RUTA_IMAGENES = 'imagenes/productos';

    public function index(Request $request)
    {
        $busqueda = $request->query('q');
        $categoria = $request->query('categoria');
        $estado = $request->query('estado');

        $productos = Producto::query()
            ->with('categoria')
            ->when($busqueda, function ($query, $busqueda) {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('codigo', 'like', "%{$busqueda}%")
                        ->orWhere('descripcion', 'like', "%{$busqueda}%");
                });
            })
            ->when($categoria, fn ($query, $categoria) => $query->where('id_categoria', $categoria))
            ->when($estado !== null && $estado !== '', fn ($query, $estado) => $query->where('estado', $estado))
            ->orderBy('nombre')
            ->paginate(12)
            ->withQueryString();

        return view('productos.index', [
            'productos' => $productos,
            'categorias' => Categoria::where('estado', true)->orderBy('nombre')->get(),
            'busqueda' => $busqueda,
            'categoriaSel' => $categoria,
            'estado' => $estado,
        ]);
    }

    /**
     * Búsqueda JSON de productos activos con stock para el POS.
     */
    public function buscar(Request $request)
    {
        $busqueda = trim((string) $request->query('q', ''));

        $productos = Producto::query()
            ->with('categoria:id,nombre')
            ->where('estado', true)
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('codigo', 'like', "%{$busqueda}%");
                });
            })
            ->orderBy('nombre')
            ->limit(20)
            ->get()
            ->map(fn ($producto) => [
                'id' => $producto->id_producto,
                'codigo' => $producto->codigo,
                'nombre' => $producto->nombre,
                'categoria' => $producto->categoria?->nombre,
                'precio' => (float) $producto->precio,
                'stock' => $producto->stock,
                'imagen' => $producto->imagen,
                'agotado' => $producto->stock <= 0,
            ]);

        return response()->json($productos);
    }

    public function create()
    {
        $categorias = Categoria::where('estado', true)->orderBy('nombre')->get();

        return view('productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        $imagen = $this->guardarImagen($request);
        if ($imagen) {
            $datos['imagen'] = $imagen;
        }

        Producto::create($datos);

        return redirect()->route('productos.index')
            ->with('success', 'Producto registrado correctamente.');
    }
    public function edit(Producto $producto)
    {
        $categorias = Categoria::where('estado', true)->orderBy('nombre')->get();

        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $datos = $this->validar($request, $producto);

        $imagen = $this->guardarImagen($request, $producto);
        if ($imagen) {
            $datos['imagen'] = $imagen;
        }

        $producto->update($datos);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function toggle(Producto $producto)
    {
        $producto->update(['estado' => ! $producto->estado]);

        $mensaje = $producto->estado
            ? 'Producto activado correctamente.'
            : 'Producto desactivado correctamente.';

        return redirect()->back()->with('success', $mensaje);
    }

    /* ------------------------------------------------------------
     | Helpers privados
     ----------------------------------------------------------- */

    private function validar(Request $request, ?Producto $producto = null): array
    {
        $reglas = [
            'id_categoria' => ['required', 'integer', 'exists:categorias,id_categoria'],
            'codigo' => [
                'required', 'string', 'max:50',
                $producto
                    ? 'unique:productos,codigo,'.$producto->id_producto.',id_producto'
                    : 'unique:productos,codigo',
            ],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'imagen' => ['nullable', 'image', 'max:2048'],
            'estado' => ['nullable', 'boolean'],
        ];

        $mensajes = [
            'id_categoria.required' => 'Debes seleccionar una categoría.',
            'id_categoria.exists' => 'La categoría seleccionada no existe.',
            'codigo.required' => 'El código del producto es obligatorio.',
            'codigo.unique' => 'Ya existe un producto con ese código.',
            'codigo.max' => 'El código no puede superar los 50 caracteres.',
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 150 caracteres.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un valor numérico.',
            'precio.min' => 'El precio no puede ser negativo.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser negativo.',
            'stock_minimo.required' => 'El stock mínimo es obligatorio.',
            'stock_minimo.integer' => 'El stock mínimo debe ser un número entero.',
            'stock_minimo.min' => 'El stock mínimo no puede ser negativo.',
            'imagen.image' => 'El archivo debe ser una imagen válida.',
            'imagen.max' => 'La imagen no puede superar los 2 MB.',
        ];

        $atributos = [
            'id_categoria' => 'categoría',
            'codigo' => 'código',
            'precio' => 'precio',
            'stock_minimo' => 'stock mínimo',
            'imagen' => 'imagen',
        ];

        $datos = $request->validate($reglas, $mensajes, $atributos);

        $datos['estado'] = $request->boolean('estado');

        return $datos;
    }

    private function guardarImagen(Request $request, ?Producto $producto = null): ?string
    {
        if (! $request->hasFile('imagen') || ! $request->file('imagen')->isValid()) {
            return null;
        }

        $archivo = $request->file('imagen');
        $nombre = uniqid('prod_').'_'.preg_replace('/[^A-Za-z0-9._-]/', '', $archivo->getClientOriginalName());

        $directorio = public_path(self::RUTA_IMAGENES);
        if (! is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }

        // Se elimina la imagen anterior al reemplazarla.
        if ($producto && $producto->imagen) {
            $anterior = public_path(self::RUTA_IMAGENES.'/'.$producto->imagen);
            if (is_file($anterior)) {
                @unlink($anterior);
            }
        }

        $archivo->move($directorio, $nombre);

        return $nombre;
    }
}


