document.addEventListener('DOMContentLoaded', () => {
    // Funcionalidade de filtros por categoria
    const filtrosCategoria = document.querySelectorAll('.filtro-categoria');
    const filtrosGenero = document.querySelectorAll('.filtro-genero');
    const botaoMostrarTodos = document.getElementById('mostrar-todos');
    
    filtrosCategoria.forEach(btn => {
        btn.addEventListener('click', () => {
            const categoria = btn.textContent.trim().toLowerCase();
            ocultarTodos();
            
            if (categoria === 'vestuário') {
                exibirContainer('feminino');
                exibirContainer('masculino');
            } else if (categoria === 'suplementos') {
                exibirContainer('suplementos');
            } else if (categoria === 'acessórios') {
                exibirContainer('acessorios');
            }
        });
    });
    
    filtrosGenero.forEach(btn => {
        btn.addEventListener('click', () => {
            const genero = btn.textContent.trim().toLowerCase();
            ocultarTodos();
            
            if (genero === 'masculino') {
                exibirContainer('masculino');
            } else if (genero === 'feminino') {
                exibirContainer('feminino');
            } else if (genero === 'unisex') {
                exibirContainer('acessorios');
                exibirContainer('suplementos');
            }
        });
    });
    
    // Botão "Mostrar Todos"
    if (botaoMostrarTodos) {
        botaoMostrarTodos.addEventListener('click', () => {
            exibirTodos();
        });
    }
    
    function ocultarTodos() {
        document.querySelectorAll('.Flex-produtos').forEach(container => {
            container.classList.add('hidden');
        });
    }
    
    function exibirTodos() {
        document.querySelectorAll('.Flex-produtos').forEach(container => {
            container.classList.remove('hidden');
        });
    }
    
    function exibirContainer(tipo) {
        const container = document.getElementById('Flex-produtos-' + tipo);
        if (container) {
            container.classList.remove('hidden');
        }
    }
    
    // Funcionalidade de carrinho (código anterior)
    document.querySelectorAll('.comprar-btn').forEach(button => {
        button.addEventListener('click', async (e) => {
            const btn = e.currentTarget;
            const prod = btn.closest('.produto');
            if (!prod) return;

            const nomeEl = prod.querySelector('.produto-nome');
            const precoEl = prod.querySelector('.produto-preco');

            const produto_nome = nomeEl ? nomeEl.textContent.trim() : '';
            let precoText = precoEl ? precoEl.textContent.trim() : '';
            // pega primeiro número encontrado (ex: 99,90 ou 39.90)
            const match = precoText.match(/[\d\.,]+/);
            let preco = match ? match[0].trim() : '0';
            preco = preco.replace(/\./g, '').replace(',', '.'); // transforma 1.234,56 -> 1234.56

            // determinar tipo pelo container pai
            const container = prod.closest('.Flex-produtos');
            let tipo = 'Acessório';
            if (container && container.id) {
                const id = container.id.toLowerCase();
                if (id.includes('feminino')) tipo = 'Feminino';
                else if (id.includes('masculino')) tipo = 'Masculino';
                else if (id.includes('suplementos')) tipo = 'Suplemento';
                else if (id.includes('acessorios') || id.includes('acessórios') || id.includes('acessorio')) tipo = 'Acessório';
            }

            const quantidade = 1;

            // Salvar no localStorage para efeito visual
            let carrinho = JSON.parse(localStorage.getItem('carrinho') || '[]');
            
            // Verificar se o produto já existe no carrinho
            const produtoExistente = carrinho.find(item => item.produto_nome === produto_nome);
            if (produtoExistente) {
                produtoExistente.quantidade += quantidade;
            } else {
                carrinho.push({ produto_nome, tipo, quantidade, preco });
            }
            
            localStorage.setItem('carrinho', JSON.stringify(carrinho));
            alert('Produto adicionado ao carrinho.');

            try {
                const resp = await fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        produto_nome,
                        tipo,
                        quantidade: String(quantidade),
                        preco: String(preco)
                    })
                });
                const json = await resp.json();
                if (json.success) {
                    // Produto adicionado ao banco com sucesso
                } else {
                    if (json.login_required) {
                        alert('Faça login para adicionar ao carrinho.');
                        window.location.href = 'login.php';
                    }
                }
            } catch (err) {
                console.error(err);
                // Continua mesmo com erro, pois o localStorage já foi atualizado
            }
        });
    });
});