<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfissionalController;
use App\Http\Controllers\UsuariaController;
use App\Http\Controllers\DenunciaController;

// ---------- Rotas públicas (login) ----------
Route::get('/', [UserController::class, 'index'])->name('auth.index');
Route::post('/login', [UserController::class, 'login'])
    ->middleware('throttle:10,1')
    ->name('auth.login');

// ---------- Rotas protegidas (administrador logado) ----------
Route::middleware('autenticacao')->group(function () {

    Route::post('/logout', [UserController::class, 'logout'])->name('auth.logout');

    Route::get('/painel/dashboard', [DashboardController::class, 'index'])->name('painel.dashboard');

    // Aprovação de cadastros de profissionais
    Route::get('/painel/cadastros', [ProfissionalController::class, 'index'])->name('cadastros.index');
    Route::post('/painel/cadastros/lote', [ProfissionalController::class, 'lote'])->name('cadastros.lote');
    Route::get('/painel/cadastros/{id}', [ProfissionalController::class, 'show'])->name('cadastros.show');
    Route::post('/painel/cadastros/{id}/decidir', [ProfissionalController::class, 'decidir'])->name('cadastros.decidir');

    // Contas de usuárias: lista, ver a conta e suspender/reativar
    Route::get('/painel/usuarias', [UsuariaController::class, 'index'])->name('usuarias.index');
    Route::get('/painel/usuarias/{id}', [UsuariaController::class, 'show'])->name('usuarias.show');
    Route::patch('/painel/usuarias/{id}/status', [UsuariaController::class, 'atualizarStatus'])->name('usuarias.status');

    // Denúncias: lista e decisão (resolver ou reabrir)
    Route::get('/painel/denuncias', [DenunciaController::class, 'index'])->name('denuncias.index');
    Route::patch('/painel/denuncias/{id}/decisao', [DenunciaController::class, 'decidir'])->name('denuncias.decidir');

    // Perfil: visualizar, editar e excluir (suspender) a conta
    Route::get('/perfil', [UserController::class, 'perfil'])->name('perfil');
    Route::get('/perfil/editar', [UserController::class, 'editar'])->name('perfil.editar');
    Route::put('/perfil/editar', [UserController::class, 'atualizar'])->name('perfil.atualizar');
    Route::put('/perfil/excluir', [UserController::class, 'excluirConta'])->name('perfil.excluir');
});
