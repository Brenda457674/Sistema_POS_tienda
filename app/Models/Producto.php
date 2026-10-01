<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    protected $primaryKey = 'id_producto';

    protected $fillable = [
        'id_categoria',
        'codigo',
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'stock_minimo',
        'imagen',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'id_categoria' => 'integer',
            'precio' => 'decimal:2',
            'stock' => 'integer',
            'stock_minimo' => 'integer',
            'estado' => 'boolean',
        ];
    }

    /* ------------------------------------------------------------
     | Relaciones
     ----------------------------------------------------------- */

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'id_producto');
    }

    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'id_producto');
    }

    /* ------------------------------------------------------------
     | Helpers de stock
     ----------------------------------------------------------- */

    public function sinStock(): bool
    {
        return $this->stock <= 0;
    }

    public function stockBajo(): bool
    {
        return $this->stock > 0 && $this->stock <= $this->stock_minimo;
    }
}
