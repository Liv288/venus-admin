<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbDenuncia', function (Blueprint $table) {
            $table->id('codDenuncia');
            $table->enum('tipoAlvo', ['Fórum publicação', 'Fórum resposta', 'Perfil profissional']);
            $table->string('codAlvo');
            $table->foreignId('codUsuarioDenunciante')->constrained('tbUsuario', 'codUsuario')->cascadeOnDelete();
            $table->string('motivoDenuncia', 255);
            $table->string('comentarioDenuncia', 255)->nullable();
            $table->enum('gravidade', ['Baixa', 'Média', 'Alta'])->default('Média');
            $table->enum('statusDenuncia', ['Pendente', 'Em análise', 'Resolvida', 'Arquivada'])->default('Pendente');
            $table->timestamp('dataDenuncia')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbDenuncia');
    }
};
