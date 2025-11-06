@extends('layout')

@section('content')
<h2>Lista de Encomendas</h2>

<div class="actions-bar" style="margin:8px 0; display:flex; gap:8px; flex-wrap:wrap;">
    <a href="{{ route('encomendas.create') }}" class="btn btn-dark">Nova Encomenda</a>
    <a href="{{ route('relatorios.encomendas') }}" class="btn btn-secondary">Ver Relatório</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Data</th>
            <th>Valor Total (R$)</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @php $totalGeral = 0; @endphp

        @forelse ($encomendas as $e)
            @php
                // Nome para exibir: cliente real -> nome_cliente -> "Cliente de Balcão"
                $displayNome = $e->cliente->nome
                    ?? ($e->nome_cliente ?: 'Cliente de Balcão');

                // É balcão se NÃO há cliente relacionado
                $isBalcao = is_null($e->cliente_id);

                // Total da encomenda
                $total = $e->itens->sum(fn($item) => ($item->quantidade ?? 0) * ($item->preco_unitario ?? 0));
                $totalGeral += $total;
            @endphp
            <tr>
                <td>
                    @if($isBalcao)
                        <span style="color:#888;">{{ $displayNome }}</span>
                        <span style="
                            margin-left:6px; padding:2px 6px; font-size:12px;
                            background:#eee; color:#555; border:1px solid #ddd; border-radius:4px;
                        ">balcão</span>
                    @else
                        {{ $displayNome }}
                    @endif
                </td>
                <td>{{ \Illuminate\Support\Carbon::parse($e->data_encomenda)->format('d/m/Y') }}</td>
                <td style="text-align:right;">{{ number_format($total, 2, ',', '.') }}</td>
                <td>
                    <form action="{{ route('encomendas.destroy', $e->id) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" onclick="return confirm('Excluir encomenda?')">Excluir</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" style="text-align:center;">Nenhuma encomenda registrada.</td></tr>
        @endforelse
    </tbody>

    @if($encomendas->isNotEmpty())
        <tfoot>
            <tr style="font-weight:bold; background:#f9f9f9;">
                <td colspan="2" style="text-align:right;">Total Geral:</td>
                <td style="text-align:right;">{{ number_format($totalGeral, 2, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    @endif
</table>
@endsection
