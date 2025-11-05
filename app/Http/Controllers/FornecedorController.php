<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fornecedor;

class FornecedorController extends Controller
{
    public function index()
    {
        $fornecedores = Fornecedor::all();
        return view('fornecedores.index', compact('fornecedores'));
    }

    public function create()
    {
        return view('fornecedores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome'      => 'required|string|max:100',
            'cnpj'      => 'required|string|max:18',
            'endereco'  => 'nullable|string|max:150',
            'telefone'  => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:100',
        ]);

        Fornecedor::create($request->only(['nome','cnpj','endereco','telefone','email']));

        return redirect()->route('fornecedores.index')->with('success', 'Fornecedor cadastrado!');
    }

    public function edit(Fornecedor $fornecedor)
    {
        return view('fornecedores.edit', compact('fornecedor'));
    }

    public function update(Request $request, Fornecedor $fornecedor)
    {
        $request->validate([
            'nome'      => 'required|string|max:100',
            'cnpj'      => 'required|string|max:18',
            'endereco'  => 'nullable|string|max:150',
            'telefone'  => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:100',
        ]);

        $fornecedor->update($request->only(['nome','cnpj','endereco','telefone','email']));

        return redirect()->route('fornecedores.index')->with('success', 'Fornecedor atualizado!');
    }

    public function destroy(Fornecedor $fornecedor)
    {
        $fornecedor->delete();
        return redirect()->route('fornecedores.index')->with('success', 'Fornecedor excluído!');
    }
}
