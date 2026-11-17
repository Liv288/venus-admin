/* =========================================================
   CONTAS DE USUÁRIAS
   JavaScript básico: só getElementById, classList e if.
   As funções chamadas pelos onclick estão no HTML.
   ========================================================= */

// Qual situação será aplicada: 'Suspensa' ou 'Ativa'
var statusEscolhido = '';


/* =========================================================
   1) LISTA: abrir e fechar o menu de filtro
   ========================================================= */
var botaoFiltro = document.getElementById('lu-filtro-btn');
var menuFiltro = document.getElementById('lu-menu');

if (botaoFiltro && menuFiltro) {
    botaoFiltro.addEventListener('click', function (evento) {
        evento.stopPropagation();
        var aberto = menuFiltro.classList.toggle('is-open');
        botaoFiltro.setAttribute('aria-expanded', aberto);
    });

    // Clicar fora do menu fecha ele
    document.addEventListener('click', function () {
        menuFiltro.classList.remove('is-open');
        botaoFiltro.setAttribute('aria-expanded', 'false');
    });
}


/* =========================================================
   1.1) LISTA: baixar PDF
   Usa a função baixarPdf() do adm.js
   ========================================================= */
function baixarPdfUsuarias() {
    baixarPdf(
        document.getElementById('lu-toast'),
        'Lista de usuárias',
        ['Nome', 'E-mail', 'Idade', 'Cadastro', 'Status'],
        LINHAS_PDF,
        'usuarias'
    );
}


/* =========================================================
   2) CONTA: pop-up de confirmação (suspender / reativar)
   ========================================================= */
function abrirConfirmaConta(status) {
    statusEscolhido = status;

    var titulo = document.getElementById('cu-modal-titulo');
    var texto = document.getElementById('cu-modal-texto');
    var icone = document.getElementById('cu-modal-icone');

    if (status === 'Suspensa') {
        titulo.textContent = 'Suspender esta conta?';
        texto.textContent = NOME_USUARIA + ' ficará sem acesso ao aplicativo até a conta ser reativada.';
        icone.innerHTML = '<i data-lucide="user-x"></i>';
        icone.classList.remove('lst-icone--verde');
    } else {
        titulo.textContent = 'Reativar esta conta?';
        texto.textContent = NOME_USUARIA + ' voltará a ter acesso ao aplicativo.';
        icone.innerHTML = '<i data-lucide="user-check"></i>';
        icone.classList.add('lst-icone--verde');
    }

    lucide.createIcons();
    document.getElementById('cu-modal').classList.add('is-open');
}

function fecharConfirmaConta() {
    document.getElementById('cu-modal').classList.remove('is-open');
}

function confirmarConta() {
    document.getElementById('cu-form-status').value = statusEscolhido;
    document.getElementById('cu-form').submit();
}