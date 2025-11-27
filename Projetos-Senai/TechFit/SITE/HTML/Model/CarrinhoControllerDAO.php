<?php
require_once 'Connection.php';

class CarrinhoDAO {
    private $conn;

    public function __construct() {
        $this->conn = Connection::getInstance();
    }
    
    public function registrarCompra($idCliente, $idProduto) {
        try {
            // Verifica se a tabela Compra existe e se o cliente/produto são válidos, etc.
            
            $stmt = $this->conn->prepare("
                INSERT INTO Compra (id_cliente, id_produtos)
                VALUES (:idCliente, :idProduto)
            ");
            
            $stmt->execute([
                ':idCliente' => $idCliente,
                ':idProduto' => $idProduto
            ]);

            return true;
        } catch (PDOException $e) {
            // Em caso de erro (ex: chave estrangeira não existe), lança a exceção
            throw new Exception("Erro ao registrar a compra: " . $e->getMessage());
        }
    }

    // Método para BUSCAR o histórico de compras de um cliente (útil para o perfil!)
    public function listarComprasPorCliente($idCliente) {
        $stmt = $this->conn->prepare("
            SELECT P.nome_produtos, P.tipo_produtos, P.data_venc_produtos, C.id_produtos
            FROM Compra C
            JOIN Produtos P ON C.id_produtos = P.id_produtos
            WHERE C.id_cliente = :idCliente
            ORDER BY C.id_compra DESC 
            -- Assumindo que você adicione 'id_compra' como PK AUTO_INCREMENT na tabela Compra
        ");
        
        $stmt->execute([':idCliente' => $idCliente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

}