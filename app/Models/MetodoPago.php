<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    protected $table = 'metodos_pago';

    protected $primaryKey = 'id_metodo_pago';

    // La tabla no tiene columnas created_at / updated_at.
    public $timestamps = false;

    protected $fillable = [
        'nombre',
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
        return $this->hasMany(Venta::class, 'id_metodo_pago');
    }
}
