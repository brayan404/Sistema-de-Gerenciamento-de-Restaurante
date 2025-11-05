<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compras';
    protected $fillable = ['fornecedor_id', 'nota_fiscal', 'data_compra'];
    public $timestamps = false;

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class);
    }

    public function itens()
    {
        return $this->hasMany(ItensCompra::class, 'compra_id');
    }
}
