<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private const UFS = [
        'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA',
        'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO',
    ];

    // ---------- LOGIN / LOGOUT ----------

    public function index(Request $request)
    {
        // Quem já está logado vai direto para o painel
        if ($request->session()->has('id')) {
            return redirect()->route('painel.dashboard');
        }

        return view('welcome');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'senha' => 'required|string',
        ], [
            'email.required' => 'Campo e-mail obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'senha.required' => 'Campo senha obrigatório.',
        ]);

        $admin = User::where('emailAdmin', $request->email)->first();

        if (!$admin) {
            return redirect()->route('auth.index')->withInput($request->only('email'))
                ->with('erro', 'E-mail não encontrado.');
        }

        // Conta excluída (suspensa) se comporta como se não existisse
        if ($admin->statusConta === 'Suspensa') {
            return redirect()->route('auth.index')->withInput($request->only('email'))
                ->with('erro', 'Esta conta não existe.');
        }

        if (!Hash::check($request->senha, $admin->senhaAdmin)) {
            return redirect()->route('auth.index')->withInput($request->only('email'))
                ->with('erro', 'Senha incorreta.');
        }

        $request->session()->regenerate();

        session([
            'id' => $admin->codAdmin,
            'nome' => $admin->nomeAdmin,
        ]);

        return redirect()->route('painel.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->regenerate();

        return redirect()->route('auth.index');
    }

    // ---------- PERFIL ----------

    public function perfil()
    {
        $admin = $this->admin();

        return view('user.perfil-adm', compact('admin'));
    }

    public function editar()
    {
        $admin = $this->admin();
        $ufs = self::UFS;

        return view('user.update-adm', compact('admin', 'ufs'));
    }

    public function atualizar(Request $request)
    {
        $admin = $this->admin();

        // Telefone, CEP e nº fiscal são validados só pelos números
        $request->merge([
            'telAdmin' => preg_replace('/\D/', '', (string) $request->telAdmin),
            'cepAdmin' => preg_replace('/\D/', '', (string) $request->cepAdmin),
            'ufAdmin' => strtoupper(trim((string) $request->ufAdmin)),
        ]);

        $request->validate([
            'nomeAdmin' => ['required', 'string', 'min:2', 'max:120', "regex:/^[\p{L}][\p{L}\s'.\-]*$/u"],
            'emailAdmin' => ['required', 'email', 'max:150', Rule::unique('tbadmin', 'emailAdmin')->ignore($admin->codAdmin, 'codAdmin')],
            'telAdmin' => ['nullable', 'regex:/^\d{10,11}$/'],
            'paisAdmin' => ['nullable', 'string', 'max:60'],
            'cidadeAdmin' => ['nullable', 'string', 'max:100'],
            'ufAdmin' => ['nullable', Rule::in(self::UFS)],
            'cepAdmin' => ['nullable', 'regex:/^\d{8}$/'],
            'ruaAdmin' => ['nullable', 'string', 'max:150'],
            'numeroAdmin' => ['nullable', 'string', 'max:10'],
            'complementoAdmin' => ['nullable', 'string', 'max:100'],
        ], [
            'nomeAdmin.required' => 'Campo nome obrigatório.',
            'nomeAdmin.min' => 'O nome deve ter no mínimo :min letras.',
            'nomeAdmin.max' => 'O nome deve ter no máximo :max caracteres.',
            'nomeAdmin.regex' => 'O nome deve conter apenas letras.',

            'emailAdmin.required' => 'Campo e-mail obrigatório.',
            'emailAdmin.email' => 'Informe um e-mail válido.',
            'emailAdmin.unique' => 'Esse e-mail já está cadastrado.',
            'emailAdmin.max' => 'O e-mail deve ter no máximo :max caracteres.',

            'telAdmin.regex' => 'Informe o telefone com DDD. Ex: 11 99999-9999.',

            'paisAdmin.max' => 'O país deve ter no máximo :max caracteres.',
            'cidadeAdmin.max' => 'A cidade deve ter no máximo :max caracteres.',
            'ufAdmin.in' => 'Estado inválido. Escolha uma das opções.',
            'cepAdmin.regex' => 'Informe o CEP com 8 números. Ex: 01100-000.',
            'ruaAdmin.max' => 'A rua deve ter no máximo :max caracteres.',
            'numeroAdmin.max' => 'O número deve ter no máximo :max caracteres.',
            'complementoAdmin.max' => 'O complemento deve ter no máximo :max caracteres.',
        ]);

        $tel = $request->telAdmin;
        if ($tel !== '') {
            $tel = substr($tel, 0, 2) . ' ' . (strlen($tel) === 11
                ? substr($tel, 2, 5) . '-' . substr($tel, 7)
                : substr($tel, 2, 4) . '-' . substr($tel, 6));
        }

        $cep = $request->cepAdmin;
        if ($cep !== '') {
            $cep = substr($cep, 0, 5) . '-' . substr($cep, 5);
        }

        $admin->nomeAdmin = trim($request->nomeAdmin);
        $admin->emailAdmin = $request->emailAdmin;
        $admin->telAdmin = $tel !== '' ? $tel : null;
        $admin->paisAdmin = $request->filled('paisAdmin') ? trim($request->paisAdmin) : null;
        $admin->cidadeAdmin = $request->filled('cidadeAdmin') ? trim($request->cidadeAdmin) : null;
        $admin->ufAdmin = $request->filled('ufAdmin') ? $request->ufAdmin : null;
        $admin->cepAdmin = $cep !== '' ? $cep : null;
        $admin->ruaAdmin = $request->filled('ruaAdmin') ? trim($request->ruaAdmin) : null;
        $admin->numeroAdmin = $request->filled('numeroAdmin') ? trim($request->numeroAdmin) : null;
        $admin->complementoAdmin = $request->filled('complementoAdmin') ? trim($request->complementoAdmin) : null;
        $admin->save();

        // O nome aparece na barra superior, então atualiza na sessão também
        session(['nome' => $admin->nomeAdmin]);

        return redirect()->route('perfil')->with('sucesso', 'Perfil atualizado com sucesso.');
    }

    // ---------- EXCLUIR CONTA (ativa -> suspensa) ----------

    public function excluirConta(Request $request)
    {
        $admin = $this->admin();

        $admin->statusConta = 'Suspensa';
        $admin->save();

        $request->session()->flush();
        $request->session()->regenerate();

        return redirect()->route('auth.index')->with('sucesso', 'Sua conta foi excluída.');
    }

    // ---------- Auxiliar ----------

    private function admin(): User
    {
        return User::findOrFail(session('id'));
    }
}
