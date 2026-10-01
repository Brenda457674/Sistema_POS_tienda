<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';

    protected $primaryKey = 'id_movimiento';

    // La tabla no tiene columnas created_at / updated_at (usa fecha_movimiento).
    public $timestamps = false;

    protected $fillable = [
        'id_producto',
        'id_usuario',
        'tipo',
        'cantidad',
        'stock_final',
        'motivo',
        'fecha_movimiento',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'stock_final' => 'integer',
            'fecha_movimiento' => 'datetime',
        ];
    }

    /* ------------------------------------------------------------
     | Relaciones
     ----------------------------------------------------------- */

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}
