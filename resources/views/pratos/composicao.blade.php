@extends('layout')

@section('content')
<h2>Composição do Prato: {{ $prato->nome }}</h2>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div style="margin:10px 0; padding:10px; border:1px solid #ddd; border-radius:8px;">
  <strong>Preço de venda:</strong>
  R$ {{ number_format($prato->preco_unitario, 2, ',', '.') }}
  <br>
  <strong>Custo de produção (ingredientes):</strong>
  R$ {{ number_format($custoTotal, 2, ',', '.') }}
  <br>
  <strong>Margem estimada:</strong>
  R$ {{ number_format(($prato->preco_unitario ?? 0) - $custoTotal, 2, ',', '.') }}
</div>

<hr style="margin:15px 0;">

<h3>Adicionar Ingrediente</h3>
<form action="{{ route('pratos.composicao.store', $prato->id) }}" method="POST">
    @csrf
    <label>Ingrediente:</label>
    <select name="ingrediente_id" required>
        <option value="">Selecione...</option>
        @foreach($ingredientes as $ing)
            <option value="{{ $ing->id }}">
                {{ $ing->nome }} ({{ $ing->unidade }})
                @if(!is_null($ing->preco_unitario))
                  — R$ {{ number_format($ing->preco_unitario, 2, ',', '.') }}/{{ $ing->unidade }}
                @endif
            </option>
        @endforeach
    </select>

    <label>Quantidade (na unidade do ingrediente):</label>
    <input type="number" name="quantidade" step="0.001" min="0.001" placeholder="Ex.: 0.250" required>

    <button type="submit" class="btn btn-dark">Adicionar</button>
    <a href="{{ route('pratos.index') }}" class="btn btn-secondary">Voltar</a>
</form>

<hr style="margin:15px 0;">

<h3>Ingredientes do Prato</h3>
<table>
  <thead>
    <tr>
      <th>Ingrediente</th>
      <th>Unidade</th>
      <th>Qtd</th>
      <th>Preço Unit. (R$)</th>
      <th>Subtotal (R$)</th>
      <th>Ações</th>
    </tr>
  </thead>
  <tbody>
    @forelse($prato->composicoes as $c)
      @php
        $preco = $c->ingrediente?->preco_unitario ?? 0;
        $sub   = $preco * $c->quantidade;
      @endphp
      <tr>
        <td>{{ $c->ingrediente?->nome }}</td>
        <td>{{ $c->ingrediente?->unidade }}</td>
        <td>{{ number_format($c->quantidade, 3, ',', '.') }}</td>
        <td>{{ number_format($preco, 2, ',', '.') }}</td>
        <td>{{ number_format($sub,   2, ',', '.') }}</td>
        <td>
          <form action="{{ route('pratos.composicao.destroy', [$prato->id, $c->id]) }}" method="POST" style="display:inline-block">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger" onclick="return confirm('Remover este ingrediente?')">Excluir</button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="6" style="text-align:center;">Nenhum ingrediente adicionado.</td></tr>
    @endforelse
  </tbody>
  <tfoot>
    <tr>
      <td colspan="4" style="text-align:right; font-weight:bold;">Custo total</td>
      <td style="font-weight:bold;">R$ {{ number_format($custoTotal, 2, ',', '.') }}</td>
      <td></td>
    </tr>
  </tfoot>
</table>
@endsection
