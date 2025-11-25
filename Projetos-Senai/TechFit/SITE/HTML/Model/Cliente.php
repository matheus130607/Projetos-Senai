<?php

class Cliente {
    private $id_cliente;
    private $nome_cliente;
    private $CPF_cliente;
    private $CEP_cliente;
    private $data_nasc_cliente;
    private $email_cliente;
    private $endereco_cliente;
    private $estado_cliente;
    private $senha_cliente;
    private $perfil_acesso; // 'cliente' ou 'admin'

    public function __construct(
        $nome, 
        $cpf, 
        $cep, 
        $dataNasc, 
        $email, 
        $endereco, 
        $estado, 
        $senha, 
        $id = null, 
        $perfil = 'cliente'
    ) {
        $this->id_cliente = $id;
        $this->nome_cliente = $nome;
        $this->CPF_cliente = $cpf;
        $this->CEP_cliente = $cep;
        $this->data_nasc_cliente = $dataNasc;
        $this->email_cliente = $email;
        $this->endereco_cliente = $endereco;
        $this->estado_cliente = $estado;
        $this->senha_cliente = $senha;
        $this->perfil_acesso = $perfil; 
    }

    // Getters
    public function getId() { return $this->id_cliente; }
    public function getNome() { return $this->nome_cliente; }
    public function getCPF() { return $this->CPF_cliente; }
    public function getCEP() { return $this->CEP_cliente; }
    public function getDataNasc() { return $this->data_nasc_cliente; }
    public function getEmail() { return $this->email_cliente; }
    public function getEndereco() { return $this->endereco_cliente; }
    public function getEstado() { return $this->estado_cliente; }
    public function getSenha() { return $this->senha_cliente; }
    public function getPerfilAcesso() { return $this->perfil_acesso; }

    // Setters
    public function setId($id) { $this->id_cliente = $id; return $this; }
    public function setNome($nome) { $this->nome_cliente = $nome; return $this; }
    public function setCPF($cpf) { $this->CPF_cliente = $cpf; return $this; }
    public function setCEP($cep) { $this->CEP_cliente = $cep; return $this; }
    public function setDataNasc($dataNasc) { $this->data_nasc_cliente = $dataNasc; return $this; }
    public function setEmail($email) { $this->email_cliente = $email; return $this; }
    public function setEndereco($endereco) { $this->endereco_cliente = $endereco; return $this; }
    public function setEstado($estado) { $this->estado_cliente = $estado; return $this; }
    public function setSenha($senha) { $this->senha_cliente = $senha; return $this; }
    public function setPerfilAcesso($perfil) { $this->perfil_acesso = $perfil; return $this; }
}
?>