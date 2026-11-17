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
        Schema::create('tbChamadoSuporte', function (Blueprint $table) {
            $table->id('codChamado');
            $table->foreignId('codProfissionalSaude')->nullable()->constrained('tbProfissionalSaude', 'codProfissionalSaude')->nullOnDelete();
            $table->string('assuntoChamado', 150);
            $table->text('descricaoChamado');
            $table->string('anexoChamado', 255);
            $table->enum('statusChamado', ['Aberto', 'Em atendimento', 'Resolvido'])->default('Aberto');
            $table->timestamp('dataAbertura')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbChamadoSuporte');
    }
};
