@extends('layout')

@section('content')
<h2>Lista de Ingredientes</h2>

<div style="margin:10px 0;">
    <a href="{{ route('ingredientes.create') }}" class="btn btn-dark">Novo Ingrediente</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table>
    <thead>
        <tr>
            <th>Nome</th>
            <th>Estoque</th>
            <th>Preço Unit. (R$)</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($ingredientes as $ingrediente)
            @php
                $baixo = ($ingrediente->estoque ?? 0) < 0.100;
            @endphp
            <tr>
                <td>{{ $ingrediente->nome }}</td>
                <td class="{{ $baixo ? 'estoque-baixo' : '' }}">
                    {{ number_format($ingrediente->estoque, 3, ',', '.') }}
                    @if(!empty($ingrediente->unidade))
                        {{ $ingrediente->unidade }}
                    @endif
                </td>
                <td>{{ number_format($ingrediente->preco_unitario ?? 0, 2, ',', '.') }}</td>
                <td>
                    <a href="{{ route('ingredientes.edit', $ingrediente->id) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('ingredientes.destroy', $ingrediente->id) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" onclick="return confirm('Excluir ingrediente?')">Excluir</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" style="text-align:center;">Nenhum ingrediente cadastrado.</td></tr>
        @endforelse
    </tbody>
</table>

<style>
    .estoque-baixo {
        color: #b00020;
        font-weight: 600;
    }
</style>
@endsection
