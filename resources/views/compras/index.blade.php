@extends('layout')

@section('content')
<h2>Lista de Compras</h2>

<div style="margin:10px 0; display:flex; gap:8px; flex-wrap:wrap;">
    <a href="{{ route('compras.create') }}" class="btn btn-dark">Nova Compra</a>
    <a href="{{ route('relatorios.compras') }}" class="btn btn-secondary">Ver Relatório</a>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger">
    <ul style="margin:0;">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<table>
  <thead>
    <tr>
      <th style="width:15%;">Data</th>
      <th style="width:45%;">Fornecedor</th>
      <th style="width:20%;">Valor Total (R$)</th>
      <th style="width:20%;">Ações</th>
    </tr>
  </thead>
  <tbody>
    @forelse ($compras as $c)
      @php
        $total = $c->itens->sum(function($it){
          return (float)$it->quantidade * (float)$it->preco_unitario;
        });
      @endphp
      <tr>
        <td>{{ \Illuminate\Support\Carbon::parse($c->data_compra)->format('d/m/Y') }}</td>
        <td>{{ $c->fornecedor?->nome ?? '—' }}</td>
        <td>{{ number_format($total, 2, ',', '.') }}</td>
        <td>
          <form action="{{ route('compras.destroy', $c->id) }}" method="POST" style="display:inline-block"
                onsubmit="return confirm('Excluir esta compra? O estoque será revertido.');">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger">Excluir</button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="4" style="text-align:center;">Nenhuma compra registrada.</td></tr>
    @endforelse
  </tbody>
</table>
@endsection
