<?php
class Agendamento {
    private $id_agendamento;
    private $data_agendamento; // datetime
    private $id_funcionario;
    private $id_modalidade;
    // Campos auxiliares para exibição
    public $nome_funcionario;
    public $nome_modalidade;

    public function __construct($data, $idFunc, $idMod, $id = null, $nomeFunc = null, $nomeMod = null) {
        $this->id_agendamento = $id;
        $this->data_agendamento = $data;
        $this->id_funcionario = $idFunc;
        $this->id_modalidade = $idMod;
        $this->nome_funcionario = $nomeFunc;
        $this->nome_modalidade = $nomeMod;
    }

    public function getId() { return $this->id_agendamento; }
    public function getData() { return $this->data_agendamento; }
    public function getIdFuncionario() { return $this->id_funcionario; }
    public function getIdModalidade() { return $this->id_modalidade; }

    public function setId($id) { $this->id_agendamento = $id; return $this; }
    public function setData($d) { $this->data_agendamento = $d; return $this; }
    public function setIdFuncionario($i) { $this->id_funcionario = $i; return $this; }
    public function setIdModalidade($i) { $this->id_modalidade = $i; return $this; }
}
?>
