<?php
require_once 'Produto.php';
require_once 'Connection.php'; // Verifique se o caminho do Connection.php está correto

class ProdutoDAO {
    private $conn;

    public function __construct() {
        $this->conn = Connection::getInstance();
    }
    
    // Certifique-se que a tabela seja criada (para evitar o erro que tivemos antes!)
    // Se a tabela já estiver criada no seu banco, pode ignorar este bloco 'exec'
    private function criarTabelaSeNaoExistir() {
        $this->conn->exec("
            CREATE TABLE IF NOT EXISTS Produtos (
                id_produtos INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
                tipo_produtos VARCHAR(100) NOT NULL,
                nome_produtos VARCHAR(100) NOT NULL,
                quant_produtos INT NOT NULL,
                data_venc_produtos DATETIME NOT NULL
            )
        ");
    }

    // CREATE: Cadastra novo produto
    public function criar(Produto $produto) {
        // Opcional: Chama a criação da tabela, caso a conexão não garanta isso.
        $this->criarTabelaSeNaoExistir(); 

        $stmt = $this->conn->prepare("
            INSERT INTO Produtos 
                (tipo_produtos, nome_produtos, quant_produtos, data_venc_produtos)
            VALUES 
                (:tipo, :nome, :quantidade, :dataVenc)
        ");
        $stmt->execute([
            ':tipo' => $produto->getTipo(),
            ':nome' => $produto->getNome(),
            ':quantidade' => $produto->getQuantidade(),
            ':dataVenc' => $produto->getDataVencimento()
        ]);
        return $this->conn->lastInsertId();
    }

    // READ ALL: Lista todos os produtos
    public function ler() {
        $stmt = $this->conn->query("SELECT * FROM Produtos ORDER BY nome_produtos");
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Produto(
                $row['tipo_produtos'], 
                $row['nome_produtos'], 
                $row['quant_produtos'], 
                $row['data_venc_produtos'], 
                $row['id_produtos']
            );
        }
        return $result;
    }
    
    // READ ONE: Busca um produto por ID (usado para edição)
    public function buscarPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM Produtos WHERE id_produtos = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Produto(
                $row['tipo_produtos'], 
                $row['nome_produtos'], 
                $row['quant_produtos'], 
                $row['data_venc_produtos'], 
                $row['id_produtos']
            );
        }
        return null;
    }

    // UPDATE: Atualiza o produto
    public function atualizar(Produto $produto) {
        $stmt = $this->conn->prepare("
            UPDATE Produtos
            SET tipo_produtos = :tipo, nome_produtos = :nome, 
                quant_produtos = :quantidade, data_venc_produtos = :dataVenc
            WHERE id_produtos = :id
        ");
        $stmt->execute([
            ':id' => $produto->getId(),
            ':tipo' => $produto->getTipo(),
            ':nome' => $produto->getNome(),
            ':quantidade' => $produto->getQuantidade(),
            ':dataVenc' => $produto->getDataVencimento()
        ]);
    }

    // DELETE: Exclui o produto
    public function deletar($id) {
        $stmt = $this->conn->prepare("DELETE FROM Produtos WHERE id_produtos = :id");
        $stmt->execute([':id' => $id]);
    }
}
?>