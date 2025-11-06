<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Encomenda;
use App\Models\ItemEncomenda;
use App\Models\Cliente;
use App\Models\Prato;
use App\Models\Ingrediente;

class EncomendaController extends Controller
{
    public function index()
    {
        $encomendas = Encomenda::query()
            ->select([
                'encomendas.*',
                DB::raw('ROW_NUMBER() OVER (ORDER BY data_encomenda DESC, id DESC) AS numero_relatorio'),
            ])
            ->with(['cliente', 'itens.prato'])
            ->orderByDesc('data_encomenda')
            ->orderByDesc('id')
            ->get();

        return view('encomendas.index', compact('encomendas'));
    }

    public function create()
    {
        $clientes = Cliente::whereRaw('LOWER(nome) <> LOWER(?)', ['Cliente de Balcão'])
            ->orderBy('nome')
            ->get();

        $pratos = Prato::orderBy('nome')->get();

        return view('encomendas.create', compact('clientes','pratos'));
    }

    private function consumoPorIngrediente(array $itens): array
    {
        $consumo = [];
        foreach ($itens as $it) {
            $prato = Prato::with('composicoes')->findOrFail($it['prato_id']);
            foreach ($prato->composicoes as $c) {
                $qtd = (float)$it['quantidade'] * (float)$c->quantidade;
                $consumo[$c->ingrediente_id] = ($consumo[$c->ingrediente_id] ?? 0) + $qtd;
            }
        }
        return $consumo;
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id'             => 'nullable|exists:clientes,id',
            'data_encomenda'         => 'required|date',
            'itens'                  => 'required|array|min:1',
            'itens.*.prato_id'       => 'required|exists:pratos,id',
            'itens.*.quantidade'     => 'required|numeric|min:1',
            'itens.*.preco_unitario' => 'required|numeric|min:0',
            'nome_cliente'           => 'nullable|string|max:100',
            'endereco_cliente'       => 'nullable|string|max:150',
            'telefone_cliente'       => 'nullable|string|max:20',
        ]);

        if (!$request->filled('cliente_id')) {
            $request->validate([
                'endereco_cliente' => 'required|string|max:150',
            ]);
        }

        $consumo = $this->consumoPorIngrediente($request->itens);

        $faltas = [];
        foreach ($consumo as $ingredienteId => $qtdNecessaria) {
            $ing = Ingrediente::findOrFail($ingredienteId);
            if ($ing->estoque < $qtdNecessaria) {
                $faltas[] = "Ingrediente **{$ing->nome}** — necessário: "
                          . number_format($qtdNecessaria, 3, ',', '.')." {$ing->unidade}; "
                          . "disponível: ".number_format($ing->estoque, 3, ',', '.')." {$ing->unidade}";
            }
        }

        if (!empty($faltas)) {
            return back()
                ->with('error', 'Estoque insuficiente para alguns ingredientes:')
                ->withErrors($faltas)
                ->withInput();
        }

        DB::transaction(function () use ($request, $consumo) {

            $temCliente = $request->filled('cliente_id');

            $encomenda = Encomenda::create([
                'cliente_id'       => $temCliente ? $request->cliente_id : null,
                'data_encomenda'   => $request->data_encomenda,
                'nome_cliente'     => $temCliente ? null : ($request->nome_cliente ?: null),
                'endereco_cliente' => $temCliente ? null : $request->endereco_cliente,
                'telefone_cliente' => $temCliente ? null : ($request->telefone_cliente ?: null),
            ]);

            foreach ($request->itens as $it) {
                ItemEncomenda::create([
                    'encomenda_id'   => $encomenda->id,
                    'prato_id'       => $it['prato_id'],
                    'quantidade'     => $it['quantidade'],
                    'preco_unitario' => $it['preco_unitario'],
                ]);
            }

            foreach ($consumo as $ingredienteId => $qtd) {
                Ingrediente::where('id', $ingredienteId)
                    ->update(['estoque' => DB::raw('estoque - ' . (float)$qtd)]);
            }
        });

        return redirect()->route('encomendas.index')
            ->with('success', 'Encomenda registrada e estoque atualizado!');
    }

    public function destroy(Encomenda $encomenda)
    {
        DB::transaction(function () use ($encomenda) {
            $encomenda->load('itens.prato.composicoes');

            $itens = $encomenda->itens->map(fn($i) => [
                'prato_id'   => $i->prato_id,
                'quantidade' => $i->quantidade,
            ])->all();

            $consumo = $this->consumoPorIngrediente($itens);

            foreach ($consumo as $ingredienteId => $qtd) {
                Ingrediente::where('id', $ingredienteId)
                    ->update(['estoque' => DB::raw('estoque + ' . (float)$qtd)]);
            }

            $encomenda->delete();
        });

        return redirect()->route('encomendas.index')->with('success', 'Encomenda cancelada e estoque revertido.');
    }

    public function relatorio()
    {
        $encomendas = Encomenda::query()
            ->select([
                'encomendas.*',
                DB::raw('ROW_NUMBER() OVER (ORDER BY data_encomenda DESC, id DESC) AS numero_relatorio'),
            ])
            ->with(['cliente','itens.prato'])
            ->orderByDesc('data_encomenda')
            ->orderByDesc('id')
            ->get();

        return view('relatorios.encomendas', compact('encomendas'));
    }
}
