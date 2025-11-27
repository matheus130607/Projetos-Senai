<?php

class Produto {
    private $id;
    private $tipo;
    private $nome;
    private $quantidade;
    private $dataVencimento;

    public function __construct($tipo, $nome, $quantidade, $dataVencimento, $id = null) {
        $this->id = $id;
        $this->tipo = $tipo;
        $this->nome = $nome;
        $this->quantidade = $quantidade;
        $this->dataVencimento = $dataVencimento;
    }

    // --- Getters ---

    public function getId() {
        return $this->id;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getQuantidade() {
        return $this->quantidade;
    }

    // Retorna a data no formato do banco de dados (AAAA-MM-DD HH:MM:SS)
    public function getDataVencimento() {
        return $this->dataVencimento;
    }

    // --- Setters (Opcionais, mas úteis) ---

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setQuantidade($quantidade) {
        $this->quantidade = $quantidade;
    }

    public function setDataVencimento($dataVencimento) {
        $this->dataVencimento = $dataVencimento;
    }
    
    // O ID é geralmente definido pelo banco de dados, então o setter é menos comum
}
?>