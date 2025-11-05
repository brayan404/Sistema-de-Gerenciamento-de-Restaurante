<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prato;
use App\Models\Ingrediente;
use App\Models\Composicao;

class ComposicaoController extends Controller
{
    // Tela de composição por prato
    public function show(Prato $prato)
    {
        $ingredientes = Ingrediente::all();

        // custo total do prato com base no preço unitário de cada ingrediente
        $custoTotal = $prato->composicoes->sum(function ($c) {
            $preco = $c->ingrediente?->preco_unitario ?? 0;
            return $c->quantidade * $preco;
        });

        return view('pratos.composicao', compact('prato', 'ingredientes', 'custoTotal'));
    }

    // Adiciona um ingrediente à composição
    public function store(Request $request, Prato $prato)
    {
        $request->validate([
            'ingrediente_id' => 'required|exists:ingredientes,id',
            'quantidade'     => 'required|numeric|min:0.001',
        ]);

        Composicao::create([
            'prato_id'       => $prato->id,
            'ingrediente_id' => $request->ingrediente_id,
            'quantidade'     => $request->quantidade,
        ]);

        return redirect()->route('pratos.composicao', $prato->id)
            ->with('success', 'Ingrediente adicionado à composição!');
    }

    // Remove um item da composição
    public function destroy(Prato $prato, Composicao $composicao)
    {
        // garante que pertence ao prato
        if ($composicao->prato_id === $prato->id) {
            $composicao->delete();
        }

        return redirect()->route('pratos.composicao', $prato->id)
            ->with('success', 'Ingrediente removido da composição!');
    }
}
