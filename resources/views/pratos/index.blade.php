@extends('layout')

@section('content')
<h2>Lista de Pratos</h2>

<div style="margin:10px 0;">
<a href="{{ route('pratos.create') }}" class="btn btn-dark">Novo Prato</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table>
    <thead>
        <tr>
            <th>Imagem</th>
            <th>Nome</th>
            <th>Preço (R$)</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($pratos as $prato)
            <tr>
                <td>
                    @if($prato->imagem)
                        <img src="{{ asset('img/pratos/'.$prato->imagem) }}" width="80" height="60" style="object-fit:cover; border-radius:6px;">
                    @else
                        <span class="text-muted">Sem imagem</span>
                    @endif
                </td>
                <td>{{ $prato->nome }}</td>
                <td>{{ number_format($prato->preco_unitario, 2, ',', '.') }}</td>
                <td>
                    {{-- NOVO: botão para montagem da composição do prato --}}
                    <a href="{{ route('pratos.composicao', $prato->id) }}" class="btn btn-dark">Composição</a>

                    <a href="{{ route('pratos.edit', $prato->id) }}" class="btn btn-warning">Editar</a>

                    <form action="{{ route('pratos.destroy', $prato->id) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" onclick="return confirm('Excluir prato?')">Excluir</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align:center;">Nenhum prato cadastrado.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection