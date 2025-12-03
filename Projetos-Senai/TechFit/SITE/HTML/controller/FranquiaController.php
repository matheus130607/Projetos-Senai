<?php
require_once __DIR__ . '/../Model/FranquiaDAO.php';
require_once __DIR__ . '/../Model/Franquia.php';

class FranquiaController {
    private $dao;
    private $lastError;
    public function __construct() { $this->dao = new FranquiaDAO(); }
    public function ler() {
        try {
            return $this->dao->ler();
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }
    public function getLastError() { return $this->lastError ?? null; }
    public function criar($nome, $desc, $end, $cidade, $estado, $tel) {
        $f = new Franquia($nome, $desc, $end, $cidade, $estado, $tel);
        return $this->dao->criar($f);
    }
    public function buscarPorId($id) { return $this->dao->buscarPorId($id); }
    public function atualizar($id, $nome, $desc, $end, $cidade, $estado, $tel) {
        $f = new Franquia($nome, $desc, $end, $cidade, $estado, $tel, $id);
        $this->dao->atualizar($f);
    }
    public function deletar($id) { $this->dao->excluir($id); }
}
?>
