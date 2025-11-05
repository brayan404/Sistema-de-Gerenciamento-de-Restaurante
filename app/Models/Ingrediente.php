<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingrediente extends Model
{
    protected $table = 'ingredientes';
    protected $fillable = ['nome','unidade','estoque'];
    public $timestamps = false;

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }
}
