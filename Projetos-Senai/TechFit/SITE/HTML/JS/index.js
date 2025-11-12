// Pagina Usuario

        function Login() {
            window.location.href = "login.html"; 
        
        }

        function plano() {
            location.href ="Pag_Inicial_CL.html #planos-container";
        }

        function faleconosco() {
            location.href ="#links-footer"
        }

        function loja() {
            window.location.href = "loja.html"
        }

        function home() {
            window.location.href = "Pag_Inicial_CL.html"
        }

        function entrar() {
            window.location.href = "Pag_Inicial_CL.html"
        }

        function entrar_login() {
            window.location.href = "login.html"
        }

        function modalidades() {
            window.location.href = "modalidades.html"
        }

        function franquias() {
            window.location.href = "franquias.html"
        }

        function saibamais_franc() {
            window.location.href = "saibamais_franc.html"
        }

        function perfil_usuario() {
            window.location.href = "perfil_usuario.html"
        }


    

// Pagina Modalidades

        function Unidade(secao) {
            document.getElementById('boxe').classList.add('hidden');
            document.getElementById('pilates').classList.add('hidden');


            document.getElementById(secao).classList.remove('hidden');
        }
        
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
  