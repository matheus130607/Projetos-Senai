document.addEventListener('DOMContentLoaded', () => {
    // 1. Mapeamento dos elementos HTML
    const filtrosSidebar = document.querySelector('.loja-sidebar-filters');
    const inputPesquisa = document.getElementById('pesquisa_produtos');
    const todosProdutos = document.querySelectorAll('.produto'); // Todos os cards individuais

    // Mapeamento de todos os contêineres de produtos por ID
    const containerAcessorios = document.getElementById('Flex-produtos-acessorios');
    const containerFeminino = document.getElementById('Flex-produtos-feminino');
    const containerMasculino = document.getElementById('Flex-produtos-masculino');
    const containerSuplementos = document.getElementById('Flex-produtos-suplementos');

    // Array com todos os contêineres para facilitar a manipulação
    const todosContainers = [
        containerAcessorios,
        containerFeminino,
        containerMasculino,
        containerSuplementos
    ];

    // 2. Estado Inicial: Mostrar todos os produtos ao carregar a página
    function mostrarTodosContainers() {
        todosContainers.forEach(container => {
            // Remove a classe 'hidden' de todos os contêineres principais
            container.classList.remove('hidden'); 
        });
        // Garante que todos os cards individuais dentro dos contêineres também estejam visíveis
        todosProdutos.forEach(produto => produto.style.display = 'flex');
    }
    
    // Inicia mostrando tudo
    mostrarTodosContainers(); 

    // 3. Função principal de Filtragem (acionada pelos botões)
    function aplicarFiltro(filtro) {
        // 3.1. Esconde todos os contêineres primeiro
        todosContainers.forEach(container => container.classList.add('hidden'));

        // 3.2. Limpa a pesquisa e mostra todos os cards
        inputPesquisa.value = '';
        todosProdutos.forEach(p => p.style.display = 'flex'); 

        // 3.3. Lógica para exibir contêineres com base no filtro
        switch (filtro) {
            case 'Vestuário':
                // Mostra produtos femininos e masculinos
                if (containerFeminino) containerFeminino.classList.remove('hidden');
                if (containerMasculino) containerMasculino.classList.remove('hidden');
                break;
            case 'Feminino':
                // Mostra apenas Feminino
                if (containerFeminino) containerFeminino.classList.remove('hidden');
                break;
            case 'Masculino':
                // Mostra apenas Masculino
                if (containerMasculino) containerMasculino.classList.remove('hidden');
                break;
            case 'Unisex':
                // Mostra ambos (tratado como Vestuário, mas pode ser ajustado se houver um container 'Unisex' específico)
                if (containerFeminino) containerFeminino.classList.remove('hidden');
                if (containerMasculino) containerMasculino.classList.remove('hidden');
                break;
            case 'Acessórios':
                // Mostra apenas Acessórios
                if (containerAcessorios) containerAcessorios.classList.remove('hidden');
                break;
            case 'Suplementos':
                // Mostra apenas Suplementos
                if (containerSuplementos) containerSuplementos.classList.remove('hidden');
                break;
            default:
                // Se nenhum filtro for aplicado (ou for um filtro desconhecido), mostra todos
                mostrarTodosContainers();
                break;
        }
    }

    // 4. Listener para os Botões de Filtro
    filtrosSidebar.addEventListener('click', (event) => {
        if (event.target.tagName === 'BUTTON') {
            const filtro = event.target.textContent.trim();
            aplicarFiltro(filtro);
        }
    });

    // 5. Função de Pesquisa (Input)
    function pesquisarProdutos() {
        const termo = inputPesquisa.value.toLowerCase().trim();
        
        // Se a pesquisa estiver vazia, volta ao estado inicial (mostra todos os containers)
        if (termo === '') {
            mostrarTodosContainers();
            return;
        }
        
        // Quando a pesquisa está ativa, todos os contêineres são mostrados para que os cards 
        // correspondentes fiquem visíveis, independentemente do filtro anterior.
        todosContainers.forEach(container => container.classList.remove('hidden'));

        let encontrouResultadoNaPesquisa = false;

        todosProdutos.forEach(produto => {
            // Pega o nome do produto. Assumimos que a tag com o nome é a .produto-nome
            const nomeProduto = produto.querySelector('.produto-nome')?.textContent.toLowerCase() || '';
            
            if (nomeProduto.includes(termo)) {
                // Produto que corresponde ao termo de busca
                produto.style.display = 'flex'; // Mostra o card do produto
                encontrouResultadoNaPesquisa = true;
            } else {
                // Produto que NÃO corresponde ao termo de busca
                produto.style.display = 'none'; // Esconde o card do produto
            }
        });
        
        // Lógica adicional (Opcional): Se não encontrou nada, você pode mostrar uma mensagem ou deixar a tela vazia.
        // Neste caso, se não houver resultado, a tela ficará vazia pois todos os cards terão display: none
        if (!encontrouResultadoNaPesquisa && termo.length > 0) {
            console.log('Nenhum produto encontrado com o termo:', termo);
        }
    }

    // 6. Listener do Input de Pesquisa (executa a cada tecla digitada)
    inputPesquisa.addEventListener('input', pesquisarProdutos);
}); 
    function comprar() {
        location.href = "compra.html"
    }

    // Arquivo: loja.js (Adicionar esta função e o listener de eventos)

// Função que envia o ID do produto para o PHP via AJAX
function adicionarAoCarrinho(produtoId) {
    if (!produtoId) {
        console.error("ID do produto não encontrado.");
        return;
    }
    
    // URL do controlador PHP
    const url = 'CarrinhoController.php'; 

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=adicionar&id=${produtoId}` // Envia a ação e o ID
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(`Produto adicionado: ${data.message}\nTotal de itens no carrinho: ${data.total_itens}`);
            // Opcional: Recarregar o carrinho no perfil_usuario se ele estiver aberto em outra aba
        } else {
            alert(`Erro ao adicionar: ${data.message}`);
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
        alert('Ocorreu um erro de comunicação com o servidor.');
    });
}


document.addEventListener('DOMContentLoaded', () => {
    // ... (Código existente de filtros e pesquisa)

    // 7. Listener para os botões "Compre Agora" (Usando a classe .comprar-btn)
    document.querySelectorAll('.comprar-btn').forEach(button => {
        button.addEventListener('click', (event) => {
            // Pega o ID do produto do atributo data-id do botão
            const produtoId = event.currentTarget.getAttribute('data-id');
            adicionarAoCarrinho(produtoId);
        });
    });
});