@extends('layout')

@section('content')
<h2>Relatório de Compras</h2>

@if($compras->isEmpty())
  <p style="margin-top:10px;">Nenhuma compra encontrada.</p>
@else
  <table>
    <thead>
      <tr>
        <th style="width:12%;">Data</th>
        <th style="width:28%;">Fornecedor</th>
        <th style="width:20%;">Nota Fiscal</th>
        <th style="width:30%;">Itens (qtd × vlr unit = subtotal)</th>
        <th style="width:10%;">Total (R$)</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($compras as $c)
        @php
          $total = 0;
          $linhas = [];
          foreach ($c->itens as $it) {
              $qtd = (float)($it->quantidade ?? 0);
              $vu  = (float)($it->preco_unitario ?? 0);
              $sub = $qtd * $vu;
              $total += $sub;

              $nomeIng = $it->ingrediente->nome ?? '—';
              $unid    = $it->ingrediente->unidade ?? '';
              $linhas[] = sprintf(
                  '%s — %s %s × %s = %s',
                  $nomeIng,
                  number_format($qtd, 3, ',', '.'),
                  $unid,
                  number_format($vu, 2, ',', '.'),
                  number_format($sub, 2, ',', '.')
              );
          }
        @endphp
        <tr>
          <td>{{ \Illuminate\Support\Carbon::parse($c->data_compra)->format('d/m/Y') }}</td>
          <td>{{ $c->fornecedor?->nome ?? '—' }}</td>
          <td>{{ $c->nota_fiscal ?? '—' }}</td>
          <td>
            {{-- Todos os itens em uma única célula, um por linha --}}
            @foreach($linhas as $l)
              <div>{{ $l }}</div>
            @endforeach
          </td>
          <td><strong>{{ number_format($total, 2, ',', '.') }}</strong></td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endif

<a href="{{ route('compras.index') }}" class="btn btn-secondary" style="margin-top:10px;">Voltar</a>
@endsection