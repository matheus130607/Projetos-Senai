<?php
require_once __DIR__ . '/../Model/AgendamentoDAO.php';
require_once __DIR__ . '/../Model/ClienteDAO.php';
require_once __DIR__ . '/../Model/ModalidadeDAO.php';

class AgendamentoController {
    private $dao;
    private $clienteDao;
    private $modalidadeDao;
    private $lastError;

    public function __construct() {
        $this->dao = new AgendamentoDAO();
        $this->clienteDao = new ClienteDAO();
        $this->modalidadeDao = new ModalidadeDAO();
    }

    public function ler() {
        try {
            return $this->dao->lerAgendamentos();
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    public function getLastError() { return $this->lastError ?? null; }

    public function criar($data, $idFunc, $idMod) {
        $ag = new Agendamento($data, $idFunc, $idMod);
        return $this->dao->criarAgendamento($ag);
    }

    public function buscarPorId($id) {
        return $this->dao->buscarPorId($id);
    }

    public function atualizar($id, $data, $idFunc, $idMod) {
        $ag = new Agendamento($data, $idFunc, $idMod, $id);
        $this->dao->atualizarAgendamento($ag);
    }

    public function deletar($id) {
        $this->dao->excluirAgendamento($id);
    }

    // Métodos auxiliares para popular selects nas views
    public function listarFuncionariosAdmin() {
        // Usa ClienteDAO para retornar apenas perfis admin
        $todos = $this->clienteDao->lerClientes();
        $admins = array_filter($todos, function($c){ return $c->getPerfilAcesso() === 'admin'; });
        return $admins;
    }

    public function listarModalidades() {
        return $this->modalidadeDao->ler();
    }
}
?>
