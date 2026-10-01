<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Mostrar el formulario de inicio de sesión.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect(Auth::user()->rutaInicio());
        }

        return view('auth.login');
    }

    /**
     * Autenticar al usuario (usuario + contraseña).
     */
    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'usuario' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ], [], [
            'usuario' => 'usuario',
            'password' => 'contraseña',
        ]);

        $usuario = Usuario::where('usuario', $credenciales['usuario'])->first();

        if (! $usuario) {
            return back()
                ->withInput($request->only('usuario'))
                ->withErrors(['usuario' => 'El usuario no existe.']);
        }

        if (! $usuario->estado) {
            return back()
                ->withInput($request->only('usuario'))
                ->withErrors(['usuario' => 'Tu usuario está desactivado. Contacta al administrador.']);
        }

        if (! Auth::attempt($credenciales)) {
            return back()
                ->withInput($request->only('usuario'))
                ->withErrors(['password' => 'La contraseña es incorrecta.']);
        }

        $request->session()->regenerate();

        return redirect()->intended($usuario->rutaInicio());
    }

    /**
     * Cerrar sesión.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
