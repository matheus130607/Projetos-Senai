<?php
// Caminhos robustos
require_once __DIR__ . '/Cliente.php';
require_once __DIR__ . '/Connection.php'; // Assumindo que Connection.php está na pasta raiz ou acima

class ClienteDAO {
    private $conn;

    public function __construct() {
        $this->conn = Connection::getInstance();
    }

    // CREATE: Cadastra novo cliente (inclui o perfil)
    public function criarCliente(Cliente $cliente) {
        $stmt = $this->conn->prepare("
            INSERT INTO Clientes 
                (nome_cliente, CPF_cliente, CEP_cliente, data_nasc_cliente, email_cliente, 
                 endereco_cliente, estado_cliente, senha_cliente, perfil_acesso)
            VALUES 
                (:nome, :cpf, :cep, :dataNasc, :email, :endereco, :estado, :senha, :perfil)
        ");
        $stmt->execute([
            ':nome' => $cliente->getNome(),
            ':cpf' => $cliente->getCPF(),
            ':cep' => $cliente->getCEP(),
            ':dataNasc' => $cliente->getDataNasc(),
            ':email' => $cliente->getEmail(),
            ':endereco' => $cliente->getEndereco(),
            ':estado' => $cliente->getEstado(),
            ':senha' => $cliente->getSenha(),
            ':perfil' => $cliente->getPerfilAcesso()
        ]);
        return $this->conn->lastInsertId();
    }

    // READ ALL: Lista todos os clientes
    public function lerClientes() {
        $stmt = $this->conn->query("SELECT * FROM Clientes ORDER BY nome_cliente");
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Cliente(
                $row['nome_cliente'], $row['CPF_cliente'], $row['CEP_cliente'], 
                $row['data_nasc_cliente'], $row['email_cliente'], $row['endereco_cliente'], 
                $row['estado_cliente'], $row['senha_cliente'], $row['id_cliente'], 
                $row['perfil_acesso']
            );
        }
        return $result;
    }

    // READ ONE: Busca um cliente por ID (Usado para edição no ADM)
    public function buscarPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM Clientes WHERE id_cliente = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Cliente(
                $row['nome_cliente'], $row['CPF_cliente'], $row['CEP_cliente'], 
                $row['data_nasc_cliente'], $row['email_cliente'], $row['endereco_cliente'], 
                $row['estado_cliente'], $row['senha_cliente'], $row['id_cliente'],
                $row['perfil_acesso']
            );
        }
        return null;
    }

    // READ ONE: Busca um cliente por Email (USADO NO LOGIN)
    public function buscarPorEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM Clientes WHERE email_cliente = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Cliente(
                $row['nome_cliente'], $row['CPF_cliente'], $row['CEP_cliente'], 
                $row['data_nasc_cliente'], $row['email_cliente'], $row['endereco_cliente'], 
                $row['estado_cliente'], $row['senha_cliente'], $row['id_cliente'],
                $row['perfil_acesso']
            );
        }
        return null;
    }
    
    // UPDATE: Atualiza o cliente
    public function atualizarCliente(Cliente $cliente) {
        $stmt = $this->conn->prepare("
            UPDATE Clientes
            SET nome_cliente = :nome, CPF_cliente = :cpf, CEP_cliente = :cep, 
                data_nasc_cliente = :dataNasc, email_cliente = :email, 
                endereco_cliente = :endereco, estado_cliente = :estado, 
                senha_cliente = :senha, perfil_acesso = :perfil
            WHERE id_cliente = :id
        ");
        $stmt->execute([
            ':id' => $cliente->getId(),
            ':nome' => $cliente->getNome(),
            ':cpf' => $cliente->getCPF(),
            ':cep' => $cliente->getCEP(),
            ':dataNasc' => $cliente->getDataNasc(),
            ':email' => $cliente->getEmail(),
            ':endereco' => $cliente->getEndereco(),
            ':estado' => $cliente->getEstado(),
            ':senha' => $cliente->getSenha(),
            ':perfil' => $cliente->getPerfilAcesso()
        ]);
    }

    // DELETE: Exclui o cliente
    public function excluirCliente($id) {
        $stmt = $this->conn->prepare("DELETE FROM Clientes WHERE id_cliente = :id");
        $stmt->execute([':id' => $id]);
    }
}
?>