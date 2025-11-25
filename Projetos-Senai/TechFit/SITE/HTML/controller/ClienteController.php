<?php
// Caminhos robustos, acessando a pasta Model/ que está acima
require_once __DIR__ . '/../Model/ClienteDAO.php';
require_once __DIR__ . '/../Model/Cliente.php';

class ClienteController {
    private $dao;

    public function __construct() {
        $this->dao = new ClienteDAO();
    }

    // Lógica para determinar o perfil com base no e-mail
    private function determinarPerfil($email) {
        if (substr($email, -12) === '@techfit.com') {
            return 'admin';
        }
        return 'cliente';
    }

    // Lista todos os clientes (READ)
    public function ler() {
        return $this->dao->lerClientes();
    }

    // Cadastra novo cliente (CREATE) - Define o perfil
    public function criar($nome, $cpf, $cep, $dataNasc, $email, $endereco, $estado, $senha) {
        
        $perfil = $this->determinarPerfil($email);

        $cliente = new Cliente($nome, $cpf, $cep, $dataNasc, $email, $endereco, $estado, $senha, null, $perfil);
        $this->dao->criarCliente($cliente);
    }
    
    // Busca cliente por ID
    public function buscarPorId($id) {
        return $this->dao->buscarPorId($id);
    }

    // Atualiza cliente (Usado no ADM)
    public function atualizar($id, $nome, $cpf, $cep, $dataNasc, $email, $endereco, $estado, $senha, $perfil) {
        $cliente = new Cliente($nome, $cpf, $cep, $dataNasc, $email, $endereco, $estado, $senha, $id, $perfil);
        $this->dao->atualizarCliente($cliente);
    }

    // Exclui cliente
    public function deletar($id) {
        
        $this->dao->excluirCliente($id); 
    }
    
    // Lógica de Login
    public function login($email, $senha) {
        $cliente = $this->dao->buscarPorEmail($email);

        if ($cliente) {
            // Comparação de senha em texto puro
            if ($cliente->getSenha() === $senha) {
                return $cliente; 
            }
        }
        return null;
    }
}
?>