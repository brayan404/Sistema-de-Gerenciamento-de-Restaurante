<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prato extends Model
{
    protected $table = 'pratos';
    protected $fillable = ['nome', 'preco_unitario', 'imagem'];
    public $timestamps = false;

    public function itens()
    {
        return $this->hasMany(ItemEncomenda::class);
    }

    public function composicoes()
    {
        return $this->hasMany(\App\Models\Composicao::class, 'prato_id');
    }
}
