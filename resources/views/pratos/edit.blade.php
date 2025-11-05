@extends('layout')

@section('content')
<h2>Editar Prato</h2>

<form action="{{ route('pratos.update', $prato->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <label>Nome:</label>
    <input type="text" name="nome" value="{{ $prato->nome }}" required>

    <label>Preço Unitário (R$):</label>
    <input type="number" step="0.01" name="preco_unitario" value="{{ $prato->preco_unitario }}" required>

    <label>Imagem Atual:</label><br>
    @if($prato->imagem)
        <img src="{{ asset('img/pratos/' . $prato->imagem) }}" width="150" height="100" style="object-fit:cover; border-radius:6px;">
    @else
        <span class="text-muted">Sem imagem cadastrada</span>
    @endif
    <br><br>

    <label>Nova Imagem (opcional):</label>
    <input type="file" name="imagem" accept="image/*">

    <button type="submit" class="btn btn-dark">Atualizar</button>
    <a href="{{ route('pratos.index') }}" class="btn btn-secondary">Voltar</a>
</form>
@endsection
