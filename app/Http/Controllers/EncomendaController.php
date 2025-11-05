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
    // ====== LISTA ======
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

    // ====== FORM NOVA ======
    public function create()
    {
        $clientes = Cliente::whereRaw('LOWER(nome) <> LOWER(?)', ['Cliente de Balcão'])
            ->orderBy('nome')
            ->get();

        $pratos = Prato::orderBy('nome')->get();

        return view('encomendas.create', compact('clientes','pratos'));
    }

    // ====== UTIL: consolida consumo por ingrediente ======
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

    // ====== SALVAR ======
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

        $clienteId = $request->cliente_id ?: $this->getClienteBalcaoId();

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

        // ====== Persistência + baixa no estoque ======
        DB::transaction(function () use ($request, $consumo, $clienteId) {
            if (empty($request->cliente_id)) {
                $clienteBalcao = Cliente::find($clienteId);
                $atualizacoes = [];

                if ($request->filled('endereco_cliente')) {
                    $atualizacoes['endereco'] = $request->endereco_cliente;
                }
                if ($request->filled('telefone_cliente')) {
                    $atualizacoes['telefone'] = $request->telefone_cliente;
                }

                if (!empty($atualizacoes)) {
                    $clienteBalcao->update($atualizacoes);
                }
            }

            $encomenda = Encomenda::create([
                'cliente_id'     => $clienteId,
                'data_encomenda' => $request->data_encomenda,
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

    // ====== CLIENTE DE BALCÃO ======
    private function getClienteBalcaoId(): int
    {
        $balcao = Cliente::firstOrCreate(
            ['nome' => 'Cliente de Balcão'],
            ['endereco' => 'Não informado', 'telefone' => null, 'email' => null]
        );

        return (int) $balcao->id;
    }

    // ====== EXCLUIR ======
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

            $encomenda->delete(); // ON DELETE CASCADE
        });

        return redirect()->route('encomendas.index')->with('success', 'Encomenda cancelada e estoque revertido.');
    }

    // ====== RELATÓRIO ======
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
