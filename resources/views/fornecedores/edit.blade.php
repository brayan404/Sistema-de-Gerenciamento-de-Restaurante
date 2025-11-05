@extends('layout')

@section('content')
<h2>Editar Fornecedor</h2>

<form action="{{ route('fornecedores.update', $fornecedor->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Nome:</label>
    <input type="text" name="nome" value="{{ $fornecedor->nome }}" required>

    <label>CNPJ:</label>
    <input type="text" name="cnpj" value="{{ $fornecedor->cnpj }}" required>

    <label>Endereço:</label>
    <input type="text" name="endereco" value="{{ $fornecedor->endereco }}">

    <label>Telefone:</label>
    <input type="text" name="telefone" value="{{ $fornecedor->telefone }}">

    <label>Email:</label>
    <input type="email" name="email" value="{{ $fornecedor->email }}">

    <button type="submit" class="btn btn-dark">Atualizar</button>
    <a href="{{ route('fornecedores.index') }}" class="btn btn-secondary">Voltar</a>
</form>
@endsection