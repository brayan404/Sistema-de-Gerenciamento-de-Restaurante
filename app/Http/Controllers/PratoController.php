<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\Prato;

class PratoController extends Controller
{
    public function index()
    {
        $pratos = Prato::all();
        return view('pratos.index', compact('pratos'));
    }

    public function create()
    {
        return view('pratos.create');
    }

    public function store(Request $request)
    {
        $dados = $request->only(['nome', 'preco_unitario']);

    if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
        $dir = public_path('img/pratos');
        File::ensureDirectoryExists($dir, 0775, true); // cria se não existir

        $nomeArquivo = time().'.'.$request->file('imagem')->extension();
        $request->file('imagem')->move($dir, $nomeArquivo);

        $dados['imagem'] = $nomeArquivo;
    }

    Prato::create($dados);
        return redirect()->route('pratos.index')->with('success', 'Prato cadastrado com sucesso!');
    }

    public function edit(Prato $prato)
    {
        return view('pratos.edit', compact('prato'));
    }

    public function update(Request $request, Prato $prato)
    {
        $dados = $request->only(['nome', 'preco_unitario']);

        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            $nomeArquivo = time() . '.' . $request->imagem->extension();
            $request->imagem->move(public_path('img/pratos'), $nomeArquivo);
            $dados['imagem'] = $nomeArquivo;
        }

        $prato->update($dados);
        return redirect()->route('pratos.index')->with('success', 'Prato atualizado com sucesso!');
    }

    public function destroy(Prato $prato)
    {
        $prato->delete();
        return redirect()->route('pratos.index')->with('success', 'Prato excluído!');
    }
}
