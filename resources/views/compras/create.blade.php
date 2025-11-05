@extends('layout')

@section('content')
<h2>Nova Compra</h2>

@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if ($errors->any())
  <div class="alert alert-danger">
    <ul style="margin:0; padding-left:18px;">
      @foreach ($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form action="{{ route('compras.store') }}" method="POST" id="form-compra">
  @csrf

  <div class="grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
    <div>
      <label>Fornecedor:</label>
      <select name="fornecedor_id" required>
        <option value="">-- selecione --</option>
        @foreach($fornecedores as $f)
          <option value="{{ $f->id }}">{{ $f->nome }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label>Data da compra:</label>
      <input type="date" name="data_compra" value="{{ old('data_compra', date('Y-m-d')) }}" required>
    </div>

    <div>
      <label>Nota fiscal (opcional):</label>
      <input type="text" name="nota_fiscal" value="{{ old('nota_fiscal') }}">
    </div>
  </div>

  <h3 style="margin-top:18px;">Itens da compra</h3>

  <table id="tabela-itens">
    <thead>
      <tr>
        <th style="width:38%;">Ingrediente</th>
        <th style="width:18%;">Qtd</th>
        <th style="width:18%;">Preço Unit. (R$)</th>
        <th style="width:10%;">Unid.</th>
        <th style="width:16%;">Ação</th>
      </tr>
    </thead>
    <tbody>
      <!-- linha inicial (index 0) -->
      <tr class="item-row">
        <td>
          <select name="itens[0][ingrediente_id]" class="sel-ingrediente" required>
            <option value="">-- selecione --</option>
            @foreach($ingredientes as $i)
              <option value="{{ $i->id }}" data-unidade="{{ $i->unidade }}">{{ $i->nome }}</option>
            @endforeach
          </select>
        </td>
        <td><input type="number" step="0.001" min="0.001" name="itens[0][quantidade]" required></td>
        <td><input type="number" step="0.01" min="0" name="itens[0][preco_unitario]" required></td>
        <td class="col-unidade">—</td>
        <td>
          <button type="button" class="btn btn-secondary btn-add">Adicionar</button>
          <button type="button" class="btn btn-light btn-remove" disabled>Remover</button>
        </td>
      </tr>
    </tbody>
  </table>

  <button class="btn btn-dark" type="submit" style="margin-top:12px;">Salvar</button>
  <a href="{{ route('compras.index') }}" class="btn btn-secondary">Voltar</a>

</form>

<script>
(function() {
  const tbody = document.querySelector('#tabela-itens tbody');

  function reindex() {
    [...tbody.querySelectorAll('tr.item-row')].forEach((tr, idx) => {
      tr.querySelectorAll('select, input').forEach(el => {
        const isName = el.getAttribute('name');
        if (isName) {
          const newName = isName.replace(/itens\[\d+\]/, 'itens[' + idx + ']');
          el.setAttribute('name', newName);
        }
      });
      const btnRemove = tr.querySelector('.btn-remove');
      if (btnRemove) btnRemove.disabled = (tbody.querySelectorAll('tr.item-row').length === 1);
    });
  }

  function updateUnidade(tr) {
    const sel = tr.querySelector('.sel-ingrediente');
    const unidadeCell = tr.querySelector('.col-unidade');
    const opt = sel.options[sel.selectedIndex];
    unidadeCell.textContent = opt && opt.dataset.unidade ? opt.dataset.unidade : '—';
  }

  tbody.addEventListener('change', function(e){
    if (e.target.classList.contains('sel-ingrediente')) {
      updateUnidade(e.target.closest('tr'));
    }
  });

  tbody.addEventListener('click', function(e){
    if (e.target.classList.contains('btn-add')) {
      const tr = e.target.closest('tr');
      const clone = tr.cloneNode(true);
      clone.querySelectorAll('input').forEach(i => i.value = '');
      const sel = clone.querySelector('select.sel-ingrediente');
      sel.selectedIndex = 0;
      clone.querySelector('.col-unidade').textContent = '—';
      tbody.appendChild(clone);
      reindex();
    }
    if (e.target.classList.contains('btn-remove')) {
      const rows = tbody.querySelectorAll('tr.item-row');
      if (rows.length > 1) {
        e.target.closest('tr').remove();
        reindex();
      }
    }
  });

  updateUnidade(tbody.querySelector('tr.item-row'));
})();
</script>
@endsection
