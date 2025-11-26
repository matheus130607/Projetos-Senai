<?php
date_default_timezone_set('America/Sao_Paulo');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Controle de Acesso
if (!isset($_SESSION['user_perfil']) || $_SESSION['user_perfil'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../controller/ModalidadeController.php';
require_once __DIR__ . '/../controller/ClienteController.php'; // Necessário para buscar professores

$controller = new ModalidadeController();
$clienteController = new ClienteController(); // Instância para buscar funcionários
$mensagem = '';
$modalidadeParaEdicao = null;

// Obtém a lista de funcionários (clientes com perfil 'admin')
$listaTotalClientes = $clienteController->ler(); 
$listaFuncionarios = [];

// Filtra apenas os administradores/funcionários para serem os responsáveis
foreach ($listaTotalClientes as $cliente) {
    if ($cliente->getPerfilAcesso() === 'admin') {
        // Mapeia ID do cliente (que é o id_funcionario) para o Nome
        $listaFuncionarios[$cliente->getId()] = $cliente->getNome(); 
    }
}


// --- Lógica de Ações (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $id = $_POST['id_modalidade'] ?? null;
    $idFuncionarioPost = $_POST['id_funcionario'] ?? null;
    
    try {
        if ($acao === 'salvar') {
            // Ação de CRIAÇÃO/INSERÇÃO
            $controller->criar(
                $_POST['nome_modalidades'],
                $_POST['tipo_modalidades'],
                $idFuncionarioPost
            );
            $mensagem = "Sucesso! Modalidade '{$_POST['nome_modalidades']}' cadastrada.";

        } elseif ($acao === 'deletar' && $id) {
            // Ação de EXCLUSÃO
            $controller->deletar($id);
            $mensagem = "Sucesso! Modalidade ID $id excluída.";
            
        } elseif ($acao === 'editar' && $id) {
            // Ação de PREPARAR FORMULÁRIO para edição
            $modalidadeParaEdicao = $controller->buscarPorId($id);
            if (!$modalidadeParaEdicao) {
                $mensagem = "Erro: Modalidade não encontrada para edição.";
            }

        } elseif ($acao === 'atualizar' && $id) {
            // Ação de ATUALIZAR os dados
            $controller->atualizar(
                $id,
                $_POST['nome_modalidades'],
                $_POST['tipo_modalidades'],
                $idFuncionarioPost
            );
            $mensagem = "Sucesso! Modalidade ID $id atualizada.";
            // Redireciona para limpar o POST
            header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=' . urlencode($mensagem));
            exit;
        }
    } catch (Exception $e) {
        $mensagem = "Erro ao processar ação: " . $e->getMessage();
    }
}

// Carrega a lista de modalidades atualizada (o DAO já faz JOIN para trazer o nome do funcionário)
$listaModalidades = $controller->ler();

// Ordena por ID (menor -> maior)
if (is_array($listaModalidades)) {
    usort($listaModalidades, function($a, $b) { return $a->getId() <=> $b->getId(); });
}

// Verifica se há mensagem de redirecionamento na URL
if (isset($_GET['msg'])) {
    $mensagem = htmlspecialchars($_GET['msg']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADM Tech Fit - Gerenciamento de Modalidades</title>
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
            <a class="nav-link" href="adm_produtos.php">Produtos</a>
            <a class="nav-link active" aria-current="page" href="adm_modalidades.php">Modalidades</a>
            <a class="nav-link disabled" href="#">Agendamentos (Em Breve)</a>
            <a class="nav-link disabled" href="#">Franquias (Em Breve)</a>
        </nav>
        <?php if ($mensagem): ?>
            <div class="alert <?php echo strpos($mensagem, 'Sucesso!') !== false ? 'alert-success' : 'alert-warning'; ?>" role="alert">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <?php 
        $isEdicao = $modalidadeParaEdicao !== null;
        $formTitle = $isEdicao ? "Editando Modalidade ID: " . $modalidadeParaEdicao->getId() : "Cadastrar Nova Modalidade";
        $acaoForm = $isEdicao ? 'atualizar' : 'salvar';
        ?>

        <hr>
        <h2><?php echo $formTitle; ?></h2>
        <form method="POST" class="p-3 mb-4 border rounded bg-light">
            <input type="hidden" name="acao" value="<?php echo $acaoForm; ?>">
            <?php if ($isEdicao): ?>
                <input type="hidden" name="id_modalidade" value="<?php echo $modalidadeParaEdicao->getId(); ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="nome" class="form-label">Nome da Modalidade (Ex: Yoga, Zumba):</label>
                    <input type="text" class="form-control" id="nome" name="nome_modalidades" 
                           value="<?php echo $isEdicao ? htmlspecialchars($modalidadeParaEdicao->getNome()) : ''; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="tipo" class="form-label">Tipo (Ex: Aeróbico, Força, Mente/Corpo):</label>
                    <input type="text" class="form-control" id="tipo" name="tipo_modalidades" 
                           value="<?php echo $isEdicao ? htmlspecialchars($modalidadeParaEdicao->getTipo()) : ''; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="id_funcionario" class="form-label">Professor Responsável:</label>
                    <select class="form-select" id="id_funcionario" name="id_funcionario" required>
                        <option value="">Selecione um Professor</option>
                        <?php foreach ($listaFuncionarios as $idFunc => $nomeFunc): 
                            $selected = ($isEdicao && $modalidadeParaEdicao->getIdFuncionario() == $idFunc) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $idFunc; ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($nomeFunc); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-<?php echo $isEdicao ? 'success' : 'primary'; ?> me-2">
                <?php echo $isEdicao ? 'Salvar Alterações' : 'Cadastrar Modalidade'; ?>
            </button>
            <?php if ($isEdicao): ?>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">Cancelar Edição</a>
            <?php endif; ?>
        </form>
        <hr>

        <h2>Lista de Modalidades (Total: <?php echo count($listaModalidades); ?>)</h2>
        <div class="mb-3">
            <input type="text" id="searchModalidades" class="form-control" placeholder="Pesquisar por nome..." onkeyup="filterTableByName('modalidadesTable', 1, this.value)">
        </div>
        <table id="modalidadesTable" class="table table-striped table-hover mt-3">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Professor Responsável (ID)</th>
                    <th style="width: 150px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaModalidades)): ?>
                    <tr>
                        <td colspan="5" class="text-center">Nenhuma modalidade cadastrada.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($listaModalidades as $modalidade): ?>
                    <tr>
                        <td><?php echo $modalidade->getId(); ?></td>
                        <td><?php echo htmlspecialchars($modalidade->getNome()); ?></td>
                        <td><?php echo htmlspecialchars($modalidade->getTipo()); ?></td>
                        <td>
                            <?php 
                                // Exibe o nome do funcionário, que agora está na propriedade $nomeFuncionario
                                echo htmlspecialchars($modalidade->nomeFuncionario ?? 'ID não encontrado: ' . $modalidade->getIdFuncionario()); 
                            ?>
                        </td>
                        <td class="acoes">
                            <form method="POST">
                                <input type="hidden" name="acao" value="editar">
                                <input type="hidden" name="id_modalidade" value="<?php echo $modalidade->getId(); ?>">
                                <button type="submit" class="btn btn-sm btn-primary">Editar</button>
                            </form>
                            
                            <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir a modalidade: <?php echo htmlspecialchars($modalidade->getNome()); ?>?');">
                                <input type="hidden" name="acao" value="deletar">
                                <input type="hidden" name="id_modalidade" value="<?php echo $modalidade->getId(); ?>">
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