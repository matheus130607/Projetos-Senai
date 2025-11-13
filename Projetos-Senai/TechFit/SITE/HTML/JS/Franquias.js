//pagina Franquias

        // Função para a barra de pesquisa na página de franquias
function buscarFranquia() {
    const input = document.querySelector('.barra_pesquisa input');
    const filtro = input.value.toLowerCase();
    const franquias = document.querySelectorAll('.franquias');

    franquias.forEach(franquia => {
        const titulo = franquia.querySelector('h3').textContent.toLowerCase();
        if (titulo.includes(filtro)) {
            franquia.style.display = '';
        } else {
            franquia.style.dis//pagina Franquias

        // Função para a barra de pesquisa na página de franquias
function buscarFranquia() {
    const input = document.querySelector('.barra_pesquisa input');
    const filtro = input.value.toLowerCase();
    const franquias = document.querySelectorAll('.franquias');

    franquias.forEach(franquia => {
        const titulo = franquia.querySelector('h3').textContent.toLowerCase();
        if (titulo.includes(filtro)) {
            franquia.style.display = '';
        } else {
            franquia.style.display = 'none';
        }
    });
}

// Adiciona evento de input na barra de pesquisa
document.querySelector('.barra_pesquisa input').addEventListener('input', buscarFranquia);


    //--- Script para popular o modal com informações da franquia ---//


    document.addEventListener('DOMContentLoaded', function () {
        var franquiaModal = document.getElementById('franquiaModal');
        
        franquiaModal.addEventListener('show.bs.modal', function (event) {
            // Botão que acionou o modal
            var button = event.relatedTarget; 

            // Extrai as informações dos atributos data-franquia-*
            var nome = button.getAttribute('data-franquia-nome');
            var descricao = button.getAttribute('data-franquia-descricao');
            var localizacao = button.getAttribute('data-franquia-localizacao');

            // Atualiza o conteúdo do modal
            var modalTitle = franquiaModal.querySelector('.modal-title');
            var modalDescricao = franquiaModal.querySelector('#modal-descricao');
            var modalLocalizacao = franquiaModal.querySelector('#modal-localizacao');

            modalTitle.textContent = nome;
            modalDescricao.textContent = descricao;
            modalLocalizacao.textContent = localizacao;
        });
    });
// A CHAVE "}" EXTRA QUE ESTAVA AQUI FOI REMOVIDAplay = 'none';
        }
    });
}

// Adiciona evento de input na barra de pesquisa
document.querySelector('.barra_pesquisa input').addEventListener('input', buscarFranquia);


    //--- Script para popular o modal com informações da franquia ---//


    document.addEventListener('DOMContentLoaded', function () {
        var franquiaModal = document.getElementById('franquiaModal');
        
        franquiaModal.addEventListener('show.bs.modal', function (event) {
            // Botão que acionou o modal
            var button = event.relatedTarget; 

            // Extrai as informações dos atributos data-franquia-*
            var nome = button.getAttribute('data-franquia-nome');
            var descricao = button.getAttribute('data-franquia-descricao');
            var localizacao = button.getAttribute('data-franquia-localizacao');

            // Atualiza o conteúdo do modal
            var modalTitle = franquiaModal.querySelector('.modal-title');
            var modalDescricao = franquiaModal.querySelector('#modal-descricao');
            var modalLocalizacao = franquiaModal.querySelector('#modal-localizacao');

            modalTitle.textContent = nome;
            modalDescricao.textContent = descricao;
            modalLocalizacao.textContent = localizacao;
        });
    });