<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Encomenda; // ✅ para checar vínculos

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::orderBy('nome')->get();
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome'     => 'required|string|max:100',
            'endereco' => 'nullable|string|max:150',
            'telefone' => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:100',
        ]);

        Cliente::create($request->only(['nome','endereco','telefone','email']));

        return redirect()->route('clientes.index')->with('success','Cliente cadastrado!');
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nome'     => 'required|string|max:100',
            'endereco' => 'nullable|string|max:150',
            'telefone' => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:100',
        ]);

        $cliente->update($request->only(['nome','endereco','telefone','email']));

        return redirect()->route('clientes.index')->with('success','Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        try {
            // 🔒 1) Não permitir excluir o “Cliente de Balcão”
            if (strcasecmp($cliente->nome, 'Cliente de Balcão') === 0) {
                return redirect()->route('clientes.index')
                    ->with('error', 'O cliente padrão de balcão não pode ser excluído.');
            }

            // 🔒 2) Checar se há encomendas vinculadas (mensagem amigável)
            $temEncomendas = Encomenda::where('cliente_id', $cliente->id)->exists();
            if ($temEncomendas) {
                return redirect()->route('clientes.index')
                    ->with('error', 'Não é possível excluir este cliente porque ele possui encomendas registradas.');
            }

            // ✅ 3) Pode excluir
            $cliente->delete();

            return redirect()->route('clientes.index')
                ->with('success', 'Cliente excluído com sucesso!');

        } catch (\Illuminate\Database\QueryException $e) {
            // FK ou outros erros SQL
            if ($e->getCode() == '23000') {
                return redirect()->route('clientes.index')
                    ->with('error', 'Não é possível excluir este cliente porque ele possui encomendas registradas.');
            }
            return redirect()->route('clientes.index')
                ->with('error', 'Erro ao excluir cliente.');
        } catch (\Exception $e) {
            return redirect()->route('clientes.index')
                ->with('error', 'Ocorreu um erro inesperado ao excluir o cliente.');
        }
    }
}