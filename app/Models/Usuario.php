<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';

    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nombre',
        'usuario',
        'email',
        'password',
        'rol',
        'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    /* ------------------------------------------------------------
     | Relaciones
     ----------------------------------------------------------- */

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_usuario');
    }

    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'id_usuario');
    }

    /* ------------------------------------------------------------
     | Helpers
     ----------------------------------------------------------- */

    public function esAdministrador(): bool
    {
        return $this->rol === 'Administrador';
    }

    public function esVendedor(): bool
    {
        return $this->rol === 'Vendedor';
    }

    public function esAlmacenero(): bool
    {
        return $this->rol === 'Almacenero';
    }

    /**
     * Ruta a la que se redirige al usuario tras iniciar sesión.
     */
    public function rutaInicio(): string
    {
        if ($this->esAdministrador()) {
            return '/dashboard';
        }

        if ($this->esVendedor()) {
            return '/pos';
        }

        return '/inventario';
    }

    public function iniciales(): string
    {
        $partes = preg_split('/\s+/', trim($this->nombre));

        $iniciales = strtoupper(substr($partes[0] ?? 'U', 0, 1));

        if (count($partes) > 1) {
            $iniciales .= strtoupper(substr(end($partes), 0, 1));
        }

        return $iniciales;
    }
}
