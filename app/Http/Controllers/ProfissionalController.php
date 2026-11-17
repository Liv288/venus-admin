<?php

namespace App\Http\Controllers;

use App\Mail\CadastroAprovadoMail;
use App\Mail\CadastroRecusadoMail;
use App\Mail\ReenvioDocumentosMail;
use App\Models\Profissional;
use App\Models\ReenvioDocumentos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ProfissionalController extends Controller
{
    private const MENSAGENS = [
        'aprovar' => 'Cadastro aprovado.',
        'reenvio' => 'Reenvio solicitado.',
        'reprovar' => 'Cadastro reprovado.',
    ];

    // ---------- LISTA ----------

    public function index()
    {
        // Do cadastro mais antigo para o mais novo (quem espera há mais tempo aparece primeiro)
        $profissionais = Profissional::orderBy('dataCadastro')->get();

        return view('cadastros.index', compact('profissionais'));
    }

    // ---------- ANÁLISE DE UM CADASTRO ----------

    public function show(string $id)
    {
        $profissional = Profissional::findOrFail($id);

        // Navegação da fila: anterior e próximo entre os pendentes
        $fila = Profissional::pendentes()->orderBy('dataCadastro')->pluck('codProfissionalSaude')->all();
        $posicao = array_search($profissional->codProfissionalSaude, $fila);

        $anterior = ($posicao !== false && $posicao > 0) ? $fila[$posicao - 1] : null;
        $proximo = ($posicao !== false && $posicao < count($fila) - 1) ? $fila[$posicao + 1] : null;

        return view('cadastros.analisar', compact('profissional', 'fila', 'posicao', 'anterior', 'proximo'));
    }

    // ---------- DECISÃO (um cadastro) ----------

    public function decidir(Request $request, string $id)
    {
        $request->validate([
            'decisao' => 'required|in:aprovar,reenvio,reprovar',
            'motivo' => 'required_unless:decisao,aprovar|nullable|string|min:10|max:500',
            'documentos' => ['required_if:decisao,reenvio', 'nullable', 'array', 'min:1'],
            'documentos.*' => 'in:' . implode(',', array_keys(ReenvioDocumentos::DOCUMENTOS)),
        ], [
            'decisao.required' => 'Escolha uma decisão.',
            'documentos.required_if' => 'Escolha pelo menos um documento para reenviar.',
            'motivo.required_unless' => 'Escreva o motivo.',
            'motivo.min' => 'O motivo precisa ter pelo menos 10 letras.',
        ]);

        $profissional = Profissional::findOrFail($id);

        // Só cadastros pendentes podem ser decididos
        if ($profissional->status_codigo !== 'p') {
            return redirect()->route('cadastros.index')->with('erro', 'Este cadastro já foi analisado.');
        }

        $emailEnviado = $this->aplicar($profissional, $request->decisao, $request->motivo, (array) $request->documentos);

        $mensagem = self::MENSAGENS[$request->decisao];
        if (!$emailEnviado) {
            $mensagem .= ' Mas o e-mail não pôde ser enviado.';
        }

        return redirect()->route('cadastros.index')->with('sucesso', $mensagem);
    }

    // ---------- DECISÃO EM LOTE (aprovar ou pedir reenvio de vários) ----------

    public function lote(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
            'decisao' => 'required|in:aprovar,reenvio',
            'motivo' => 'required_if:decisao,reenvio|nullable|string|min:10|max:500',
            'documentos' => ['required_if:decisao,reenvio', 'nullable', 'array', 'min:1'],
            'documentos.*' => 'in:' . implode(',', array_keys(ReenvioDocumentos::DOCUMENTOS)),
        ]);

        $total = 0;
        $emailsFalharam = 0;

        foreach (Profissional::pendentes()->whereIn('codProfissionalSaude', $request->ids)->get() as $profissional) {
            if (!$this->aplicar($profissional, $request->decisao, $request->motivo, (array) $request->documentos)) {
                $emailsFalharam++;
            }
            $total++;
        }

        return response()->json(['total' => $total, 'emailsFalharam' => $emailsFalharam]);
    }

    // ---------- Grava o resultado no banco e avisa o profissional por e-mail ----------
    // statusVerificacao só aceita: Em análise, Aprovado ou Pendência.
    // Por isso:
    //   reenvio  = Pendência + conta Ativa     (profissional recebe um link para reenviar)
    //   reprovar = Pendência + conta Suspensa  (veja Profissional::getStatusCodigoAttribute)
    // O motivo fica em motivoVerificacao.
    // Retorna false se o e-mail não pôde ser enviado (a decisão continua salva).

    private function aplicar(Profissional $profissional, string $decisao, ?string $motivo = null, array $documentos = []): bool
    {
        // Qualquer decisão invalida links de reenvio anteriores
        ReenvioDocumentos::where('codProfissionalSaude', $profissional->codProfissionalSaude)->delete();

        if ($decisao === 'aprovar') {
            $profissional->statusVerificacao = 'Aprovado';
            $profissional->statusConta = 'Ativa';
            $profissional->motivoVerificacao = null;
        } elseif ($decisao === 'reenvio') {
            $profissional->statusVerificacao = 'Pendência';
            $profissional->statusConta = 'Ativa';
            $profissional->motivoVerificacao = $motivo;
        } else {
            $profissional->statusVerificacao = 'Pendência';
            $profissional->statusConta = 'Suspensa';
            $profissional->motivoVerificacao = $motivo;
        }

        $profissional->save();

        try {
            if ($decisao === 'aprovar') {
                Mail::to($profissional->emailProfissionalSaude)->send(new CadastroAprovadoMail($profissional));
            } elseif ($decisao === 'reenvio') {
                $this->enviarEmailReenvio($profissional, (string) $motivo, $documentos);
            } else {
                Mail::to($profissional->emailProfissionalSaude)->send(new CadastroRecusadoMail($profissional, (string) $motivo));
            }
        } catch (\Throwable $e) {
            Log::error('Falha ao enviar e-mail da decisão de cadastro', [
                'profissional' => $profissional->codProfissionalSaude,
                'decisao' => $decisao,
                'erro' => $e->getMessage(),
            ]);

            return false;
        }

        return true;
    }

    // Cria o link de reenvio (aponta para o site do profissional) e manda o e-mail
    private function enviarEmailReenvio(Profissional $profissional, string $motivo, array $documentos): void
    {
        $chave = Str::random(64);

        ReenvioDocumentos::create([
            'codProfissionalSaude' => $profissional->codProfissionalSaude,
            'chave' => $chave,
            'documentos' => implode(',', $documentos),
        ]);

        $nomes = [];
        foreach (ReenvioDocumentos::DOCUMENTOS as $codigo => $nome) {
            if (in_array($codigo, $documentos, true)) {
                $nomes[] = $nome;
            }
        }

        // Sem URL fixa (uso local): mesmo host do admin + porta do site do profissional
        $base = config('venus.profissional_url')
            ?: request()->getScheme() . '://' . request()->getHost() . ':' . config('venus.profissional_porta');

        $link = rtrim($base, '/') . '/user.reenviar-documentos/' . $chave;

        Mail::to($profissional->emailProfissionalSaude)
            ->send(new ReenvioDocumentosMail($profissional, $motivo, $nomes, $link, config('venus.reenvio_dias')));
    }
}
