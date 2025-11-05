@extends('layout')

@section('content')
<h2>Novo Cliente</h2>

@if ($errors->any())
  <div class="alert alert-danger">
    <ul style="margin:0;">
      @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('clientes.store') }}">
  @csrf

  <label>Nome</label>
  <input type="text" name="nome" value="{{ old('nome') }}" required>

  <label>Endereço</label>
  <input type="text" name="endereco" value="{{ old('endereco') }}">

  <label>Telefone</label>
  <input type="text" name="telefone" value="{{ old('telefone') }}">

  <label>Email</label>
  <input type="email" name="email" placeholder="exemplo@email.com" value="{{ old('email') }}" >

  <button class="btn btn-dark" type="submit">Salvar</button>
  <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Voltar</a>
</form>
@endsection
