<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $primaryKey = 'id_cliente';

    protected $fillable = [
        'nombre',
        'documento',
        'telefono',
        'email',
        'direccion',
        'estado',
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
        return $this->hasMany(Venta::class, 'id_cliente');
    }
}
