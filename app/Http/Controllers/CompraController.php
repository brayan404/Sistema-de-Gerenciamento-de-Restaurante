<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Compra;
use App\Models\Fornecedor;
use App\Models\Ingrediente;
use App\Models\ItensCompra;

class CompraController extends Controller
{
    public function index()
    {
        $compras = Compra::with(['fornecedor', 'itens.ingrediente'])
                    ->orderBy('data_compra','desc')
                    ->get();

        return view('compras.index', compact('compras'));
    }

    public function create()
    {
        $fornecedores = Fornecedor::all();
        $ingredientes = Ingrediente::all();
        return view('compras.create', compact('fornecedores', 'ingredientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fornecedor_id'            => 'required|exists:fornecedores,id',
            'data_compra'              => 'required|date',
            'nota_fiscal'              => 'nullable|string|max:50',
            'itens'                    => 'required|array|min:1',
            'itens.*.ingrediente_id'   => 'required|exists:ingredientes,id',
            'itens.*.quantidade'       => 'required|numeric|min:0.001',
            'itens.*.preco_unitario'   => 'required|numeric|min:0',
        ]);

        DB::transaction(function() use ($request) {
            $compra = Compra::create([
                'fornecedor_id' => (int)$request->fornecedor_id,
                'data_compra'   => $request->data_compra,
                'nota_fiscal'   => $request->nota_fiscal,
            ]);

            foreach ($request->itens as $it) {
                ItensCompra::create([
                    'compra_id'      => $compra->id,
                    'ingrediente_id' => (int)$it['ingrediente_id'],
                    'quantidade'     => (float)$it['quantidade'],
                    'preco_unitario' => (float)$it['preco_unitario'],
                ]);

                Ingrediente::where('id', (int)$it['ingrediente_id'])->update([
                    'estoque'        => DB::raw('estoque + ' . (float)$it['quantidade']),
                    'preco_unitario' => (float)$it['preco_unitario'],
                ]);

            }
        });

        return redirect()->route('compras.index')->with('success', 'Compra registrada com múltiplos itens e estoque atualizado!');
    }
    
    public function destroy(Compra $compra)
    {
        DB::transaction(function() use ($compra) {
            $compra->load('itens');

            foreach ($compra->itens as $item) {
                Ingrediente::where('id', $item->ingrediente_id)
                    ->update(['estoque' => DB::raw('estoque - ' . (float)$item->quantidade)]);
            }

            $compra->delete();
        });

        return redirect()->route('compras.index')->with('success', 'Compra cancelada e estoque revertido.');
    }

    public function relatorio()
    {
        $compras = \App\Models\Compra::with(['fornecedor', 'itens.ingrediente'])
            ->orderByDesc('data_compra')
            ->get();

        return view('relatorios.compras', compact('compras'));
    }

}
