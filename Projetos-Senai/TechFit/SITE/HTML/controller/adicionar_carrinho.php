<?php
session_start();
require_once '../Model/Connection.php'; // Ajuste o caminho conforme sua estrutura

// Verifica se o usuário está logado (Simulação - ajuste conforme seu sistema de login)
// Se não tiver login, você pode fixar um ID temporário para testes, ex: $id_cliente = 1;
if (!isset($_SESSION['id_cliente'])) {
    $id_cliente = 1; 
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não logado.']);
    exit;
} else {
    $id_cliente = $_SESSION['id_cliente'];
}

// Recebe os dados do JSON enviado pelo JS
$dados = json_decode(file_get_contents("php://input"), true);

if (isset($dados['id_produto'])) {
    $id_produto = $dados['id_produto'];
    $conn = Connection::getInstance();

    // Verifica se já existe no carrinho para apenas aumentar quantidade
    $stmt = $conn->prepare("SELECT id_carrinho, quantidade FROM Carrinho WHERE id_cliente = :cliente AND id_produtos = :produto");
    $stmt->execute([':cliente' => $id_cliente, ':produto' => $id_produto]);
    $itemExistente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($itemExistente) {
        // Atualiza quantidade
        $novaQuant = $itemExistente['quantidade'] + 1;
        $update = $conn->prepare("UPDATE Carrinho SET quantidade = :quant WHERE id_carrinho = :id");
        $update->execute([':quant' => $novaQuant, ':id' => $itemExistente['id_carrinho']]);
    } else {
        // Insere novo
        $insert = $conn->prepare("INSERT INTO Carrinho (id_cliente, id_produtos, quantidade) VALUES (:cliente, :produto, 1)");
        $insert->execute([':cliente' => $id_cliente, ':produto' => $id_produto]);
    }

    echo json_encode(['sucesso' => true, 'mensagem' => 'Produto adicionado ao banco!']);
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos.']);
}
?>