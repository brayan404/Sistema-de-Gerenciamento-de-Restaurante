@extends('layout')

@section('content')
<h2>Nova Encomenda</h2>

@if (session('error'))
  <div class="alert alert-danger" style="margin-bottom:8px;">
    {{ session('error') }}
  </div>
@endif

@if ($errors->any())
  <div class="alert alert-danger">
    <ul style="margin:0; padding-left:18px;">
      @foreach ($errors->all() as $error)
        <li>{!! $error !!}</li>
      @endforeach
    </ul>
  </div>
@endif

<form action="{{ route('encomendas.store') }}" method="POST" id="form-encomenda">
    @csrf

    <label>Cliente (opcional):</label>
    <select name="cliente_id" id="cliente_id">
        <option value="">Cliente de Balcão (padrão)</option>
        @foreach ($clientes as $c)
            @continue (strcasecmp($c->nome, 'Cliente de Balcão') === 0)
            <option value="{{ $c->id }}" {{ old('cliente_id') == $c->id ? 'selected' : '' }}>
                {{ $c->nome }}
            </option>
        @endforeach
    </select>

    <div id="balcao-extra" style="display:none; margin-bottom:10px;">
        <label>Nome (balcão) — opcional:</label>
        <input type="text" name="nome_cliente" id="nome_cliente" value="{{ old('nome_cliente') }}">

        <label>Endereço (balcão) — obrigatório:</label>
        <input type="text" name="endereco_cliente" id="endereco_cliente" value="{{ old('endereco_cliente') }}">

        <label>Telefone (balcão) — opcional:</label>
        <input type="text" name="telefone_cliente" id="telefone_cliente" value="{{ old('telefone_cliente') }}">
    </div>

    <small id="hint-balcao" class="text-muted" style="display:block; margin-top:-10px; margin-bottom:10px;">
    Não selecionando, a encomenda será registrada para "Cliente de Balcão".
    </small>

    <label>Data:</label>
    <input type="date" name="data_encomenda" value="{{ old('data_encomenda', date('Y-m-d')) }}" required>

    <hr style="margin: 15px 0;">

    <div style="display:flex; align-items:center; justify-content:space-between;">
        <h3>Itens da Encomenda</h3>
        <button type="button" class="btn btn-warning" id="btn-add">+ Adicionar Item</button>
    </div>

    <table id="tabela-itens">
        <thead>
            <tr>
                <th style="width:45%;">Prato</th>
                <th style="width:15%;">Quantidade</th>
                <th style="width:20%;">Preço (R$)</th>
                <th style="width:20%;">Subtotal (R$)</th>
                <th style="width:5%;"></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right; font-weight:bold;">Total</td>
                <td><span id="total-geral">0,00</span></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top:15px;">
        <button type="submit" class="btn btn-dark">Salvar Encomenda</button>
        <a href="{{ route('encomendas.index') }}" class="btn btn-secondary">Voltar</a>
    </div>
</form>

<template id="tpl-item">
    <tr>
        <td>
            <select class="prato-select" name="itens[IDX][prato_id]" required>
                <option value="">Selecione...</option>
                @foreach ($pratos as $p)
                    <option value="{{ $p->id }}" data-preco="{{ number_format($p->preco_unitario, 2, '.', '') }}">
                        {{ $p->nome }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" class="qtd-input" name="itens[IDX][quantidade]" min="1" value="1" required>
        </td>
        <td>
            <input type="number" class="preco-input" name="itens[IDX][preco_unitario]" step="0.01" min="0" required>
        </td>
        <td>
            <input type="text" class="subtotal-input" value="0,00" readonly>
        </td>
        <td style="text-align:center;">
            <button type="button" class="btn btn-danger remove-row">x</button>
        </td>
    </tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const sel = document.getElementById('cliente_id');
  const hint = document.getElementById('hint-balcao');
  const bloco = document.getElementById('balcao-extra'); // (vamos criar já já)
  const nome = document.getElementById('nome_cliente');
  const end  = document.getElementById('endereco_cliente');

  function toggleBalcao() {
    const usandoBalcao = !sel.value;
    hint.style.display  = usandoBalcao ? 'block' : 'none';
    if (bloco) bloco.style.display = usandoBalcao ? 'block' : 'none';

    if (end)  end.required  = usandoBalcao;
    if (nome) nome.required = false;
  }

  sel.addEventListener('change', toggleBalcao);
  toggleBalcao();
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.querySelector('#tabela-itens tbody');
    const tpl   = document.querySelector('#tpl-item').content;
    const totalSpan = document.getElementById('total-geral');
    const btnAdd = document.getElementById('btn-add');
    let idx = 0;

    function addRow() {
        const clone = document.importNode(tpl, true);
        clone.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace('IDX', idx);
        });
        tbody.appendChild(clone);
        wireRow(tbody.lastElementChild);
        idx++;
        recalc();
    }

    function wireRow(tr) {
        const pratoSel = tr.querySelector('.prato-select');
        const qtdInput = tr.querySelector('.qtd-input');
        const precoInput = tr.querySelector('.preco-input');

        pratoSel.addEventListener('change', () => {
            const preco = pratoSel.selectedOptions[0]?.dataset.preco || '0.00';
            precoInput.value = preco;
            recalc();
        });

        [qtdInput, precoInput].forEach(el => el.addEventListener('input', recalc));

        tr.querySelector('.remove-row').addEventListener('click', () => {
            tr.remove();
            recalc();
        });
    }

    function recalc() {
        let total = 0;
        tbody.querySelectorAll('tr').forEach(tr => {
            const qtd = parseFloat(tr.querySelector('.qtd-input').value || 0);
            const preco = parseFloat(tr.querySelector('.preco-input').value || 0);
            const sub = qtd * preco;
            tr.querySelector('.subtotal-input').value = sub.toFixed(2).replace('.', ',');
            total += sub;
        });
        totalSpan.textContent = total.toFixed(2).replace('.', ',');
    }

    btnAdd.addEventListener('click', addRow);

    addRow();
});
</script>
@endsection
