@extends('layout')

@section('content')
<h2>Novo Ingrediente</h2>

@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif
@if($errors->any())
  <div class="alert alert-danger">
    <ul style="margin:0;">
      @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('ingredientes.store') }}">
  @csrf

  <label>Nome</label>
  <input type="text" name="nome" value="{{ old('nome') }}" required>

  <label>Unidade (ex.: kg, L, un)</label>
  <input type="text" name="unidade" value="{{ old('unidade') }}" required>

  <label>Estoque inicial (opcional)</label>
  <input type="number" step="0.001" min="0" name="estoque" value="{{ old('estoque') }}">

  <button class="btn btn-dark" type="submit">Salvar</button>
  <a href="{{ route('ingredientes.index') }}" class="btn btn-secondary">Voltar</a>
</form>
@endsection
