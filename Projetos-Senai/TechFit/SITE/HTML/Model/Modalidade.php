<?php

class Modalidade {
    private $id;
    private $nome;
    private $tipo;
    private $idFuncionario; 
    
    // CORREÇÃO: Propriedade pública para armazenar o nome do funcionário/professor
    // Obtido via JOIN no DAO, mas não é um atributo de persistência direta da tabela Modalidades.
    public $nomeFuncionario; 

    public function __construct($nome, $tipo, $idFuncionario, $id = null) {
        $this->id = $id;
        $this->nome = $nome;
        $this->tipo = $tipo;
        $this->idFuncionario = $idFuncionario;
    }

    // --- Getters ---

    public function getId() {
        return $this->id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function getIdFuncionario() {
        return $this->idFuncionario;
    }

    // --- Setters (Opcionais) ---

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    public function setIdFuncionario($idFuncionario) {
        $this->idFuncionario = $idFuncionario;
    }
}
?>