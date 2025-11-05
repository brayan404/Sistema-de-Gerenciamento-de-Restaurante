<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Restaurante</title>
    <link rel="stylesheet" href="{{ asset('css/estilo.css') }}">
</head>
<body>

<header class="topo">
    <h1>Sistema de Gerenciamento de Restaurante</h1>
    <nav>
        <a href="{{ route('clientes.index') }}">Clientes</a>
        <a href="{{ route('pratos.index') }}">Pratos</a>
        <a href="{{ route('ingredientes.index') }}">Ingredientes</a>
        <a href="{{ route('fornecedores.index') }}">Fornecedores</a>
        <a href="{{ route('compras.index') }}">Compras</a>
        <a href="{{ route('encomendas.index') }}">Encomendas</a>
    </nav>
</header>

<main class="conteudo">
    @yield('content')
</main>

<footer class="rodape">
    <p>Sistema de Gerenciamento de Restaurante — Projeto Acadêmico</p>
</footer>

</body>
</html>
