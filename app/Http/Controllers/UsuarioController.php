<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->query('q');
        $rol = $request->query('rol');

        $usuarios = Usuario::query()
            ->when($busqueda, function ($query, $busqueda) {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('usuario', 'like', "%{$busqueda}%")
                        ->orWhere('email', 'like', "%{$busqueda}%");
                });
            })
            ->when($rol, fn ($query, $rol) => $query->where('rol', $rol))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('usuarios.index', compact('usuarios', 'busqueda', 'rol'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'usuario' => ['required', 'string', 'max:50', 'unique:usuarios,usuario'],
            'email' => ['required', 'email', 'max:150', 'unique:usuarios,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'rol' => ['required', Rule::in(['Administrador', 'Vendedor', 'Almacenero'])],
            'estado' => ['nullable', 'boolean'],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 100 caracteres.',
            'usuario.required' => 'El usuario es obligatorio.',
            'usuario.unique' => 'Ese nombre de usuario ya está en uso.',
            'usuario.max' => 'El usuario no puede superar los 50 caracteres.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email no tiene un formato válido.',
            'email.unique' => 'Ese email ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'rol.required' => 'Debes seleccionar un rol.',
            'rol.in' => 'El rol seleccionado no es válido.',
        ], [
            'nombre' => 'nombre',
            'usuario' => 'usuario',
            'email' => 'email',
            'password' => 'contraseña',
            'rol' => 'rol',
        ]);

        $datos['password'] = Hash::make($datos['password']);
        $datos['estado'] = $request->boolean('estado');

        Usuario::create($datos);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario registrado correctamente.');
    }

    public function edit(Usuario $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'usuario' => [
                'required', 'string', 'max:50',
                Rule::unique('usuarios', 'usuario')->ignore($usuario->id_usuario, 'id_usuario'),
            ],
            'email' => [
                'required', 'email', 'max:150',
                Rule::unique('usuarios', 'email')->ignore($usuario->id_usuario, 'id_usuario'),
            ],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'rol' => ['required', Rule::in(['Administrador', 'Vendedor', 'Almacenero'])],
            'estado' => ['nullable', 'boolean'],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'usuario.required' => 'El usuario es obligatorio.',
            'usuario.unique' => 'Ese nombre de usuario ya está en uso.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email no tiene un formato válido.',
            'email.unique' => 'Ese email ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'rol.required' => 'Debes seleccionar un rol.',
            'rol.in' => 'El rol seleccionado no es válido.',
        ], [
            'nombre' => 'nombre',
            'usuario' => 'usuario',
            'email' => 'email',
            'password' => 'contraseña',
            'rol' => 'rol',
        ]);

        if (! empty($datos['password'])) {
            $datos['password'] = Hash::make($datos['password']);
        } else {
            unset($datos['password']);
        }

        $datos['estado'] = $request->boolean('estado');

        $usuario->update($datos);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function toggle(Usuario $usuario)
    {
        if ($usuario->id_usuario === auth()->id() && $usuario->estado) {
            return redirect()->back()
                ->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $usuario->update(['estado' => ! $usuario->estado]);

        $mensaje = $usuario->estado
            ? 'Usuario activado correctamente.'
            : 'Usuario desactivado correctamente.';

        return redirect()->back()->with('success', $mensaje);
    }
}
