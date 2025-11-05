@extends('layout')

@section('content')
<h2>Relatório de Encomendas</h2>

@if($encomendas->isEmpty())
  <p>Nenhuma encomenda encontrada.</p>
@else
  @foreach ($encomendas as $e)
    @php
        $isBalcao  = !empty($e->endereco_cliente) || !empty($e->nome_cliente) || !empty($e->telefone_cliente);
        $nomeCli   = $e->nome_cliente ?: ($e->cliente->nome ?? '—');
        $telCli    = $isBalcao ? ($e->telefone_cliente ?: '—') : ($e->cliente->telefone ?? '—');
        $endCli    = $e->endereco_cliente ?: ($e->cliente->endereco ?? '—');
        $data      = \Illuminate\Support\Carbon::parse($e->data_encomenda ?? $e->data)->format('d/m/Y');
    @endphp

    <div style="border:1px solid #ccc; border-radius:8px; padding:10px; margin-bottom:15px;">
      <h3 style="margin-bottom:6px;">
        {{-- agora o número vem do SQL (sem buracos) --}}
        Encomenda Nº {{ $e->numero_relatorio }}
        @if($isBalcao)
          <span style="font-size:12px; font-weight:600; color:#555;">(Balcão)</span>
        @endif
      </h3>

      <p style="margin:2px 0;"><strong>Data:</strong> {{ $data }}</p>
      <p style="margin:2px 0;">
        <strong>Cliente:</strong> {{ $nomeCli }}
        @if($isBalcao)
          <span style="font-size:12px; font-weight:600; color:#555;">(Balcão)</span>
        @endif
        <br>
        <strong>Telefone:</strong> {{ $telCli }}<br>
        <strong>Endereço:</strong> {{ $endCli }}
      </p>

      @if($e->itens->isNotEmpty())
        <table style="width:100%; border-collapse:collapse; margin-top:10px;">
          <thead>
            <tr style="background:#eee;">
              <th style="text-align:left;  padding:6px;">Prato</th>
              <th style="text-align:right; padding:6px;">Qtd</th>
              <th style="text-align:right; padding:6px;">Preço (R$)</th>
              <th style="text-align:right; padding:6px;">Subtotal (R$)</th>
            </tr>
          </thead>
          <tbody>
            @php $total = 0; @endphp
            @foreach ($e->itens as $item)
              @php
                $q   = (float)($item->quantidade ?? 0);
                $pu  = (float)($item->preco_unitario ?? 0);
                $sub = $q * $pu;
                $total += $sub;
              @endphp
              <tr>
                <td style="padding:6px;">{{ $item->prato?->nome }}</td>
                <td style="text-align:right; padding:6px;">{{ number_format($q, 2, ',', '.') }}</td>
                <td style="text-align:right; padding:6px;">{{ number_format($pu, 2, ',', '.') }}</td>
                <td style="text-align:right; padding:6px;">{{ number_format($sub, 2, ',', '.') }}</td>
              </tr>
            @endforeach
            <tr style="font-weight:bold; background:#f6f6f6;">
              <td colspan="3" style="text-align:right; padding:6px;">Total:</td>
              <td style="text-align:right; padding:6px;">{{ number_format($total, 2, ',', '.') }}</td>
            </tr>
          </tbody>
        </table>
      @else
        <p style="margin-top:8px;">Nenhum prato vinculado a esta encomenda.</p>
      @endif
    </div>
  @endforeach
@endif

<a href="{{ route('encomendas.index') }}" class="btn btn-secondary" style="margin-top:10px;">Voltar</a>
@endsection
