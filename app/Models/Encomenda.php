<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encomenda extends Model
{
    protected $table = 'encomendas';
    protected $fillable = ['cliente_id', 'data_encomenda', 'nome_cliente', 'endereco_cliente', 'telefone_cliente'];
    public $timestamps = false;

    public function cliente()
    {
        return $this->belongsTo(\App\Models\Cliente::class); 
    }
    
    public function itens()
    {
        return $this->hasMany(\App\Models\ItemEncomenda::class, 'encomenda_id');
    }
}
