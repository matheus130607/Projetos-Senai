<?php
require_once 'Modalidade.php';
require_once 'Connection.php'; 

class ModalidadeDAO {
    private $conn;

    public function __construct() {
        $this->conn = Connection::getInstance();
    }
    
    // Método de segurança: criação da tabela com a nova estrutura
    private function criarTabelaSeNaoExistir() {
        // ATENÇÃO: Assegura que a FOREIGN KEY referencia uma tabela chamada 'Clientes' com coluna 'id_cliente'
        $this->conn->exec("
            CREATE TABLE IF NOT EXISTS Modalidades (
                id_modalidades INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
                nome_modalidades VARCHAR(255) NOT NULL,
                tipo_modalidades VARCHAR(100) NOT NULL,
                id_funcionario INT NOT NULL,
                FOREIGN KEY (id_funcionario) REFERENCES Clientes (id_cliente)
            )
        ");
    }

    // CREATE: Cadastra nova modalidade
    public function criar(Modalidade $modalidade) {
        $this->criarTabelaSeNaoExistir(); 

        $stmt = $this->conn->prepare("
            INSERT INTO Modalidades 
                (nome_modalidades, tipo_modalidades, id_funcionario)
            VALUES 
                (:nome, :tipo, :idFuncionario)
        ");
        $stmt->execute([
            ':nome' => $modalidade->getNome(),
            ':tipo' => $modalidade->getTipo(),
            ':idFuncionario' => $modalidade->getIdFuncionario()
        ]);
        return $this->conn->lastInsertId();
    }

    // READ ALL: Lista todas as modalidades (COM JOIN para obter o nome do Professor)
    public function ler() {
        $stmt = $this->conn->query("
            SELECT 
                m.id_modalidades, m.nome_modalidades, m.tipo_modalidades, m.id_funcionario, 
                f.nome_cliente as nome_funcionario  
            FROM 
                Modalidades m
            JOIN 
                Clientes f ON m.id_funcionario = f.id_cliente
            ORDER BY 
                m.nome_modalidades
        ");
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Cria o objeto Modalidade
            $modalidade = new Modalidade(
                $row['nome_modalidades'], 
                $row['tipo_modalidades'], 
                $row['id_funcionario'], 
                $row['id_modalidades']
            );
            // Adiciona o nome do funcionário à nova propriedade pública
            $modalidade->nomeFuncionario = $row['nome_funcionario']; 
            $result[] = $modalidade;
        }
        return $result;
    }
    
    // READ ONE: Busca uma modalidade por ID
    public function buscarPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM Modalidades WHERE id_modalidades = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Modalidade(
                $row['nome_modalidades'], 
                $row['tipo_modalidades'], 
                $row['id_funcionario'], 
                $row['id_modalidades']
            );
        }
        return null;
    }

    // UPDATE: Atualiza a modalidade
    public function atualizar(Modalidade $modalidade) {
        $stmt = $this->conn->prepare("
            UPDATE Modalidades
            SET nome_modalidades = :nome, tipo_modalidades = :tipo, 
                id_funcionario = :idFuncionario
            WHERE id_modalidades = :id
        ");
        $stmt->execute([
            ':id' => $modalidade->getId(),
            ':nome' => $modalidade->getNome(),
            ':tipo' => $modalidade->getTipo(),
            ':idFuncionario' => $modalidade->getIdFuncionario()
        ]);
    }

    // DELETE: Exclui a modalidade
    public function deletar($id) {
        $stmt = $this->conn->prepare("DELETE FROM Modalidades WHERE id_modalidades = :id");
        $stmt->execute([':id' => $id]);
    }
}
?>