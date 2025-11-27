<?php
require_once __DIR__ . '/Agendamento.php';
require_once __DIR__ . '/Connection.php';
require_once __DIR__ . '/ModalidadeDAO.php';

class AgendamentoDAO {
    private $conn;
    public function __construct() {
        $this->conn = Connection::getInstance();
    }

    public function lerAgendamentos() {
        // Tenta buscar o nome do funcionário (se estiver em Clientes com perfil admin) e da modalidade
        $sql = "SELECT a.*, m.nome_modalidades, c.nome_cliente as nome_funcionario
                FROM Agendamentos a
                LEFT JOIN Modalidades m ON a.id_modalidades = m.id_modalidades
                LEFT JOIN Clientes c ON a.id_funcionario = c.id_cliente
                ORDER BY a.id_agendamentos ASC";

        $stmt = $this->conn->query($sql);
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Agendamento(
                $row['data_agendamentos'],
                $row['id_funcionario'],
                $row['id_modalidades'],
                $row['id_agendamentos'],
                $row['nome_funcionario'] ?? null,
                $row['nome_modalidades'] ?? null
            );
        }
        return $result;
    }

    public function buscarPorId($id) {
        $stmt = $this->conn->prepare("SELECT a.*, m.nome_modalidades, c.nome_cliente as nome_funcionario
            FROM Agendamentos a
            LEFT JOIN Modalidades m ON a.id_modalidades = m.id_modalidades
            LEFT JOIN Clientes c ON a.id_funcionario = c.id_cliente
            WHERE a.id_agendamentos = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Agendamento(
                $row['data_agendamentos'],
                $row['id_funcionario'],
                $row['id_modalidades'],
                $row['id_agendamentos'],
                $row['nome_funcionario'] ?? null,
                $row['nome_modalidades'] ?? null
            );
        }
        return null;
    }

    public function criarAgendamento(Agendamento $a) {
        $stmt = $this->conn->prepare("INSERT INTO Agendamentos (data_agendamentos, id_funcionario, id_modalidades)
            VALUES (:data, :idFunc, :idMod)");
        $stmt->execute([
            ':data' => $a->getData(),
            ':idFunc' => $a->getIdFuncionario(),
            ':idMod' => $a->getIdModalidade()
        ]);
        return $this->conn->lastInsertId();
    }

    public function atualizarAgendamento(Agendamento $a) {
        $stmt = $this->conn->prepare("UPDATE Agendamentos SET data_agendamentos = :data, id_funcionario = :idFunc, id_modalidades = :idMod WHERE id_agendamentos = :id");
        $stmt->execute([
            ':data' => $a->getData(),
            ':idFunc' => $a->getIdFuncionario(),
            ':idMod' => $a->getIdModalidade(),
            ':id' => $a->getId()
        ]);
    }

    public function excluirAgendamento($id) {
        $stmt = $this->conn->prepare("DELETE FROM Agendamentos WHERE id_agendamentos = :id");
        $stmt->execute([':id' => $id]);
    }
}
?>
