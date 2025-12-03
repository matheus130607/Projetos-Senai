<?php
class Franquia {
    private $id_franquia;
    private $nome;
    private $descricao;
    private $endereco;
    private $cidade;
    private $estado;
    private $telefone;

    public function __construct($nome, $descricao = null, $endereco = null, $cidade = null, $estado = 'SP', $telefone = null, $id = null) {
        $this->id_franquia = $id;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->endereco = $endereco;
        $this->cidade = $cidade;
        $this->estado = $estado;
        $this->telefone = $telefone;
    }

    public function getId() { return $this->id_franquia; }
    public function getNome() { return $this->nome; }
    public function getDescricao() { return $this->descricao; }
    public function getEndereco() { return $this->endereco; }
    public function getCidade() { return $this->cidade; }
    public function getEstado() { return $this->estado; }
    public function getTelefone() { return $this->telefone; }

    public function setId($id) { $this->id_franquia = $id; return $this; }
}
?>
