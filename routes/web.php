<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PratoController;
use App\Http\Controllers\IngredienteController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\EncomendaController;
use App\Http\Controllers\ComposicaoController;

Route::get('/', function () {
    return redirect()->route('clientes.index');
});

Route::resource('clientes', ClienteController::class);
Route::resource('pratos', PratoController::class);
Route::resource('ingredientes', IngredienteController::class);
Route::resource('fornecedores', FornecedorController::class)
     ->parameters(['fornecedores' => 'fornecedor']);

Route::resource('compras', CompraController::class);
Route::resource('encomendas', EncomendaController::class);
Route::get('pratos/{prato}/composicao', [ComposicaoController::class, 'show'])->name('pratos.composicao');
Route::post('pratos/{prato}/composicao', [ComposicaoController::class, 'store'])->name('pratos.composicao.store');
Route::delete('pratos/{prato}/composicao/{composicao}', [ComposicaoController::class, 'destroy'])->name('pratos.composicao.destroy');

Route::get('relatorios/encomendas', [EncomendaController::class, 'relatorio'])->name('relatorios.encomendas');
Route::get('relatorios/compras', [CompraController::class, 'relatorio'])->name('relatorios.compras');
