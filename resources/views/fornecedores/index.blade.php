@extends('layout')

@section('content')
<h2>Lista de Fornecedores</h2>

<div style="margin:10px 0;">
    <a href="{{ route('fornecedores.create') }}" class="btn btn-dark">Novo Fornecedor</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table style="table-layout: fixed; width: 100%;">
    <thead>
        <tr>
            <th style="width: 22%;">Nome</th>
            <th style="width: 12%;">CNPJ</th>
            <th style="width: 12%;">Telefone</th>
            <th style="width: 19%;">Email</th>
            <th style="width: 21%;">Endereço</th>
            <th style="width: 14%;">Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($fornecedores as $fornecedor)
            <tr>
                <td>{{ $fornecedor->nome }}</td>
                <td>{{ $fornecedor->cnpj }}</td>
                <td>
                    @if($fornecedor->telefone)
                        {{ preg_replace('/(\d{2})(\d{4,5})(\d{4})/', '$1 $2-$3', preg_replace('/\D/', '', $fornecedor->telefone)) }}
                    @else
                        —
                    @endif
                </td>
                <td>{{ $fornecedor->email ?? '—' }}</td>
                <td>{{ $fornecedor->endereco ?? '—' }}</td>
                <td style="text-align:center;">
                    <div style="display:flex; justify-content:center; gap:6px;">
                        <a href="{{ route('fornecedores.edit', $fornecedor->id) }}" class="btn btn-warning">Editar</a>
                        <form action="{{ route('fornecedores.destroy', $fornecedor->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" onclick="return confirm('Excluir fornecedor?')">Excluir</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;">Nenhum fornecedor cadastrado.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection