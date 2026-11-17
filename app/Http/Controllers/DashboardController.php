<?php

namespace App\Http\Controllers;

use App\Models\Chamado;
use App\Models\Denuncia;
use App\Models\Profissional;
use App\Models\Usuaria;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Períodos que o administrador pode escolher e de quantos em quantos dias cada ponto do gráfico é desenhado
    private const PERIODOS = [7 => 1, 30 => 3, 90 => 7];

    // Coluna da data de cadastro de cada tabela
    private const COLUNA_DATA_USUARIA = 'dataCadastro';
    private const COLUNA_DATA_PROFISSIONAL = 'dataCadastro';

    public function index(Request $request)
    {
        $admin = User::findOrFail(session('id'));

        // Período vem da URL (?periodo=30). Se vier algo diferente, usa 30 dias.
        $periodo = (int) $request->query('periodo', 30);
        if (!array_key_exists($periodo, self::PERIODOS)) {
            $periodo = 30;
        }
        $inicio = now()->subDays($periodo - 1)->startOfDay();

        // ---------- Cards de resumo ----------
        $resumo = [
            'usuariasAtivas' => Usuaria::ativas()->count(),
            'usuariasNovas' => Usuaria::where(self::COLUNA_DATA_USUARIA, '>=', $inicio)->count(),
            'profissionaisAprovados' => Profissional::aprovados()->count(),
            'profissionaisNovos' => Profissional::where(self::COLUNA_DATA_PROFISSIONAL, '>=', $inicio)->count(),
            'cadastrosPendentes' => Profissional::pendentes()->count(),
            'denunciasPendentes' => Denuncia::pendentes()->count(),
            'chamadosAbertos' => Chamado::abertos()->count(),
        ];

        // ---------- Gráfico e fila prioritária ----------
        $grafico = $this->grafico($inicio, $periodo);
        $fila = $this->filaPrioritaria();

        // ---------- Últimos cadastros de profissionais ----------
        $ultimosProfissionais = Profissional::orderByDesc(self::COLUNA_DATA_PROFISSIONAL)->limit(5)->get();

        return view('painel.dashboard', compact('admin', 'periodo', 'resumo', 'grafico', 'fila', 'ultimosProfissionais'));
    }

    /**
     * Total acumulado de usuárias e de profissionais ao longo do período.
     * Cada ponto do gráfico mostra quantas pessoas existiam até aquele dia.
     */
    private function grafico(Carbon $inicio, int $periodo): array
    {
        $passo = self::PERIODOS[$periodo];
        $usuarias = $this->cadastrosPorDia(Usuaria::class, self::COLUNA_DATA_USUARIA, $inicio);
        $profissionais = $this->cadastrosPorDia(Profissional::class, self::COLUNA_DATA_PROFISSIONAL, $inicio);

        // Quem já tinha se cadastrado antes do início do período
        $totalUsuarias = Usuaria::where(self::COLUNA_DATA_USUARIA, '<', $inicio)->count();
        $totalProfissionais = Profissional::where(self::COLUNA_DATA_PROFISSIONAL, '<', $inicio)->count();

        $rotulos = [];
        $serieUsuarias = [];
        $serieProfissionais = [];

        $dia = $inicio->copy();
        $hoje = now()->startOfDay();

        while ($dia->lte($hoje)) {
            // Soma os cadastros dos dias que entram neste ponto
            $fim = $dia->copy()->addDays($passo - 1)->min($hoje);
            for ($d = $dia->copy(); $d->lte($fim); $d->addDay()) {
                $chave = $d->toDateString();
                $totalUsuarias += $usuarias[$chave] ?? 0;
                $totalProfissionais += $profissionais[$chave] ?? 0;
            }

            $rotulos[] = $fim->format('d/m');
            $serieUsuarias[] = $totalUsuarias;
            $serieProfissionais[] = $totalProfissionais;

            $dia = $fim->copy()->addDay();
        }

        return [
            'rotulos' => $rotulos,
            'usuarias' => $serieUsuarias,
            'profissionais' => $serieProfissionais,
        ];
    }

    // Devolve algo como ['2026-11-03' => 2, '2026-11-05' => 1]
    // $coluna é a coluna da data de cadastro da tabela
    private function cadastrosPorDia(string $model, string $coluna, Carbon $inicio): array
    {
        return $model::where($coluna, '>=', $inicio)
            ->select(DB::raw("DATE($coluna) as dia"), DB::raw('COUNT(*) as total'))
            ->groupBy('dia')
            ->pluck('total', 'dia')
            ->map(fn ($total) => (int) $total)
            ->all();
    }

    /**
     * O que está esperando resposta do administrador, do mais antigo para o mais novo.
     */
    private function filaPrioritaria(): array
    {
        $itens = [];

        foreach (Profissional::pendentes()->get() as $p) {
            $itens[] = [
                'tipo' => 'Cadastro',
                'icone' => 'user-check',
                'titulo' => $p->nomeProfissionalSaude,
                'detalhe' => $p->categoriaProfissional,
                'data' => $p->dataCadastro,
            ];
        }

        foreach (Denuncia::pendentes()->with('usuaria')->get() as $d) {
            $itens[] = [
                'tipo' => 'Denúncia',
                'icone' => 'flag',
                'titulo' => $d->motivoDenuncia,
                'detalhe' => $d->usuaria?->nomeUsuario,
                'data' => $d->dataDenuncia,
            ];
        }

        foreach (Chamado::abertos()->with('profissional')->get() as $c) {
            $itens[] = [
                'tipo' => 'Suporte',
                'icone' => 'headset',
                'titulo' => $c->assuntoChamado,
                'detalhe' => $c->profissional?->nomeProfissionalSaude,
                'data' => $c->dataAbertura,
            ];
        }

        usort($itens, fn ($a, $b) => $a['data'] <=> $b['data']);

        return array_slice($itens, 0, 6);
    }
}