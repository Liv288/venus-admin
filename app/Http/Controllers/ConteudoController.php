<?php

namespace App\Http\Controllers;

use App\Models\CategoriaConteudo;
use App\Models\Conteudo;
use App\Models\TipoConteudo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ConteudoController extends Controller
{
    // ---------- FORMULÁRIO ----------

    public function create()
    {
        $categorias = CategoriaConteudo::orderBy('nomeTipoCategoria')->get();
        $formatos = TipoConteudo::orderBy('codTipoConteudo')->get();

        return view('conteudos.criar', compact('categorias', 'formatos'));
    }

    // ---------- SALVAR (rascunho ou publicar) ----------

    public function store(Request $request)
    {
        $publicar = $request->acao === 'publicar';

        $request->validate([
            'acao' => 'required|in:rascunho,publicar',
            'titulo' => 'required|string|max:150',
            'categoria' => 'required|exists:tbtipocategoria,codTipoCategoria',
            'formato' => 'required|exists:tbTipoConteudo,codTipoConteudo',
            'texto' => 'required|string|max:8000',
            'fontes' => 'required|string|max:500',
            // A capa só é obrigatória na hora de publicar
            'capa' => [Rule::requiredIf($publicar), 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'titulo.required' => 'Informe o título do conteúdo.',
            'titulo.max' => 'O título deve ter no máximo :max caracteres.',
            'categoria.required' => 'Selecione a categoria.',
            'categoria.exists' => 'Categoria inválida. Escolha uma das opções.',
            'formato.required' => 'Selecione o formato do conteúdo.',
            'formato.exists' => 'Formato inválido. Escolha uma das opções.',
            'texto.required' => 'Escreva o texto do conteúdo.',
            'texto.max' => 'O texto deve ter no máximo :max caracteres.',
            'fontes.required' => 'Cite ao menos uma fonte ou referência.',
            'fontes.max' => 'As fontes devem ter no máximo :max caracteres.',
            'capa.required' => 'Envie uma capa para publicar o conteúdo.',
            'capa.image' => 'A capa precisa ser uma imagem.',
            'capa.mimes' => 'A capa deve ser PNG, JPG ou WEBP.',
            'capa.max' => 'A capa deve ter no máximo 5 MB.',
        ]);

        // Guarda a imagem da capa (se veio uma)
        $caminhoCapa = null;
        if ($request->hasFile('capa')) {
            $caminhoCapa = $request->file('capa')->store('capas-conteudos', 'public');
        }

        try {
            // A tabela não tem coluna para as fontes, então elas vão no fim do texto.
            // A data de criação (dataCriacao) e a de atualização o próprio model preenche.
            Conteudo::create([
                'codAdmin' => session('id'),
                'codTipoCategoria' => $request->categoria,
                'codTipoConteudo' => $request->formato,
                'tituloConteudo' => trim($request->titulo),
                'corpoConteudo' => trim($request->texto) . "\n\nFontes e referências:\n" . trim($request->fontes),
                'capaConteudo' => $caminhoCapa,
                'tempoLeituraMinutos' => Conteudo::calcularTempoLeitura($request->texto),
                'statusConteudo' => $publicar ? 'Publicado' : 'Rascunho',
            ]);
        } catch (\Throwable $erro) {
            // Se o banco falhou, não deixa a imagem solta no disco
            if ($caminhoCapa) {
                Storage::disk('public')->delete($caminhoCapa);
            }

            throw $erro;
        }

        return redirect()->route('conteudos.criar')
            ->with('sucesso', $publicar ? 'Conteúdo publicado com sucesso!' : 'Conteúdo salvo como rascunho!');
    }
}
