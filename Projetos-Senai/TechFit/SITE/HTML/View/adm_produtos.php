<?php
// adm_produtos.php

// Define o fuso horário
date_default_timezone_set('America/Sao_Paulo');

// Inicia a sessão se necessário
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Controle de Acesso (Apenas Admin)
if (!isset($_SESSION['user_perfil']) || $_SESSION['user_perfil'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Importa o Controller de Produtos
require_once __DIR__ . '/../controller/ProdutoController.php';

$controller = new ProdutoController();
$mensagem = '';
$produtoParaEdicao = null;

// --- 2. Lógica de Ações (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $id = $_POST['id_produto'] ?? null;
    
    // Tratamento da data (HTML date input -> Formato aceito pelo banco/classe)
    $dataVencPost = $_POST['data_venc_produtos'] ?? '';
    // Se a data vier vazia, definimos como null, senão formatamos (opcional, mas seguro)
    $dataVencFormatada = empty($dataVencPost) ? null : $dataVencPost; 

    // Campos do formulário
    // Nota: A ordem dos parâmetros deve corresponder ao método criar/atualizar no ProdutoController
    // Controller::criar($tipo, $nome, $quantidade, $dataVencimento)
    
    $tipo = $_POST['tipo_produtos'] ?? '';
    $nome = $_POST['nome_produtos'] ?? '';
    $quant = $_POST['quant_produtos'] ?? 0;

    try {
        if ($acao === 'salvar') {
            // Ação de CRIAÇÃO
            $controller->criar(
                $tipo,
                $nome,
                $quant,
                $dataVencFormatada
            );
            $mensagem = "Sucesso! Produto '$nome' cadastrado.";

        } elseif ($acao === 'deletar' && $id) {
            // Ação de EXCLUSÃO
            $controller->deletar($id);
            $mensagem = "Sucesso! Produto ID $id excluído.";
            
        } elseif ($acao === 'editar' && $id) {
            // Ação de PREPARAR EDIÇÃO
            $produtoParaEdicao = $controller->buscarPorId($id);
            if (!$produtoParaEdicao) {
                $mensagem = "Erro: Produto não encontrado para edição.";
            }

        } elseif ($acao === 'atualizar' && $id) {
            // Ação de ATUALIZAR
            $controller->atualizar(
                $id,
                $tipo,
                $nome,
                $quant,
                $dataVencFormatada
            );
            $mensagem = "Sucesso! Produto ID $id atualizado.";
            
            // Redireciona para limpar o POST e remover query params antigos
            header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=' . urlencode($mensagem));
            exit;
        }
    } catch (Exception $e) {
        $mensagem = "Erro ao processar ação: " . $e->getMessage();
    }
}

// Carrega a lista de produtos do banco
$listaProdutos = $controller->ler();
// Ordena por ID (menor -> maior)
if (is_array($listaProdutos)) {
    usort($listaProdutos, function($a, $b) {
        return $a->getId() <=> $b->getId();
    });
}

// Verifica mensagem na URL (pós-redirecionamento)
if (isset($_GET['msg'])) {
    $mensagem = htmlspecialchars($_GET['msg']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADM Tech Fit - Gerenciamento de Produtos</title>
    <link rel="stylesheet" href="./adm.css">
</head>
<body>
    <div class="container mt-5">
        
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h1>Painel ADM Tech Fit</h1>
            <a href="logout.php" class="btn btn-danger">Sair / Logout</a>
        </div>

        <nav class="nav nav-tabs mb-4">
            <a class="nav-link" href="adm_clientes.php">Clientes</a>
            <a class="nav-link active" aria-current="page" href="adm_produtos.php">Produtos</a>
            <a class="nav-link" href="adm_modalidades.php">Modalidades</a>
            <a class="nav-link disabled" href="#">Agendamentos (Em Breve)</a>
            <a class="nav-link disabled" href="#">Franquias (Em Breve)</a>
        </nav>

        <?php if ($mensagem): ?>
            <div class="alert <?php echo strpos($mensagem, 'Sucesso!') !== false ? 'alert-success' : 'alert-warning'; ?>" role="alert">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <?php 
        // Lógica de exibição do formulário (Edição vs Cadastro)
        $isEdicao = $produtoParaEdicao !== null;
        $formTitle = $isEdicao ? "Editando Produto ID: " . $produtoParaEdicao->getId() : "Cadastrar Novo Produto";
        $acaoForm = $isEdicao ? 'atualizar' : 'salvar';
        
        // Prepara a data para o input HTML (Y-m-d) caso esteja editando
        $dataValue = '';
        if ($isEdicao && $produtoParaEdicao->getDataVencimento()) {
            $dataValue = date('Y-m-d', strtotime($produtoParaEdicao->getDataVencimento()));
        }
        ?>

        <hr>
        <h2><?php echo $formTitle; ?></h2>
        
        <form method="POST" class="p-3 mb-4 border rounded bg-light">
            <input type="hidden" name="acao" value="<?php echo $acaoForm; ?>">
            <?php if ($isEdicao): ?>
                <input type="hidden" name="id_produto" value="<?php echo $produtoParaEdicao->getId(); ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome do Produto:</label>
                    <input type="text" class="form-control" id="nome" name="nome_produtos" 
                           placeholder="Ex: Whey Protein, Camiseta, Luva..."
                           value="<?php echo $isEdicao ? htmlspecialchars($produtoParaEdicao->getNome()) : ''; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="tipo" class="form-label">Tipo / Categoria:</label>
                    <select class="form-select" id="tipo" name="tipo_produtos" required>
                        <option value="">Selecione uma categoria</option>
                        <option value="Suplemento" <?php echo ($isEdicao && $produtoParaEdicao->getTipo() === 'Suplemento') ? 'selected' : ''; ?>>Suplemento</option>
                        <option value="Vestuário" <?php echo ($isEdicao && $produtoParaEdicao->getTipo() === 'Vestuário') ? 'selected' : ''; ?>>Vestuário</option>
                        <option value="Acessório" <?php echo ($isEdicao && $produtoParaEdicao->getTipo() === 'Acessório') ? 'selected' : ''; ?>>Acessório</option>
                        <option value="Alimento" <?php echo ($isEdicao && $produtoParaEdicao->getTipo() === 'Alimento') ? 'selected' : ''; ?>>Alimento</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="quantidade" class="form-label">Quantidade em Estoque:</label>
                    <input type="number" class="form-control" id="quantidade" name="quant_produtos" 
                           value="<?php echo $isEdicao ? htmlspecialchars($produtoParaEdicao->getQuantidade()) : ''; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="dataVenc" class="form-label">Data de Vencimento (ou Validade):</label>
                    <input type="date" class="form-control" id="dataVenc" name="data_venc_produtos" 
                           value="<?php echo $dataValue; ?>" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-<?php echo $isEdicao ? 'success' : 'primary'; ?> me-2">
                <?php echo $isEdicao ? 'Salvar Alterações' : 'Cadastrar Produto'; ?>
            </button>
            <?php if ($isEdicao): ?>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">Cancelar Edição</a>
            <?php endif; ?>
        </form>
        <hr>

        <h2>Lista de Estoque (Total: <?php echo count($listaProdutos); ?>)</h2>
        <div class="mb-3">
            <input type="text" id="searchProdutos" class="form-control" placeholder="Pesquisar por nome..." onkeyup="filterTableByName('produtosTable', 2, this.value)">
        </div>
        <table id="produtosTable" class="table table-striped table-hover mt-3">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Nome do Produto</th>
                    <th>Qtd.</th>
                    <th>Vencimento</th>
                    <th style="width: 150px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaProdutos)): ?>
                    <tr>
                        <td colspan="6" class="text-center">Nenhum produto cadastrado no sistema.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($listaProdutos as $prod): ?>
                    <tr>
                        <td><?php echo $prod->getId(); ?></td>
                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($prod->getTipo()); ?></span></td>
                        <td><?php echo htmlspecialchars($prod->getNome()); ?></td>
                        <td>
                            <?php 
                            // Destaque visual para estoque baixo
                            $qtd = $prod->getQuantidade();
                            $classeQtd = $qtd < 10 ? 'text-danger fw-bold' : '';
                            echo "<span class='$classeQtd'>$qtd</span>"; 
                            ?>
                        </td>
                        <td>
                            <?php 
                                // Formata data para BR (d/m/Y)
                                $dataOriginal = $prod->getDataVencimento();
                                echo ($dataOriginal && $dataOriginal != '0000-00-00 00:00:00') 
                                    ? date('d/m/Y', strtotime($dataOriginal)) 
                                    : 'N/A'; 
                            ?>
                        </td>
                        <td class="acoes">
                            <form method="POST">
                                <input type="hidden" name="acao" value="editar">
                                <input type="hidden" name="id_produto" value="<?php echo $prod->getId(); ?>">
                                <button type="submit" class="btn btn-sm btn-primary">Editar</button>
                            </form>
                            
                            <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir o produto: <?php echo htmlspecialchars($prod->getNome()); ?>?');">
                                <input type="hidden" name="acao" value="deletar">
                                <input type="hidden" name="id_produto" value="<?php echo $prod->getId(); ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

    </div>

    <script>
        function filterTableByName(tableId, nameColIndex, query) {
            const filter = query.trim().toLowerCase();
            const table = document.getElementById(tableId);
            if (!table) return;
            const rows = table.tBodies[0].rows;
            for (let i = 0; i < rows.length; i++) {
                const cell = rows[i].cells[nameColIndex];
                if (!cell) continue;
                const text = cell.textContent || cell.innerText;
                rows[i].style.display = text.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
            }
        }
    </script>
</body>
</html>