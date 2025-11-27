<?php
session_start();
require_once '../Model/Connection.php';

if (!isset($_SESSION['id_cliente'])) {
    echo json_encode([]); // Retorna array vazio se não logado
    exit;
}

$conn = Connection::getInstance();
// Faz um JOIN para pegar o nome e preço do produto baseado no ID salvo no carrinho
$sql = "SELECT c.quantidade, p.nome_produtos, p.tipo_produtos 
        FROM Carrinho c 
        JOIN Produtos p ON c.id_produtos = p.id_produtos 
        WHERE c.id_cliente = :id";

$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $_SESSION['id_cliente']]);
$itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($itens);
?>