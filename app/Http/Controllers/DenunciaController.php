<?php

namespace App\Http\Controllers;

use App\Models\Denuncia;
use Illuminate\Http\Request;

class DenunciaController extends Controller
{
    // ---------- LISTA ----------

    public function index()
    {
        // Mais recentes primeiro (a tela filtra, busca e ordena sem recarregar)
        $denuncias = Denuncia::with('usuaria')->orderByDesc('dataDenuncia')->get();

        // "Em análise" conta como pendente e "Arquivada" como resolvida (a tela tem só esses dois grupos)
        $pendentes = $denuncias->whereIn('statusDenuncia', Denuncia::ABERTAS)->count();
        $resolvidas = $denuncias->whereIn('statusDenuncia', Denuncia::ENCERRADAS)->count();
        $total = $denuncias->count();

        return view('denuncias.index', compact('denuncias', 'total', 'pendentes', 'resolvidas'));
    }

    // ---------- DECISÃO (marcar como resolvida ou reabrir) ----------

    public function decidir(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:pendente,resolvida',
            'justificativa' => 'required|string|max:1000',
        ], [
            'status.required' => 'O status da denúncia é obrigatório.',
            'status.in' => 'O status informado é inválido.',
            'justificativa.required' => 'Informe a justificativa antes de confirmar.',
            'justificativa.max' => 'A justificativa pode ter no máximo :max caracteres.',
        ]);

        $denuncia = Denuncia::findOrFail($id);

        // A tela manda em minúsculas; no banco o status começa com letra maiúscula
        $novoStatus = ucfirst($request->status);

        $jaEstaAberta = in_array($denuncia->statusDenuncia, Denuncia::ABERTAS);

        if ($jaEstaAberta === ($novoStatus === 'Pendente')) {
            return response()->json([
                'message' => 'A denúncia já está com este status.',
            ], 422);
        }

        // Troca o status. A justificativa é exigida na tela, mas por enquanto não é salva:
        // a tabela tbDenuncia não tem coluna para a justificativa nem para o administrador.
        $denuncia->statusDenuncia = $novoStatus;
        $denuncia->save();

        return response()->json([
            'message' => $novoStatus === 'Resolvida' ? 'Denúncia marcada como resolvida.' : 'Denúncia reaberta.',
            'status' => $request->status,
        ]);
    }
}
