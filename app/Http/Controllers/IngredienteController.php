<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingrediente;

class IngredienteController extends Controller
{
    public function index()
    {
        $ingredientes = Ingrediente::orderBy('nome')->get();
        return view('ingredientes.index', compact('ingredientes'));
    }

    public function create()
    {
        return view('ingredientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome'    => 'required|string|max:100',
            'unidade' => 'required|string|max:20',
            // estoque opcional (se quiser permitir cadastrar um valor inicial)
            'estoque' => 'nullable|numeric|min:0',
        ]);

        $dados = $request->only(['nome','unidade']);
        if ($request->filled('estoque')) {
            $dados['estoque'] = (float)$request->estoque;
        }

        // NUNCA receber preco_unitario aqui (preço é atualizado pelas compras)
        Ingrediente::create($dados);

        return redirect()->route('ingredientes.index')->with('success', 'Ingrediente cadastrado!');
    }

    public function edit(Ingrediente $ingrediente)
    {
        return view('ingredientes.edit', compact('ingrediente'));
    }

    public function update(Request $request, Ingrediente $ingrediente)
    {
        $request->validate([
            'nome'    => 'required|string|max:100',
            'unidade' => 'required|string|max:20',
            'estoque' => 'nullable|numeric|min:0',
        ]);

        $dados = $request->only(['nome','unidade']);
        if ($request->filled('estoque')) {
            $dados['estoque'] = (float)$request->estoque;
        }

        // NÃO aceitar preco_unitario via edição
        $ingrediente->update($dados);

        return redirect()->route('ingredientes.index')->with('success', 'Ingrediente atualizado!');
    }

    public function destroy(Ingrediente $ingrediente)
    {
        $ingrediente->delete();
        return redirect()->route('ingredientes.index')->with('success', 'Ingrediente excluído!');
    }
}
