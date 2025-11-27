<?php
require_once __DIR__ . '/Franquia.php';
require_once __DIR__ . '/Connection.php';

class FranquiaDAO {
    private $conn;
    public function __construct() { $this->conn = Connection::getInstance(); }

    public function ler() {
        $stmt = $this->conn->query("SELECT * FROM Franquias ORDER BY id_franquia ASC");
        $res = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = new Franquia(
                $row['nome_franquia'],
                $row['descricao_franquia'],
                $row['endereco_franquia'],
                $row['cidade_franquia'],
                $row['estado_franquia'],
                $row['telefone_franquia'],
                $row['id_franquia']
            );
        }
        return $res;
    }

    public function buscarPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM Franquias WHERE id_franquia = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) return new Franquia(
            $row['nome_franquia'],
            $row['descricao_franquia'],
            $row['endereco_franquia'],
            $row['cidade_franquia'],
            $row['estado_franquia'],
            $row['telefone_franquia'],
            $row['id_franquia']
        );
        return null;
    }

    public function criar(Franquia $f) {
        $stmt = $this->conn->prepare("INSERT INTO Franquias (nome_franquia, descricao_franquia, endereco_franquia, cidade_franquia, estado_franquia, telefone_franquia) VALUES (:nome, :desc, :end, :cidade, :estado, :tel)");
        $stmt->execute([
            ':nome' => $f->getNome(),
            ':desc' => $f->getDescricao(),
            ':end' => $f->getEndereco(),
            ':cidade' => $f->getCidade(),
            ':estado' => $f->getEstado(),
            ':tel' => $f->getTelefone()
        ]);
        return $this->conn->lastInsertId();
    }

    public function atualizar(Franquia $f) {
        $stmt = $this->conn->prepare("UPDATE Franquias SET nome_franquia = :nome, descricao_franquia = :desc, endereco_franquia = :end, cidade_franquia = :cidade, estado_franquia = :estado, telefone_franquia = :tel WHERE id_franquia = :id");
        $stmt->execute([
            ':nome' => $f->getNome(),
            ':desc' => $f->getDescricao(),
            ':end' => $f->getEndereco(),
            ':cidade' => $f->getCidade(),
            ':estado' => $f->getEstado(),
            ':tel' => $f->getTelefone(),
            ':id' => $f->getId()
        ]);
    }

    public function excluir($id) {
        $stmt = $this->conn->prepare("DELETE FROM Franquias WHERE id_franquia = :id");
        $stmt->execute([':id' => $id]);
    }
}
?>
