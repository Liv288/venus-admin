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
        Schema::create('tbUsuario', function (Blueprint $table) {
            $table->id('codUsuario');
            $table->string('nomeUsuario', 120);
            $table->string('apelidoUsuario', 60)->nullable();
            $table->string('emailUsuario', 150)->unique()->nullable();
            $table->string('senhaUsuario', 255);
            $table->date('dataNascimentoUsuario');
            $table->string('cidadeUsuario', 100)->nullable();
            $table->string('ufUsuario', 2)->nullable();
            $table->string('fotoPerfilUsuario', 255)->nullable();
            $table->enum('temaUsuario', ['Claro', 'Escuro'])->default('Claro');
            $table->string('idiomaUsuario', 10)->default('pt-BR');
            $table->tinyInteger('anonimaPadraoUsuario')->default(0);
            $table->enum('statusUsuario', ['ativa', 'suspensa', 'excluida'])->default('ativa');
            $table->timestamp('dataCadastro')->useCurrent();
            $table->timestamp('dataAtualizacao')->useCurrent()->useCurrentOnUpdate(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbUsuario');
    }
};
