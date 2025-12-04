// Pagina Modalidades

function Unidade(secao) {
    document.getElementById('boxe').classList.add('hidden');
    document.getElementById('pilates').classList.add('hidden');
    document.getElementById(secao).classList.remove('hidden');
}

// Filtro de unidades por cidade
document.getElementById('pesquisa_unidade').addEventListener('input', function () {
    const filtro = this.value.trim().toLowerCase();
    const cards = document.querySelectorAll('.modalidade');

    cards.forEach(card => {
        const local = card.dataset.local.toLowerCase();
        card.style.display = (local.includes(filtro) || filtro === "") ? "flex" : "none";
    });
});

// Abrir modal de inscrição
let _modalidadeSelecionada = null;
let _horarioSelecionado = null;

function Inscrição(modalidade) {
    _modalidadeSelecionada = modalidade;
    _horarioSelecionado = null;

    const modal = document.getElementById('inscricao-modal');
    const titulo = document.getElementById('modal-titulo');
    const subtitulo = document.getElementById('modal-subtitulo');
    const dataInput = document.getElementById('data-agendamento');

    titulo.textContent = 'Inscrição: ' + modalidade.charAt(0).toUpperCase() + modalidade.slice(1);
    subtitulo.textContent = 'Escolha a data para a aula de ' + modalidade + ':';
    dataInput.value = '';

    document.querySelectorAll('.horario-btn').forEach(btn => btn.classList.remove('selecionado'));

    modal.classList.remove('hidden');
    setTimeout(() => modal.classList.add('show'), 10); // animação fade
    document.body.style.overflow = "hidden"; // trava scroll
}

// Comportamentos do modal
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('inscricao-modal');
    const btnCancelar = document.getElementById('cancelar-inscricao');
    const btnConfirmar = document.getElementById('confirmar-inscricao');
    const dataInput = document.getElementById('data-agendamento');

    // Seleção de horário
    document.querySelectorAll('.horario-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.horario-btn').forEach(b => b.classList.remove('selecionado'));
            btn.classList.add('selecionado');
            _horarioSelecionado = btn.getAttribute('data-horario');
        });
    });

    // FECHAR MODAL
    function fecharModal() {
        modal.classList.remove('show');
        setTimeout(() => modal.classList.add('hidden'), 200);
        document.body.style.overflow = ""; 
    }

    if (btnCancelar) btnCancelar.addEventListener('click', fecharModal);

    // Fechar ao clicar fora (backdrop)
    modal.addEventListener('click', (e) => {
        if (e.target === modal) fecharModal();
    });

    // CONFIRMAR AGENDAMENTO
    if (btnConfirmar) btnConfirmar.addEventListener('click', () => {
        if (!dataInput.value) {
            alert('Selecione uma data para continuar.');
            return;
        }

        if (!_horarioSelecionado) {
            alert('Selecione um horário para continuar.');
            return;
        }

        const agendamentos = JSON.parse(localStorage.getItem('agendamentos') || '[]');
        agendamentos.unshift({
            modalidade: _modalidadeSelecionada || 'Indefinida',
            data: dataInput.value,
            horario: _horarioSelecionado,
            id: Date.now()
        });

        localStorage.setItem('agendamentos', JSON.stringify(agendamentos));

        fecharModal();
        alert('Aula agendada para ' + dataInput.value + ' às ' + _horarioSelecionado + ' - ' + (_modalidadeSelecionada || ''));
    });
});
