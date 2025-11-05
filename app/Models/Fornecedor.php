<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fornecedor extends Model
{
    protected $table = 'fornecedores';
    protected $fillable = ['nome', 'cnpj', 'endereco', 'telefone', 'email'];
    public $timestamps = false;

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }
}