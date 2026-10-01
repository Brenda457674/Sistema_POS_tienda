<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->query('q');

        $clientes = Cliente::query()
            ->when($busqueda, function ($query, $busqueda) {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('documento', 'like', "%{$busqueda}%")
                        ->orWhere('telefono', 'like', "%{$busqueda}%")
                        ->orWhere('email', 'like', "%{$busqueda}%");
                });
            })
            ->withCount('ventas')
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('clientes.index', compact('clientes', 'busqueda'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        Cliente::create($datos);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente registrado correctamente.');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load('ventas.metodoPago');

        $ventas = Venta::with(['usuario', 'metodoPago', 'detalles.producto'])
            ->where('id_cliente', $cliente->id_cliente)
            ->orderByDesc('fecha_venta')
            ->paginate(10);

        $totalComprado = $cliente->ventas()->sum('total');

        return view('clientes.show', compact('cliente', 'ventas', 'totalComprado'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $datos = $this->validar($request, $cliente);

        $cliente->update($datos);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function toggle(Cliente $cliente)
    {
        $cliente->update(['estado' => ! $cliente->estado]);

        $mensaje = $cliente->estado
            ? 'Cliente activado correctamente.'
            : 'Cliente desactivado correctamente.';

        return redirect()->back()->with('success', $mensaje);
    }

    private function validar(Request $request, ?Cliente $cliente = null): array
    {
        $reglas = [
            'nombre' => ['required', 'string', 'max:150'],
            'documento' => [
                'required', 'string', 'max:20',
                'regex:/^[0-9A-Za-z.-]+$/',
                $cliente
                    ? 'unique:clientes,documento,'.$cliente->id_cliente.',id_cliente'
                    : 'unique:clientes,documento',
            ],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'boolean'],
        ];

        $mensajes = [
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 150 caracteres.',
            'documento.required' => 'El documento del cliente es obligatorio.',
            'documento.unique' => 'Ya existe un cliente con ese documento.',
            'documento.regex' => 'El documento solo puede contener letras, números, puntos o guiones.',
            'documento.max' => 'El documento no puede superar los 20 caracteres.',
            'telefono.max' => 'El teléfono no puede superar los 20 caracteres.',
            'email.email' => 'El email no tiene un formato válido.',
            'email.max' => 'El email no puede superar los 150 caracteres.',
            'direccion.max' => 'La dirección no puede superar los 255 caracteres.',
        ];

        $atributos = [
            'documento' => 'documento',
            'telefono' => 'teléfono',
            'direccion' => 'dirección',
        ];

        $datos = $request->validate($reglas, $mensajes, $atributos);

        // Si el email está vacío y la BD lo marca como único, guardamos null.
        if (array_key_exists('email', $datos) && ($datos['email'] === '' || $datos['email'] === null)) {
            $datos['email'] = null;
        }

        $datos['estado'] = $request->boolean('estado');

        return $datos;
    }
}
