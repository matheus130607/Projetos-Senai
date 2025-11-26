<?php

// 1. INICIALIZAÇÃO DA SESSÃO
// Assegura que a sessão esteja iniciada para armazenar o carrinho
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. INCLUSÃO DE DEPENDÊNCIAS (Ajuste o caminho se necessário)
require_once 'ProdutoDAO.php'; 

class CarrinhoController {
    private $produtoDAO;

    public function __construct() {
        $this->produtoDAO = new ProdutoDAO();
        // Inicializa o carrinho na sessão se ainda não existir
        // Estrutura: $_SESSION['carrinho'] = [id_produto => quantidade, ...]
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = []; 
        }
    }

    /**
     * Adiciona um produto ao carrinho ou incrementa sua quantidade.
     * @param int $produtoId ID do produto a ser adicionado.
     * @param int $quantidade Quantidade a ser adicionada.
     */
    public function adicionarProduto($produtoId, $quantidade = 1) {
        $produtoId = (int)$produtoId;
        
        // Verifica se o produto existe no DB para obter detalhes (nome, preço)
        $produto = $this->produtoDAO->buscarPorId($produtoId);

        if (!$produto) {
            return ['status' => 'error', 'message' => 'Produto não encontrado (ID: ' . $produtoId . ').'];
        }

        // Adiciona ou incrementa a quantidade
        if (isset($_SESSION['carrinho'][$produtoId])) {
            $_SESSION['carrinho'][$produtoId] += $quantidade;
        } else {
            $_SESSION['carrinho'][$produtoId] = $quantidade;
        }

        // Retorna a quantidade total atualizada no carrinho
        $totalItens = array_sum($_SESSION['carrinho']);
        
        return ['status' => 'success', 'message' => 'Produto adicionado ao carrinho!', 'total_itens' => $totalItens];
    }

    /**
     * Obtém os detalhes completos dos itens do carrinho para exibição.
     */
    public function getCarrinhoDetalhado() {
        $carrinhoDetalhado = [];
        $totalGeral = 0;

        foreach ($_SESSION['carrinho'] as $produtoId => $quantidade) {
            $produto = $this->produtoDAO->buscarPorId($produtoId);
            
            if ($produto) {
                // É CRÍTICO que Produto.php e ProdutoDAO.php tenham o método getPreco()
                $precoUnitario = $produto->getPreco(); 
                $subTotal = $precoUnitario * $quantidade;
                $totalGeral += $subTotal;
                
                $carrinhoDetalhado[] = [
                    'id' => $produto->getId(),
                    'nome' => $produto->getNome(),
                    'preco_unitario' => number_format($precoUnitario, 2, ',', '.'),
                    'quantidade' => $quantidade,
                    'subtotal' => number_format($subTotal, 2, ',', '.')
                ];
            } else {
                // Limpa o carrinho de IDs de produtos que não existem mais
                unset($_SESSION['carrinho'][$produtoId]);
            }
        }

        return [
            'itens' => $carrinhoDetalhado,
            'total' => number_format($totalGeral, 2, ',', '.')
        ];
    }
    
    /**
     * Processa a requisição AJAX.
     */
    public function processarRequisicao() {
        $action = $_POST['action'] ?? $_GET['action'] ?? null;
        $response = ['status' => 'error', 'message' => 'Ação inválida ou não especificada.'];

        header('Content-Type: application/json');

        switch ($action) {
            case 'adicionar':
                $id = $_POST['id'] ?? null;
                if ($id) {
                    $response = $this->adicionarProduto($id, 1);
                } else {
                    $response['message'] = 'ID do produto não fornecido.';
                }
                break;
            case 'obter':
                // Retorna o carrinho detalhado para exibição
                $response = ['status' => 'success', 'carrinho' => $this->getCarrinhoDetalhado()];
                break;
        }

        echo json_encode($response);
    }
}

// Execução do Controller: Este bloco garante que o controller só seja executado quando chamado via AJAX.
if (isset($_POST['action']) || isset($_GET['action'])) {
    $controller = new CarrinhoController();
    $controller->processarRequisicao();
}
?>