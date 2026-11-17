<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Autenticacao
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('id')) {
            return redirect()->route('auth.index')
                ->with('erro', 'Você precisa estar logado para acessar essa página.');
        }

        // Se a conta foi suspensa (ou apagada) depois do login, derruba a sessão
        $ativo = User::where('codAdmin', $request->session()->get('id'))
            ->where('statusConta', 'Ativa')
            ->exists();

        if (!$ativo) {
            $request->session()->flush();
            $request->session()->regenerate();

            return redirect()->route('auth.index')
                ->with('erro', 'Esta conta não existe.');
        }

        return $next($request);
    }
}
