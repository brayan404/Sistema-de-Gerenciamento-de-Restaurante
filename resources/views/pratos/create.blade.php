@extends('layout')

@section('content')
<h2>Novo Prato</h2>

<form action="{{ route('pratos.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <label>Nome:</label>
    <input type="text" name="nome" required>

    <label>Preço Unitário (R$):</label>
    <input type="number" step="0.01" name="preco_unitario" required>

    <label>Imagem:</label>
    <input type="file" name="imagem" accept="image/*">

    <button type="submit" class="btn btn-dark">Salvar</button>
    <a href="{{ route('pratos.index') }}" class="btn btn-secondary">Voltar</a>
</form>
@endsection
