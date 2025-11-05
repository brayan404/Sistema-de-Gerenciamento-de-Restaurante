@extends('layout')

@section('content')
<h2>Editar Ingrediente</h2>

@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif
@if($errors->any())
  <div class="alert alert-danger">
    <ul style="margin:0;">
      @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('ingredientes.update', $ingrediente->id) }}">
  @csrf
  @method('PUT')

  <label>Nome</label>
  <input type="text" name="nome" value="{{ old('nome', $ingrediente->nome) }}" required>

  <label>Unidade (ex.: kg, L, un)</label>
  <input type="text" name="unidade" value="{{ old('unidade', $ingrediente->unidade) }}" required>

  <label>Estoque</label>
  <input type="number" step="0.001" min="0" name="estoque" value="{{ old('estoque', $ingrediente->estoque) }}">

  <div class="alert alert-info" style="margin-top:10px;">
    <strong>Preço de referência:</strong>
    {{ number_format($ingrediente->preco_unitario, 2, ',', '.') }} (atualizado automaticamente pelas compras)
  </div>

  <button class="btn btn-dark" type="submit">Salvar</button>
  <a class="btn btn-secondary" href="{{ route('ingredientes.index') }}">Voltar</a>
</form>
@endsection
