<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Profissional extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
    protected $table = 'tbprofissionalsaude';
    
    protected $primaryKey = 'codProfissionalSaude';
    
    const CREATED_AT = 'dataCadastro';
    const UPDATED_AT = 'dataAtualizacao';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nomeProfissionalSaude',
        'emailProfissionalSaude',
        'telProfissionalSaude',
        'senhaProfissionalSaude',
        'duasEtapasAtiva',
        'provedorLoginProfissional',
        'googleIdProfissional',
        'categoriaProfissional',
        'especialidadeProfissionalSaude',
        'conselhoClasseProfissionalSaude',
        'apresentacaoProfissional',
        'paisProfissional',
        'cidadeProfissional',
        'ufProfissional',
        'cepProfissional',
        'ruaProfissional',
        'numeroProfissional',
        'complementoProfissional',
        'nrFiscalProfissional',
        'fotoPerfilProfissional',
        'statusVerificacao',
        'motivoVerificacao',
        'statusConta',
        'comprovanteConselhoClasseProfissional',
        'documentoComplementarProfissional',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'senhaProfissionalSaude',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'duasEtapasAtiva' => 'boolean',
        ];
    }

    // O site do profissional grava NULL em statusVerificacao quando a pessoa
    // acaba de se cadastrar. Aqui, NULL e "Pendente" significam a mesma coisa.
    public function scopePendentes($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('statusVerificacao')
              ->orWhereIn('statusVerificacao', ['Pendente', 'Em análise']);
        });
    }

    public function scopeAprovados($query)
    {
        return $query->where('statusVerificacao', 'Aprovado');
    }

    // ---------- Apoio para a tela de aprovação de cadastros ----------

    // Status em uma letra, o mesmo código que o JavaScript da tela usa:
    // p = pendente, r = reenvio solicitado, a = aprovado, x = reprovado
    public function getStatusCodigoAttribute(): string
    {
        return match ($this->statusVerificacao) {
            'Aprovado' => 'a',
            // O banco só tem 3 valores: Pendência + conta Suspensa = reprovado; Pendência + conta Ativa = reenvio
            'Pendência' => $this->statusConta === 'Suspensa' ? 'x' : 'r',
            'Reenvio solicitado' => 'r',
            'Reprovado' => 'x',
            default => 'p',
        };
    }

    public function getStatusRotuloAttribute(): string
    {
        return match ($this->status_codigo) {
            'a' => 'Aprovado',
            'r' => 'Reenvio solicitado',
            'x' => 'Reprovado',
            default => 'Pendente',
        };
    }

    // Conselho de classe: usa o que o profissional informou no cadastro.
    // Se ele não preencheu (cadastros antigos), tenta descobrir pela categoria.
    public function getConselhoAttribute(): string
    {
        $informado = trim((string) $this->conselhoClasseProfissionalSaude);

        if ($informado !== '') {
            return mb_strtoupper($informado);
        }

        // A categoria é digitada à mão no cadastro, então procuramos por pedaços do texto
        $categoria = mb_strtolower($this->categoriaProfissional ?? '');

        $conselhos = [
            'CRM' => ['ginec', 'obstet', 'mastolog', 'médic', 'medic'],
            'CRP' => ['psic'],
            'CRN' => ['nutri'],
            'COREN' => ['enfermeir'],
            'CREFITO' => ['fisio'],
        ];

        foreach ($conselhos as $sigla => $palavras) {
            foreach ($palavras as $palavra) {
                if (str_contains($categoria, $palavra)) {
                    return $sigla;
                }
            }
        }

        return '—';
    }

    // Site de consulta pública de cada conselho
    public function getConsultaUrlAttribute(): string
    {
        return match ($this->conselho) {
            'CRM' => 'https://portal.cfm.org.br/busca-medicos/',
            'CRP' => 'https://cadastro.cfp.org.br/',
            'CRN' => 'https://www.cfn.org.br/',
            'COREN' => 'https://servicos.cofen.gov.br/consulta-profissional/',
            'CREFITO' => 'https://www.coffito.gov.br/',
            default => 'https://www.google.com/search?q=consulta+publica+conselho+profissional',
        };
    }

    // URL pública de um arquivo salvo pelo Portal do Profissional.
    // Retorna null se estiver vazio ou se for um caminho temporário perdido (ex.: C:\xampp\tmp\php1234.tmp).
    public static function urlArquivo(?string $caminho): ?string
    {
        if (!$caminho || preg_match('#^[A-Za-z]:[\\\\/]|\.tmp$#', $caminho)) {
            return null;
        }

        // Arquivos de exemplo do seeder ficam em public/images
        if (file_exists(public_path('images/' . $caminho))) {
            return asset('images/' . $caminho);
        }

        return asset('storage/' . $caminho);
    }
}