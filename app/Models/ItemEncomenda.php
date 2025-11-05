<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemEncomenda extends Model
{
    protected $table = 'itens_encomenda';
    protected $fillable = ['encomenda_id', 'prato_id', 'quantidade', 'preco_unitario'];
    public $timestamps = false;

    public function prato()
    {
        return $this->belongsTo(Prato::class);
    }

    public function encomenda()
    {
        return $this->belongsTo(Encomenda::class);
    }
}
