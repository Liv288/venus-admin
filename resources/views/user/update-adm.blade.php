<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar perfil - Vênus</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.469.0/dist/umd/lucide.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/sidebar-pesquisa.css') }}">
    <link rel="stylesheet" href="{{ asset('css/perfil-config.css') }}">
</head>
<body>

    @include('layouts.sidebar')

    <div class="direita">
        @include('layouts.barra-pesquisa')

        <div class="wrap">

            <div class="head">
                <div>
                    <h1>Editar perfil</h1>
                    <p class="sm">Atualize seus dados para manter o perfil em dia.</p>
                </div>
                <a href="{{ route('perfil') }}" class="bk" aria-label="Voltar">
                    <i data-lucide="arrow-left"></i>
                </a>
            </div>

            @if ($errors->any())
                <div class="alerta erro" role="alert">Confira os campos destacados e tente de novo.</div>
            @endif

            <form class="ef" action="{{ route('perfil.atualizar') }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                <div class="card" style="text-align:center">
                    <div class="pav" style="margin:6px auto 14px">
                        <span>{{ mb_strtoupper(mb_substr($admin->nomeAdmin, 0, 1)) }}</span>
                    </div>
                    <b>{{ $admin->nomeAdmin }}</b>
                </div>

                <div class="stack">

                    <div class="card">
                        <div class="sech">
                            <div class="ico"><i data-lucide="user-round"></i></div>
                            <div>
                                <h3>Informações pessoais</h3>
                                <p>Como você aparece para a equipe.</p>
                            </div>
                        </div>

                        <div class="two">
                            <div class="f">
                                <label for="nomeAdmin">Nome <em>*</em></label>
                                <input type="text" id="nomeAdmin" name="nomeAdmin" maxlength="120"
                                       value="{{ old('nomeAdmin', $admin->nomeAdmin) }}"
                                       class="{{ $errors->has('nomeAdmin') ? 'invalido' : '' }}">
                                @error('nomeAdmin')<div class="erro-campo">{{ $message }}</div>@enderror
                            </div>
                            <div class="f">
                                <label for="emailAdmin">E-mail <em>*</em></label>
                                <input type="email" id="emailAdmin" name="emailAdmin" maxlength="150"
                                       value="{{ old('emailAdmin', $admin->emailAdmin) }}"
                                       class="{{ $errors->has('emailAdmin') ? 'invalido' : '' }}">
                                @error('emailAdmin')<div class="erro-campo">{{ $message }}</div>@enderror
                            </div>
                            <div class="f">
                                <label for="telAdmin">Telefone</label>
                                <input type="text" id="telAdmin" name="telAdmin" maxlength="14" inputmode="numeric"
                                       placeholder="11 99999-9999"
                                       value="{{ old('telAdmin', $admin->telAdmin) }}"
                                       class="{{ $errors->has('telAdmin') ? 'invalido' : '' }}">
                                @error('telAdmin')<div class="erro-campo">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="sech">
                            <div class="ico"><i data-lucide="map-pin"></i></div>
                            <div>
                                <h3>Endereço</h3>
                                <p>Usado em relatórios e documentos oficiais.</p>
                            </div>
                        </div>

                        <div class="two">
                            <div class="f">
                                <label for="paisAdmin">País</label>
                                <input type="text" id="paisAdmin" name="paisAdmin" maxlength="60"
                                       value="{{ old('paisAdmin', $admin->paisAdmin) }}"
                                       class="{{ $errors->has('paisAdmin') ? 'invalido' : '' }}">
                                @error('paisAdmin')<div class="erro-campo">{{ $message }}</div>@enderror
                            </div>
                            <div class="f">
                                <label for="cidadeAdmin">Cidade</label>
                                <input type="text" id="cidadeAdmin" name="cidadeAdmin" maxlength="100"
                                       value="{{ old('cidadeAdmin', $admin->cidadeAdmin) }}"
                                       class="{{ $errors->has('cidadeAdmin') ? 'invalido' : '' }}">
                                @error('cidadeAdmin')<div class="erro-campo">{{ $message }}</div>@enderror
                            </div>
                            <div class="f">
                                <label for="ufAdmin">Estado (UF)</label>
                                <select id="ufAdmin" name="ufAdmin" class="{{ $errors->has('ufAdmin') ? 'invalido' : '' }}">
                                    <option value="">Selecione</option>
                                    @foreach ($ufs as $uf)
                                        <option value="{{ $uf }}" @selected(old('ufAdmin', $admin->ufAdmin) === $uf)>{{ $uf }}</option>
                                    @endforeach
                                </select>
                                @error('ufAdmin')<div class="erro-campo">{{ $message }}</div>@enderror
                            </div>
                            <div class="f">
                                <label for="cepAdmin">CEP</label>
                                <input type="text" id="cepAdmin" name="cepAdmin" maxlength="9" inputmode="numeric"
                                       placeholder="00000-000"
                                       value="{{ old('cepAdmin', $admin->cepAdmin) }}"
                                       class="{{ $errors->has('cepAdmin') ? 'invalido' : '' }}">
                                @error('cepAdmin')<div class="erro-campo">{{ $message }}</div>@enderror
                            </div>
                            <div class="f">
                                <label for="ruaAdmin">Rua</label>
                                <input type="text" id="ruaAdmin" name="ruaAdmin" maxlength="150"
                                       value="{{ old('ruaAdmin', $admin->ruaAdmin) }}"
                                       class="{{ $errors->has('ruaAdmin') ? 'invalido' : '' }}">
                                @error('ruaAdmin')<div class="erro-campo">{{ $message }}</div>@enderror
                            </div>
                            <div class="f">
                                <label for="numeroAdmin">Número</label>
                                <input type="text" id="numeroAdmin" name="numeroAdmin" maxlength="10"
                                       value="{{ old('numeroAdmin', $admin->numeroAdmin) }}"
                                       class="{{ $errors->has('numeroAdmin') ? 'invalido' : '' }}">
                                @error('numeroAdmin')<div class="erro-campo">{{ $message }}</div>@enderror
                            </div>
                            <div class="f">
                                <label for="complementoAdmin">Complemento</label>
                                <input type="text" id="complementoAdmin" name="complementoAdmin" maxlength="100"
                                       value="{{ old('complementoAdmin', $admin->complementoAdmin) }}"
                                       class="{{ $errors->has('complementoAdmin') ? 'invalido' : '' }}">
                                @error('complementoAdmin')<div class="erro-campo">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="savebar">
                        <span class="sm">As alterações só valem depois de salvar.</span>
                        <div style="display:flex;gap:10px">
                            <a href="{{ route('perfil') }}" class="btn g">Cancelar</a>
                            <button type="submit" class="btn p">
                                <i data-lucide="check"></i> Salvar alterações
                            </button>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <script src="{{ asset('js/adm.js') }}"></script>
<script>
    // Máscaras simples: telefone "11 99999-9999" e CEP "00000-000"
    (function () {
        var tel = document.getElementById('telAdmin');
        var cep = document.getElementById('cepAdmin');

        tel.addEventListener('input', function () {
            var n = tel.value.replace(/\D/g, '').slice(0, 11);
            var texto = n;
            if (n.length > 2) {
                var resto = n.slice(2);
                var corte = n.length > 10 ? 5 : 4;
                texto = n.slice(0, 2) + ' ' + resto.slice(0, corte) + (resto.length > corte ? '-' + resto.slice(corte) : '');
            }
            tel.value = texto;
        });

        cep.addEventListener('input', function () {
            var n = cep.value.replace(/\D/g, '').slice(0, 8);
            cep.value = n.length > 5 ? n.slice(0, 5) + '-' + n.slice(5) : n;
        });
    })();
</script>
    <script>lucide.createIcons();</script>

</body>
</html>
