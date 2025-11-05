<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Composicao extends Model
{
    protected $table = 'composicao';
    protected $fillable = ['prato_id', 'ingrediente_id', 'quantidade'];
    public $timestamps = false;

    public function prato()
    {
        return $this->belongsTo(Prato::class);
    }

    public function ingrediente()
    {
        return $this->belongsTo(Ingrediente::class);
    }
}
