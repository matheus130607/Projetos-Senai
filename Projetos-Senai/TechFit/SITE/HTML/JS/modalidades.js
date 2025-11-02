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