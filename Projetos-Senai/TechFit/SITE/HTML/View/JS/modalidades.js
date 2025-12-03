// Pagina Modalidades

        function Unidade(secao) {
            document.getElementById('boxe').classList.add('hidden');
            document.getElementById('pilates').classList.add('hidden');


            document.getElementById(secao).classList.remove('hidden');
        }

        // Filtro de unidades por cidade
    document.getElementById('pesquisa_unidade').addEventListener('input', function () {
    const filtro = this.value.trim().toLowerCase(); // texto digitado
    const cards = document.querySelectorAll('.modalidade'); // todos os cards de qualquer modalidade

    cards.forEach(card => {
    const local = card.dataset.local.toLowerCase();

    if (local.includes(filtro) || filtro === "") {
      card.style.display = "flex"; // mostra
    } else {
      card.style.display = "none"; // esconde
    }
  });
});

// Abrir modal de inscrição e salvar agendamento (visual)
let _modalidadeSelecionada = null;
let _horarioSelecionado = null;

function Inscrição(modalidade) {
  _modalidadeSelecionada = modalidade;
  _horarioSelecionado = null;
  const modal = document.getElementById('inscricao-modal');
  const titulo = document.getElementById('modal-titulo');
  const subtitulo = document.getElementById('modal-subtitulo');
  const dataInput = document.getElementById('data-agendamento');

  if (!modal || !dataInput) return;
  titulo.textContent = 'Inscrição: ' + modalidade.charAt(0).toUpperCase() + modalidade.slice(1);
  subtitulo.textContent = 'Escolha a data para a aula de ' + modalidade + ':';
  dataInput.value = '';
  
  // Remover seleção anterior dos botões de horário
  document.querySelectorAll('.horario-btn').forEach(btn => {
    btn.classList.remove('selecionado');
  });
  
  modal.classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('inscricao-modal');
  const btnCancelar = document.getElementById('cancelar-inscricao');
  const btnConfirmar = document.getElementById('confirmar-inscricao');
  const dataInput = document.getElementById('data-agendamento');
  
  // Adicionar evento aos botões de horário
  document.querySelectorAll('.horario-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.horario-btn').forEach(b => b.classList.remove('selecionado'));
      btn.classList.add('selecionado');
      _horarioSelecionado = btn.getAttribute('data-horario');
    });
  });

  if (btnCancelar) btnCancelar.addEventListener('click', () => {
    if (modal) modal.classList.add('hidden');
  });

  if (btnConfirmar) btnConfirmar.addEventListener('click', () => {
    if (!dataInput || !dataInput.value) {
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

    if (modal) modal.classList.add('hidden');
    alert('Aula agendada para ' + dataInput.value + ' às ' + _horarioSelecionado + ' - ' + (_modalidadeSelecionada || '')); 
  });
});
