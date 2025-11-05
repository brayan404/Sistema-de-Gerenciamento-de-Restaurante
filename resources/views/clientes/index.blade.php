@extends('layout')

@section('content')
<h2>Lista de Clientes</h2>

<div style="margin:10px 0;">
  <a href="{{ route('clientes.create') }}" class="btn btn-dark">Novo Cliente</a>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<table>
  <thead>
    <tr>
      <th>Nome</th>
      <th>Endereço</th>
      <th>Telefone</th>
      <th>Email</th>
      <th>Ações</th>
    </tr>
  </thead>
  <tbody>
    @forelse($clientes as $c)
      <tr>
        <td>{{ $c->nome }}</td>
        <td>{{ $c->endereco ?? '—' }}</td>
        <td>
          @if($c->telefone)
            {{ preg_replace('/(\d{2})(\d{5})(\d{4})/', '$1 $2-$3', preg_replace('/\D/', '', $c->telefone)) }}
          @else
            —
          @endif
        </td>
        <td>{{ $c->email ?? '—' }}</td>
        <td>
          <a href="{{ route('clientes.edit', $c->id) }}" class="btn btn-warning">Editar</a>
          <form action="{{ route('clientes.destroy', $c->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Excluir cliente?');">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger">Excluir</button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="5" style="text-align:center;">Nenhum cliente cadastrado.</td></tr>
    @endforelse
  </tbody>
</table>
@endsection