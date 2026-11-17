<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbadmin', function (Blueprint $table) {
            $table->id('codAdmin');
            $table->string('nomeAdmin', 120);
            $table->string('emailAdmin', 150)->unique();
            $table->string('telAdmin', 14)->nullable();
            $table->string('senhaAdmin', 255);
            $table->string('paisAdmin', 60)->nullable();
            $table->string('cidadeAdmin', 100)->nullable();
            $table->string('ufAdmin', 2)->nullable();
            $table->string('cepAdmin', 10)->nullable();
            $table->string('ruaAdmin', 150)->nullable();
            $table->string('numeroAdmin', 10)->nullable();
            $table->string('complementoAdmin', 100)->nullable();
            $table->string('nrFiscalAdmin', 20)->nullable();
            $table->enum('statusConta', ['Ativa', 'Suspensa'])->default('Ativa');
            $table->timestamp('dataCadastro')->useCurrent();
            $table->timestamp('dataAtualizacao')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbadmin');
    }
};
