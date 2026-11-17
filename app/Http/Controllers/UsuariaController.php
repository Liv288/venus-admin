<?php

namespace App\Http\Controllers;

use App\Models\Usuaria;
use Illuminate\Http\Request;

class UsuariaController extends Controller
{
    // valor no banco => texto na tela
    private const STATUS = ['ativa' => 'Ativa', 'suspensa' => 'Suspensa', 'excluida' => 'Excluída'];

    private const MENSAGENS = [
        'Suspensa' => 'Conta suspensa.',
        'Ativa' => 'Conta reativada.',
    ];

    // ---------- LISTA ----------

    public function index(Request $request)
    {
        $busca = trim((string) $request->query('busca'));

        // Se o filtro vier com algo diferente dos status existentes, ignora
        $status = $request->query('status');
        if (!array_key_exists((string) $status, self::STATUS)) {
            $status = null;
        }

        $consulta = Usuaria::query()
            ->when($status, fn ($q) => $q->where('statusUsuario', $status))
            ->when($busca !== '', function ($q) use ($busca) {
                $q->where(function ($q) use ($busca) {
                    $q->where('nomeUsuario', 'like', "%{$busca}%")
                      ->orWhere('emailUsuario', 'like', "%{$busca}%");
                });
            })
            ->orderByDesc('dataCadastro');

        $usuarias = (clone $consulta)->paginate(10)->withQueryString();

        // O PDF leva todas as usuárias da busca/filtro atual, não só as da página aberta
        $linhasPdf = $consulta->get()->map(fn ($u) => [
            $u->nomeUsuario,
            $u->emailUsuario,
            $u->idade !== null ? $u->idade . ' anos' : '—',
            $u->dataCadastro->format('d/m/Y'),
            $u->status_rotulo,
        ])->all();

        $statusLista = self::STATUS;

        return view('usuarias.index', compact('usuarias', 'busca', 'status', 'statusLista', 'linhasPdf'));
    }

    // ---------- CONTA DE UMA USUÁRIA ----------

    public function show(string $id)
    {
        $usuaria = Usuaria::findOrFail($id);

        $totalDenuncias = $usuaria->denuncias()->count();

        // Só as 5 mais recentes
        $denuncias = $usuaria->denuncias()->latest('dataDenuncia')->limit(5)->get();

        return view('usuarias.show', compact('usuaria', 'totalDenuncias', 'denuncias'));
    }

    // ---------- SUSPENDER / REATIVAR ----------

    public function atualizarStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:Ativa,Suspensa',
        ]);

        $usuaria = Usuaria::findOrFail($id);

        // Conta excluída/desativada pela própria usuária: só ela pode voltar
        if ($usuaria->statusUsuario === 'excluida') {
            return back()->with('erro', 'Esta conta foi desativada pela própria usuária.');
        }

        // A tela manda "Ativa" ou "Suspensa"; no banco fica em minúsculas
        $novoStatus = strtolower($request->status);

        if ($usuaria->statusUsuario === $novoStatus) {
            return back()->with('erro', 'A conta já está com esta situação.');
        }

        $usuaria->statusUsuario = $novoStatus;
        $usuaria->save();

        return back()->with('sucesso', self::MENSAGENS[$request->status]);
    }
}