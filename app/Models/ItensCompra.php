<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItensCompra extends Model
{
    protected $table = 'itens_compra';
    public $timestamps = false;
    protected $fillable = ['compra_id','ingrediente_id','quantidade','preco_unitario'];

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }

    public function ingrediente()
    {
        return $this->belongsTo(Ingrediente::class, 'ingrediente_id');
    }
}
