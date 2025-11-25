<?php
// Caminhos robustos
require_once __DIR__ . '/../Model/ProdutoDAO.php';
require_once __DIR__ . '/../Model/Produto.php';

class ProdutoController {
    private $dao;

    public function __construct() {
        $this->dao = new ProdutoDAO();
    }

    // Cria um novo produto (CREATE)
    public function criar($tipo, $nome, $quantidade, $dataVencimento) {
        // Validações de negócios (opcional: verificar estoque, formato da data, etc.)
        
        $produto = new Produto($tipo, $nome, $quantidade, $dataVencimento);
        $this->dao->criar($produto);
    }
    
    // Lista todos os produtos (READ)
    public function ler() {
        return $this->dao->ler();
    }
    
    // Busca um produto por ID
    public function buscarPorId($id) {
        return $this->dao->buscarPorId($id);
    }

    // Atualiza um produto existente (UPDATE)
    public function atualizar($id, $tipo, $nome, $quantidade, $dataVencimento) {
        // O ID é necessário para saber qual produto atualizar
        $produto = new Produto($tipo, $nome, $quantidade, $dataVencimento, $id);
        $this->dao->atualizar($produto);
    }

    // Exclui um produto (DELETE)
    public function deletar($id) {
        $this->dao->deletar($id);
    }
}
?>