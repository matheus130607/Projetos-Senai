<?php
require_once __DIR__ . '/../Model/ModalidadeDAO.php';
require_once __DIR__ . '/../Model/Modalidade.php';

class ModalidadeController {
    private $dao;

    public function __construct() {
        $this->dao = new ModalidadeDAO();
    }

    // Cria nova modalidade (CREATE)
    public function criar($nome, $tipo, $idFuncionario) {
        
        if (!is_numeric($idFuncionario) || $idFuncionario <= 0) {
             throw new Exception("É necessário selecionar um Funcionário/Professor válido.");
        }
        
        $modalidade = new Modalidade($nome, $tipo, $idFuncionario);
        $this->dao->criar($modalidade);
    }
    
    // Lista todas as modalidades (READ)
    public function ler() {
        return $this->dao->ler();
    }
    
    // Busca modalidade por ID
    public function buscarPorId($id) {
        return $this->dao->buscarPorId($id);
    }

    // Atualiza modalidade existente (UPDATE)
    public function atualizar($id, $nome, $tipo, $idFuncionario) {
         if (!is_numeric($idFuncionario) || $idFuncionario <= 0) {
             throw new Exception("É necessário selecionar um Funcionário/Professor válido.");
        }
        $modalidade = new Modalidade($nome, $tipo, $idFuncionario, $id);
        $this->dao->atualizar($modalidade);
    }

    // Exclui modalidade (DELETE)
    public function deletar($id) {
        $this->dao->deletar($id);
    }
}
?>