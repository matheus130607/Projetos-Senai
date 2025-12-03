document.addEventListener('DOMContentLoaded', () => {
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
                    alert('Produto adicionado ao carrinho.');
                    // opcional: atualizar contador visual do carrinho
                } else {
                    if (json.login_required) {
                        alert('Faça login para adicionar ao carrinho.');
                        window.location.href = 'login.php';
                    } else {
                        alert(json.error || 'Erro ao adicionar ao carrinho.');
                    }
                }
            } catch (err) {
                console.error(err);
                alert('Erro na requisição ao servidor.');
            }
        });
    });
});