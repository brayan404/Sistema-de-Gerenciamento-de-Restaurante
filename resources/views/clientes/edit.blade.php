@extends('layout')

@section('content')
<h2>Editar Cliente</h2>

@if ($errors->any())
  <div class="alert alert-danger">
    <ul style="margin:0;">
      @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('clientes.update', $cliente->id) }}">
  @csrf
  @method('PUT')

  <label>Nome</label>
  <input type="text" name="nome" value="{{ old('nome', $cliente->nome) }}" required>

  <label>Endereço</label>
  <input type="text" name="endereco" value="{{ old('endereco', $cliente->endereco) }}">

  <label>Telefone</label>
  <input type="text" name="telefone" value="{{ old('telefone', $cliente->telefone) }}">

  <label>Email</label>  {{-- ✅ novo --}}
  <input type="email" name="email" value="{{ old('email', $cliente->email) }}">

  <button class="btn btn-dark" type="submit">Salvar</button>
  <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Voltar</a>
</form>
@endsection
