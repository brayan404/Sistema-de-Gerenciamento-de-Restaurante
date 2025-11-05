@extends('layout')

@section('content')
<h2>Novo Fornecedor</h2>

<form action="{{ route('fornecedores.store') }}" method="POST">
    @csrf

    <label>Nome:</label>
    <input type="text" name="nome" required>

    <label>CNPJ:</label>
    <input type="text" name="cnpj" placeholder="00.000.000/0000-00" required>

    <label>Endereço:</label>
    <input type="text" name="endereco" placeholder="Rua, número, bairro...">

    <label>Telefone:</label>
    <input type="text" name="telefone" placeholder="(XX) XXXX-XXXX">

    <label>Email:</label>
    <input type="email" name="email" placeholder="exemplo@email.com">

    <button type="submit" class="btn btn-dark">Salvar</button>
    <a href="{{ route('fornecedores.index') }}" class="btn btn-secondary">Voltar</a>
</form>
@endsection